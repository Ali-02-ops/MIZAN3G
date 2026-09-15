<?php

namespace Tests\Feature;

use App\Services\Generations\OllamaProvider;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OllamaProviderTest extends TestCase
{
    public function test_it_uses_the_configured_local_ollama_endpoint_with_non_streaming_json_output(): void
    {
        config(['services.ollama.url' => 'http://ollama.test', 'services.ollama.timeout' => 30]);
        Http::fake([
            'http://ollama.test/api/generate' => Http::response([
                'response' => '[{"source_phrase":"gotong-royong"}]',
                'prompt_eval_count' => 12,
                'eval_count' => 8,
            ]),
        ]);

        $result = app(OllamaProvider::class)->generate('qwen3:8b', 'Extract terms', ['format' => 'json', 'temperature' => 0.2]);

        $this->assertSame('[{"source_phrase":"gotong-royong"}]', $result['translated_text']);
        $this->assertSame(12, $result['input_tokens']);
        $this->assertSame(8, $result['output_tokens']);
        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'http://ollama.test/api/generate'
                && $request['model'] === 'qwen3:8b'
                && $request['stream'] === false
                && $request['think'] === false
                && $request['format'] === 'json';
        });
    }
}
