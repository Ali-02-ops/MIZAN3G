<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class WorkspaceController extends Controller
{
    public function show(string $screen)
    {
        abort_unless(in_array($screen, ['projects', 'documents', 'inventory', 'audits', 'results', 'settings'], true), 404);

        return view('workspace.screen', ['screen' => $screen]);
    }
}
