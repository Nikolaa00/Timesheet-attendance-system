<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Services\ResetPasswordService;

class PasswordResetController extends Controller
{
    private ResetPasswordService $service;

    public function __construct(ResetPasswordService $service)
    {
        $this->service = $service;
    }

    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $this->service->sendResetLink($request->email);

        return response()->json([
            'message' => 'Reset link sent to email'
        ]);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $this->service->resetPassword($request->validated());

        return response()->json([
            'message' => 'Password reset successful'
        ]);
    }
}
