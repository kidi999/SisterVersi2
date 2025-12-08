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
        // Tambahkan kolom audit ke semua tabel
        $tables = [
            'fakultas',
            'program_studi',
            'dosen',
            'mahasiswa',
            'mata_kuliah',
            'kelas',
            'jadwal_kuliah',
            'krs',
            'nilai'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('created_by', 100)->nullable()->after('id');
                $table->timestamp('created_at')->nullable()->after('created_by');
                $table->string('updated_by', 100)->nullable()->after('updated_at');
                $table->string('deleted_by', 100)->nullable()->after('updated_by');
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'fakultas',
            'program_studi',
            'dosen',
            'mahasiswa',
            'mata_kuliah',
            'kelas',
            'jadwal_kuliah',
            'krs',
            'nilai'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['created_by', 'created_at', 'updated_by', 'deleted_by']);
                $table->dropSoftDeletes();
            });
        }
    }
};
