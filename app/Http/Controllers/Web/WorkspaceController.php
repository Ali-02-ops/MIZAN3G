<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class WorkspaceController extends Controller
{
    public function show(string $screen)
    {
        abort_unless(in_array($screen, ['projects', 'documents', 'inventory', 'workstation', 'audits', 'expert-reviews', 'results', 'reports', 'settings'], true), 404);

        return match ($screen) {
            'audits' => view('workspace.audits'),
            'results' => view('workspace.results'),
            'reports' => view('workspace.reports'),
            'workstation' => view('workspace.workstation'),
            'settings' => view('workspace.settings'),
            default => view('workspace.screen', ['screen' => $screen]),
        };
    }
}
