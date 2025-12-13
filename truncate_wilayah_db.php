<?php
// Jalankan: php truncate_wilayah_db.php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Bootstrap Laravel app
$app = require_once __DIR__.'/bootstrap/app.php';

// Set DB connection (pakai default Laravel config)
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Nonaktifkan foreign key checks
Capsule::statement('SET FOREIGN_KEY_CHECKS=0;');
Capsule::table('villages')->truncate();
Capsule::table('sub_regencies')->truncate();
Capsule::table('regencies')->truncate();
Capsule::table('provinces')->truncate();
Capsule::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Tabel provinces, regencies, sub_regencies, villages berhasil dikosongkan.\n";
