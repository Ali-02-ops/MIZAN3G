<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->sentence(3);

        return [
            'organisation_id' => Organisation::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('####'),
            'description' => fake()->paragraph(),
            'objective' => fake()->sentence(),
            'source_language' => 'ms',
            'target_language' => 'ar',
            'framework' => 'GHAZALA',
            'status' => ProjectStatus::Draft,
            'created_by' => User::factory(),
        ];
    }
}
