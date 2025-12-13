<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$ignore = [
    'migrations',
    'failed_jobs',
    'password_reset_tokens',
    'sessions',
    'cache',
    'cache_locks',
    'jobs',
    'job_batches',
    'personal_access_tokens',
];

$dbName = DB::connection()->getDatabaseName();
$tablesRaw = DB::select('SHOW TABLES');

$tables = [];
foreach ($tablesRaw as $row) {
    $rowArr = (array) $row;
    $tables[] = array_values($rowArr)[0];
}

$empty = [];
foreach ($tables as $table) {
    if (in_array($table, $ignore, true)) {
        continue;
    }

    try {
        $count = DB::table($table)->count();
    } catch (Throwable $e) {
        continue;
    }

    if ($count === 0) {
        $empty[] = $table;
    }
}

sort($empty);

echo "Database: {$dbName}\n";
echo 'EMPTY_TABLES=' . count($empty) . "\n\n";

foreach ($empty as $table) {
    echo $table . "\n";
}
