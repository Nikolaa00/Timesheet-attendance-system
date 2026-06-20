<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First closure: Change the column type/nullability
        Schema::table('subsidiaries', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });

        // Second closure: Rename the column
        Schema::table('subsidiaries', function (Blueprint $table) {
            $table->renameColumn('phone', 'phone_number');
        });
    }

    public function down(): void
    {
        // First closure: Revert the column rename
        Schema::table('subsidiaries', function (Blueprint $table) {
            $table->renameColumn('phone_number', 'phone');
        });

        // Second closure: Revert the column nullability change
        Schema::table('subsidiaries', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
