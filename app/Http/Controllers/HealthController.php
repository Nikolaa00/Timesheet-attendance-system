<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Exceptions\ApiException;

class HealthController extends Controller
{
    public function check()
    {
        try {
            DB::connection()->getDatabaseName();

            return response()->json([
                'status' => 'ok',
                'database' => 'connected'
            ], 200);
        } catch (\Exception $e) {
             throw new ApiException(
            message: 'Database connection failed.',
            loc: ['server'],
            type: 'database_error',
            status: 503
        );
        }
    }
}