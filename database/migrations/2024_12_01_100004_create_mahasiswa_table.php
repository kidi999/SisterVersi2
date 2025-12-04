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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_studi_id')->constrained('program_studi')->onDelete('cascade');
            $table->string('nim', 20)->unique();
            $table->string('nama_mahasiswa', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->unique();
            $table->string('tahun_masuk', 4);
            $table->integer('semester')->default(1);
            $table->decimal('ipk', 3, 2)->default(0.00);
            $table->enum('status', ['Aktif', 'Cuti', 'Lulus', 'DO', 'Mengundurkan Diri'])->default('Aktif');
            $table->string('nama_wali', 100)->nullable();
            $table->string('telepon_wali', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
