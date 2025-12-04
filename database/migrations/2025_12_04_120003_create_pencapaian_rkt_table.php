<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pencapaian_rkt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_rkt_id')->constrained('kegiatan_rkt')->onDelete('cascade');
            $table->enum('periode', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4', 'Semester 1', 'Semester 2', 'Tahunan']);
            $table->date('tanggal_laporan');
            $table->text('capaian')->nullable();
            $table->decimal('persentase_capaian', 5, 2)->default(0.00); // 0.00 - 100.00
            $table->decimal('realisasi_anggaran', 15, 2)->default(0);
            $table->text('kendala')->nullable();
            $table->text('solusi')->nullable();
            $table->text('rencana_tindak_lanjut')->nullable();
            $table->string('file_dokumentasi', 255)->nullable();
            $table->foreignId('dilaporkan_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->enum('status_verifikasi', ['Pending', 'Diverifikasi', 'Ditolak'])->default('Pending');
            $table->text('catatan_verifikasi')->nullable();
            
            // Audit fields
            $table->unsignedBigInteger('inserted_by')->nullable();
            $table->timestamp('inserted_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Indexes
            $table->index('periode');
            $table->index('status_verifikasi');
            $table->index('kegiatan_rkt_id');
            $table->index('tanggal_laporan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pencapaian_rkt');
    }
};
