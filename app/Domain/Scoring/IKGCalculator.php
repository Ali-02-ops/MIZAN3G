<?php

namespace App\Domain\Scoring;

class IKGCalculator
{
    /** @param array<int, array{PA?:int, PB?:int, PC?:int}> $termRatings */
    public function calculate(array $termRatings): ?array
    {
        $eligible = 0;
        $unstable = 0;
        foreach ($termRatings as $ratings) {
            if (! isset($ratings['PA'], $ratings['PB'], $ratings['PC'])) {
                continue;
            }
            $eligible++;
            if ($ratings['PA'] !== $ratings['PB'] || $ratings['PB'] !== $ratings['PC']) {
                $unstable++;
            }
        }

        return $eligible === 0 ? null : ['score' => $unstable / $eligible, 'unstable_terms' => $unstable, 'eligible_terms' => $eligible, 'excluded_terms' => count($termRatings) - $eligible];
    }
}
