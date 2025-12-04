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
        Schema::create('pertemuan_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kuliah_id')->constrained('jadwal_kuliah')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            
            // Detail pertemuan
            $table->integer('pertemuan_ke'); // Pertemuan ke berapa (1-16)
            $table->date('tanggal_pertemuan');
            $table->time('jam_mulai_actual')->nullable(); // Jam mulai aktual (bisa beda dari jadwal)
            $table->time('jam_selesai_actual')->nullable(); // Jam selesai aktual
            
            // Materi
            $table->string('topik_bahasan', 200)->nullable(); // Topik yang dibahas
            $table->text('materi')->nullable(); // Detail materi
            $table->text('catatan')->nullable(); // Catatan dosen
            
            // Status pertemuan
            $table->enum('status', ['Terjadwal', 'Berlangsung', 'Selesai', 'Dibatalkan', 'Diganti'])->default('Terjadwal');
            $table->string('alasan_batal', 200)->nullable(); // Alasan jika dibatalkan/diganti
            $table->date('tanggal_pengganti')->nullable(); // Tanggal pengganti jika diganti
            
            // File materi
            $table->string('file_materi')->nullable(); // Path file materi (PDF, PPT, dll)
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('tanggal_pertemuan');
            $table->index('status');
            $table->index(['jadwal_kuliah_id', 'pertemuan_ke']);
            $table->index('deleted_at');
            
            // Unique constraint: satu jadwal hanya punya 1 pertemuan untuk pertemuan_ke yang sama
            $table->unique(['jadwal_kuliah_id', 'pertemuan_ke', 'deleted_at'], 'unique_pertemuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertemuan_kuliah');
    }
};
