<?php

namespace App\Domain\Scoring;

class SKBCalculator
{
    /** @param array<int, int|null> $ratings */
    public function calculate(array $ratings): ?array
    {
        $valid = array_values(array_filter($ratings, fn ($rating) => in_array($rating, [0, 1, 2], true)));
        if ($valid === []) {
            return null;
        }
        $numerator = array_sum($valid);

        return ['score' => $numerator / (2 * count($valid)), 'numerator' => $numerator, 'denominator' => 2 * count($valid), 'evaluated_units' => count($valid), 'missing_units' => count($ratings) - count($valid)];
    }
}
