<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\FileUpload;
use App\Models\PendaftaranMahasiswa;
use App\Models\ProgramStudi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PmbStoreAttachmentLinkingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pmb_store_links_only_draft_attachments_to_created_pendaftaran(): void
    {
        Notification::fake();
        Storage::fake('public');

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FK01',
            'nama_fakultas' => 'Fakultas Teknik',
            'singkatan' => 'FT',
            'dekan' => null,
            'alamat' => null,
            'telepon' => null,
            'email' => null,
        ]);

        $prodi = ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'TI01',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'kaprodi' => null,
            'akreditasi' => 'A',
        ]);

        // Upload draft attachment as guest (allowed only for PendaftaranMahasiswa::class, fileable_id=0)
        $uploadResponse = $this->postJson(route('api.file-upload.upload'), [
            'file' => UploadedFile::fake()->create('ktp.pdf', 120, 'application/pdf'),
            'fileable_type' => PendaftaranMahasiswa::class,
            'fileable_id' => 0,
            'category' => 'pmb',
            'description' => 'KTP',
        ]);

        $uploadResponse->assertStatus(200);
        $draftFileId = (int) $uploadResponse->json('file.id');

        // Create a non-draft attachment (should NOT be re-linked)
        $existingPendaftaran = PendaftaranMahasiswa::create([
            'tahun_akademik' => '2025/2026',
            'jalur_masuk' => 'Mandiri',
            'program_studi_id' => $prodi->id,
            'no_pendaftaran' => PendaftaranMahasiswa::generateNoPendaftaran('2025/2026', $prodi->id),
            'nama_lengkap' => 'Existing',
            'jenis_kelamin' => 'L',
            'email' => 'existing@example.test',
            'status' => 'Pending',
            'email_verification_token' => 'token',
        ]);

        $nonDraftFile = FileUpload::create([
            'fileable_type' => PendaftaranMahasiswa::class,
            'fileable_id' => $existingPendaftaran->id,
            'file_name' => 'other.pdf',
            'file_path' => 'uploads/pendaftaranmahasiswa/other.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 10,
            'category' => 'pmb',
            'description' => 'Other',
            'order' => 0,
            'created_by' => 'Seeder',
        ]);

        // Submit PMB form with both file IDs included
        $currentYear = (int) date('Y');
        $tahunAkademik = $currentYear . '/' . ($currentYear + 1);

        $response = $this->post(route('pmb.store'), [
            'tahun_akademik' => $tahunAkademik,
            'jalur_masuk' => 'Mandiri',
            'program_studi_id' => $prodi->id,
            'nama_lengkap' => 'Budi PMB',
            'jenis_kelamin' => 'L',
            'email' => 'budi.pmb@example.test',
            'file_ids' => [$draftFileId, $nonDraftFile->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pendaftaran_mahasiswa', [
            'email' => 'budi.pmb@example.test',
            'nama_lengkap' => 'Budi PMB',
        ]);

        $createdPendaftaran = PendaftaranMahasiswa::where('email', 'budi.pmb@example.test')->firstOrFail();

        $draftFile = FileUpload::findOrFail($draftFileId);
        $this->assertSame(PendaftaranMahasiswa::class, $draftFile->fileable_type);
        $this->assertSame($createdPendaftaran->id, (int) $draftFile->fileable_id);

        $nonDraftFile->refresh();
        $this->assertSame($existingPendaftaran->id, (int) $nonDraftFile->fileable_id);

        Notification::assertSentTimes(\App\Notifications\PendaftaranEmailVerification::class, 1);
    }
}
