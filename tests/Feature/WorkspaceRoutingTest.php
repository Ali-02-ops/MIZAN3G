<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class WorkspaceRoutingTest extends TestCase
{
    public function test_workspace_screens_require_authentication_and_render_for_a_user(): void
    {
        $this->get('/projects')->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())->get('/projects')
            ->assertOk()
            ->assertSee('Projects');
    }
}
