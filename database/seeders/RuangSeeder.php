<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ruang;
use App\Models\Fakultas;
use App\Models\ProgramStudi;

class RuangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fakultas = Fakultas::first();
        $prodi = ProgramStudi::first();

        $ruangData = [
            // Ruang Universitas
            [
                'kode_ruang' => 'AU-101',
                'nama_ruang' => 'Aula Utama',
                'gedung' => 'Rektorat',
                'lantai' => '1',
                'kapasitas' => 500,
                'jenis_ruang' => 'Aula',
                'tingkat_kepemilikan' => 'Universitas',
                'fakultas_id' => null,
                'program_studi_id' => null,
                'fasilitas' => 'Proyektor, Sound System, AC, Panggung',
                'status' => 'Aktif',
                'keterangan' => 'Aula untuk acara universitas dan wisuda'
            ],
            [
                'kode_ruang' => 'PU-201',
                'nama_ruang' => 'Perpustakaan Pusat',
                'gedung' => 'Perpustakaan',
                'lantai' => '2',
                'kapasitas' => 200,
                'jenis_ruang' => 'Perpustakaan',
                'tingkat_kepemilikan' => 'Universitas',
                'fakultas_id' => null,
                'program_studi_id' => null,
                'fasilitas' => 'Koleksi buku, Komputer, WiFi, AC',
                'status' => 'Aktif',
                'keterangan' => 'Perpustakaan untuk semua mahasiswa'
            ],
            [
                'kode_ruang' => 'RU-301',
                'nama_ruang' => 'Ruang Rapat Senat',
                'gedung' => 'Rektorat',
                'lantai' => '3',
                'kapasitas' => 50,
                'jenis_ruang' => 'Ruang Rapat',
                'tingkat_kepemilikan' => 'Universitas',
                'fakultas_id' => null,
                'program_studi_id' => null,
                'fasilitas' => 'Proyektor, Video Conference, AC, Meja Rapat',
                'status' => 'Aktif',
                'keterangan' => 'Ruang rapat untuk pimpinan universitas'
            ],
        ];

        // Tambahkan ruang fakultas jika ada fakultas
        if ($fakultas) {
            $ruangData = array_merge($ruangData, [
                [
                    'kode_ruang' => 'FK-A101',
                    'nama_ruang' => 'Ruang Kelas A101',
                    'gedung' => 'A',
                    'lantai' => '1',
                    'kapasitas' => 40,
                    'jenis_ruang' => 'Kelas',
                    'tingkat_kepemilikan' => 'Fakultas',
                    'fakultas_id' => $fakultas->id,
                    'program_studi_id' => null,
                    'fasilitas' => 'Proyektor, AC, Whiteboard, Kursi Lipat',
                    'status' => 'Aktif',
                    'keterangan' => 'Kelas untuk semua prodi di fakultas'
                ],
                [
                    'kode_ruang' => 'FK-LAB1',
                    'nama_ruang' => 'Laboratorium Komputer 1',
                    'gedung' => 'A',
                    'lantai' => '2',
                    'kapasitas' => 35,
                    'jenis_ruang' => 'Lab',
                    'tingkat_kepemilikan' => 'Fakultas',
                    'fakultas_id' => $fakultas->id,
                    'program_studi_id' => null,
                    'fasilitas' => '35 unit komputer, Proyektor, AC, LAN',
                    'status' => 'Aktif',
                    'keterangan' => 'Lab komputer untuk praktikum'
                ],
                [
                    'kode_ruang' => 'FK-SEM1',
                    'nama_ruang' => 'Ruang Seminar Fakultas',
                    'gedung' => 'A',
                    'lantai' => '3',
                    'kapasitas' => 80,
                    'jenis_ruang' => 'Ruang Seminar',
                    'tingkat_kepemilikan' => 'Fakultas',
                    'fakultas_id' => $fakultas->id,
                    'program_studi_id' => null,
                    'fasilitas' => 'Proyektor, Sound System, AC, WiFi',
                    'status' => 'Aktif',
                    'keterangan' => 'Untuk seminar dan workshop fakultas'
                ],
            ]);
        }

        // Tambahkan ruang prodi jika ada prodi
        if ($prodi) {
            $ruangData = array_merge($ruangData, [
                [
                    'kode_ruang' => 'PR-101',
                    'nama_ruang' => 'Ruang Kelas Prodi 101',
                    'gedung' => 'B',
                    'lantai' => '1',
                    'kapasitas' => 30,
                    'jenis_ruang' => 'Kelas',
                    'tingkat_kepemilikan' => 'Prodi',
                    'fakultas_id' => $prodi->fakultas_id,
                    'program_studi_id' => $prodi->id,
                    'fasilitas' => 'Proyektor, AC, Whiteboard',
                    'status' => 'Aktif',
                    'keterangan' => 'Kelas khusus untuk prodi ini'
                ],
                [
                    'kode_ruang' => 'PR-LAB',
                    'nama_ruang' => 'Lab Khusus Prodi',
                    'gedung' => 'B',
                    'lantai' => '2',
                    'kapasitas' => 25,
                    'jenis_ruang' => 'Lab',
                    'tingkat_kepemilikan' => 'Prodi',
                    'fakultas_id' => $prodi->fakultas_id,
                    'program_studi_id' => $prodi->id,
                    'fasilitas' => 'Peralatan lab khusus, Komputer, AC',
                    'status' => 'Aktif',
                    'keterangan' => 'Lab praktikum khusus prodi'
                ],
                [
                    'kode_ruang' => 'PR-202',
                    'nama_ruang' => 'Ruang Kelas Prodi 202',
                    'gedung' => 'B',
                    'lantai' => '2',
                    'kapasitas' => 35,
                    'jenis_ruang' => 'Kelas',
                    'tingkat_kepemilikan' => 'Prodi',
                    'fakultas_id' => $prodi->fakultas_id,
                    'program_studi_id' => $prodi->id,
                    'fasilitas' => 'Proyektor, AC, Whiteboard, WiFi',
                    'status' => 'Dalam Perbaikan',
                    'keterangan' => 'Sedang renovasi AC'
                ],
            ]);
        }

        foreach ($ruangData as $data) {
            Ruang::updateOrCreate(
                ['kode_ruang' => $data['kode_ruang']],
                $data
            );
        }

        $this->command->info('Berhasil membuat ' . count($ruangData) . ' data ruang');
    }
}
