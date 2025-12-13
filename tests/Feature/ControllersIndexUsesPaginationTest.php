<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ControllersIndexUsesPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_data_list_controllers_use_paginate_in_index(): void
    {
        $controllersPath = app_path('Http/Controllers');

        $excludedControllers = [
            // Not a list page
            'DashboardController.php',
            // Profile pages
            'ProfileController.php',
            'ProfilMahasiswaController.php',
            // Public PMB landing
            'PmbController.php',
            // API helpers
            'RegionController.php',
            // Upload endpoint
            'FileUploadController.php',
        ];

        $missing = [];

        foreach (File::files($controllersPath) as $file) {
            $filename = $file->getFilename();
            if (!str_ends_with($filename, 'Controller.php')) {
                continue;
            }

            if (in_array($filename, $excludedControllers, true)) {
                continue;
            }

            $contents = File::get($file->getPathname());
            if (!str_contains($contents, 'function index')) {
                continue;
            }

            $pos = strpos($contents, 'function index');
            if ($pos === false) {
                continue;
            }

            $rest = substr($contents, $pos);

            $endPos = null;
            if (preg_match('/\n\s*(public|protected|private)\s+function\s+\w+\s*\(/', $rest, $m, PREG_OFFSET_CAPTURE, 1) === 1) {
                $endPos = $m[0][1];
            }

            $indexBlock = $endPos !== null ? substr($rest, 0, $endPos) : $rest;

            $usesPaginate = str_contains($indexBlock, 'paginate(') || str_contains($indexBlock, 'simplePaginate(');

            if (!$usesPaginate) {
                $missing[] = $filename;
            }
        }

        $this->assertTrue(
            empty($missing),
            "Controllers with index() missing paginate():\n- " . implode("\n- ", $missing)
        );
    }
}
