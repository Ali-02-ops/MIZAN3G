<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_authenticate_with_a_limited_lifetime_api_token(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.id', $user->getKey())
            ->assertJsonStructure(['token', 'expires_at']);

        $this->assertDatabaseHas('mizan3g_personal_access_tokens', [
            'tokenable_id' => $user->getKey(),
            'tokenable_type' => User::class,
        ]);
        $this->assertFalse(Schema::hasTable('personal_access_tokens'));
    }

    public function test_invalid_login_does_not_issue_a_token(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertUnprocessable();

        $this->assertDatabaseCount('mizan3g_personal_access_tokens', 0);
    }
}
