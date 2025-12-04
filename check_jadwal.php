<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::select('DESCRIBE jadwal_kuliah');
foreach ($columns as $col) {
    echo sprintf("%-25s %-20s %-5s\n", $col->Field, $col->Type, $col->Null);
}
