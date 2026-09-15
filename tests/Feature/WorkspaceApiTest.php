<?php

namespace Tests\Feature;

use App\Enums\OrganisationRole;
use App\Models\CulturalCategory;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_researcher_can_operate_the_project_document_and_inventory_workspace(): void
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->for($user, 'owner')->create();
        $organisation->users()->attach($user, ['role' => OrganisationRole::Researcher->value, 'joined_at' => now()]);
        $category = CulturalCategory::query()->create(['code' => 'SOC', 'name' => 'Social culture', 'framework' => 'GHAZALA', 'sort_order' => 1, 'active' => true]);
        $token = $user->createToken('test', ['mizan3g:api'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/auth/me')
            ->assertOk()->assertJsonPath('organisations.0.id', $organisation->id);

        $project = $this->withToken($token)->postJson("/api/v1/organisations/{$organisation->id}/projects", [
            'name' => 'Malay narratives', 'source_language' => 'ms', 'target_language' => 'ar',
        ])->assertCreated()->json();

        $document = $this->withToken($token)->postJson("/api/v1/projects/{$project['id']}/documents", [
            'title' => 'Source narrative', 'source_language' => 'ms', 'text_content' => 'Teks asal.',
        ])->assertCreated()->json();

        $version = $document['versions'][0];
        $this->withToken($token)->postJson("/api/v1/document-versions/{$version['id']}/cultural-terms", [
            'source_phrase' => 'kampung', 'category_id' => $category->id, 'selected_for_audit' => true,
        ])->assertCreated()->assertJsonPath('source_phrase', 'kampung');

        $this->withToken($token)->getJson("/api/v1/projects/{$project['id']}/documents")
            ->assertOk()->assertJsonCount(1);
        $this->withToken($token)->getJson("/api/v1/document-versions/{$version['id']}/cultural-terms")
            ->assertOk()->assertJsonCount(1);
    }

    public function test_token_without_mizan_ability_cannot_access_workspace_api(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('restricted', ['other:ability'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/projects')->assertForbidden();
    }
}
