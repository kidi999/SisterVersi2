<?php

// Quick static audit: scan controllers for view('name') and report missing blade files.

$root = __DIR__;
$controllersDir = $root . '/app/Http/Controllers';
$viewsDir = $root . '/resources/views';

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllersDir));

$missing = [];
$found = [];

foreach ($rii as $file) {
    if ($file->isDir()) continue;
    if (pathinfo($file->getFilename(), PATHINFO_EXTENSION) !== 'php') continue;

    $contents = file_get_contents($file->getPathname());
    if ($contents === false) continue;

    // Match: view('something') or view("something")
    if (!preg_match_all('/\bview\(\s*[\'\"]([^\'\"]+)[\'\"]/m', $contents, $m)) {
        continue;
    }

    foreach ($m[1] as $viewName) {
        // Skip dynamic-ish (rare): if contains {$ or concatenation markers
        if (str_contains($viewName, '$') || str_contains($viewName, '{') || str_contains($viewName, '}')) {
            continue;
        }

        $found[$viewName] = true;

        // Convert dot notation to path; hyphens are valid as folder/file names.
        $path = $viewsDir . '/' . str_replace('.', '/', $viewName) . '.blade.php';
        if (!file_exists($path)) {
            $missing[$viewName] = $path;
        }
    }
}

ksort($missing);
ksort($found);

echo "FOUND_VIEWS=" . count($found) . PHP_EOL;
echo "MISSING_VIEWS=" . count($missing) . PHP_EOL . PHP_EOL;

foreach ($missing as $name => $path) {
    $rel = str_replace($root . '/', '', str_replace('\\', '/', $path));
    echo $name . " => " . $rel . PHP_EOL;
}
