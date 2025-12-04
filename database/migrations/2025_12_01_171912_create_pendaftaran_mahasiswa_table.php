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
        Schema::create('pendaftaran_mahasiswa', function (Blueprint $table) {
            $table->id();
            
            // Tahun Akademik & Program Studi
            $table->string('tahun_akademik', 10); // contoh: 2025/2026
            $table->enum('jalur_masuk', ['SNBP', 'SNBT', 'Mandiri', 'Transfer'])->default('Mandiri');
            $table->unsignedBigInteger('program_studi_id');
            $table->foreign('program_studi_id')->references('id')->on('program_studi')->onDelete('restrict');
            
            // Data Pribadi
            $table->string('no_pendaftaran', 20)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('nik', 16)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama', 20)->nullable();
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai'])->default('Belum Kawin');
            $table->string('kewarganegaraan', 30)->default('Indonesia');
            
            // Alamat
            $table->text('alamat')->nullable();
            $table->unsignedBigInteger('village_id')->nullable();
            $table->foreign('village_id')->references('id')->on('villages')->onDelete('set null');
            $table->string('kode_pos', 10)->nullable();
            
            // Kontak
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100);
            
            // Pendidikan Terakhir
            $table->string('asal_sekolah', 100)->nullable();
            $table->string('jurusan_sekolah', 50)->nullable();
            $table->string('tahun_lulus', 4)->nullable();
            $table->decimal('nilai_rata_rata', 5, 2)->nullable();
            
            // Data Orang Tua/Wali
            $table->string('nama_ayah', 100)->nullable();
            $table->string('pekerjaan_ayah', 50)->nullable();
            $table->string('nama_ibu', 100)->nullable();
            $table->string('pekerjaan_ibu', 50)->nullable();
            $table->string('nama_wali', 100)->nullable();
            $table->string('telepon_wali', 20)->nullable();
            $table->text('alamat_wali')->nullable();
            
            // Status Pendaftaran
            $table->enum('status', ['Pending', 'Diverifikasi', 'Diterima', 'Ditolak', 'Dieksport'])->default('Pending');
            $table->text('catatan')->nullable(); // catatan dari admin
            $table->timestamp('tanggal_daftar')->useCurrent();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->unsignedBigInteger('verifikasi_by')->nullable(); // user yang memverifikasi
            $table->foreign('verifikasi_by')->references('id')->on('users')->onDelete('set null');
            
            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_mahasiswa');
    }
};
