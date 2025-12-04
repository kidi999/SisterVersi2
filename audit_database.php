<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== DATABASE AUDIT - STRUKTUR TABEL ===\n\n";

// Get all tables
$tables = DB::select('SHOW TABLES');
$dbName = DB::connection()->getDatabaseName();

$issues = [];
$insertedFields = [];
$allTables = [];

foreach ($tables as $table) {
    $tableName = $table->{"Tables_in_$dbName"};
    $allTables[] = $tableName;
    
    // Get columns
    $columns = DB::select("DESCRIBE $tableName");
    
    echo "TABLE: $tableName\n";
    echo str_repeat("-", 80) . "\n";
    
    $hasCreatedBy = false;
    $hasUpdatedBy = false;
    $hasDeletedBy = false;
    $hasCreatedAt = false;
    $hasUpdatedAt = false;
    $hasDeletedAt = false;
    $hasInsertedFields = false;
    
    foreach ($columns as $column) {
        $field = $column->Field;
        $type = $column->Type;
        $null = $column->Null;
        $key = $column->Key;
        $default = $column->Default;
        
        echo sprintf("  %-25s %-20s %-5s %-5s %s\n", 
            $field, 
            $type, 
            $null, 
            $key,
            $default ? "DEFAULT: $default" : ""
        );
        
        // Check for audit fields
        if ($field === 'created_by') $hasCreatedBy = true;
        if ($field === 'updated_by') $hasUpdatedBy = true;
        if ($field === 'deleted_by') $hasDeletedBy = true;
        if ($field === 'created_at') $hasCreatedAt = true;
        if ($field === 'updated_at') $hasUpdatedAt = true;
        if ($field === 'deleted_at') $hasDeletedAt = true;
        
        // Check for inserted fields (SHOULD NOT EXIST)
        if (str_contains($field, 'inserted')) {
            $hasInsertedFields = true;
            $insertedFields[] = "$tableName -> $field";
        }
    }
    
    echo "\n  Audit Status:\n";
    echo "    created_by: " . ($hasCreatedBy ? "✓" : "✗") . "\n";
    echo "    updated_by: " . ($hasUpdatedBy ? "✓" : "✗") . "\n";
    echo "    deleted_by: " . ($hasDeletedBy ? "✓" : "✗") . "\n";
    echo "    created_at: " . ($hasCreatedAt ? "✓" : "✗") . "\n";
    echo "    updated_at: " . ($hasUpdatedAt ? "✓" : "✗") . "\n";
    echo "    deleted_at: " . ($hasDeletedAt ? "✓" : "✗") . "\n";
    
    if ($hasInsertedFields) {
        echo "    ⚠️  WARNING: Has 'inserted' fields!\n";
    }
    
    echo "\n\n";
    
    // Collect issues
    if ($hasInsertedFields) {
        $issues[] = "❌ $tableName: Has 'inserted' fields";
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 80) . "\n\n";

echo "Total Tables: " . count($allTables) . "\n\n";

echo "Tables:\n";
foreach ($allTables as $table) {
    echo "  - $table\n";
}

if (count($insertedFields) > 0) {
    echo "\n❌ ISSUES FOUND - Fields with 'inserted':\n";
    foreach ($insertedFields as $field) {
        echo "  - $field\n";
    }
} else {
    echo "\n✅ No 'inserted_by', 'inserted_at', or 'inserted_time' fields found!\n";
}

if (count($issues) > 0) {
    echo "\n❌ TOTAL ISSUES: " . count($issues) . "\n";
    foreach ($issues as $issue) {
        echo "  $issue\n";
    }
} else {
    echo "\n✅ Database structure is clean and consistent!\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
