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
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 200);
            $table->string('singkatan', 50)->nullable();
            $table->enum('jenis', ['Negeri', 'Swasta']);
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            
            // Akreditasi
            $table->enum('akreditasi', ['A', 'B', 'C', 'Unggul', 'Baik Sekali', 'Baik', 'Belum Terakreditasi'])->nullable();
            $table->string('no_sk_akreditasi', 100)->nullable();
            $table->date('tanggal_akreditasi')->nullable();
            $table->date('tanggal_berakhir_akreditasi')->nullable();
            
            // Pendirian
            $table->string('no_sk_pendirian', 100)->nullable();
            $table->date('tanggal_pendirian')->nullable();
            $table->string('no_izin_operasional', 100)->nullable();
            $table->date('tanggal_izin_operasional')->nullable();
            
            // Pimpinan
            $table->string('rektor', 100)->nullable();
            $table->string('nip_rektor', 50)->nullable();
            $table->string('wakil_rektor_1', 100)->nullable();
            $table->string('wakil_rektor_2', 100)->nullable();
            $table->string('wakil_rektor_3', 100)->nullable();
            $table->string('wakil_rektor_4', 100)->nullable();
            
            // Kontak
            $table->string('email', 100)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('website', 100)->nullable();
            
            // Alamat
            $table->text('alamat')->nullable();
            $table->foreignId('village_id')->nullable()->constrained('villages')->onDelete('set null');
            $table->string('kode_pos', 10)->nullable();
            
            // Logo dan Identitas Visual
            $table->string('logo_path', 255)->nullable();
            $table->string('nama_file_logo', 255)->nullable();
            
            // Informasi Tambahan
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->text('sejarah')->nullable();
            $table->text('keterangan')->nullable();
            
            // Audit Trail
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->string('deleted_by', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};
