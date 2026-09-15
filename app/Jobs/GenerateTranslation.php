<?php

namespace App\Jobs;

use App\Models\Generation;
use App\Services\Generations\TranslationProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GenerateTranslation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $generationId) {}

    public function handle(TranslationProviderFactory $providers): void
    {
        $generation = Generation::query()->with('model')->findOrFail($this->generationId);
        if ($generation->status === 'COMPLETED') {
            return;
        }
        $generation->update(['status' => 'RUNNING', 'started_at' => $generation->started_at ?? now()]);
        $result = $providers->make($generation->model->provider_snapshot)->generate($generation->model->provider_model_id_snapshot, $generation->submitted_prompt, $generation->model->parameters_snapshot_json ?? []);
        $generation->update(['status' => 'COMPLETED', 'raw_response_text' => $result['raw_response'], 'translated_text' => $result['translated_text'], 'input_tokens' => $result['input_tokens'], 'output_tokens' => $result['output_tokens'], 'provider_request_id' => $result['provider_request_id'], 'completed_at' => now()]);
    }

    public function failed(Throwable $exception): void
    {
        Generation::query()->whereKey($this->generationId)->update(['status' => 'FAILED', 'error_message' => 'Provider request failed after retry attempts. Check server logs and provider configuration.', 'completed_at' => now()]);
    }
}
