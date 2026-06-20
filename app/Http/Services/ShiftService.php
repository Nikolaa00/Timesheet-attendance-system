<?php
namespace App\Http\Services;
use App\Models\Shift;
class ShiftService
{
public function all()
    {
        return Shift::orderBy('name')->get();
    }
}