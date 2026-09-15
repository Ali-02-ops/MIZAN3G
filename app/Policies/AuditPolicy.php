<?php

namespace App\Policies;

use App\Models\Audit;
use App\Models\Project;
use App\Models\User;

class AuditPolicy
{
    public function view(User $user, Audit $audit): bool
    {
        return $user->can('view', $audit->project);
    }

    public function create(User $user, Project $project): bool
    {
        return $user->can('update', $project);
    }

    public function update(User $user, Audit $audit): bool
    {
        return $user->can('update', $audit->project) && $audit->status === 'DRAFT';
    }

    public function run(User $user, Audit $audit): bool
    {
        return $user->can('update', $audit->project) && $audit->status === 'READY_TO_GENERATE';
    }
}
