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
        Schema::create('absensi_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_kuliah_id')->constrained('pertemuan_kuliah')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('krs_id')->constrained('krs')->onDelete('cascade'); // Link ke KRS untuk validasi
            
            // Status kehadiran
            $table->enum('status_kehadiran', ['Hadir', 'Izin', 'Sakit', 'Alpa'])->default('Alpa');
            $table->time('waktu_absen')->nullable(); // Waktu mahasiswa absen (untuk cek keterlambatan)
            $table->boolean('is_terlambat')->default(false);
            $table->integer('menit_keterlambatan')->default(0);
            
            // Bukti
            $table->text('keterangan')->nullable(); // Keterangan jika izin/sakit
            $table->string('bukti_file')->nullable(); // File bukti (surat sakit, dll)
            
            // Koordinat GPS (jika absen pakai sistem GPS)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Metode absensi
            $table->enum('metode_absensi', ['Manual', 'QR Code', 'GPS', 'Barcode', 'Fingerprint'])->default('Manual');
            
            // Verifikasi
            $table->boolean('is_verified')->default(false); // Apakah sudah diverifikasi dosen
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status_kehadiran');
            $table->index(['pertemuan_kuliah_id', 'mahasiswa_id']);
            $table->index('is_verified');
            $table->index('deleted_at');
            
            // Unique constraint: satu mahasiswa hanya punya 1 absensi per pertemuan
            $table->unique(['pertemuan_kuliah_id', 'mahasiswa_id', 'deleted_at'], 'unique_absensi_mhs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_mahasiswa');
    }
};
