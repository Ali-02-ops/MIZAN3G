<?php

namespace App\Services\Generations;

use Illuminate\Support\Facades\Http;

class OpenAiProvider implements TranslationProvider
{
    public function generate(string $model, string $prompt, array $parameters = []): array
    {
        $response = Http::acceptJson()->withToken(config('services.openai.key'))->timeout(120)->post('https://api.openai.com/v1/responses', ['model' => $model, 'input' => $prompt, 'store' => false, 'text' => ['format' => ['type' => 'text']]])->throw();
        $data = $response->json();

        return ['raw_response' => $response->body(), 'translated_text' => $data['output_text'] ?? '', 'input_tokens' => $data['usage']['input_tokens'] ?? null, 'output_tokens' => $data['usage']['output_tokens'] ?? null, 'provider_request_id' => $response->header('x-request-id')];
    }
}
