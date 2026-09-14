<?php

namespace Tests\Feature;

use App\Models\CulturalCategory;
use Database\Seeders\GhazalaFrameworkSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GhazalaFrameworkSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_all_eight_ghazala_categories_idempotently(): void
    {
        $this->seed(GhazalaFrameworkSeeder::class);
        $this->seed(GhazalaFrameworkSeeder::class);

        $this->assertSame(8, CulturalCategory::query()->where('framework', 'GHAZALA')->count());
        $this->assertDatabaseHas('mizan3g_cultural_categories', ['code' => 'POLITICAL', 'framework' => 'GHAZALA']);
        $this->assertDatabaseHas('mizan3g_cultural_subcategories', ['code' => 'HISTORICAL_INSTITUTION']);
    }
}
