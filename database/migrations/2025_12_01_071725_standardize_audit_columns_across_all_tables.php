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
        // List of all tables that need standardization
        $tables = [
            'fakultas',
            'program_studis',
            'mata_kuliahs',
            'dosen',
            'mahasiswa',
            'kelas',
            'jadwal_kuliahs',
            'krs',
            'nilais',
            'provinces',
            'regencies',
            'sub_regencies',
            'villages',
            'file_uploads',
            'jabatan_struktural',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Rename inserted_by to created_by if exists
                    if (Schema::hasColumn($tableName, 'inserted_by')) {
                        $table->renameColumn('inserted_by', 'created_by');
                    }
                    
                    // Drop inserted_at if created_at already exists (fakultas case)
                    if (Schema::hasColumn($tableName, 'inserted_at') && Schema::hasColumn($tableName, 'created_at')) {
                        $table->dropColumn('inserted_at');
                    }
                    // Otherwise rename inserted_at to created_at
                    elseif (Schema::hasColumn($tableName, 'inserted_at')) {
                        $table->renameColumn('inserted_at', 'created_at');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'fakultas',
            'program_studis',
            'mata_kuliahs',
            'dosen',
            'mahasiswa',
            'kelas',
            'jadwal_kuliahs',
            'krs',
            'nilais',
            'provinces',
            'regencies',
            'sub_regencies',
            'villages',
            'file_uploads',
            'jabatan_struktural',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'created_by')) {
                        $table->renameColumn('created_by', 'inserted_by');
                    }
                    
                    if (Schema::hasColumn($tableName, 'created_at')) {
                        $table->renameColumn('created_at', 'inserted_at');
                    }
                });
            }
        }
    }
};
