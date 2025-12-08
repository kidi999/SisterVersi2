<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        $tables = [
            'kegiatan_rkt',
            'pencapaian_rkt',
            'program_rkt',
            'rencana_kerja_tahunan',
        ];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $t) use ($tableName) {
                if (Schema::hasColumn($tableName, 'inserted_by')) {
                    $t->renameColumn('inserted_by', 'created_by');
                }
                if (Schema::hasColumn($tableName, 'inserted_at')) {
                    $t->renameColumn('inserted_at', 'created_at');
                }
            });
        }
    }
    public function down() {
        $tables = [
            'kegiatan_rkt',
            'pencapaian_rkt',
            'program_rkt',
            'rencana_kerja_tahunan',
        ];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $t) use ($tableName) {
                if (Schema::hasColumn($tableName, 'created_by')) {
                    $t->renameColumn('created_by', 'inserted_by');
                }
                if (Schema::hasColumn($tableName, 'created_at')) {
                    $t->renameColumn('created_at', 'inserted_at');
                }
            });
        }
    }
};
