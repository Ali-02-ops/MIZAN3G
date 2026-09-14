<?php

namespace Tests\Feature;

use App\Enums\OrganisationRole;
use App\Models\AiModelConfiguration;
use App\Models\Audit;
use App\Models\CulturalCategory;
use App\Models\CulturalTerm;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\PromptVersion;
use App\Models\User;
use App\Services\Audits\AuditFreezeService;
use App\Services\Documents\DocumentVersionService;
use Database\Seeders\DefaultPromptSeeder;
use Database\Seeders\GhazalaFrameworkSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditFreezeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_freeze_creates_immutable_scientific_snapshots(): void
    {
        $this->seed([GhazalaFrameworkSeeder::class, DefaultPromptSeeder::class]);
        $user = User::factory()->create();
        $organisation = Organisation::factory()->for($user, 'owner')->create();
        $organisation->users()->attach($user, ['role' => OrganisationRole::Researcher->value, 'joined_at' => now()]);
        $project = Project::factory()->for($organisation)->for($user, 'creator')->create();
        $document = app(DocumentVersionService::class)->createDocument($project, $user, ['title' => 'Source', 'source_language' => 'ms', 'text_content' => 'Malayan Union']);
        $term = CulturalTerm::query()->create(['document_version_id' => $document->current_version_id, 'source_phrase' => 'Malayan Union', 'category_id' => CulturalCategory::query()->where('code', 'POLITICAL')->value('id'), 'selected_for_audit' => true, 'created_by' => $user->id]);
        $audit = Audit::query()->create(['project_id' => $project->id, 'document_version_id' => $document->current_version_id, 'name' => 'Pilot', 'created_by' => $user->id]);
        $model = AiModelConfiguration::query()->create(['project_id' => $project->id, 'provider' => 'MANUAL', 'display_name' => 'Imported output', 'provider_model_id' => 'manual', 'execution_environment' => 'IMPORTED_OUTPUT', 'created_by' => $user->id]);

        $frozen = app(AuditFreezeService::class)->freeze($audit, collect([$term]), PromptVersion::query()->get(), collect([$model]));

        $this->assertSame('READY_TO_GENERATE', $frozen->status);
        $this->assertNotNull($frozen->frozen_at);
        $this->assertDatabaseCount('mizan3g_audit_terms', 1);
        $this->assertDatabaseCount('mizan3g_audit_prompts', 3);
        $this->assertDatabaseCount('mizan3g_audit_models', 1);
        $this->assertSame(3, PromptVersion::query()->whereNotNull('locked_at')->count());
    }
}
