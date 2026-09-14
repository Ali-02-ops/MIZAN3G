<?php

namespace App\Models;

use App\Enums\OrganisationRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'mizan3g_users';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class, 'mizan3g_organisation_user')
            ->withPivot(['role', 'joined_at'])
            ->withTimestamps();
    }

    public function ownedOrganisations(): HasMany
    {
        return $this->hasMany(Organisation::class, 'owner_user_id');
    }

    public function createdProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function roleForOrganisation(Organisation $organisation): ?OrganisationRole
    {
        $membership = $this->organisations()
            ->whereKey($organisation->getKey())
            ->first();

        return $membership === null
            ? null
            : OrganisationRole::tryFrom($membership->pivot->role);
    }

    /**
     * @param  array<OrganisationRole>  $roles
     */
    public function hasOrganisationRole(Organisation $organisation, array $roles): bool
    {
        $role = $this->roleForOrganisation($organisation);

        return $role !== null && in_array($role, $roles, true);
    }
}
