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
        Schema::table('fakultas', function (Blueprint $table) {
            // Add dekan_id as foreign key to dosen table
            $table->foreignId('dekan_id')->nullable()->after('singkatan')->constrained('dosen')->onDelete('set null');
            
            // Keep dekan field for backward compatibility or manual override
            // But it will be auto-populated from dosen name
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fakultas', function (Blueprint $table) {
            $table->dropForeign(['dekan_id']);
            $table->dropColumn('dekan_id');
        });
    }
};
