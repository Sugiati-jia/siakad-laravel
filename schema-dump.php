<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = DB::select('SHOW TABLES');
$database = env('DB_DATABASE', 'siakads3_db');
$key = 'Tables_in_' . $database;
if (!isset($tables[0]->$key)) {
    // try to get the first property dynamically
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

echo json_encode($schema, JSON_PRETTY_PRINT);
