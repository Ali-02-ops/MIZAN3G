<?php

namespace App\Services\Generations;

interface TranslationProvider
{
    /** @return array{raw_response:string, translated_text:string, input_tokens:?int, output_tokens:?int, provider_request_id:?string} */
    public function generate(string $model, string $prompt, array $parameters = []): array;
}
