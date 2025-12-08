<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_rkt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencana_kerja_tahunan_id')->constrained('rencana_kerja_tahunan')->onDelete('cascade');
            $table->string('kode_program', 50);
            $table->string('nama_program', 255);
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['Akademik', 'Penelitian', 'Pengabdian', 'Kemahasiswaan', 'Infrastruktur', 'SDM', 'Kerjasama', 'Lainnya']);
            $table->decimal('anggaran', 15, 2)->default(0);
            $table->date('target_mulai');
            $table->date('target_selesai');
            $table->string('penanggung_jawab', 255)->nullable();
            $table->text('indikator_kinerja')->nullable();
            $table->integer('urutan')->default(0);
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Indexes
            $table->index('kode_program');
            $table->index('kategori');
            $table->index('rencana_kerja_tahunan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_rkt');
    }
};
