<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PendaftaranMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PmbGuestUploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_upload_only_for_pmb_draft_attachments(): void
    {
        Storage::fake('public');

        $response = $this->postJson(route('api.file-upload.upload'), [
            'file' => UploadedFile::fake()->create('ktp.pdf', 200, 'application/pdf'),
            'fileable_type' => PendaftaranMahasiswa::class,
            'fileable_id' => 0,
            'category' => 'pmb',
            'description' => 'KTP',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseCount('file_uploads', 1);

        $fileUpload = \App\Models\FileUpload::query()->firstOrFail();
        $this->assertSame('PMB Guest', $fileUpload->created_by);
        $this->assertSame(PendaftaranMahasiswa::class, $fileUpload->fileable_type);
        $this->assertSame(0, (int) $fileUpload->fileable_id);

        Storage::disk('public')->assertExists($fileUpload->file_path);
    }

    public function test_guest_upload_is_forbidden_for_non_draft_pmb_attachment(): void
    {
        Storage::fake('public');

        $response = $this->postJson(route('api.file-upload.upload'), [
            'file' => UploadedFile::fake()->create('kk.pdf', 200, 'application/pdf'),
            'fileable_type' => PendaftaranMahasiswa::class,
            'fileable_id' => 123,
            'category' => 'pmb',
        ]);

        $response->assertStatus(403);
        $response->assertJsonPath('success', false);
        $this->assertDatabaseCount('file_uploads', 0);
    }

    public function test_guest_upload_is_forbidden_for_non_pmb_type_even_with_zero_id(): void
    {
        Storage::fake('public');

        $response = $this->postJson(route('api.file-upload.upload'), [
            'file' => UploadedFile::fake()->create('file.pdf', 200, 'application/pdf'),
            'fileable_type' => \App\Models\Mahasiswa::class,
            'fileable_id' => 0,
            'category' => 'general',
        ]);

        $response->assertStatus(403);
        $response->assertJsonPath('success', false);
        $this->assertDatabaseCount('file_uploads', 0);
    }

    public function test_guest_cannot_access_delete_download_and_getfiles_routes(): void
    {
        $this->delete(route('api.file-upload.destroy', ['id' => 1]))
            ->assertStatus(302)
            ->assertRedirect(route('login'));

        $this->get(route('api.file-upload.download', ['id' => 1]))
            ->assertStatus(302)
            ->assertRedirect(route('login'));

        $this->get(route('api.file-upload.getFiles', [
            'fileable_type' => PendaftaranMahasiswa::class,
            'fileable_id' => 0,
        ]))
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_upload_for_non_pmb_types(): void
    {
        Storage::fake('public');

        $user = User::factory()->withSuperAdminRole()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('api.file-upload.upload'), [
            'file' => UploadedFile::fake()->create('doc.pdf', 200, 'application/pdf'),
            'fileable_type' => \App\Models\Fakultas::class,
            'fileable_id' => 0,
            'category' => 'general',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseCount('file_uploads', 1);

        $fileUpload = \App\Models\FileUpload::query()->firstOrFail();
        $this->assertSame($user->name, $fileUpload->created_by);

        Storage::disk('public')->assertExists($fileUpload->file_path);
    }
}
