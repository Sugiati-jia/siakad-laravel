<?php
$tables = DB::select('SHOW TABLES');
$database = env('DB_DATABASE', 'siakad-baru');
$key = 'Tables_in_' . $database;

$schema = [];
foreach ($tables as $tableInfo) {
    $tableName = $tableInfo->$key;
    $columns = DB::select("SHOW COLUMNS FROM `$tableName`");
    $schema[$tableName] = [];
    foreach ($columns as $column) {
        $schema[$tableName][] = $column->Field . ' (' . $column->Type . ')';
    }
}
file_put_contents('schema.json', json_encode($schema, JSON_PRETTY_PRINT));
echo "Schema dumped successfully.\n";
