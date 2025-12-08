<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rencana_kerja_tahunan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rkt', 50)->unique();
            $table->string('judul_rkt', 255);
            $table->year('tahun');
            $table->enum('level', ['Universitas', 'Fakultas', 'Prodi']);
            $table->foreignId('university_id')->nullable()->constrained('universities')->onDelete('cascade');
            $table->foreignId('fakultas_id')->nullable()->constrained('fakultas')->onDelete('cascade');
            $table->foreignId('program_studi_id')->nullable()->constrained('program_studi')->onDelete('cascade');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('anggaran', 15, 2)->default(0);
            $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Dalam Proses', 'Selesai', 'Dibatalkan'])->default('Draft');
            $table->text('catatan_penolakan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_disetujui')->nullable();
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Indexes
            $table->index('kode_rkt');
            $table->index('tahun');
            $table->index('level');
            $table->index('status');
            $table->index(['fakultas_id', 'tahun']);
            $table->index(['program_studi_id', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rencana_kerja_tahunan');
    }
};
