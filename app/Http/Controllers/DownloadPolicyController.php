<?php

namespace App\Http\Controllers;

use App\Http\Services\DownloadPolicyService;
class DownloadPolicyController extends Controller
{
    protected DownloadPolicyService $downloadService;

    public function __construct(DownloadPolicyService $downloadService)
    {
        $this->downloadService = $downloadService;
    }
    public function downloadRules()
    {
        $path = $this->downloadService->getRulesPdf();

        return response()->download($path, 'timeshift-rules.pdf');
    }
}
