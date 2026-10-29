<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $tables = DB::select('SHOW TABLES');
    $database = env('DB_DATABASE');
    $key = 'Tables_in_' . $database;
    if (!isset($tables[0]->$key)) {
        $key = array_keys((array)$tables[0])[0];
    }

    $schema = [];
    foreach ($tables as $tableInfo) {
        $tableName = $tableInfo->$key;
        $columns = DB::select("SHOW COLUMNS FROM `$tableName`");
        $schema[$tableName] = [];
        foreach ($columns as $column) {
            $schema[$tableName][] = $column->Field . ' (' . $column->Type . ')';
        }
    }

    header('Content-Type: application/json');
    echo json_encode($schema, JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
