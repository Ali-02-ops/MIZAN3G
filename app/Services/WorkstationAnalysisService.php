<?php

namespace App\Services;

use App\Models\CulturalCategory;
use App\Services\Generations\TranslationProviderFactory;
use JsonException;
use UnexpectedValueException;

class WorkstationAnalysisService
{
    public function analyze(string $sourceText, string $sourceLanguage, string $targetLanguage, string $analysisModel): array
    {
        $languages = ['ms' => 'Malay', 'en' => 'English', 'id' => 'Indonesian', 'ar' => 'Arabic'];
        $categories = CulturalCategory::query()->where('active', true)->orderBy('sort_order')->get(['id', 'code', 'name']);
        $selection = match ($analysisModel) {
            'qwen' => ['provider' => 'OLLAMA', 'model' => config('services.mizan3g_extraction.ollama_model')],
            'gemini' => ['provider' => 'GEMINI', 'model' => config('services.mizan3g_extraction.gemini_model')],
            'deepseek' => ['provider' => 'DEEPSEEK', 'model' => config('services.mizan3g_extraction.deepseek_model')],
            default => throw new UnexpectedValueException('Unsupported analysis model.'),
        };
        $client = app(TranslationProviderFactory::class)->make($selection['provider']);
        $model = $selection['model'];
        $isLocalQwen = $analysisModel === 'qwen';
        $translationPrompt = "Translate the following {$languages[$sourceLanguage]} text into {$languages[$targetLanguage]}. Preserve cultural terms faithfully; do not explain or add notes. Text:\n{$sourceText}";
        $translation = $client->generate($model, $translationPrompt, ['max_tokens' => $isLocalQwen ? 600 : 1600, 'timeout' => $isLocalQwen ? 45 : null])['translated_text'];
        $schema = ['type' => 'ARRAY', 'items' => ['type' => 'OBJECT', 'properties' => [
            'source_phrase' => ['type' => 'STRING'], 'translated_phrase' => ['type' => 'STRING'], 'category_code' => ['type' => 'STRING', 'enum' => $categories->pluck('code')->all()], 'cultural_significance' => ['type' => 'STRING'],
        ], 'required' => ['source_phrase', 'translated_phrase', 'category_code', 'cultural_significance']]];
        $parameters = ['format' => 'json', 'max_tokens' => $isLocalQwen ? 600 : 1600, 'timeout' => $isLocalQwen ? 60 : null, 'response_schema' => $schema];
        $categoryRules = $categories->map(fn ($category) => "{$category->code} ({$category->name})")->join(', ');
        $prompt = "Identify every distinct culturally significant term or phrase in this {$languages[$sourceLanguage]} text (up to 50). Include practices, ceremonies, food, material culture, institutions, and culturally loaded language. Do not stop after the first term: inspect the entire text and return each distinct item. For each item, provide its exact {$languages[$targetLanguage]} rendering as translated_phrase; use the completed translation below as the authority. Return only a valid JSON array, with no explanation or Markdown: [{\"source_phrase\":string,\"translated_phrase\":string,\"category_code\":string,\"cultural_significance\":string}]. Use only these category codes: {$categoryRules}. Classify ceremonies and religious observances as RELIGION, communal practices as SOCIAL, and food, clothing, objects, or other tangible culture as MATERIAL when applicable. Source text:\n{$sourceText}\nCompleted translation:\n{$translation}";
        try {
            $terms = $this->decodeTerms($client->generate($model, $prompt, $parameters)['translated_text']);
            if (! $isLocalQwen) {
                $firstPass = collect($terms)->pluck('source_phrase')->filter()->join(', ');
                $recallPrompt = "Review this {$languages[$sourceLanguage]} text for additional cultural terms missed by the first pass. Do not repeat: {$firstPass}. Pay special attention to ceremonial food, feasts, material culture, and institutions. Return only a valid JSON array with the same fields and category codes. Text:\n{$sourceText}";
                $terms = array_merge($terms, $this->decodeTerms($client->generate($model, $recallPrompt, $parameters)['translated_text']));
            } else {
                $alreadyFound = collect($terms)->pluck('source_phrase')->filter()->join(', ');
                $materialPrompt = "Independently scan this {$languages[$sourceLanguage]} text for every culturally significant food, dish, clothing item, object, or other tangible cultural item. Do not repeat these already found terms: {$alreadyFound}. Do not return ceremonies or general social practices in this pass. For each item, provide its exact {$languages[$targetLanguage]} rendering from the completed translation. Return only a valid JSON array, with no explanation or Markdown: [{\"source_phrase\":string,\"translated_phrase\":string,\"category_code\":\"MATERIAL\",\"cultural_significance\":string}]. Source text:\n{$sourceText}\nCompleted translation:\n{$translation}";
                try {
                    $terms = array_merge($terms, $this->decodeTerms($client->generate($model, $materialPrompt, $parameters)['translated_text']));
                } catch (\Throwable) {
                    // Keep the general-pass proposals if the focused pass cannot return valid JSON in time.
                }
            }
            $culturalNotice = null;
        } catch (\Throwable) {
            $terms = [];
            $culturalNotice = 'Translation completed, but this model could not produce structured cultural proposals for this text.';
        }

        return ['translation' => trim($translation), 'terms' => collect($terms)->filter(fn ($term) => is_array($term) && isset($term['source_phrase'], $term['translated_phrase'], $term['category_code']) && $categories->contains('code', $term['category_code']))->unique(fn ($term) => mb_strtolower(trim($term['source_phrase'])))->values()->map(fn ($term) => ['source_phrase' => trim($term['source_phrase']), 'translated_phrase' => trim($term['translated_phrase']), 'category_code' => $term['category_code'], 'category_name' => $categories->firstWhere('code', $term['category_code'])->name, 'cultural_significance' => trim($term['cultural_significance'] ?? '')])->all(), 'cultural_notice' => $culturalNotice];
    }

    private function decodeTerms(string $raw): array
    {
        $json = preg_replace('/^```(?:json)?\s*|\s*```$/', '', trim($raw));
        $json = preg_replace('/[\x00-\x1F]/', ' ', $json);
        try { $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR); } catch (JsonException) { throw new UnexpectedValueException('The AI returned an invalid cultural-term response.'); }
        return match (true) {
            is_array($decoded) && array_is_list($decoded) => $decoded,
            is_array($decoded) && isset($decoded['terms']) && is_array($decoded['terms']) => $decoded['terms'],
            is_array($decoded) && isset($decoded['cultural_terms']) && is_array($decoded['cultural_terms']) => $decoded['cultural_terms'],
            is_array($decoded) && isset($decoded['source_phrase']) => [$decoded],
            default => throw new UnexpectedValueException('The AI response did not contain cultural terms.'),
        };
    }
}
