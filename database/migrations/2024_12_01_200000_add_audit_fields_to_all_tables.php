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

        foreach ($tables as $tblName) {
            Schema::table($tblName, function (Blueprint $table) use ($tblName) {
                // Untuk program_studi, kolom audit & softDeletes sudah ada di migrasi create
                if ($tblName !== 'program_studi') {
                    $table->string('created_by', 100)->nullable()->after('id');
                    $table->string('updated_by', 100)->nullable()->after('updated_at');
                    $table->string('deleted_by', 100)->nullable()->after('updated_by');
                    $table->softDeletes();
                }
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

        foreach ($tables as $tblName) {
            Schema::table($tblName, function (Blueprint $table) use ($tblName) {
                if ($tblName !== 'program_studi') {
                    $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
                    $table->dropSoftDeletes();
                }
            });
        }
    }
};
