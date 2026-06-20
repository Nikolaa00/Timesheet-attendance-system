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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('last_name');
            $table->foreignId('created_by')
                ->nullable()
                ->after('created_at')
                ->constrained('users')
                ->nullOnDelete();
            $table->boolean('is_logged_in')->default(false);
            $table->boolean('auto_attendance')->default(false);
        });

        DB::table('users')->get()->each(function ($user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'username' => strtolower($user->first_name . '.' . $user->last_name),
                ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->change();
            $table->unique('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);

        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('created_by');
            $table->dropColumn('is_logged_in');
            $table->dropColumn('auto_attendance');
        });
    }
};
