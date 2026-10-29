<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$konsentrasi = DB::table('konsentrasi_keahlian')->get();
$kelas = DB::table('kelas')->get();

echo "Konsentrasi Keahlian:\n";
echo json_encode($konsentrasi, JSON_PRETTY_PRINT) . "\n\n";

echo "Kelas:\n";
echo json_encode($kelas, JSON_PRETTY_PRINT) . "\n";
