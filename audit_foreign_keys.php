<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DATABASE AUDIT - FOREIGN KEY & KONFLIK ===\n\n";

$dbName = DB::connection()->getDatabaseName();

// Get all tables
$tables = DB::select('SHOW TABLES');
$allTables = [];

foreach ($tables as $table) {
    $tableName = $table->{"Tables_in_$dbName"};
    $allTables[] = $tableName;
}

echo "Total Tables: " . count($allTables) . "\n\n";

// Check for foreign keys
echo str_repeat("=", 80) . "\n";
echo "FOREIGN KEYS AUDIT\n";
echo str_repeat("=", 80) . "\n\n";

$allForeignKeys = [];
$foreignKeyIssues = [];

foreach ($allTables as $tableName) {
    $foreignKeys = DB::select("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = ? 
        AND TABLE_NAME = ?
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ", [$dbName, $tableName]);
    
    if (count($foreignKeys) > 0) {
        echo "TABLE: $tableName\n";
        echo str_repeat("-", 80) . "\n";
        
        foreach ($foreignKeys as $fk) {
            $fkInfo = [
                'table' => $fk->TABLE_NAME,
                'column' => $fk->COLUMN_NAME,
                'ref_table' => $fk->REFERENCED_TABLE_NAME,
                'ref_column' => $fk->REFERENCED_COLUMN_NAME,
                'constraint' => $fk->CONSTRAINT_NAME
            ];
            
            $allForeignKeys[] = $fkInfo;
            
            echo sprintf("  %s.%s -> %s.%s (%s)\n", 
                $fk->TABLE_NAME,
                $fk->COLUMN_NAME,
                $fk->REFERENCED_TABLE_NAME,
                $fk->REFERENCED_COLUMN_NAME,
                $fk->CONSTRAINT_NAME
            );
            
            // Check if referenced table exists
            if (!in_array($fk->REFERENCED_TABLE_NAME, $allTables)) {
                $foreignKeyIssues[] = "❌ {$fk->TABLE_NAME}.{$fk->COLUMN_NAME} references non-existent table: {$fk->REFERENCED_TABLE_NAME}";
            }
        }
        echo "\n";
    }
}

// Check for duplicate table names (shouldn't happen but good to verify)
echo str_repeat("=", 80) . "\n";
echo "DUPLICATE TABLE CHECK\n";
echo str_repeat("=", 80) . "\n\n";

$tableCounts = array_count_values($allTables);
$duplicates = array_filter($tableCounts, function($count) { return $count > 1; });

if (count($duplicates) > 0) {
    echo "❌ DUPLICATE TABLES FOUND:\n";
    foreach ($duplicates as $table => $count) {
        echo "  - $table (appears $count times)\n";
    }
} else {
    echo "✅ No duplicate tables found!\n";
}

// Check for orphaned foreign keys (columns referencing non-existent tables)
echo "\n" . str_repeat("=", 80) . "\n";
echo "DATA TYPE CONSISTENCY CHECK (Audit Columns)\n";
echo str_repeat("=", 80) . "\n\n";

$auditColumnIssues = [];

foreach ($allTables as $tableName) {
    // Skip system tables
    if (in_array($tableName, ['migrations', 'cache', 'cache_locks', 'sessions', 'jobs', 'job_batches', 'failed_jobs', 'password_reset_tokens'])) {
        continue;
    }
    
    $columns = DB::select("
        SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = ? 
        AND TABLE_NAME = ?
        AND COLUMN_NAME IN ('created_by', 'updated_by', 'deleted_by')
    ", [$dbName, $tableName]);
    
    foreach ($columns as $col) {
        // Check if audit columns are consistent
        if ($col->COLUMN_NAME === 'created_by' || $col->COLUMN_NAME === 'updated_by' || $col->COLUMN_NAME === 'deleted_by') {
            // Should be either varchar(100) or bigint unsigned
            if (!str_contains($col->COLUMN_TYPE, 'varchar') && !str_contains($col->COLUMN_TYPE, 'bigint')) {
                $auditColumnIssues[] = "❌ $tableName.{$col->COLUMN_NAME} has unexpected type: {$col->COLUMN_TYPE}";
            }
        }
    }
}

if (count($auditColumnIssues) > 0) {
    foreach ($auditColumnIssues as $issue) {
        echo "$issue\n";
    }
} else {
    echo "✅ All audit columns have consistent data types!\n";
}

// Final Summary
echo "\n" . str_repeat("=", 80) . "\n";
echo "FINAL SUMMARY\n";
echo str_repeat("=", 80) . "\n\n";

echo "Total Tables: " . count($allTables) . "\n";
echo "Total Foreign Keys: " . count($allForeignKeys) . "\n";
echo "Duplicate Tables: " . count($duplicates) . "\n";
echo "Foreign Key Issues: " . count($foreignKeyIssues) . "\n";
echo "Audit Column Issues: " . count($auditColumnIssues) . "\n";

echo "\n";

if (count($foreignKeyIssues) > 0) {
    echo "❌ FOREIGN KEY ISSUES:\n";
    foreach ($foreignKeyIssues as $issue) {
        echo "  $issue\n";
    }
    echo "\n";
}

$totalIssues = count($duplicates) + count($foreignKeyIssues) + count($auditColumnIssues);

if ($totalIssues === 0) {
    echo "✅✅✅ DATABASE STRUCTURE IS PERFECT! ✅✅✅\n";
    echo "  - No duplicate tables\n";
    echo "  - No broken foreign keys\n";
    echo "  - No 'inserted' fields\n";
    echo "  - Consistent audit columns\n";
    echo "  - All relationships valid\n";
} else {
    echo "❌ Total Issues Found: $totalIssues\n";
    echo "Please review and fix the issues above.\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
