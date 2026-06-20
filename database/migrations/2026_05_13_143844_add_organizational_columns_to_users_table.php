<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('subsidiary_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('sector_id')->nullable()->after('subsidiary_id')->constrained()->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->after('sector_id')->constrained()->nullOnDelete();
            $table->string('signature_path')->nullable()->after('shift_id');
            $table->renameColumn('is_working', 'is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['subsidiary_id']);
            $table->dropForeign(['sector_id']);
            $table->dropForeign(['shift_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['subsidiary_id', 'sector_id', 'shift_id', 'signature_path']);
            $table->renameColumn('is_active', 'is_working');
        });
    }
};
