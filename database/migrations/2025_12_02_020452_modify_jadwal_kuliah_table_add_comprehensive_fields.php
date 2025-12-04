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
        // Drop old ruangan column first (already have ruang_id)
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_kuliah', 'ruangan')) {
                $table->dropColumn('ruangan');
            }
        });
        
        // Update audit columns from varchar to bigint foreign key
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            // Drop existing varchar audit columns
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
        
        // Add new audit columns as foreign keys
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('ruang_id')->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->after('updated_by')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            // Drop foreign key audit columns
            $table->dropForeign(['deleted_by']);
            $table->dropColumn('deleted_by');
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
        
        // Re-add varchar audit columns
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->string('deleted_by', 100)->nullable();
            
            // Re-add ruangan column
            $table->string('ruangan', 20)->nullable();
        });
    }
};
