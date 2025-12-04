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
        Schema::table('mata_kuliah', function (Blueprint $table) {
            // Change column types from varchar to bigint unsigned
            $table->unsignedBigInteger('updated_by')->nullable()->change();
            $table->unsignedBigInteger('deleted_by')->nullable()->change();
            
            // Add foreign keys
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
            
            // Revert column types back to varchar
            $table->string('updated_by', 100)->nullable()->change();
            $table->string('deleted_by', 100)->nullable()->change();
        });
    }
};
