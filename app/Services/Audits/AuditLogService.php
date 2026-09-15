<?php

namespace App\Services\Audits;

use App\Models\AuditLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogService
{
    public function record(User $user, Project $project, string $action, Model $entity, array $old = [], array $new = [], ?Request $request = null): void
    {
        AuditLog::query()->create(['user_id' => $user->id, 'organisation_id' => $project->organisation_id, 'project_id' => $project->id, 'action' => $action, 'entity_type' => $entity::class, 'entity_id' => $entity->getKey(), 'old_values_json' => $old ?: null, 'new_values_json' => $new ?: null, 'ip_address' => $request?->ip(), 'user_agent' => $request?->userAgent()]);
    }
}
