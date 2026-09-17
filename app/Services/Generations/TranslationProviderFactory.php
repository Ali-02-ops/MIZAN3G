<?php

namespace App\Services\Generations;

use InvalidArgumentException;

class TranslationProviderFactory
{
    public function make(string $provider): TranslationProvider
    {
        return match ($provider) {
            'OPENAI' => app(OpenAiProvider::class),
            'ANTHROPIC' => app(AnthropicProvider::class),
            'GEMINI' => app(GeminiProvider::class),
            'OLLAMA' => app(OllamaProvider::class),
            default => throw new InvalidArgumentException("Unsupported automated provider: {$provider}"),
        };
    }
}
