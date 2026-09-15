<?php

namespace App\Domain\Scoring;

use Illuminate\Support\Collection;

class RatingResolver
{
    /** @return array{value:int, imputed:bool}|null */
    public function resolve(Collection $ratings, string $mode): ?array
    {
        $submitted = $ratings->where('status', 'SUBMITTED');
        $expert = $submitted->where('reviewer_role', 'EXPERT')->sortByDesc('submitted_at')->first();
        $researcher = $submitted->where('reviewer_role', 'RESEARCHER')->sortByDesc('submitted_at')->first();
        if (in_array($mode, ['VERIFIED_ONLY', 'EXPERT_ONLY'], true)) {
            return $expert ? ['value' => $expert->rating_value, 'imputed' => false] : null;
        }
        if ($mode === 'MANUSCRIPT_COMPATIBLE') {
            return $expert ? ['value' => $expert->rating_value, 'imputed' => false] : ($researcher ? ['value' => $researcher->rating_value, 'imputed' => true] : null);
        }

        return null;
    }
}
