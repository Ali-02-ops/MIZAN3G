<?php

namespace Tests\Feature;

use App\Domain\Scoring\IKGCalculator;
use App\Domain\Scoring\RatingResolver;
use App\Domain\Scoring\SKBCalculator;
use Tests\TestCase;

class ScoringCalculatorTest extends TestCase
{
    public function test_skb_uses_only_valid_submitted_ratings(): void
    {
        $result = app(SKBCalculator::class)->calculate([2, 2, 1, 0, null]);

        $this->assertSame(0.625, $result['score']);
        $this->assertSame(5, $result['numerator']);
        $this->assertSame(8, $result['denominator']);
        $this->assertSame(1, $result['missing_units']);
    }

    public function test_ikg_requires_all_three_prompt_ratings(): void
    {
        $result = app(IKGCalculator::class)->calculate([
            ['PA' => 2, 'PB' => 2, 'PC' => 2],
            ['PA' => 2, 'PB' => 1, 'PC' => 2],
            ['PA' => 0, 'PB' => 0, 'PC' => 0],
            ['PA' => 2, 'PB' => 2],
        ]);

        $this->assertEqualsWithDelta(1 / 3, $result['score'], 0.000001);
        $this->assertSame(1, $result['unstable_terms']);
        $this->assertSame(3, $result['eligible_terms']);
        $this->assertSame(1, $result['excluded_terms']);
    }

    public function test_rating_resolution_respects_scoring_mode_and_discloses_imputation(): void
    {
        $ratings = collect([(object) ['status' => 'SUBMITTED', 'reviewer_role' => 'RESEARCHER', 'rating_value' => 2, 'submitted_at' => now()]]);
        $resolver = app(RatingResolver::class);
        $this->assertNull($resolver->resolve($ratings, 'VERIFIED_ONLY'));
        $this->assertSame(['value' => 2, 'imputed' => true], $resolver->resolve($ratings, 'MANUSCRIPT_COMPATIBLE'));
    }
}
