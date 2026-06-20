<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    "user_id",
    "date",
    'day_name',
    "check_in_time",
    "check_out_time",
    "auto_check_out",
    'check_in_signature',
    'check_out_signature',
    'break_duration',
    'regular_hourse',
    'overtime_hours',
    'holiday_hours',
    'effective_hours'
])]

#[Hidden([
    'created_at',
    'updated_at',
])]

class Attendance extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',

            'check_in_time' => 'datetime',
            'check_out_time' => 'datetime',
            'auto_check_out' => 'boolean',
            'break_duration' => 'immutable_datetime:H:i',
            'regular_hourse' => 'immutable_datetime:H:i',
            'overtime_hours' => 'immutable_datetime:H:i',
            'holiday_hours' => 'immutable_datetime:H:i',
            'effective_hours' => 'immutable_datetime:H:i',
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
