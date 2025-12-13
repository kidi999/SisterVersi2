<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexViewsHavePaginationControlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_index_views_include_pagination_controls(): void
    {
        $basePath = base_path('resources/views');

        $excludedRelativePaths = [
            // Landing page PMB (bukan listing)
            'pmb/index.blade.php',
            // Profil mahasiswa (halaman profil, bukan listing)
            'profil-mahasiswa/index.blade.php',
        ];

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if (!$file->isFile()) {
                continue;
            }

            if (strtolower($file->getFilename()) !== 'index.blade.php') {
                continue;
            }

            $relative = str_replace('\\', '/', str_replace($basePath . DIRECTORY_SEPARATOR, '', $file->getPathname()));
            if (in_array($relative, $excludedRelativePaths, true)) {
                continue;
            }

            $files[] = $file->getPathname();
        }

        $this->assertNotEmpty($files, 'No index.blade.php views found to audit.');

        foreach ($files as $filePath) {
            $contents = file_get_contents($filePath);
            $this->assertIsString($contents);

            $hasLinksCall = (bool) preg_match('/->\s*links\s*\(/', $contents);
            $hasPaginationWrapper = str_contains($contents, 'pagination-wrapper');

            $relative = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $filePath);

            $this->assertTrue(
                $hasLinksCall || $hasPaginationWrapper,
                "Missing pagination controls in view: {$relative}"
            );
        }
    }
}
