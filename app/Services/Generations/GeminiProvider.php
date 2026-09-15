<?php

namespace App\Services\Generations;

use Illuminate\Support\Facades\Http;

class GeminiProvider implements TranslationProvider
{
    public function generate(string $model, string $prompt, array $parameters = []): array
    {
        $generationConfig = array_filter(['temperature' => $parameters['temperature'] ?? null, 'topP' => $parameters['top_p'] ?? null, 'maxOutputTokens' => $parameters['max_tokens'] ?? null]);
        if (($parameters['format'] ?? null) === 'json') {
            $generationConfig['responseMimeType'] = 'application/json';
            $generationConfig['thinkingConfig'] = ['thinkingLevel' => 'MINIMAL'];
        }
        if (isset($parameters['response_schema'])) {
            $generationConfig['responseSchema'] = $parameters['response_schema'];
        }

        $response = Http::acceptJson()->withHeader('x-goog-api-key', config('services.gemini.key'))->timeout(120)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", ['contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]], 'generationConfig' => $generationConfig])->throw();
        $data = $response->json();
        $parts = $data['candidates'][0]['content']['parts'] ?? [];
        $text = collect($parts)->reject(fn (array $part) => $part['thought'] ?? false)->pluck('text')->filter()->join("\n");

        return ['raw_response' => $response->body(), 'translated_text' => $text, 'input_tokens' => $data['usageMetadata']['promptTokenCount'] ?? null, 'output_tokens' => $data['usageMetadata']['candidatesTokenCount'] ?? null, 'provider_request_id' => null];
    }
}
