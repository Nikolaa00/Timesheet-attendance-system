<?php
namespace App\Http\Services;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function syncCheckInState(User $user): void
    {
        $isCheckedIn = Attendance::query()
            ->where('user_id', $user->id)
            ->whereNull('check_out_time')
            ->exists();

        if ($user->is_logged_in !== $isCheckedIn) {
            $user->update(['is_logged_in' => $isCheckedIn]);
            $user->is_logged_in = $isCheckedIn;
        }
    }

    public function checkIn(User $user): Attendance
    {
        $active = Attendance::where('user_id', $user->id)
            ->whereNull('check_out_time')
            ->exists();

        if ($active) {
            throw ValidationException::withMessages([
                'attendance' => ['You are already checked in. Please check out before checking in again.'],
            ]);
        }

        $user->update(['is_logged_in' => true]);

        return Attendance::create([
            'user_id' => $user->id,
            'date' => now(),
            'check_in_time' => now(),
            'auto_check_out' => false,
        ]);;
    }

    public function checkOut(User $user): Attendance
    {
        $attendance = Attendance::where('user_id', $user->id)
            ->whereNull('check_out_time')
            ->latest()
            ->first();

        if (!$attendance) {
            throw ValidationException::withMessages([
                'attendance' => ['You are not currently checked in.'],
            ]);
        }

        $attendance->update([
            'check_out_time' => now(),
            'auto_check_out' => false,
        ]);

        $user->update(['is_logged_in' => false]);

        return $attendance;
    }

    public function autoCheckOut(): void
    {
        $staleAttendanceIds = Attendance::query()
            ->whereNull('check_out_time')
            ->where('check_in_time', '<=', now()->subHours(8))
            ->pluck('id', 'user_id');

        if ($staleAttendanceIds->isEmpty()) {
            return;
        }

        Attendance::query()
            ->whereIn('id', $staleAttendanceIds->values())
            ->update([
                'check_out_time' => now(),
                'auto_check_out' => true,
            ]);

        User::query()
            ->whereIn('id', $staleAttendanceIds->keys())
            ->update(['is_logged_in' => false]);
    }

    public function getStatus(User $user): ?Attendance
    {
        $attendance = Attendance::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$attendance) {
            throw ValidationException::withMessages([
                'attendance' => ['No active attendance record found.'],
            ]);
        }

        return $attendance;
    }

    public function getAllStates()
    {
        return User::query()
            ->whereIn('role', ['admin', 'employee'])
            ->select('id', 'is_logged_in')
            ->orderBy('id')
            ->get();
    }
}