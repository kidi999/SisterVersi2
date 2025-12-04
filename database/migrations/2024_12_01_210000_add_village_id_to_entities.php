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
        // Tambahkan village_id ke tabel fakultas
        Schema::table('fakultas', function (Blueprint $table) {
            $table->foreignId('village_id')->nullable()->after('email')->constrained('villages')->onDelete('set null');
        });

        // Tambahkan village_id ke tabel dosen
        Schema::table('dosen', function (Blueprint $table) {
            $table->foreignId('village_id')->nullable()->after('alamat')->constrained('villages')->onDelete('set null');
        });

        // Tambahkan village_id ke tabel mahasiswa
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->foreignId('village_id')->nullable()->after('alamat')->constrained('villages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fakultas', function (Blueprint $table) {
            $table->dropForeign(['village_id']);
            $table->dropColumn('village_id');
        });

        Schema::table('dosen', function (Blueprint $table) {
            $table->dropForeign(['village_id']);
            $table->dropColumn('village_id');
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['village_id']);
            $table->dropColumn('village_id');
        });
    }
};
