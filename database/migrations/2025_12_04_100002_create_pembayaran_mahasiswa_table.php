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
        Schema::create('pembayaran_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_mahasiswa_id')->constrained('tagihan_mahasiswa')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            
            // Detail pembayaran
            $table->string('nomor_pembayaran', 50)->unique(); // PMB/2024/001/00001
            $table->decimal('jumlah_bayar', 15, 2);
            $table->date('tanggal_bayar');
            $table->time('waktu_bayar')->nullable();
            
            // Metode pembayaran
            $table->enum('metode_pembayaran', [
                'Transfer Bank',
                'Tunai',
                'Virtual Account',
                'E-Wallet',
                'Kartu Kredit/Debit',
                'Lainnya'
            ])->default('Transfer Bank');
            
            $table->string('nama_bank', 100)->nullable(); // BCA, Mandiri, BNI, BRI, etc
            $table->string('nomor_rekening', 50)->nullable();
            $table->string('nama_pemilik_rekening', 100)->nullable();
            $table->string('nomor_referensi', 100)->nullable(); // nomor transaksi dari bank
            
            // Bukti pembayaran
            $table->string('bukti_pembayaran')->nullable(); // path to file upload
            
            // Status verifikasi
            $table->enum('status_verifikasi', ['Pending', 'Diverifikasi', 'Ditolak'])->default('Pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            
            $table->text('keterangan')->nullable();
            
            // Audit fields
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->string('deleted_by', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('nomor_pembayaran', 'idx_nomor_bayar');
            $table->index('tanggal_bayar', 'idx_tgl_bayar');
            $table->index('status_verifikasi', 'idx_status_verif');
            $table->index(['mahasiswa_id', 'tanggal_bayar'], 'idx_mhs_tgl_bayar');
            $table->index('deleted_at', 'idx_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_mahasiswa');
    }
};
