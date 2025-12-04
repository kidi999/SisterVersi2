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
        Schema::create('hari_libur', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100); // Nama hari libur
            $table->date('tanggal'); // Tanggal libur
            $table->enum('jenis', ['Nasional', 'Keagamaan', 'Lokal', 'Akademik'])->default('Nasional');
            // Nasional: Libur nasional (17 Agustus, dll)
            // Keagamaan: Libur keagamaan (Lebaran, Natal, dll)
            // Lokal: Libur daerah/provinsi
            // Akademik: Libur akademik (UTS, UAS, Libur semester)
            $table->text('keterangan')->nullable();
            $table->boolean('is_recurring')->default(false); // Apakah berulang tiap tahun
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('tanggal');
            $table->index('jenis');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hari_libur');
    }
};
