<?php

namespace App\Services\Generations;

use Illuminate\Support\Facades\Http;

class AnthropicProvider implements TranslationProvider
{
    public function generate(string $model, string $prompt, array $parameters = []): array
    {
        $response = Http::acceptJson()->withHeaders(['x-api-key' => config('services.anthropic.key'), 'anthropic-version' => '2023-06-01'])->timeout(120)->post('https://api.anthropic.com/v1/messages', ['model' => $model, 'max_tokens' => $parameters['max_tokens'] ?? 4096, 'messages' => [['role' => 'user', 'content' => $prompt]]])->throw();
        $data = $response->json();

        return ['raw_response' => $response->body(), 'translated_text' => $data['content'][0]['text'] ?? '', 'input_tokens' => $data['usage']['input_tokens'] ?? null, 'output_tokens' => $data['usage']['output_tokens'] ?? null, 'provider_request_id' => $response->header('request-id')];
    }
}
