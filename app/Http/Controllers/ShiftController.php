<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ShiftResource;
use App\Http\Services\ShiftService;

class ShiftController extends Controller
{
    protected ShiftService $service;

    public function __construct(ShiftService $service)
    {
        $this->service = $service;
    }
    public function all()
    {
        $shifts = $this->service->all();

        return response()->json([
            'data' => ShiftResource::collection($shifts)
        ]);
    }
}
