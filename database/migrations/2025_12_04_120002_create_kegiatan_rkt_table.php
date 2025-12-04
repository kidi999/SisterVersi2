<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan_rkt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_rkt_id')->constrained('program_rkt')->onDelete('cascade');
            $table->string('kode_kegiatan', 50);
            $table->string('nama_kegiatan', 255);
            $table->text('deskripsi')->nullable();
            $table->decimal('anggaran', 15, 2)->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['Belum Mulai', 'Dalam Proses', 'Selesai', 'Tertunda', 'Dibatalkan'])->default('Belum Mulai');
            $table->integer('urutan')->default(0);
            
            // Audit fields
            $table->unsignedBigInteger('inserted_by')->nullable();
            $table->timestamp('inserted_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Indexes
            $table->index('kode_kegiatan');
            $table->index('status');
            $table->index('program_rkt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan_rkt');
    }
};
