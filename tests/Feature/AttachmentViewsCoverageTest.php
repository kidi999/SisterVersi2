<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AttachmentViewsCoverageTest extends TestCase
{
    public function test_create_edit_show_views_have_attachment_ui(): void
    {
        $viewsPath = resource_path('views');

        $excludedRelativePaths = [
            // User account profile (bukan modul akademik)
            'profile/edit.blade.php',
        ];

        $missingCreateEdit = [];
        $missingShow = [];

        foreach (File::allFiles($viewsPath) as $file) {
            $relative = str_replace('\\', '/', $file->getRelativePathname());

            if (in_array($relative, $excludedRelativePaths, true)) {
                continue;
            }

            $filename = $file->getFilename();

            $isCreateOrEdit = $filename === 'create.blade.php' || $filename === 'edit.blade.php';
            $isShow = $filename === 'show.blade.php';

            if (!$isCreateOrEdit && !$isShow) {
                continue;
            }

            $contents = File::get($file->getPathname());

            if ($isCreateOrEdit) {
                // Lampiran pada create/edit wajib ada UI upload
                // (komponen bisa berupa include atau Blade component)
                $hasUploadComponent = str_contains($contents, "components.file-upload")
                    || str_contains($contents, "<x-file-upload")
                    || str_contains($contents, "id=\"fileUploadSection\"");

                if (!$hasUploadComponent) {
                    $missingCreateEdit[] = $relative;
                }

                continue;
            }

            if ($isShow) {
                // Lampiran pada show minimal menampilkan list/download file
                $hasAttachmentSection = preg_match('/\\->files\\b/', $contents) === 1
                    || str_contains($contents, 'api.file-upload.download')
                    || str_contains($contents, 'Lampiran');

                if (!$hasAttachmentSection) {
                    $missingShow[] = $relative;
                }
            }
        }

        $messageParts = [];
        if (!empty($missingCreateEdit)) {
            $messageParts[] = "Missing lampiran upload UI (create/edit):\n- " . implode("\n- ", $missingCreateEdit);
        }
        if (!empty($missingShow)) {
            $messageParts[] = "Missing lampiran section (show):\n- " . implode("\n- ", $missingShow);
        }

        $this->assertTrue(empty($missingCreateEdit) && empty($missingShow), implode("\n\n", $messageParts));
    }
}
