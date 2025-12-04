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
        Schema::table('mata_kuliah', function (Blueprint $table) {
            // Add level_matkul column
            $table->enum('level_matkul', ['universitas', 'fakultas', 'prodi'])
                  ->default('prodi')
                  ->after('program_studi_id');
            
            // Add fakultas_id column (nullable)
            $table->foreignId('fakultas_id')
                  ->nullable()
                  ->after('program_studi_id')
                  ->constrained('fakultas')
                  ->onDelete('cascade');
            
            // Make program_studi_id nullable (karena matkul universitas dan fakultas tidak punya prodi)
            $table->foreignId('program_studi_id')
                  ->nullable()
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropForeign(['fakultas_id']);
            $table->dropColumn(['level_matkul', 'fakultas_id']);
            
            // Restore program_studi_id to not nullable
            $table->foreignId('program_studi_id')
                  ->nullable(false)
                  ->change();
        });
    }
};
