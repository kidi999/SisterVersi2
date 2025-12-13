<?php

namespace Database\Seeders;

use App\Models\AbsensiMahasiswa;
use App\Models\AkreditasiFakultas;
use App\Models\AkreditasiProgramStudi;
use App\Models\AkreditasiUniversitas;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\FileUpload;
use App\Models\JabatanStruktural;
use App\Models\JadwalKuliah;
use App\Models\JenisPembayaran;
use App\Models\KegiatanRkt;
use App\Models\Kelas;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use App\Models\PendaftaranMahasiswa;
use App\Models\PembayaranMahasiswa;
use App\Models\PencapaianRkt;
use App\Models\PertemuanKuliah;
use App\Models\ProgramRkt;
use App\Models\ProgramStudi;
use App\Models\RencanaKerjaTahunan;
use App\Models\Role;
use App\Models\Semester;
use App\Models\TagihanMahasiswa;
use App\Models\TahunAkademik;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DummyDataSeeder extends Seeder
{
    /** @var array<string, string[]> */
    private array $columnsCache = [];

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedCoreRoles();

            [$fakultas, $prodi] = $this->seedFakultasAndProdi();
            [$dosen, $mahasiswa] = $this->seedDosenAndMahasiswa($fakultas, $prodi);
            $users = $this->seedUsers($fakultas, $prodi, $dosen, $mahasiswa);

            [$tahunAkademik, $semester] = $this->seedTahunAkademikAndSemester($prodi);
            [, , $jadwal, $krs] = $this->seedAkademikFlow($fakultas, $prodi, $dosen, $mahasiswa, $tahunAkademik, $semester, $users['admin_universitas'] ?? null);

            $this->seedKeuangan($mahasiswa, $tahunAkademik, $semester, $users['admin_universitas'] ?? null);
            $this->seedPertemuanDanAbsensi($jadwal, $tahunAkademik, $semester, $krs, $mahasiswa, $users['dosen'] ?? null);
            $this->seedPmb($prodi, $users['admin_universitas'] ?? null);

            $this->seedAkreditasi($fakultas, $prodi, $users['admin_universitas'] ?? null);
            $this->seedRkt($fakultas, $prodi, $users['admin_universitas'] ?? null, $users['dosen'] ?? null);

            $this->seedJabatanStruktural($dosen, $fakultas, $prodi);
        });
    }

    private function seedAkreditasi(Fakultas $fakultas, ProgramStudi $prodi, ?User $actor): void
    {
        if (!Schema::hasTable('akreditasi_universitas') && !Schema::hasTable('akreditasi_fakultas') && !Schema::hasTable('akreditasi_program_studi')) {
            return;
        }

        $university = University::query()->first();
        if (!$university) {
            $villageId = $this->firstVillageId();
            $university = University::withTrashed()->updateOrCreate(
                ['kode' => 'UNIVDUMMY'],
                $this->onlyExistingColumns('universities', [
                    'nama' => 'Universitas Dummy',
                    'singkatan' => 'UD',
                    'jenis' => 'Swasta',
                    'status' => 'Aktif',
                    'akreditasi' => 'A',
                    'rektor' => 'Rektor Dummy',
                    'email' => 'univ@sister.test',
                    'telepon' => '021-00000000',
                    'alamat' => 'Jl. Kampus Dummy',
                    'village_id' => $villageId,
                    'created_by' => $actor?->id,
                ])
            );
            if (method_exists($university, 'restore') && $university->trashed()) {
                $university->restore();
            }
        }

        $tahun = (int) now()->format('Y');
        $tanggalSk = now()->subYears(1)->toDateString();
        $tanggalBerakhir = now()->addYears(4)->toDateString();

        if (Schema::hasTable('akreditasi_universitas')) {
            $akrUniv = AkreditasiUniversitas::withTrashed()->updateOrCreate(
                [
                    'university_id' => $university->id,
                    'nomor_sk' => "DUMMY/SK/AKR/UNIV/{$tahun}",
                ],
                $this->onlyExistingColumns('akreditasi_universitas', [
                    'university_id' => $university->id,
                    'lembaga_akreditasi' => 'BAN-PT',
                    'nomor_sk' => "DUMMY/SK/AKR/UNIV/{$tahun}",
                    'tanggal_sk' => $tanggalSk,
                    'tanggal_berakhir' => $tanggalBerakhir,
                    'peringkat' => 'Unggul',
                    'tahun_akreditasi' => $tahun,
                    'catatan' => 'Dummy akreditasi universitas',
                    'status' => 'Aktif',
                    'created_by' => $actor?->id,
                    'updated_by' => $actor?->id,
                ])
            );
            if (method_exists($akrUniv, 'restore') && $akrUniv->trashed()) {
                $akrUniv->restore();
            }

            $this->seedFileUploadForModel(
                AkreditasiUniversitas::class,
                $akrUniv->id,
                'dummy-akreditasi-universitas.pdf',
                'akreditasi/dummy-akreditasi-universitas.pdf',
                'application/pdf',
                'Dokumen akreditasi universitas (dummy)'
            );
        }

        if (Schema::hasTable('akreditasi_fakultas')) {
            $akrFak = AkreditasiFakultas::withTrashed()->updateOrCreate(
                [
                    'fakultas_id' => $fakultas->id,
                    'nomor_sk' => "DUMMY/SK/AKR/FAK/{$tahun}",
                ],
                $this->onlyExistingColumns('akreditasi_fakultas', [
                    'fakultas_id' => $fakultas->id,
                    'lembaga_akreditasi' => 'BAN-PT',
                    'nomor_sk' => "DUMMY/SK/AKR/FAK/{$tahun}",
                    'tanggal_sk' => $tanggalSk,
                    'tanggal_berakhir' => $tanggalBerakhir,
                    'peringkat' => 'A',
                    'tahun_akreditasi' => $tahun,
                    'catatan' => 'Dummy akreditasi fakultas',
                    'status' => 'Aktif',
                    'created_by' => $actor?->id,
                    'updated_by' => $actor?->id,
                ])
            );
            if (method_exists($akrFak, 'restore') && $akrFak->trashed()) {
                $akrFak->restore();
            }

            $this->seedFileUploadForModel(
                AkreditasiFakultas::class,
                $akrFak->id,
                'dummy-akreditasi-fakultas.pdf',
                'akreditasi/dummy-akreditasi-fakultas.pdf',
                'application/pdf',
                'Dokumen akreditasi fakultas (dummy)'
            );
        }

        if (Schema::hasTable('akreditasi_program_studi')) {
            $akrProdi = AkreditasiProgramStudi::withTrashed()->updateOrCreate(
                [
                    'program_studi_id' => $prodi->id,
                    'nomor_sk' => "DUMMY/SK/AKR/PRODI/{$tahun}",
                ],
                $this->onlyExistingColumns('akreditasi_program_studi', [
                    'program_studi_id' => $prodi->id,
                    'lembaga_akreditasi' => 'BAN-PT',
                    'nomor_sk' => "DUMMY/SK/AKR/PRODI/{$tahun}",
                    'tanggal_sk' => $tanggalSk,
                    'tanggal_berakhir' => $tanggalBerakhir,
                    'peringkat' => 'A',
                    'tahun_akreditasi' => $tahun,
                    'catatan' => 'Dummy akreditasi program studi',
                    'status' => 'Aktif',
                    'created_by' => $actor?->id,
                    'updated_by' => $actor?->id,
                ])
            );
            if (method_exists($akrProdi, 'restore') && $akrProdi->trashed()) {
                $akrProdi->restore();
            }

            $this->seedFileUploadForModel(
                AkreditasiProgramStudi::class,
                $akrProdi->id,
                'dummy-akreditasi-prodi.pdf',
                'akreditasi/dummy-akreditasi-prodi.pdf',
                'application/pdf',
                'Dokumen akreditasi program studi (dummy)'
            );
        }
    }

    private function seedJabatanStruktural(Dosen $dosen, Fakultas $fakultas, ProgramStudi $prodi): void
    {
        if (!Schema::hasTable('jabatan_struktural')) {
            return;
        }

        $tahun = (int) now()->format('Y');
        $nomorSk = "SK/DUMMY/DEKAN/{$tahun}";
        $fileSkPath = "sk/{$nomorSk}.pdf";

        $this->ensureDummyPublicPdf($fileSkPath, "SK Dekan Dummy {$tahun}");

        $jabatan = JabatanStruktural::withTrashed()->updateOrCreate(
            [
                'dosen_id' => $dosen->id,
                'jenis_jabatan' => 'dekan',
                'fakultas_id' => $fakultas->id,
            ],
            $this->onlyExistingColumns('jabatan_struktural', [
                'dosen_id' => $dosen->id,
                'fakultas_id' => $fakultas->id,
                'program_studi_id' => null,
                'jenis_jabatan' => 'dekan',
                'nama_jabatan' => 'Dekan Fakultas Teknik (Dummy)',
                'nomor_sk' => $nomorSk,
                'tanggal_sk' => now()->subMonths(6)->toDateString(),
                'tanggal_mulai' => now()->subMonths(3)->toDateString(),
                'tanggal_selesai' => now()->addYears(2)->toDateString(),
                'status' => 'aktif',
                'keterangan' => 'Dummy jabatan struktural untuk simulasi',
                'file_sk_path' => $fileSkPath,
                'created_by' => 'DummyDataSeeder',
                'created_at' => now(),
                'updated_by' => 'DummyDataSeeder',
                'updated_at' => now(),
            ])
        );
        if (method_exists($jabatan, 'restore') && $jabatan->trashed()) {
            $jabatan->restore();
        }

        $this->seedFileUploadForModel(
            JabatanStruktural::class,
            $jabatan->id,
            basename($fileSkPath),
            $fileSkPath,
            'application/pdf',
            'File SK jabatan (dummy)'
        );

        // Minimal role ketua prodi
        $nomorSkProdi = "SK/DUMMY/KAPRODI/{$tahun}";
        $fileSkPathProdi = "sk/{$nomorSkProdi}.pdf";
        $this->ensureDummyPublicPdf($fileSkPathProdi, "SK Kaprodi Dummy {$tahun}");

        $jabatanProdi = JabatanStruktural::withTrashed()->updateOrCreate(
            [
                'dosen_id' => $dosen->id,
                'jenis_jabatan' => 'ketua_prodi',
                'program_studi_id' => $prodi->id,
            ],
            $this->onlyExistingColumns('jabatan_struktural', [
                'dosen_id' => $dosen->id,
                'fakultas_id' => $fakultas->id,
                'program_studi_id' => $prodi->id,
                'jenis_jabatan' => 'ketua_prodi',
                'nama_jabatan' => 'Ketua Program Studi TI (Dummy)',
                'nomor_sk' => $nomorSkProdi,
                'tanggal_sk' => now()->subMonths(6)->toDateString(),
                'tanggal_mulai' => now()->subMonths(2)->toDateString(),
                'tanggal_selesai' => now()->addYears(2)->toDateString(),
                'status' => 'aktif',
                'keterangan' => 'Dummy jabatan struktural prodi untuk simulasi',
                'file_sk_path' => $fileSkPathProdi,
                'created_by' => 'DummyDataSeeder',
                'created_at' => now(),
                'updated_by' => 'DummyDataSeeder',
                'updated_at' => now(),
            ])
        );
        if (method_exists($jabatanProdi, 'restore') && $jabatanProdi->trashed()) {
            $jabatanProdi->restore();
        }

        $this->seedFileUploadForModel(
            JabatanStruktural::class,
            $jabatanProdi->id,
            basename($fileSkPathProdi),
            $fileSkPathProdi,
            'application/pdf',
            'File SK jabatan kaprodi (dummy)'
        );
    }

    private function seedFileUploadForModel(
        string $fileableType,
        int $fileableId,
        string $fileName,
        string $filePath,
        ?string $mimeType,
        ?string $description
    ): void {
        if (!Schema::hasTable('file_uploads')) {
            return;
        }

        // Ensure the dummy file exists so downloads don't 404.
        if ($mimeType === 'application/pdf') {
            $this->ensureDummyPublicPdf($filePath, $fileName);
        } else {
            $this->ensureDummyPublicFile($filePath, "Dummy file: {$fileName}\n");
        }

        $size = Storage::disk('public')->exists($filePath)
            ? (int) Storage::disk('public')->size($filePath)
            : 1;

        FileUpload::withTrashed()->updateOrCreate(
            [
                'fileable_type' => $fileableType,
                'fileable_id' => $fileableId,
                'file_path' => $filePath,
            ],
            $this->onlyExistingColumns('file_uploads', [
                'fileable_type' => $fileableType,
                'fileable_id' => $fileableId,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $mimeType,
                'file_size' => $size,
                'description' => $description,
                'category' => 'document',
                'order' => 0,
                'created_by' => 'DummyDataSeeder',
                'created_at' => now(),
                'updated_by' => 'DummyDataSeeder',
                'updated_at' => now(),
            ])
        );
    }

    private function ensureDummyPublicFile(string $relativePath, string $contents): void
    {
        if (!Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->put($relativePath, $contents);
        }
    }

    private function ensureDummyPublicPdf(string $relativePath, string $title): void
    {
        if (Storage::disk('public')->exists($relativePath)) {
            return;
        }

        // Minimal, not feature-complete PDF, but enough to be served and downloaded.
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n4 0 obj\n<< /Length 62 >>\nstream\nBT\n/F1 18 Tf\n72 720 Td\n({$title}) Tj\nET\nendstream\nendobj\n5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\nxref\n0 6\n0000000000 65535 f \n0000000015 00000 n \n0000000064 00000 n \n0000000121 00000 n \n0000000278 00000 n \n0000000391 00000 n \ntrailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n470\n%%EOF\n";

        Storage::disk('public')->put($relativePath, $pdf);
    }

    private function seedRkt(Fakultas $fakultas, ProgramStudi $prodi, ?User $approver, ?User $reporter): void
    {
        if (!Schema::hasTable('rencana_kerja_tahunan')) {
            return;
        }

        $universityId = Schema::hasTable('universities') ? (int) (DB::table('universities')->value('id') ?? 0) : 0;
        $universityId = $universityId > 0 ? $universityId : null;

        $tahun = (int) now()->format('Y');
        $kodeRkt = "RKTU/{$tahun}/0001";

        $rkt = RencanaKerjaTahunan::withTrashed()->updateOrCreate(
            ['kode_rkt' => $kodeRkt],
            $this->onlyExistingColumns('rencana_kerja_tahunan', [
                'kode_rkt' => $kodeRkt,
                'judul_rkt' => "RKT Universitas {$tahun} (Dummy)",
                'deskripsi' => 'Dummy rencana kerja tahunan',
                'tahun' => $tahun,
                'level' => 'Universitas',
                'university_id' => $universityId,
                'fakultas_id' => null,
                'program_studi_id' => null,
                'tanggal_mulai' => now()->startOfYear()->toDateString(),
                'tanggal_selesai' => now()->endOfYear()->toDateString(),
                'anggaran' => 100000000,
                'status' => RencanaKerjaTahunan::STATUS_DISETUJUI,
                'disetujui_oleh' => $approver?->id,
                'tanggal_disetujui' => now(),
                'created_by' => $approver?->id,
                'created_at' => now(),
                'updated_by' => $approver?->id,
                'updated_at' => now(),
            ])
        );
        if (method_exists($rkt, 'restore') && $rkt->trashed()) {
            $rkt->restore();
        }

        if (Schema::hasTable('program_rkt')) {
            $program = ProgramRkt::withTrashed()->updateOrCreate(
                [
                    'rencana_kerja_tahunan_id' => $rkt->id,
                    'kode_program' => "PRG/{$tahun}/001",
                ],
                $this->onlyExistingColumns('program_rkt', [
                    'rencana_kerja_tahunan_id' => $rkt->id,
                    'kode_program' => "PRG/{$tahun}/001",
                    'nama_program' => 'Peningkatan Mutu Akademik (Dummy)',
                    'deskripsi' => 'Dummy program RKT',
                    'kategori' => 'Akademik',
                    'anggaran' => 50000000,
                    'target_mulai' => now()->startOfYear()->addMonths(0)->toDateString(),
                    'target_selesai' => now()->startOfYear()->addMonths(6)->toDateString(),
                    'penanggung_jawab' => $reporter?->name,
                    'indikator_kinerja' => 'Minimal 1 kegiatan terlaksana',
                    'urutan' => 1,
                    'created_by' => $approver?->id,
                    'created_at' => now(),
                    'updated_by' => $approver?->id,
                    'updated_at' => now(),
                ])
            );
            if (method_exists($program, 'restore') && $program->trashed()) {
                $program->restore();
            }

            if (Schema::hasTable('kegiatan_rkt')) {
                $kegiatan = KegiatanRkt::withTrashed()->updateOrCreate(
                    [
                        'program_rkt_id' => $program->id,
                        'kode_kegiatan' => "KGT/{$tahun}/001",
                    ],
                    $this->onlyExistingColumns('kegiatan_rkt', [
                        'program_rkt_id' => $program->id,
                        'kode_kegiatan' => "KGT/{$tahun}/001",
                        'nama_kegiatan' => 'Workshop Kurikulum (Dummy)',
                        'deskripsi' => 'Dummy kegiatan RKT',
                        'anggaran' => 10000000,
                        'tanggal_mulai' => now()->startOfYear()->addMonths(1)->toDateString(),
                        'tanggal_selesai' => now()->startOfYear()->addMonths(1)->addDays(2)->toDateString(),
                        'status' => 'Selesai',
                        'urutan' => 1,
                        'created_by' => $approver?->id,
                        'created_at' => now(),
                        'updated_by' => $approver?->id,
                        'updated_at' => now(),
                    ])
                );
                if (method_exists($kegiatan, 'restore') && $kegiatan->trashed()) {
                    $kegiatan->restore();
                }

                if (Schema::hasTable('pencapaian_rkt')) {
                    $pencapaian = PencapaianRkt::withTrashed()->updateOrCreate(
                        [
                            'kegiatan_rkt_id' => $kegiatan->id,
                            'periode' => 'Semester 1',
                            'tanggal_laporan' => now()->startOfYear()->addMonths(2)->toDateString(),
                        ],
                        $this->onlyExistingColumns('pencapaian_rkt', [
                            'kegiatan_rkt_id' => $kegiatan->id,
                            'periode' => 'Semester 1',
                            'tanggal_laporan' => now()->startOfYear()->addMonths(2)->toDateString(),
                            'capaian' => 'Kegiatan terlaksana sesuai rencana (Dummy)',
                            'persentase_capaian' => 100.00,
                            'realisasi_anggaran' => 9500000,
                            'kendala' => null,
                            'solusi' => null,
                            'rencana_tindak_lanjut' => 'Evaluasi dan perbaikan berkelanjutan',
                            'file_dokumentasi' => null,
                            'dilaporkan_oleh' => $reporter?->id,
                            'diverifikasi_oleh' => $approver?->id,
                            'tanggal_verifikasi' => now(),
                            'status_verifikasi' => 'Diverifikasi',
                            'catatan_verifikasi' => 'Dummy verifikasi',
                            'created_by' => $reporter?->id,
                            'created_at' => now(),
                            'updated_by' => $approver?->id,
                            'updated_at' => now(),
                        ])
                    );
                    if (method_exists($pencapaian, 'restore') && $pencapaian->trashed()) {
                        $pencapaian->restore();
                    }
                }
            }
        }

        // Seed minimal RKT level Fakultas
        if (Schema::hasTable('rencana_kerja_tahunan')) {
            $kodeRktFak = "RKTF/{$tahun}/0001";
            RencanaKerjaTahunan::withTrashed()->updateOrCreate(
                ['kode_rkt' => $kodeRktFak],
                $this->onlyExistingColumns('rencana_kerja_tahunan', [
                    'kode_rkt' => $kodeRktFak,
                    'judul_rkt' => "RKT Fakultas {$tahun} (Dummy)",
                    'deskripsi' => 'Dummy rencana kerja tahunan level fakultas',
                    'tahun' => $tahun,
                    'level' => 'Fakultas',
                    'university_id' => $universityId,
                    'fakultas_id' => $fakultas->id,
                    'program_studi_id' => null,
                    'tanggal_mulai' => now()->startOfYear()->toDateString(),
                    'tanggal_selesai' => now()->endOfYear()->toDateString(),
                    'anggaran' => 25000000,
                    'status' => RencanaKerjaTahunan::STATUS_DRAFT,
                    'created_by' => $approver?->id,
                    'created_at' => now(),
                    'updated_by' => $approver?->id,
                    'updated_at' => now(),
                ])
            );

            $kodeRktProdi = "RKTP/{$tahun}/0001";
            RencanaKerjaTahunan::withTrashed()->updateOrCreate(
                ['kode_rkt' => $kodeRktProdi],
                $this->onlyExistingColumns('rencana_kerja_tahunan', [
                    'kode_rkt' => $kodeRktProdi,
                    'judul_rkt' => "RKT Prodi {$tahun} (Dummy)",
                    'deskripsi' => 'Dummy rencana kerja tahunan level prodi',
                    'tahun' => $tahun,
                    'level' => 'Prodi',
                    'university_id' => $universityId,
                    'fakultas_id' => $fakultas->id,
                    'program_studi_id' => $prodi->id,
                    'tanggal_mulai' => now()->startOfYear()->toDateString(),
                    'tanggal_selesai' => now()->endOfYear()->toDateString(),
                    'anggaran' => 15000000,
                    'status' => RencanaKerjaTahunan::STATUS_DRAFT,
                    'created_by' => $approver?->id,
                    'created_at' => now(),
                    'updated_by' => $approver?->id,
                    'updated_at' => now(),
                ])
            );
        }
    }

    private function seedCoreRoles(): void
    {
        $roles = [
            [Role::SUPER_ADMIN, 'Super Admin', 'Akses penuh ke seluruh sistem'],
            [Role::ADMIN_UNIVERSITAS, 'Admin Universitas', 'Administrator tingkat universitas'],
            [Role::ADMIN_FAKULTAS, 'Admin Fakultas', 'Administrator tingkat fakultas'],
            [Role::ADMIN_PRODI, 'Admin Program Studi', 'Administrator tingkat program studi'],
            [Role::DOSEN, 'Dosen', 'Dosen pengajar'],
            [Role::MAHASISWA, 'Mahasiswa', 'Mahasiswa aktif'],
        ];

        foreach ($roles as [$name, $displayName, $description]) {
            Role::query()->updateOrCreate(
                ['name' => $name],
                [
                    'display_name' => $displayName,
                    'description' => $description,
                    'created_by' => 'DummyDataSeeder',
                ]
            );
        }
    }

    /**
     * @return array{0: Fakultas, 1: ProgramStudi}
     */
    private function seedFakultasAndProdi(): array
    {
        $villageId = $this->firstVillageId();

        $fakultas = Fakultas::withTrashed()->updateOrCreate(
            ['kode_fakultas' => 'FT'],
            $this->onlyExistingColumns('fakultas', [
                'nama_fakultas' => 'Fakultas Teknik',
                'singkatan' => 'FT',
                'dekan' => 'Prof. Dummy, M.T.',
                'alamat' => 'Jl. Kampus No. 1',
                'telepon' => '021-12345678',
                'email' => 'ft@sister.test',
                'village_id' => $villageId,
            ])
        );
        if (method_exists($fakultas, 'restore') && $fakultas->trashed()) {
            $fakultas->restore();
        }

        $prodi = ProgramStudi::withTrashed()->updateOrCreate(
            ['kode_prodi' => 'TI'],
            $this->onlyExistingColumns('program_studi', [
                'fakultas_id' => $fakultas->id,
                'nama_prodi' => 'Teknik Informatika',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Dummy, M.Kom',
                'akreditasi' => 'A',
            ])
        );
        if (method_exists($prodi, 'restore') && $prodi->trashed()) {
            $prodi->restore();
        }

        return [$fakultas, $prodi];
    }

    /**
     * @return array{0: Dosen, 1: Mahasiswa}
     */
    private function seedDosenAndMahasiswa(Fakultas $fakultas, ProgramStudi $prodi): array
    {
        $villageId = $this->firstVillageId();

        $dosen = Dosen::withTrashed()->updateOrCreate(
            ['nip' => '198001012005011001'],
            $this->onlyExistingColumns('dosen', [
                'level_dosen' => 'prodi',
                'fakultas_id' => $fakultas->id,
                'program_studi_id' => $prodi->id,
                'nidn' => '0000000001',
                'nama_dosen' => 'Dosen Dummy',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => now()->subYears(40)->toDateString(),
                'alamat' => 'Jl. Dummy No. 1',
                'village_id' => $villageId,
                'telepon' => '081234567890',
                'email' => 'dosen@sister.test',
                'pendidikan_terakhir' => 'S2',
                'jabatan_akademik' => 'Lektor',
                'status' => 'Aktif',
            ])
        );
        if (method_exists($dosen, 'restore') && $dosen->trashed()) {
            $dosen->restore();
        }

        $mahasiswa = Mahasiswa::withTrashed()->updateOrCreate(
            ['nim' => '2025000001'],
            $this->onlyExistingColumns('mahasiswa', [
                'program_studi_id' => $prodi->id,
                'nama_mahasiswa' => 'Mahasiswa Dummy',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => now()->subYears(19)->toDateString(),
                'alamat' => 'Jl. Dummy No. 2',
                'village_id' => $villageId,
                'telepon' => '081298765432',
                'email' => 'mahasiswa@sister.test',
                'tahun_masuk' => '2025',
                'semester' => 1,
                'ipk' => 3.50,
                'status' => 'Aktif',
                'nama_wali' => 'Wali Dummy',
                'telepon_wali' => '081200000000',
            ])
        );
        if (method_exists($mahasiswa, 'restore') && $mahasiswa->trashed()) {
            $mahasiswa->restore();
        }

        return [$dosen, $mahasiswa];
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(Fakultas $fakultas, ProgramStudi $prodi, Dosen $dosen, Mahasiswa $mahasiswa): array
    {
        $roleIds = Role::query()->pluck('id', 'name');

        return [
            'super_admin' => $this->upsertUser([
                'email' => 'superadmin@sister.test',
                'name' => 'Super Admin',
                'role_id' => $roleIds[Role::SUPER_ADMIN] ?? null,
            ]),
            'admin_universitas' => $this->upsertUser([
                'email' => 'admin.univ@sister.test',
                'name' => 'Admin Universitas',
                'role_id' => $roleIds[Role::ADMIN_UNIVERSITAS] ?? null,
            ]),
            'admin_fakultas' => $this->upsertUser([
                'email' => 'admin.fak@sister.test',
                'name' => 'Admin Fakultas',
                'role_id' => $roleIds[Role::ADMIN_FAKULTAS] ?? null,
                'fakultas_id' => $fakultas->id,
            ]),
            'admin_prodi' => $this->upsertUser([
                'email' => 'admin.prodi@sister.test',
                'name' => 'Admin Prodi',
                'role_id' => $roleIds[Role::ADMIN_PRODI] ?? null,
                'fakultas_id' => $fakultas->id,
                'program_studi_id' => $prodi->id,
            ]),
            'dosen' => $this->upsertUser([
                'email' => 'dosen@sister.test',
                'name' => 'Dosen Dummy',
                'role_id' => $roleIds[Role::DOSEN] ?? null,
                'fakultas_id' => $fakultas->id,
                'program_studi_id' => $prodi->id,
                'dosen_id' => $dosen->id,
            ]),
            'mahasiswa' => $this->upsertUser([
                'email' => 'mahasiswa@sister.test',
                'name' => 'Mahasiswa Dummy',
                'role_id' => $roleIds[Role::MAHASISWA] ?? null,
                'fakultas_id' => $fakultas->id,
                'program_studi_id' => $prodi->id,
                'mahasiswa_id' => $mahasiswa->id,
            ]),
        ];
    }

    /**
     * @return array{0: TahunAkademik, 1: Semester}
     */
    private function seedTahunAkademikAndSemester(ProgramStudi $prodi): array
    {
        $today = now()->startOfDay();
        $tahunMulai = (int) $today->format('Y');
        $tahunSelesai = $tahunMulai + 1;
        $kode = $tahunMulai . '/' . $tahunSelesai;

        $tahunAkademik = TahunAkademik::withTrashed()->updateOrCreate(
            ['kode' => $kode],
            $this->onlyExistingColumns('tahun_akademiks', [
                'nama' => "Tahun Akademik {$kode}",
                'tahun_mulai' => $tahunMulai,
                'tahun_selesai' => $tahunSelesai,
                'tanggal_mulai' => $today->copy()->month(8)->day(1)->toDateString(),
                'tanggal_selesai' => $today->copy()->addYear()->month(7)->day(31)->toDateString(),
                'is_active' => true,
                'keterangan' => 'Dummy data',
                'created_by' => 'DummyDataSeeder',
            ])
        );
        if (method_exists($tahunAkademik, 'restore') && $tahunAkademik->trashed()) {
            $tahunAkademik->restore();
        }

        if (Schema::hasColumn('semesters', 'is_active')) {
            Semester::query()->where('tahun_akademik_id', $tahunAkademik->id)->update(['is_active' => false]);
        }

        $semester = Semester::withTrashed()->updateOrCreate(
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'program_studi_id' => $prodi->id,
                'nama_semester' => Semester::SEMESTER_GANJIL,
            ],
            $this->onlyExistingColumns('semesters', [
                'nomor_semester' => 1,
                'tanggal_mulai' => $today->copy()->month(8)->day(1)->toDateString(),
                'tanggal_selesai' => $today->copy()->month(12)->day(31)->toDateString(),
                'tanggal_mulai_perkuliahan' => $today->copy()->month(8)->day(15)->toDateString(),
                'tanggal_selesai_perkuliahan' => $today->copy()->month(12)->day(15)->toDateString(),
                'is_active' => true,
                'keterangan' => 'Dummy semester aktif',
                'created_by' => 'DummyDataSeeder',
            ])
        );
        if (method_exists($semester, 'restore') && $semester->trashed()) {
            $semester->restore();
        }

        return [$tahunAkademik, $semester];
    }

    /**
     * @return array{0: MataKuliah, 1: Kelas, 2: JadwalKuliah, 3: Krs}
     */
    private function seedAkademikFlow(
        Fakultas $fakultas,
        ProgramStudi $prodi,
        Dosen $dosen,
        Mahasiswa $mahasiswa,
        TahunAkademik $tahunAkademik,
        Semester $semester,
        ?User $actor
    ): array {
        $mataKuliah = MataKuliah::withTrashed()->updateOrCreate(
            ['kode_mk' => 'TI101'],
            $this->onlyExistingColumns('mata_kuliah', [
                'level_matkul' => 'prodi',
                'fakultas_id' => $fakultas->id,
                'program_studi_id' => $prodi->id,
                'nama_mk' => 'Pemrograman Dasar',
                'sks' => 3,
                'semester' => 1,
                'jenis' => 'Wajib',
                'deskripsi' => 'Dummy mata kuliah',
                'created_by' => $actor?->id,
            ])
        );
        if (method_exists($mataKuliah, 'restore') && $mataKuliah->trashed()) {
            $mataKuliah->restore();
        }

        $kelas = Kelas::withTrashed()->updateOrCreate(
            [
                'mata_kuliah_id' => $mataKuliah->id,
                'dosen_id' => $dosen->id,
                'kode_kelas' => 'A',
                'tahun_ajaran' => $tahunAkademik->kode,
                'semester' => Semester::SEMESTER_GANJIL,
            ],
            $this->onlyExistingColumns('kelas', [
                'nama_kelas' => 'Kelas A',
                'kapasitas' => 40,
                'terisi' => 1,
                'created_by' => $actor?->id,
            ])
        );
        if (method_exists($kelas, 'restore') && $kelas->trashed()) {
            $kelas->restore();
        }

        $ruangId = Schema::hasTable('ruang') ? DB::table('ruang')->value('id') : null;

        $jadwal = JadwalKuliah::withTrashed()->updateOrCreate(
            ['kelas_id' => $kelas->id],
            $this->onlyExistingColumns('jadwal_kuliah', [
                'kelas_id' => $kelas->id,
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:40',
                'ruang_id' => $ruangId,
                'tahun_akademik_id' => $tahunAkademik->id,
                'semester_id' => $semester->id,
                'created_by' => $actor?->id,
            ])
        );
        if (method_exists($jadwal, 'restore') && $jadwal->trashed()) {
            $jadwal->restore();
        }

        $krs = Krs::withTrashed()->updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswa->id,
                'kelas_id' => $kelas->id,
                'tahun_ajaran' => $tahunAkademik->kode,
                'semester' => Semester::SEMESTER_GANJIL,
            ],
            $this->onlyExistingColumns('krs', [
                'status' => 'Disetujui',
                'tanggal_pengajuan' => now(),
                'tanggal_persetujuan' => now(),
                'created_by' => $actor?->id,
            ])
        );
        if (method_exists($krs, 'restore') && $krs->trashed()) {
            $krs->restore();
        }

        $nilai = Nilai::withTrashed()->updateOrCreate(
            ['krs_id' => $krs->id],
            $this->onlyExistingColumns('nilai', [
                'nilai_tugas' => 85,
                'nilai_uts' => 80,
                'nilai_uas' => 90,
                'nilai_akhir' => 86,
                'nilai_huruf' => 'A',
                'bobot' => 4.00,
                'created_by' => $actor?->id,
            ])
        );
        if (method_exists($nilai, 'restore') && $nilai->trashed()) {
            $nilai->restore();
        }

        return [$mataKuliah, $kelas, $jadwal, $krs];
    }

    private function seedKeuangan(Mahasiswa $mahasiswa, TahunAkademik $tahunAkademik, Semester $semester, ?User $actor): void
    {
        $jenis = JenisPembayaran::withTrashed()->updateOrCreate(
            ['kode' => 'UKT'],
            $this->onlyExistingColumns('jenis_pembayaran', [
                'nama' => 'Uang Kuliah Tunggal',
                'kategori' => 'Tetap',
                'is_wajib' => true,
                'is_active' => true,
                'urutan' => 1,
                'created_by' => 'DummyDataSeeder',
            ])
        );
        if (method_exists($jenis, 'restore') && $jenis->trashed()) {
            $jenis->restore();
        }

        $jumlah = 3500000;
        $nomorTagihan = "TGH/DUMMY/{$tahunAkademik->kode}/TI/00001";

        $tagihan = TagihanMahasiswa::withTrashed()->updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswa->id,
                'jenis_pembayaran_id' => $jenis->id,
                'tahun_akademik_id' => $tahunAkademik->id,
                'semester_id' => $semester->id,
            ],
            $this->onlyExistingColumns('tagihan_mahasiswa', [
                'nomor_tagihan' => $nomorTagihan,
                'jumlah_tagihan' => $jumlah,
                'jumlah_dibayar' => $jumlah,
                'sisa_tagihan' => 0,
                'tanggal_tagihan' => now()->toDateString(),
                'tanggal_jatuh_tempo' => now()->addDays(30)->toDateString(),
                'tanggal_lunas' => now()->toDateString(),
                'status' => 'Lunas',
                'denda' => 0,
                'diskon' => 0,
                'keterangan' => 'Dummy tagihan lunas',
                'created_by' => 'DummyDataSeeder',
            ])
        );
        if (method_exists($tagihan, 'restore') && $tagihan->trashed()) {
            $tagihan->restore();
        }

        $nomorPembayaran = "PMB/DUMMY/" . now()->format('Y/m') . "/00001";
        $pembayaran = PembayaranMahasiswa::withTrashed()->updateOrCreate(
            ['nomor_pembayaran' => $nomorPembayaran],
            $this->onlyExistingColumns('pembayaran_mahasiswa', [
                'tagihan_mahasiswa_id' => $tagihan->id,
                'mahasiswa_id' => $mahasiswa->id,
                'jumlah_bayar' => $jumlah,
                'tanggal_bayar' => now()->toDateString(),
                'waktu_bayar' => now()->format('H:i:s'),
                'metode_pembayaran' => 'Transfer Bank',
                'nama_bank' => 'BCA',
                'status_verifikasi' => 'Diverifikasi',
                'verified_by' => $actor?->id,
                'verified_at' => now(),
                'created_by' => 'DummyDataSeeder',
            ])
        );
        if (method_exists($pembayaran, 'restore') && $pembayaran->trashed()) {
            $pembayaran->restore();
        }
    }

    private function seedPertemuanDanAbsensi(
        JadwalKuliah $jadwal,
        TahunAkademik $tahunAkademik,
        Semester $semester,
        Krs $krs,
        Mahasiswa $mahasiswa,
        ?User $dosenUser
    ): void {
        $pertemuan = PertemuanKuliah::withTrashed()->updateOrCreate(
            [
                'jadwal_kuliah_id' => $jadwal->id,
                'pertemuan_ke' => 1,
            ],
            $this->onlyExistingColumns('pertemuan_kuliah', [
                'tahun_akademik_id' => $tahunAkademik->id,
                'semester_id' => $semester->id,
                'tanggal_pertemuan' => now()->toDateString(),
                'jam_mulai_actual' => '08:00',
                'jam_selesai_actual' => '09:40',
                'topik_bahasan' => 'Pengenalan',
                'materi' => 'Dummy materi',
                'status' => 'Selesai',
                'created_by' => $dosenUser?->id,
            ])
        );
        if (method_exists($pertemuan, 'restore') && $pertemuan->trashed()) {
            $pertemuan->restore();
        }

        $absensi = AbsensiMahasiswa::withTrashed()->updateOrCreate(
            [
                'pertemuan_kuliah_id' => $pertemuan->id,
                'mahasiswa_id' => $mahasiswa->id,
            ],
            $this->onlyExistingColumns('absensi_mahasiswa', [
                'krs_id' => $krs->id,
                'status_kehadiran' => 'Hadir',
                'waktu_absen' => '08:00:00',
                'metode_absensi' => 'Manual',
                'is_verified' => true,
                'verified_by' => $dosenUser?->id,
                'verified_at' => now(),
                'created_by' => $dosenUser?->id,
            ])
        );
        if (method_exists($absensi, 'restore') && $absensi->trashed()) {
            $absensi->restore();
        }
    }

    private function seedPmb(ProgramStudi $prodi, ?User $actor): void
    {
        $villageId = $this->firstVillageId();
        $tahun = now()->format('Y');
        $tahunAkademik = $tahun . '/' . ((int) $tahun + 1);
        $noPendaftaran = "PMBDUMMY{$tahun}00000001";

        $pendaftaran = PendaftaranMahasiswa::withTrashed()->updateOrCreate(
            ['no_pendaftaran' => $noPendaftaran],
            $this->onlyExistingColumns('pendaftaran_mahasiswa', [
                'tahun_akademik' => $tahunAkademik,
                'jalur_masuk' => 'Mandiri',
                'program_studi_id' => $prodi->id,
                'nama_lengkap' => 'Calon Mahasiswa Dummy',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Dummy PMB',
                'village_id' => $villageId,
                'telepon' => '081211112222',
                'email' => 'pmb@sister.test',
                'status' => 'Diverifikasi',
                'tanggal_verifikasi' => now(),
                'verifikasi_by' => $actor?->id,
                'created_by' => $actor?->id,
            ])
        );
        if (method_exists($pendaftaran, 'restore') && $pendaftaran->trashed()) {
            $pendaftaran->restore();
        }
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function upsertUser(array $attributes): User
    {
        $email = (string) ($attributes['email'] ?? '');
        $name = (string) ($attributes['name'] ?? 'User Dummy');
        unset($attributes['email'], $attributes['name']);

        $payload = array_merge([
            'name' => $name,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'is_active' => true,
            'created_by' => 'DummyDataSeeder',
        ], $attributes);

        $payload = $this->onlyExistingColumns('users', $payload);

        return User::withTrashed()->updateOrCreate(['email' => $email], $payload);
    }

    private function firstVillageId(): ?int
    {
        if (!Schema::hasTable('villages')) {
            return null;
        }

        return DB::table('villages')->value('id');
    }

    /**
     * @param array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    private function onlyExistingColumns(string $table, array $attributes): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        $columns = $this->columnsCache[$table] ??= Schema::getColumnListing($table);
        $allowed = array_flip($columns);

        return array_intersect_key($attributes, $allowed);
    }
}
