<?php
// Ekstrak hanya baris INSERT INTO dari file SQL besar dan simpan ke file baru

$input = 'C:/Users/HP/Downloads/kidico_coa(3).sql';
$output = 'C:/Users/HP/Downloads/kidico_coa_insert_only.sql';

$in = fopen($input, 'r');
$out = fopen($output, 'w');

if (!$in || !$out) {
    echo "Gagal membuka file input/output\n";
    exit(1);
}

while (($line = fgets($in)) !== false) {
    if (preg_match('/^INSERT INTO/i', $line)) {
        fwrite($out, $line);
        // Lanjutkan menulis baris berikutnya jika statement multi-baris
        while (strpos($line, ';') === false && ($next = fgets($in)) !== false) {
            fwrite($out, $next);
            $line = $next;
        }
    }
}

fclose($in);
fclose($out);
echo "File INSERT ONLY berhasil dibuat: $output\n";
