<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sector_subsidiary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subsidiary_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['sector_id', 'subsidiary_id']);
        });

        if (Schema::hasColumn('sectors', 'subsidiary_id')) {
            DB::table('sectors')
                ->whereNotNull('subsidiary_id')
                ->orderBy('id')
                ->select(['id', 'subsidiary_id'])
                ->chunk(500, function ($sectors): void {
                    $now = now();

                    $rows = $sectors->map(fn ($sector): array => [
                        'sector_id' => $sector->id,
                        'subsidiary_id' => $sector->subsidiary_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all();

                    DB::table('sector_subsidiary')->insertOrIgnore($rows);
                });

            Schema::table('sectors', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('subsidiary_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('sectors', function (Blueprint $table): void {
            $table->foreignId('subsidiary_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
        });

        DB::table('sector_subsidiary')
            ->orderBy('sector_id')
            ->select(['sector_id', 'subsidiary_id'])
            ->chunk(500, function ($assignments): void {
                foreach ($assignments as $assignment) {
                    DB::table('sectors')
                        ->where('id', $assignment->sector_id)
                        ->whereNull('subsidiary_id')
                        ->update(['subsidiary_id' => $assignment->subsidiary_id]);
                }
            });

        Schema::dropIfExists('sector_subsidiary');
    }
};
