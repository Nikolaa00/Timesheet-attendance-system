<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sectors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subsidiary_id')->constrained()->onDelete('restrict');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sectors');
        Schema::dropIfExists('shifts');
    }
};
