<?php

namespace Tests\Feature;

use App\Models\AiModelConfiguration;
use App\Models\Audit;
use App\Models\AuditModel;
use App\Models\AuditPrompt;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\PromptTemplate;
use App\Models\PromptVersion;
use App\Models\SourceDocument;
use App\Models\SourceDocumentVersion;
use App\Models\User;
use App\Services\Generations\ImportedGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportedGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_preserves_full_output_and_increments_attempts(): void
    {
        $user = User::factory()->create();
        $org = Organisation::factory()->for($user, 'owner')->create();
        $project = Project::factory()->for($org)->for($user, 'creator')->create();
        $document = SourceDocument::query()->create(['project_id' => $project->id, 'title' => 'Source', 'source_language' => 'ms', 'created_by' => $user->id]);
        $version = SourceDocumentVersion::query()->create(['source_document_id' => $document->id, 'version_number' => 1, 'text_content' => 'Teks sumber', 'content_hash' => hash('sha256', 'Teks sumber'), 'created_by' => $user->id]);
        $audit = Audit::query()->create(['project_id' => $project->id, 'document_version_id' => $version->id, 'name' => 'A', 'status' => 'READY_TO_GENERATE', 'created_by' => $user->id]);
        $config = AiModelConfiguration::query()->create(['project_id' => $project->id, 'provider' => 'MANUAL', 'display_name' => 'Manual', 'provider_model_id' => 'manual', 'execution_environment' => 'IMPORTED_OUTPUT', 'created_by' => $user->id]);
        $model = AuditModel::query()->create(['audit_id' => $audit->id, 'ai_model_configuration_id' => $config->id, 'model_name_snapshot' => 'Manual', 'provider_snapshot' => 'MANUAL', 'parameters_snapshot_json' => []]);
        $template = PromptTemplate::query()->create(['code' => 'PA', 'name' => 'PA', 'orientation' => 'SOURCE']);
        $promptVersion = PromptVersion::query()->create(['prompt_template_id' => $template->id, 'version_number' => 1, 'prompt_body' => 'Translate', 'content_hash' => hash('sha256', 'Translate')]);
        $prompt = AuditPrompt::query()->create(['audit_id' => $audit->id, 'prompt_version_id' => $promptVersion->id, 'code' => 'PA', 'prompt_body_snapshot' => 'Translate', 'sort_order' => 1]);

        $service = app(ImportedGenerationService::class);
        $first = $service->import($audit, $model, $prompt, 'raw 1', 'Arabic 1');
        $second = $service->import($audit, $model, $prompt, 'raw 2', 'Arabic 2');

        $this->assertSame(1, $first->attempt_number);
        $this->assertSame(2, $second->attempt_number);
        $this->assertSame('Teks sumber', $first->source_text_snapshot);
        $this->assertDatabaseCount('mizan3g_generations', 2);
    }
}
