<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Http\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function login(LoginRequest $loginRequest)
    {
        $result = $this->authService->login($loginRequest->validated());

        $cookie = Cookie::make(
            name: 'session_id',
            value: $result['token'],
            minutes: config('session.lifetime', 60),
            path: '/',
            domain: config('session.domain', null),
            secure: config('session.secure', false),
            httpOnly: true,
            raw: false,
            sameSite: config('session.same_site', 'lax')
        );

        return response()
            ->json([
                'user' => new AuthResource($result['user'])
            ])
            ->cookie(
                $cookie
            );
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()
            ->json(['message' => 'Logout successful'])
            ->withoutCookie('session_id');
    }
}
