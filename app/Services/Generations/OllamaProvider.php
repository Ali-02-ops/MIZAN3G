<?php

namespace App\Services\Generations;

use Illuminate\Support\Facades\Http;

class OllamaProvider implements TranslationProvider
{
    public function generate(string $model, string $prompt, array $parameters = []): array
    {
        $options = array_filter([
            'temperature' => $parameters['temperature'] ?? null,
            'top_p' => $parameters['top_p'] ?? null,
            'num_predict' => $parameters['max_tokens'] ?? null,
            'seed' => $parameters['seed'] ?? null,
        ], static fn ($value) => $value !== null);

        $payload = ['model' => $model, 'prompt' => $prompt, 'stream' => false, 'think' => false];
        if ($options !== []) {
            $payload['options'] = $options;
        }
        if (($parameters['format'] ?? null) === 'json') {
            $payload['format'] = 'json';
        }

        $response = Http::acceptJson()
            ->baseUrl(rtrim(config('services.ollama.url'), '/'))
            ->timeout($parameters['timeout'] ?? config('services.ollama.timeout'))
            ->post('/api/generate', $payload)
            ->throw();
        $data = $response->json();

        return [
            'raw_response' => $response->body(),
            'translated_text' => $data['response'] ?? '',
            'input_tokens' => $data['prompt_eval_count'] ?? null,
            'output_tokens' => $data['eval_count'] ?? null,
            'provider_request_id' => null,
        ];
    }
}
