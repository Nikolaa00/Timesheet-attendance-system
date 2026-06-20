<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\ApiException;
use App\Exceptions\UserNotActiveException;

class AuthService
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
    ) {}

    public function login(array $validatedData)
    {
        $user = User::where('email', $validatedData['email'])->first();
        if (
            !$user || !Hash::check($validatedData['password'], $user->password)
        ) {
            throw new ApiException(
                message: 'The provided credentials are incorrect.',
                loc: ['credentials'],
                type: 'authentication_error',
                status: 401
            );
        }

        if (!$user->is_active) {
            throw new UserNotActiveException();
        }

        $this->attendanceService->syncCheckInState($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
