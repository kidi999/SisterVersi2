<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = [
    'rencana_kerja_tahunan',
    'program_rkt',
    'kegiatan_rkt',
    'pencapaian_rkt',
    'universities',
    'fakultas',
    'program_studi'
];

foreach ($tables as $table) {
    echo "\n=== {$table} ===\n";
    try {
        $columns = DB::select("SHOW COLUMNS FROM {$table}");
        foreach ($columns as $col) {
            $nullable = $col->Null == 'YES' ? 'NULL' : 'NOT NULL';
            echo "{$col->Field} ({$col->Type}) {$nullable}\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
