<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_session_authentication(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_user_can_sign_in_to_the_web_dashboard(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct-password')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'correct-password'])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($user)->get('/')->assertOk();
    }
}
