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
        Schema::table('semesters', function (Blueprint $table) {
            // Drop foreign key dan index terkait fakultas_id
            $table->dropForeign(['fakultas_id']);
            $table->dropIndex('semesters_tahun_akademik_id_fakultas_id_index');
            
            // Drop kolom fakultas_id
            $table->dropColumn('fakultas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('semesters', function (Blueprint $table) {
            // Tambahkan kembali kolom fakultas_id
            $table->unsignedBigInteger('fakultas_id')->nullable()->after('tahun_akademik_id');
            
            // Tambahkan kembali foreign key dan index
            $table->foreign('fakultas_id')->references('id')->on('fakultas')->onDelete('cascade');
            $table->index(['tahun_akademik_id', 'fakultas_id']);
        });
    }
};
