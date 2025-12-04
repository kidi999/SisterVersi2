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
        Schema::create('tagihan_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('jenis_pembayaran_id')->constrained('jenis_pembayaran')->onDelete('restrict');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            
            // Detail tagihan
            $table->string('nomor_tagihan', 50)->unique(); // TGH/2024/001/00001
            $table->decimal('jumlah_tagihan', 15, 2); // total yang harus dibayar
            $table->decimal('jumlah_dibayar', 15, 2)->default(0); // total yang sudah dibayar
            $table->decimal('sisa_tagihan', 15, 2); // sisa yang belum dibayar
            
            // Tanggal
            $table->date('tanggal_tagihan'); // kapan tagihan dibuat
            $table->date('tanggal_jatuh_tempo'); // deadline pembayaran
            $table->date('tanggal_lunas')->nullable(); // kapan lunas
            
            // Status
            $table->enum('status', ['Belum Dibayar', 'Dibayar Sebagian', 'Lunas', 'Kadaluarsa', 'Dibatalkan'])->default('Belum Dibayar');
            
            // Denda keterlambatan
            $table->decimal('denda', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            
            $table->text('keterangan')->nullable();
            
            // Audit fields
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->string('deleted_by', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('nomor_tagihan', 'idx_nomor_tagihan');
            $table->index('status', 'idx_status');
            $table->index('tanggal_jatuh_tempo', 'idx_jatuh_tempo');
            $table->index(['mahasiswa_id', 'tahun_akademik_id', 'semester_id'], 'idx_tagihan_semester');
            $table->index('deleted_at', 'idx_deleted_at');
            
            // Unique constraint: satu mahasiswa hanya punya 1 tagihan per jenis per semester
            $table->unique(['mahasiswa_id', 'jenis_pembayaran_id', 'tahun_akademik_id', 'semester_id'], 'unique_tagihan_mhs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_mahasiswa');
    }
};
