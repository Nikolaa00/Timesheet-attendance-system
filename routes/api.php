<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SubsidiaryController;
use App\Http\Controllers\DownloadPolicyController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\DashboardController;
use App\Http\Resources\AuthResource;

Route::get('/health', [HealthController::class, 'check']);

Route::get('/downloads/rules', [DownloadPolicyController::class, 'downloadRules']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);

    Route::post('/reset-password', [PasswordResetController::class, 'reset']);

    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::get('/me', function (Request $request) {
            return response()->json(new AuthResource($request->user()));
        });
    });
});

/*============== Check-in/out and logout routes ============*/

Route::middleware(['auth:sanctum', 'active', 'role:employee,admin'])->group(function () {

    Route::prefix('auth')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
    });


    Route::prefix('attendance')->group(function () {

        Route::post('/check-in', [AttendanceController::class, 'checkIn']);

        Route::post('/check-out', [AttendanceController::class, 'checkOut']);

        Route::get('/state', [AttendanceController::class, 'status']);
        Route::get('/status', [AttendanceController::class, 'status']);
    });
});

Route::middleware(['auth:sanctum', 'active', 'role:admin'])->group(function () {

    /*======================== Dashboard Routes ============*/

    Route::get('/dashboard/', [DashboardController::class, 'getCounts']);

    Route::prefix('attendance')->group(function () {
        Route::get('/states', [AttendanceController::class, 'states']);
    });

    /*======================== Subsidiary Routes ============*/

    Route::get('/subsidiaries/read', [SubsidiaryController::class, 'index']); // For filtering and pagination and searching and sorting

    Route::get('/subsidiaries/all', [SubsidiaryController::class, 'all']); // For fetching all subsidiaries without pagination

    Route::get('/subsidiaries/get/{subsidiary}', [SubsidiaryController::class, 'show']);

    Route::post('/subsidiaries/add', [SubsidiaryController::class, 'store']);

    Route::put('/subsidiaries/edit/{subsidiary}', [SubsidiaryController::class, 'update']);

    Route::delete('/subsidiaries/delete/{subsidiary}', [SubsidiaryController::class, 'destroy']);


    /*======================== Sectors Routes ============*/

    Route::get('/sectors/read', [SectorController::class, 'index']);

    Route::post('/sectors/add', [SectorController::class, 'store']);

    Route::get('/sectors/all', [SectorController::class, 'all']);

    Route::get('/sectors/get/{sector}', [SectorController::class, 'show']);

    Route::get('/sectors/{id}/users', [SectorController::class, 'users']);

    Route::put('/sectors/edit/{sector}', [SectorController::class, 'update']);

    Route::delete('/sectors/delete/{sector}', [SectorController::class, 'destroy']);

    /*======================== Users Routes ============*/

    Route::get('/employees/read', [UserController::class, 'index']);

    Route::get('/employees/all', [UserController::class, 'all']);

    Route::post('/employees/add', [UserController::class, 'store']);

    Route::get('/employees/get/{user}', [UserController::class, 'show']);

    Route::put('/employees/edit/{user}', [UserController::class, 'update']);

    Route::put('/employees/deactivate/{user}', [UserController::class, 'deactivate']);

    Route::delete('/employees/delete/{user}', [UserController::class, 'destroy']);

    /*======================== Shift Routes ============*/

    Route::get('/shifts/all', [ShiftController::class, 'all']);
});
