<?php

namespace App\Policies;

use App\Enums\OrganisationRole;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
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
    public function view(User $user, Project $project): bool
    {
        return $user->organisations()->whereKey($project->organisation_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Organisation $organisation): bool
    {
        return $user->hasOrganisationRole($organisation, [
            OrganisationRole::SuperAdmin,
            OrganisationRole::OrganisationAdmin,
            OrganisationRole::Researcher,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->hasOrganisationRole($project->organisation, [
            OrganisationRole::SuperAdmin,
            OrganisationRole::OrganisationAdmin,
            OrganisationRole::Researcher,
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->hasOrganisationRole($project->organisation, [
            OrganisationRole::SuperAdmin,
            OrganisationRole::OrganisationAdmin,
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return false;
    }
}
