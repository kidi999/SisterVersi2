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
        // Tabel Provinsi
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->string('created_by', 100)->nullable();
            $table->timestamps();
            $table->string('updated_by', 100)->nullable();
            $table->string('deleted_by', 100)->nullable();
            $table->softDeletes();
        });

        // Tabel Kabupaten/Kota
        Schema::create('regencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->onDelete('cascade');
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->enum('type', ['Kabupaten', 'Kota']);
            $table->string('created_by', 100)->nullable();
            $table->timestamps();
            $table->string('updated_by', 100)->nullable();
            $table->string('deleted_by', 100)->nullable();
            $table->softDeletes();
        });

        // Tabel Kecamatan
        Schema::create('sub_regencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regency_id')->constrained('regencies')->onDelete('cascade');
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->string('created_by', 100)->nullable();
            $table->timestamps();
            $table->string('updated_by', 100)->nullable();
            $table->string('deleted_by', 100)->nullable();
            $table->softDeletes();
        });

        // Tabel Kelurahan/Desa
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_regency_id')->constrained('sub_regencies')->onDelete('cascade');
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->string('postal_code', 10)->nullable();
            $table->string('created_by', 100)->nullable();
            $table->timestamps();
            $table->string('updated_by', 100)->nullable();
            $table->string('deleted_by', 100)->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
        Schema::dropIfExists('sub_regencies');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
};
