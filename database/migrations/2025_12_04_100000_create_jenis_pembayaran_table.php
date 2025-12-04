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
        Schema::create('jenis_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // UKT, SPP, DPP, WISUDA, etc
            $table->string('nama', 100); // Uang Kuliah Tunggal, SPP, Dana Pengembangan Pendidikan
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['Tetap', 'Variabel', 'Insidental'])->default('Tetap');
            // Tetap: wajib setiap semester (UKT, SPP)
            // Variabel: berdasarkan kondisi (Semester Pendek, Praktikum)
            // Insidental: sekali bayar (DPP, Wisuda, Cuti)
            $table->boolean('is_wajib')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(1); // untuk sorting tampilan
            
            // Audit fields
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->string('deleted_by', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('is_active');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_pembayaran');
    }
};
