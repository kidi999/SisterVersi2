<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set all existing string values to NULL before changing type
        DB::table('dosen')->update([
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null
        ]);

        Schema::table('dosen', function (Blueprint $table) {
            // Change column types from varchar to bigint unsigned
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->unsignedBigInteger('updated_by')->nullable()->change();
            $table->unsignedBigInteger('deleted_by')->nullable()->change();
            
            // Add foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
            
            // Revert column types back to varchar
            $table->string('created_by', 100)->nullable()->change();
            $table->string('updated_by', 100)->nullable()->change();
            $table->string('deleted_by', 100)->nullable()->change();
        });
    }
};
