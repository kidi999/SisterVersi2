<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexViewsHaveExportButtonsTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_index_views_include_export_excel_and_pdf_links(): void
    {
        $basePath = base_path('resources/views');

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

            $files[] = $file->getPathname();
        }

        $this->assertNotEmpty($files, 'No index.blade.php views found to audit.');

        foreach ($files as $filePath) {
            $contents = file_get_contents($filePath);
            $this->assertIsString($contents);

            $hasExportExcelRouteCall = (bool) preg_match("/route\(\s*'[^']+\\.exportExcel'\s*[),]/", $contents);
            $hasExportPdfRouteCall = (bool) preg_match("/route\(\s*'[^']+\\.exportPdf'\s*[),]/", $contents);

            // Some views may build URLs differently, but we enforce the standard naming.
            $relative = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $filePath);

            $this->assertTrue(
                $hasExportExcelRouteCall,
                "Missing exportExcel route() call in view: {$relative}"
            );

            $this->assertTrue(
                $hasExportPdfRouteCall,
                "Missing exportPdf route() call in view: {$relative}"
            );

            // Heuristic: visible text should exist for UX
            $hasExcelText = stripos($contents, 'Export Excel') !== false;
            $hasPdfText = stripos($contents, 'Export PDF') !== false;

            $this->assertTrue(
                $hasExcelText,
                "Missing visible 'Export Excel' text in view: {$relative}"
            );

            $this->assertTrue(
                $hasPdfText,
                "Missing visible 'Export PDF' text in view: {$relative}"
            );
        }
    }
}
