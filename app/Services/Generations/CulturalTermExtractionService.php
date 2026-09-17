<?php

namespace App\Services\Generations;

use App\Models\CulturalCategory;
use App\Models\SourceDocumentVersion;

class CulturalTermExtractionService
{
    public function extract(SourceDocumentVersion $version, int $userId): int
    {
        $categories = CulturalCategory::query()->where('active', true)->orderBy('sort_order')->get(['id', 'code', 'name']);
        $provider = config('services.mizan3g_extraction.provider');
        $model = match ($provider) {
            'OPENAI' => config('services.mizan3g_extraction.openai_model'),
            'ANTHROPIC' => config('services.mizan3g_extraction.anthropic_model'),
            'GEMINI' => config('services.mizan3g_extraction.gemini_model'),
            'DEEPSEEK' => config('services.mizan3g_extraction.deepseek_model'),
            'OLLAMA' => config('services.mizan3g_extraction.ollama_model'),
            default => throw new \InvalidArgumentException("Unsupported extraction provider: {$provider}"),
        };
        $prompt = 'Identify every distinct culturally significant Malay term or phrase in this source text (maximum 10). Be complete: assess communal practices, ceremonies and religious events, food or culinary items, material culture, community institutions, and culturally loaded language independently. Do not collapse different items into one proposal: for example, a communal practice, a feast ceremony, and its food can each be separate terms. Return only a valid JSON array, with no explanation or Markdown: [{"source_phrase":string,"source_sentence":string,"source_context":string,"category_code":string,"cultural_significance":string}]. Use only these category codes: '.$categories->map(fn ($category) => "{$category->code} ({$category->name})")->join(', ').'. Classify ceremonies and religious observances as RELIGION, communal practices as SOCIAL, and food, clothing, objects, or other tangible culture as MATERIAL when applicable. Do not select terms for an audit. Source text:\n'.$version->text_content;
        $schema = ['type' => 'ARRAY', 'items' => ['type' => 'OBJECT', 'properties' => [
            'source_phrase' => ['type' => 'STRING'],
            'source_sentence' => ['type' => 'STRING'],
            'source_context' => ['type' => 'STRING'],
            'category_code' => ['type' => 'STRING', 'enum' => $categories->pluck('code')->all()],
            'cultural_significance' => ['type' => 'STRING'],
        ], 'required' => ['source_phrase', 'category_code', 'cultural_significance']]];
        $parameters = ['format' => 'json', 'temperature' => 0.1, 'max_tokens' => 500, 'response_schema' => $schema];
        $providerClient = app(TranslationProviderFactory::class)->make($provider);
        $items = $this->decodeTerms($providerClient->generate($model, $prompt, $parameters)['translated_text']);
        $existingPhrases = collect($items)->pluck('source_phrase')->filter()->join(', ');
        $recallPrompt = 'Review this Malay source text for culturally significant terms that were missed by the first pass. Return only additional, distinct proposals; do not repeat these existing proposals: '.$existingPhrases.'. Pay special attention to named food or dishes served in a ceremony, feast or communal event, plus material culture and ceremony names. Return only valid JSON using the same fields and category codes. Source text:\n'.$version->text_content;
        $items = array_merge($items, $this->decodeTerms($providerClient->generate($model, $recallPrompt, $parameters)['translated_text']));
        $created = 0;
        foreach ($items as $item) {
            $category = $categories->firstWhere('code', $item['category_code'] ?? null);
            if (! $category || empty($item['source_phrase'])) {
                continue;
            }
            $term = $version->terms()->firstOrCreate(['source_phrase' => $item['source_phrase'], 'category_id' => $category->id], ['source_sentence' => $item['source_sentence'] ?? null, 'source_context' => $item['source_context'] ?? null, 'cultural_significance' => $item['cultural_significance'] ?? null, 'selected_for_audit' => false, 'created_by' => $userId]);
            $created += (int) $term->wasRecentlyCreated;
        }

        return $created;
    }

    /** @return array<int, array<string, mixed>> */
    private function decodeTerms(string $raw): array
    {
        $json = preg_replace('/^```(?:json)?\s*|\s*```$/', '', trim($raw));
        $json = preg_replace('/[\x00-\x1F]/', ' ', $json);
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return match (true) {
            is_array($decoded) && array_is_list($decoded) => $decoded,
            is_array($decoded) && isset($decoded['source_phrase']) => [$decoded],
            is_array($decoded) && isset($decoded['terms']) && is_array($decoded['terms']) => $decoded['terms'],
            is_array($decoded) && isset($decoded['cultural_terms']) && is_array($decoded['cultural_terms']) => $decoded['cultural_terms'],
            is_array($decoded) && isset($decoded['items']) && is_array($decoded['items']) => $decoded['items'],
            default => throw new \UnexpectedValueException('The AI response did not contain a cultural-term list.'),
        };
    }
}
