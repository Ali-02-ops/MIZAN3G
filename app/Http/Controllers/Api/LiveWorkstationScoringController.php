<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Generations\TranslationProviderFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LiveWorkstationScoringController extends Controller
{
    public function score(Request $request, TranslationProviderFactory $providers): JsonResponse
    {
        set_time_limit(180);
        $data = $request->validate([
            'source_text' => ['required', 'string', 'max:5000'],
            'source_language' => ['required', Rule::in(['ms', 'en', 'id', 'ar'])],
            'target_language' => ['required', Rule::in(['ms', 'en', 'id', 'ar'])],
            'analysis_model' => ['required', Rule::in(['qwen', 'gemini', 'deepseek'])],
            'terms' => ['required', 'array', 'min:1', 'max:50'],
            'terms.*.source_phrase' => ['required', 'string', 'max:1000'],
            'terms.*.translated_phrase' => ['required', 'string', 'max:1000'],
        ]);
        $selection = match ($data['analysis_model']) {
            'qwen' => ['provider' => 'OLLAMA', 'model' => config('services.mizan3g_extraction.ollama_model')],
            'gemini' => ['provider' => 'GEMINI', 'model' => config('services.mizan3g_extraction.gemini_model')],
            'deepseek' => ['provider' => 'DEEPSEEK', 'model' => config('services.mizan3g_extraction.deepseek_model')],
        };
        $client = $providers->make($selection['provider']);
        $targetLanguage = ['ar' => 'Arabic', 'ms' => 'Malay', 'en' => 'English', 'id' => 'Indonesian'][$data['target_language']];
        $procedures = [
            'PA' => 'Translate into '.$targetLanguage.' only while preserving source-culture references faithfully.',
            'PB' => 'Translate into natural '.$targetLanguage.' only for a target-culture reader while retaining traceable cultural meaning.',
            'PC' => 'Translate the full text into coherent natural '.$targetLanguage.' only at macro text level; this is not a neutral control. Do not return Malay source text.',
        ];
        $outputs = [];
        foreach ($procedures as $code => $instruction) {
            $outputs[$code] = $client->generate($selection['model'], $instruction."\nText:\n".$data['source_text'], ['max_tokens' => 2000, 'timeout' => 90])['translated_text'];
        }
        $procedureTerms = [];
        foreach ($outputs as $code => $output) {
            $prompt = 'From this '.$targetLanguage.' translation, return JSON only: an object mapping each Malay source cultural term to its exact '.$targetLanguage.' phrase. Use null when the term is not represented in this translation. Never copy, transliterate, or return the Malay source word unless it literally appears in the '.$targetLanguage.' translation. Terms: '.json_encode(collect($data['terms'])->pluck('source_phrase')->all())."\nTranslation:\n".$output;
            $raw = $client->generate($selection['model'], $prompt, ['format' => 'json', 'max_tokens' => 500])['translated_text'];
            $procedureTerms[$code] = json_decode($raw, true) ?: [];
        }
        $ratings = collect($data['terms'])->map(function (array $term) use ($outputs, $procedureTerms) {
            $phrases = collect(['PA', 'PB', 'PC'])->mapWithKeys(function (string $code) use ($term, $procedureTerms) {
                $phrase = $procedureTerms[$code][$term['source_phrase']] ?? null;

                if (! is_string($phrase) || trim($phrase) === '' || mb_strtolower(trim($phrase)) === mb_strtolower(trim($term['source_phrase']))) {
                    return [$code => null];
                }

                return [$code => trim($phrase)];
            });
            $scores = $phrases->map(fn ($phrase, $code) => $phrase !== null && mb_stripos($outputs[$code], $phrase) !== false ? 2 : null);
            return ['source_phrase' => $term['source_phrase'], 'translated_phrase' => $term['translated_phrase'], 'procedure_phrases' => $phrases, 'PA' => $scores['PA'], 'PB' => $scores['PB'], 'PC' => $scores['PC'], 'stable' => $scores->unique()->count() === 1];
        })->values();
        $eligibleRatings = $ratings->filter(fn ($row) => $row['PA'] !== null && $row['PB'] !== null && $row['PC'] !== null);
        $eligible = $eligibleRatings->count();
        return response()->json(['temporary' => true, 'outputs' => $outputs, 'ratings' => $ratings, 'eligible_terms' => $eligible, 'skb' => $eligible ? round($eligibleRatings->flatMap(fn ($row) => [$row['PA'], $row['PB'], $row['PC']])->avg() / 2, 3) : null, 'ikg' => $eligible ? round($eligibleRatings->where('stable', false)->count() / $eligible, 3) : null]);
    }
}
