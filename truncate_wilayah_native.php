<?php
// Jalankan: php truncate_wilayah_native.php
// Skrip ini langsung konek ke database pakai mysqli, tanpa Laravel

$host = 'localhost';
$user = 'root'; // ganti jika user DB Anda berbeda
$pass = '';
$db = 'sister_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error . "\n");
}

$conn->query('SET FOREIGN_KEY_CHECKS=0;');
$conn->query('TRUNCATE TABLE villages;');
$conn->query('TRUNCATE TABLE sub_regencies;');
$conn->query('TRUNCATE TABLE regencies;');
$conn->query('TRUNCATE TABLE provinces;');
$conn->query('SET FOREIGN_KEY_CHECKS=1;');

$conn->close();
echo "Tabel provinces, regencies, sub_regencies, villages berhasil dikosongkan.\n";
