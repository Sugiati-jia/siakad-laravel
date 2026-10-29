<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo '<h1>Data Tujuan Pembelajaran (Mapel 19)</h1>';
$tps = App\Models\TujuanPembelajaran::where('id_mapel', 19)->get();

echo '<table border="1" cellpadding="5">';
echo '<tr><th>ID</th><th>Mapel ID</th><th>Tingkat</th><th>Semester</th><th>Kode TP</th><th>Deskripsi</th></tr>';
foreach ($tps as $tp) {
    echo '<tr>';
    echo '<td>' . $tp->id_tujuan_pembelajaran . '</td>';
    echo '<td>' . $tp->id_mapel . '</td>';
    echo '<td>' . htmlspecialchars($tp->tingkat) . '</td>';
    echo '<td>' . $tp->id_semester . '</td>';
    echo '<td>' . htmlspecialchars($tp->kode_tp) . '</td>';
    echo '<td>' . htmlspecialchars($tp->deskripsi) . '</td>';
    echo '</tr>';
}
echo '</table>';
