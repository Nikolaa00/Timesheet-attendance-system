<?php

namespace App\Http\Controllers;


use App\Http\Services\AttendanceService;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\UserAttendanceStateResource;
class AttendanceController extends Controller
{
    private $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function checkIn()
    {
        $attendance = $this->attendanceService->checkIn(auth()->user());

        return response()->json(
            new AttendanceResource($attendance)
        );
    }
    public function checkOut()
    {
        $attendance = $this->attendanceService->checkOut(auth()->user());

        return response()->json(
            new AttendanceResource($attendance)
        );
    }
    public function status()
    {
        $attendance = $this->attendanceService->getStatus(auth()->user());

        return response()->json(
            new AttendanceResource($attendance)
        );
    }

    public function states()
    {
        $users = $this->attendanceService->getAllStates();

        return response()->json(
            UserAttendanceStateResource::collection($users)
        );
    }
}
