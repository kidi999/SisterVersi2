<?php
// import_wilayah.php
// Jalankan: php import_wilayah.php

use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/vendor/autoload.php';

// Konfigurasi DB manual (karena ini script mandiri, bukan artisan)
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'sister_db',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$sql = file_get_contents('C:/Users/HP/Downloads/kidico_coa(3).sql');

try {
    Capsule::connection()->unprepared($sql);
    echo "Import wilayah SQL selesai!\n";
} catch (Exception $e) {
    echo "Gagal import: " . $e->getMessage() . "\n";
}
