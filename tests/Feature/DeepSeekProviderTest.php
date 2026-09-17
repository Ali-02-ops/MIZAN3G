<?php

namespace Tests\Feature;

use App\Services\Generations\DeepSeekProvider;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeepSeekProviderTest extends TestCase
{
    public function test_it_sends_non_streaming_chat_requests_to_the_configured_deepseek_endpoint(): void
    {
        config(['services.deepseek.url' => 'https://deepseek.test', 'services.deepseek.key' => 'test-key', 'services.deepseek.timeout' => 30]);
        Http::fake([
            'https://deepseek.test/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => '{"terms":[]}']]],
                'usage' => ['prompt_tokens' => 12, 'completion_tokens' => 8],
            ], 200, ['x-request-id' => 'request-123']),
        ]);

        $result = app(DeepSeekProvider::class)->generate('deepseek-flash', 'Extract terms', ['format' => 'json', 'temperature' => 0.2]);

        $this->assertSame('{"terms":[]}', $result['translated_text']);
        $this->assertSame(12, $result['input_tokens']);
        $this->assertSame(8, $result['output_tokens']);
        $this->assertSame('request-123', $result['provider_request_id']);
        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://deepseek.test/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer test-key')
                && $request['model'] === 'deepseek-flash'
                && $request['stream'] === false;
        });
    }
}
