<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\SourceDocument;
use App\Models\User;

class SourceDocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->organisations()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SourceDocument $sourceDocument): bool
    {
        return $user->can('view', $sourceDocument->project);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Project $project): bool
    {
        return $user->can('create', [Project::class, $project]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SourceDocument $sourceDocument): bool
    {
        return $user->can('update', $sourceDocument->project);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SourceDocument $sourceDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SourceDocument $sourceDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SourceDocument $sourceDocument): bool
    {
        return false;
    }
}
