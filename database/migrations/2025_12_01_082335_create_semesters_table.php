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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->onDelete('cascade');
            $table->foreignId('fakultas_id')->nullable()->constrained('fakultas')->onDelete('cascade');
            $table->foreignId('program_studi_id')->nullable()->constrained('program_studi')->onDelete('cascade');
            $table->string('nama_semester', 50); // Ganjil/Genap/Pendek
            $table->integer('nomor_semester'); // 1,2,3,dst untuk tracking semester ke berapa
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->date('tanggal_mulai_perkuliahan')->nullable();
            $table->date('tanggal_selesai_perkuliahan')->nullable();
            $table->date('tanggal_mulai_uts')->nullable();
            $table->date('tanggal_selesai_uts')->nullable();
            $table->date('tanggal_mulai_uas')->nullable();
            $table->date('tanggal_selesai_uas')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('keterangan')->nullable();
            
            // Audit fields
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->string('deleted_by', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['tahun_akademik_id', 'fakultas_id']);
            $table->index(['tahun_akademik_id', 'program_studi_id']);
            $table->index('is_active');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
