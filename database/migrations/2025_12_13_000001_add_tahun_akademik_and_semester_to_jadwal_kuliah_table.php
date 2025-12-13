<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_kuliah', 'tahun_akademik_id')) {
                $table->foreignId('tahun_akademik_id')
                    ->nullable()
                    ->after('kelas_id')
                    ->constrained('tahun_akademiks')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('jadwal_kuliah', 'semester_id')) {
                $table->foreignId('semester_id')
                    ->nullable()
                    ->after('tahun_akademik_id')
                    ->constrained('semesters')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_kuliah', 'semester_id')) {
                $table->dropForeign(['semester_id']);
                $table->dropColumn('semester_id');
            }

            if (Schema::hasColumn('jadwal_kuliah', 'tahun_akademik_id')) {
                $table->dropForeign(['tahun_akademik_id']);
                $table->dropColumn('tahun_akademik_id');
            }
        });
    }
};
