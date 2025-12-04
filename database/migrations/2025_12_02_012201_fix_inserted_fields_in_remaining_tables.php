<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix inserted_by and inserted_at fields in jadwal_kuliah, mata_kuliah, nilai, program_studi
     */
    public function up(): void
    {
        // 1. jadwal_kuliah
        if (Schema::hasTable('jadwal_kuliah')) {
            // Rename inserted_by to created_by if not exists
            if (Schema::hasColumn('jadwal_kuliah', 'inserted_by') && !Schema::hasColumn('jadwal_kuliah', 'created_by')) {
                Schema::table('jadwal_kuliah', function (Blueprint $table) {
                    $table->renameColumn('inserted_by', 'created_by');
                });
            } elseif (Schema::hasColumn('jadwal_kuliah', 'inserted_by')) {
                // If created_by already exists, drop inserted_by
                Schema::table('jadwal_kuliah', function (Blueprint $table) {
                    $table->dropColumn('inserted_by');
                });
            }
            
            // Drop inserted_at (created_at already exists from timestamps())
            if (Schema::hasColumn('jadwal_kuliah', 'inserted_at')) {
                Schema::table('jadwal_kuliah', function (Blueprint $table) {
                    $table->dropColumn('inserted_at');
                });
            }
        }

        // 2. mata_kuliah
        if (Schema::hasTable('mata_kuliah')) {
            // Drop inserted_by (created_by already exists as bigint foreign key)
            if (Schema::hasColumn('mata_kuliah', 'inserted_by')) {
                Schema::table('mata_kuliah', function (Blueprint $table) {
                    $table->dropColumn('inserted_by');
                });
            }
            
            // Drop inserted_at (created_at already exists)
            if (Schema::hasColumn('mata_kuliah', 'inserted_at')) {
                Schema::table('mata_kuliah', function (Blueprint $table) {
                    $table->dropColumn('inserted_at');
                });
            }
        }

        // 3. nilai
        if (Schema::hasTable('nilai')) {
            // Rename inserted_by to created_by if not exists
            if (Schema::hasColumn('nilai', 'inserted_by') && !Schema::hasColumn('nilai', 'created_by')) {
                Schema::table('nilai', function (Blueprint $table) {
                    $table->renameColumn('inserted_by', 'created_by');
                });
            } elseif (Schema::hasColumn('nilai', 'inserted_by')) {
                Schema::table('nilai', function (Blueprint $table) {
                    $table->dropColumn('inserted_by');
                });
            }
            
            // Drop inserted_at
            if (Schema::hasColumn('nilai', 'inserted_at')) {
                Schema::table('nilai', function (Blueprint $table) {
                    $table->dropColumn('inserted_at');
                });
            }
        }

        // 4. program_studi
        if (Schema::hasTable('program_studi')) {
            // Drop inserted_by (created_by already exists as bigint foreign key)
            if (Schema::hasColumn('program_studi', 'inserted_by')) {
                Schema::table('program_studi', function (Blueprint $table) {
                    $table->dropColumn('inserted_by');
                });
            }
            
            // Drop inserted_at
            if (Schema::hasColumn('program_studi', 'inserted_at')) {
                Schema::table('program_studi', function (Blueprint $table) {
                    $table->dropColumn('inserted_at');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. jadwal_kuliah
        if (Schema::hasTable('jadwal_kuliah')) {
            Schema::table('jadwal_kuliah', function (Blueprint $table) {
                if (!Schema::hasColumn('jadwal_kuliah', 'inserted_by')) {
                    $table->string('inserted_by', 100)->nullable()->after('id');
                }
                if (!Schema::hasColumn('jadwal_kuliah', 'inserted_at')) {
                    $table->timestamp('inserted_at')->nullable()->after('inserted_by');
                }
            });
        }

        // 2. mata_kuliah
        if (Schema::hasTable('mata_kuliah')) {
            Schema::table('mata_kuliah', function (Blueprint $table) {
                if (!Schema::hasColumn('mata_kuliah', 'inserted_by')) {
                    $table->string('inserted_by', 100)->nullable()->after('id');
                }
                if (!Schema::hasColumn('mata_kuliah', 'inserted_at')) {
                    $table->timestamp('inserted_at')->nullable()->after('inserted_by');
                }
            });
        }

        // 3. nilai
        if (Schema::hasTable('nilai')) {
            Schema::table('nilai', function (Blueprint $table) {
                if (!Schema::hasColumn('nilai', 'inserted_by')) {
                    $table->string('inserted_by', 100)->nullable()->after('id');
                }
                if (!Schema::hasColumn('nilai', 'inserted_at')) {
                    $table->timestamp('inserted_at')->nullable()->after('inserted_by');
                }
            });
        }

        // 4. program_studi
        if (Schema::hasTable('program_studi')) {
            Schema::table('program_studi', function (Blueprint $table) {
                if (!Schema::hasColumn('program_studi', 'inserted_by')) {
                    $table->string('inserted_by', 100)->nullable()->after('id');
                }
                if (!Schema::hasColumn('program_studi', 'inserted_at')) {
                    $table->timestamp('inserted_at')->nullable()->after('inserted_by');
                }
            });
        }
    }
};
