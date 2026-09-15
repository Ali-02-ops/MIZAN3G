<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_screens_require_authentication_and_render_for_a_user(): void
    {
        foreach (['projects', 'audits', 'results', 'reports'] as $screen) {
            $this->get("/{$screen}")->assertRedirect(route('login'));
        }

        $user = User::factory()->create();
        $this->actingAs($user)->get('/projects')->assertOk()->assertSee('Projects');
        $this->actingAs($user)->get('/audits')->assertOk()->assertSee('Draft audit');
        $this->actingAs($user)->get('/results')->assertOk()->assertSee('Fidelity and');
        $this->actingAs($user)->get('/reports')->assertOk()->assertSee('Export a reproducible');
    }
}
