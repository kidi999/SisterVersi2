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
        if (!Schema::hasTable('ruang')) {
            Schema::create('ruang', function (Blueprint $table) {
                $table->id();
                $table->string('kode_ruang', 20)->unique();
                $table->string('nama_ruang', 100);
                $table->string('gedung', 50)->nullable();
                $table->string('lantai', 10)->nullable();
                $table->integer('kapasitas')->default(0);
                $table->enum('jenis_ruang', ['Kelas', 'Lab', 'Perpustakaan', 'Aula', 'Ruang Seminar', 'Ruang Rapat', 'Lainnya'])->default('Kelas');
                $table->enum('tingkat_kepemilikan', ['Universitas', 'Fakultas', 'Prodi']);
                $table->unsignedBigInteger('fakultas_id')->nullable();
                $table->unsignedBigInteger('program_studi_id')->nullable();
                $table->text('fasilitas')->nullable();
                $table->enum('status', ['Aktif', 'Tidak Aktif', 'Dalam Perbaikan'])->default('Aktif');
                $table->text('keterangan')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Foreign keys
                $table->foreign('fakultas_id')->references('id')->on('fakultas')->onDelete('set null');
                $table->foreign('program_studi_id')->references('id')->on('program_studi')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruang');
    }
};
