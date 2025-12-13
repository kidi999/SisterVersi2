<?php

use Illuminate\Support\Facades\DB;

// Jalankan di root Laravel: php artisan tinker --execute="require 'truncate_wilayah.php';"

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('villages')->truncate();
DB::table('sub_regencies')->truncate();
DB::table('regencies')->truncate();
DB::table('provinces')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Tabel provinces, regencies, sub_regencies, villages berhasil dikosongkan.\n";
