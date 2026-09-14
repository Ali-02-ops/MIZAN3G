<?php

namespace Database\Seeders;

use App\Models\CulturalCategory;
use App\Models\CulturalSubcategory;
use Illuminate\Database\Seeder;

class GhazalaFrameworkSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['RELIGION', 'Religion', 'Cultural references related to worship, belief and religious principles.', ['WORSHIP' => 'Worship', 'BELIEF' => 'Belief', 'RELIGIOUS_PRINCIPLE' => 'Religious principle']],
            ['SOCIAL', 'Social', 'Cultural references related to social life, institutions and customs.', ['CUSTOM' => 'Custom', 'SOCIAL_INSTITUTION' => 'Social institution']],
            ['MATERIAL', 'Material', 'Cultural references related to food, clothing, objects and built environments.', ['FOOD' => 'Food', 'CLOTHING' => 'Clothing', 'ARTEFACT' => 'Artefact']],
            ['ECOLOGY', 'Ecology', 'Cultural references related to geography, flora, fauna and climate.', ['GEOGRAPHY' => 'Geography', 'FLORA_FAUNA' => 'Flora and fauna']],
            ['LITERARY', 'Literary', 'Cultural references expressed through literary devices.', ['METAPHOR' => 'Metaphor', 'SIMILE' => 'Simile', 'PERSONIFICATION' => 'Personification']],
            ['LINGUISTIC', 'Linguistic', 'Cultural references expressed through language-specific forms.', ['PROVERB' => 'Proverb', 'DIALECT' => 'Dialect', 'IDIOM' => 'Idiom']],
            ['MENTAL_EMOTIONAL', 'Mental and Emotional', 'Cultural references related to values, attitudes and emotional expression.', ['VALUE' => 'Value', 'EMOTIONAL_EXPRESSION' => 'Emotional expression']],
            ['POLITICAL', 'Political', 'Cultural references related to organisations, ideology and historical institutions.', ['ORGANISATION' => 'Organisation', 'POLITICAL_CONCEPT' => 'Political concept', 'IDEOLOGY' => 'Ideology', 'HISTORICAL_INSTITUTION' => 'Historical institution']],
        ];

        foreach ($categories as $position => [$code, $name, $description, $subcategories]) {
            $category = CulturalCategory::query()->updateOrCreate(
                ['framework' => 'GHAZALA', 'code' => $code],
                ['name' => $name, 'description' => $description, 'sort_order' => $position + 1, 'active' => true],
            );

            foreach ($subcategories as $subcategoryCode => $subcategoryName) {
                CulturalSubcategory::query()->updateOrCreate(
                    ['category_id' => $category->getKey(), 'code' => $subcategoryCode],
                    ['name' => $subcategoryName, 'active' => true],
                );
            }
        }
    }
}
