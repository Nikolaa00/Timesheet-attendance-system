<?php

namespace App\Http\Services;

class DownloadPolicyService
{
    public function getRulesPdf(): string
    {
        $path = storage_path('app/public/policies.pdf');

        if (!file_exists($path)) {
            abort(404, 'The requested file was not found.');
        }

        return $path;
    }
}