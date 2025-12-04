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
        Schema::table('dosen', function (Blueprint $table) {
            // Ubah program_studi_id menjadi nullable dan tambah kolom level
            if (Schema::hasColumn('dosen', 'program_studi_id')) {
                $table->dropForeign(['program_studi_id']);
                $table->foreignId('program_studi_id')->nullable()->change();
                $table->foreign('program_studi_id')->references('id')->on('program_studi')->onDelete('cascade');
            }
            
            // Tambah kolom fakultas_id (nullable)
            if (!Schema::hasColumn('dosen', 'fakultas_id')) {
                $table->foreignId('fakultas_id')->nullable()->after('program_studi_id')->constrained('fakultas')->onDelete('cascade');
            }
            
            // Tambah kolom level_dosen
            if (!Schema::hasColumn('dosen', 'level_dosen')) {
                $table->enum('level_dosen', ['universitas', 'fakultas', 'prodi'])->default('prodi')->after('program_studi_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            if (Schema::hasColumn('dosen', 'fakultas_id')) {
                $table->dropForeign(['fakultas_id']);
                $table->dropColumn('fakultas_id');
            }
            
            if (Schema::hasColumn('dosen', 'level_dosen')) {
                $table->dropColumn('level_dosen');
            }
        });
    }
};
