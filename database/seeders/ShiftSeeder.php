<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shift;
use App\Enums\ShiftType;
class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (ShiftType::cases() as $type) {
            Shift::firstOrCreate([
                'name' => $type->value, 
            ]);
        }
    }
}
