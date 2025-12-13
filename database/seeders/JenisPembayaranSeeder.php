<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisPembayaran;

class JenisPembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisPembayaran = [
            [
                'kode' => 'UKT',
                'nama' => 'Uang Kuliah Tunggal',
                'deskripsi' => 'Biaya kuliah per semester berdasarkan kelompok UKT',
                'kategori' => 'Tetap',
                'is_wajib' => true,
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'kode' => 'SPP',
                'nama' => 'Sumbangan Pembinaan Pendidikan',
                'deskripsi' => 'Biaya SPP per semester untuk program non-UKT',
                'kategori' => 'Tetap',
                'is_wajib' => true,
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'kode' => 'DPP',
                'nama' => 'Dana Pengembangan Pendidikan',
                'deskripsi' => 'Biaya pengembangan pendidikan (dibayar sekali saat masuk)',
                'kategori' => 'Insidental',
                'is_wajib' => true,
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'kode' => 'PRAKTIKUM',
                'nama' => 'Biaya Praktikum',
                'deskripsi' => 'Biaya praktikum untuk mata kuliah tertentu',
                'kategori' => 'Variabel',
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 4,
            ],
            [
                'kode' => 'SEMESTER_PENDEK',
                'nama' => 'Semester Pendek',
                'deskripsi' => 'Biaya kuliah semester pendek (summer/winter course)',
                'kategori' => 'Variabel',
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 5,
            ],
            [
                'kode' => 'WISUDA',
                'nama' => 'Biaya Wisuda',
                'deskripsi' => 'Biaya penyelenggaraan wisuda',
                'kategori' => 'Insidental',
                'is_wajib' => true,
                'is_active' => true,
                'urutan' => 6,
            ],
            [
                'kode' => 'CUTI',
                'nama' => 'Biaya Cuti Akademik',
                'deskripsi' => 'Biaya administrasi cuti akademik',
                'kategori' => 'Insidental',
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 7,
            ],
            [
                'kode' => 'UJIAN_SUSULAN',
                'nama' => 'Ujian Susulan',
                'deskripsi' => 'Biaya ujian susulan',
                'kategori' => 'Insidental',
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 8,
            ],
            [
                'kode' => 'KTM',
                'nama' => 'Kartu Tanda Mahasiswa',
                'deskripsi' => 'Biaya pembuatan/perpanjangan KTM',
                'kategori' => 'Insidental',
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 9,
            ],
            [
                'kode' => 'SERTIFIKAT',
                'nama' => 'Sertifikat Kompetensi',
                'deskripsi' => 'Biaya penerbitan sertifikat kompetensi',
                'kategori' => 'Variabel',
                'is_wajib' => false,
                'is_active' => true,
                'urutan' => 10,
            ],
        ];

        foreach ($jenisPembayaran as $jenis) {
            JenisPembayaran::updateOrCreate(
                ['kode' => $jenis['kode']],
                $jenis
            );
        }
    }
}
