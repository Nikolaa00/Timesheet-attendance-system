<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('day_name', 15)->nullable()->after('date');
            $table->string('check_in_signature')->nullable()->after('auto_check_out');
            $table->string('check_out_signature')->nullable()->after('check_in_signature');
            $table->time('break_duration')->default('01:00:00')->after('check_out_signature');
            $table->time('regular_hours')->default('08:00:00')->after('break_duration');
            $table->time('overtime_hours')->default('00:00:00')->after('regular_hours');
            $table->time('holiday_hours')->default('00:00:00')->after('overtime_hours');
            $table->time('effective_hours')->default('00:00:00')->after('holiday_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'day_name',
                'check_in_signature',
                'check_out_signature',
                'break_duration',
                'regular_hours',
                'overtime_hours',
                'holiday_hours',
                'effective_hours'
            ]);
        });
    }
};
