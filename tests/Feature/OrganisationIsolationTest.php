<?php

namespace Tests\Feature;

use App\Enums\OrganisationRole;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganisationIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_membership_does_not_cross_organisation_boundaries(): void
    {
        $member = User::factory()->create();
        $otherOwner = User::factory()->create();
        $memberOrganisation = Organisation::factory()->for($member, 'owner')->create();
        $otherOrganisation = Organisation::factory()->for($otherOwner, 'owner')->create();

        $memberOrganisation->users()->attach($member, [
            'role' => OrganisationRole::Researcher->value,
            'joined_at' => now(),
        ]);
        $otherOrganisation->users()->attach($otherOwner, [
            'role' => OrganisationRole::OrganisationAdmin->value,
            'joined_at' => now(),
        ]);

        $this->assertTrue($member->can('view', $memberOrganisation));
        $this->assertFalse($member->can('view', $otherOrganisation));
        $this->assertTrue($member->can('create', [Project::class, $memberOrganisation]));
        $this->assertFalse($member->can('create', [Project::class, $otherOrganisation]));
    }

    public function test_mizan3g_models_use_prefixed_tables(): void
    {
        $this->assertSame('mizan3g_users', (new User)->getTable());
        $this->assertSame('mizan3g_organisations', (new Organisation)->getTable());
        $this->assertSame('mizan3g_projects', (new Project)->getTable());
    }
}
