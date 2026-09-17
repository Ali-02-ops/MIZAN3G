<?php

namespace App\Services\Generations;

use Illuminate\Support\Facades\Http;

class DeepSeekProvider implements TranslationProvider
{
    public function generate(string $model, string $prompt, array $parameters = []): array
    {
        $payload = array_filter([
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'stream' => false,
            'temperature' => $parameters['temperature'] ?? null,
            'top_p' => $parameters['top_p'] ?? null,
            'max_tokens' => $parameters['max_tokens'] ?? null,
        ], static fn ($value) => $value !== null);

        $response = Http::acceptJson()
            ->withToken(config('services.deepseek.key'))
            ->baseUrl(rtrim(config('services.deepseek.url'), '/'))
            ->timeout($parameters['timeout'] ?? config('services.deepseek.timeout'))
            ->post('/chat/completions', $payload)
            ->throw();
        $data = $response->json();

        return [
            'raw_response' => $response->body(),
            'translated_text' => $data['choices'][0]['message']['content'] ?? '',
            'input_tokens' => $data['usage']['prompt_tokens'] ?? null,
            'output_tokens' => $data['usage']['completion_tokens'] ?? null,
            'provider_request_id' => $response->header('x-request-id'),
        ];
    }
}
