<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subsidiary;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function getCounts(): JsonResponse
    {
        return response()->json([
            'users_count' => User::query()
                ->whereIn('role', ['admin', 'employee'])
                ->where('is_active', true)
                ->count(),
            'sectors_count' => Sector::count(),
            'subsidiaries_count' => Subsidiary::count(),
        ]);
    }
}
