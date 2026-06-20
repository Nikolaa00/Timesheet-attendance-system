<?php
namespace App\Http\Services;

use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Mail;
use Str;
use \App\Models\User;
use Illuminate\Validation\ValidationException;

class ResetPasswordService
{
    public function sendResetLink(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user || !$user->is_active) {
            return;
        }

        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert(
            [
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        $baseUrl = rtrim(config('app.web_app_url'), '/');

        $url = $baseUrl . '/reset-password' . '?token=' . urlencode($token);

        Mail::to($email)->send(new ResetPasswordMail($url, $user->first_name));
    }

    public function resetPassword(array $data): void
    {
        $record = DB::table('password_reset_tokens')
            ->where('token', $data['token'])
            ->first();

        if (!$record) {
            throw ValidationException::withMessages([
                'token' => ['Reset request not found.'],
            ]);
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            throw ValidationException::withMessages([
                'token' => ['Token expired.'],
            ]);
        }

        $user = User::where('email', $record->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'token' => ['User not found.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'token' => ['The user account is currently inactive.'],
            ]);
        }

        $user->update([
            'password' => $data['new_password'],
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $record->email)
            ->delete();
    }
}
