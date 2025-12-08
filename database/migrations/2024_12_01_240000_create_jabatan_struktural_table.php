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
        Schema::create('jabatan_struktural', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('fakultas_id')->nullable()->constrained('fakultas')->onDelete('cascade');
            $table->foreignId('program_studi_id')->nullable()->constrained('program_studi')->onDelete('cascade');
            $table->enum('jenis_jabatan', ['rektor', 'wakil_rektor', 'dekan', 'wakil_dekan', 'ketua_prodi', 'sekretaris_prodi', 'direktur', 'kepala_pusat', 'kepala_biro', 'kepala_bagian', 'kepala_lab', 'lainnya']);
            $table->string('nama_jabatan', 100); // e.g., "Dekan Fakultas Teknik"
            $table->string('nomor_sk', 50);
            $table->date('tanggal_sk');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'diberhentikan'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->string('file_sk_path')->nullable(); // Path file SK
            
            // Audit fields
            $table->string('created_by', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 100)->nullable();
            $table->softDeletes('deleted_at');
            
            // Indexes
            $table->index(['dosen_id', 'status']);
            $table->index(['fakultas_id', 'jenis_jabatan', 'status']);
            $table->index('tanggal_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan_struktural');
    }
};
