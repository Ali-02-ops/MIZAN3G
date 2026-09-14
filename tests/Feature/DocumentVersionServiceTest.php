<?php

namespace Tests\Feature;

use App\Enums\OrganisationRole;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\User;
use App\Services\Documents\DocumentVersionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentVersionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_source_text_creates_a_new_immutable_document_version(): void
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->for($user, 'owner')->create();
        $organisation->users()->attach($user, ['role' => OrganisationRole::Researcher->value, 'joined_at' => now()]);
        $project = Project::factory()->for($organisation)->for($user, 'creator')->create();
        $service = app(DocumentVersionService::class);

        $document = $service->createDocument($project, $user, [
            'title' => 'Sample Malay text',
            'source_language' => 'ms',
            'text_content' => 'Teks asal yang pertama.',
        ]);
        $firstVersion = $document->versions()->firstOrFail();

        $secondVersion = $service->createVersion($document, $user, 'Teks asal yang dikemas kini.');

        $this->assertSame(1, $firstVersion->version_number);
        $this->assertSame('Teks asal yang pertama.', $firstVersion->text_content);
        $this->assertSame(hash('sha256', 'Teks asal yang pertama.'), $firstVersion->content_hash);
        $this->assertSame(2, $secondVersion->version_number);
        $this->assertSame(hash('sha256', 'Teks asal yang dikemas kini.'), $secondVersion->content_hash);
        $this->assertSame($secondVersion->getKey(), $document->fresh()->current_version_id);
    }
}
