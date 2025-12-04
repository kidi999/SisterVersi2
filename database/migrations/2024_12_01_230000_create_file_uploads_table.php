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
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('fileable_type'); // Polymorphic type (Fakultas, Mahasiswa, etc)
            $table->unsignedBigInteger('fileable_id'); // Polymorphic id
            $table->string('file_name'); // Original filename
            $table->string('file_path'); // Storage path
            $table->string('file_type')->nullable(); // mime type
            $table->integer('file_size'); // in bytes
            $table->string('description')->nullable(); // Optional description
            $table->string('category')->default('general'); // Category: document, image, etc
            $table->integer('order')->default(0); // For ordering files
            
            // Audit fields
            $table->string('inserted_by')->nullable();
            $table->timestamp('inserted_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Indexes
            $table->index(['fileable_type', 'fileable_id']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_uploads');
    }
};
