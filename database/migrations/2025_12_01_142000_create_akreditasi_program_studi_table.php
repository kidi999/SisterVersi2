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
        Schema::create('akreditasi_program_studi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_studi_id')->constrained('program_studi')->onDelete('cascade');
            $table->string('lembaga_akreditasi', 100); // BAN-PT, LAMDIK, dll
            $table->string('nomor_sk', 100);
            $table->date('tanggal_sk');
            $table->date('tanggal_berakhir')->nullable();
            $table->enum('peringkat', ['Unggul', 'Baik Sekali', 'Baik', 'A', 'B', 'C', 'Belum Terakreditasi']);
            $table->year('tahun_akreditasi');
            $table->text('catatan')->nullable();
            $table->enum('status', ['Aktif', 'Kadaluarsa', 'Dalam Proses'])->default('Aktif');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akreditasi_program_studi');
    }
};
