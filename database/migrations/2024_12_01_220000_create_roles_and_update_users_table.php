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
        // Tabel Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->string('inserted_by', 100)->nullable();
            $table->timestamp('inserted_at')->nullable();
            $table->timestamps();
            $table->string('updated_by', 100)->nullable();
            $table->string('deleted_by', 100)->nullable();
            $table->softDeletes();
        });

        // Update tabel users untuk menambahkan relasi
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->onDelete('set null');
            $table->foreignId('fakultas_id')->nullable()->after('role_id')->constrained('fakultas')->onDelete('set null');
            $table->foreignId('program_studi_id')->nullable()->after('fakultas_id')->constrained('program_studi')->onDelete('set null');
            $table->foreignId('dosen_id')->nullable()->after('program_studi_id')->constrained('dosen')->onDelete('set null');
            $table->foreignId('mahasiswa_id')->nullable()->after('dosen_id')->constrained('mahasiswa')->onDelete('set null');
            $table->boolean('is_active')->default(true)->after('password');
            $table->string('inserted_by', 100)->nullable()->after('remember_token');
            $table->timestamp('inserted_at')->nullable()->after('inserted_by');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['fakultas_id']);
            $table->dropForeign(['program_studi_id']);
            $table->dropForeign(['dosen_id']);
            $table->dropForeign(['mahasiswa_id']);
            $table->dropColumn([
                'role_id', 'fakultas_id', 'program_studi_id', 
                'dosen_id', 'mahasiswa_id', 'is_active',
                'inserted_by', 'inserted_at', 'updated_by', 'deleted_by'
            ]);
            $table->dropSoftDeletes();
        });
        
        Schema::dropIfExists('roles');
    }
};
