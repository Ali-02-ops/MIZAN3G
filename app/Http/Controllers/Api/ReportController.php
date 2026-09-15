<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Services\Scoring\AuditScoreService;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function download(Audit $audit, AuditScoreService $scores): Response
    {
        $this->authorize('view', $audit);
        $audit->load(['project', 'documentVersion.document', 'terms', 'prompts', 'models']);

        return Pdf::loadView('reports.audit', ['audit' => $audit, 'scores' => $scores->calculate($audit)])->download("mizan3g-audit-{$audit->id}.pdf");
    }
}
