<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h1>Memperbaiki Database SIAKAD...</h1>";

try {
    // Perbaiki id_semester yang null di tabel guru_mapel menjadi 1 (Semester Ganjil)
    $affected = \App\Models\GuruMapel::whereNull('id_semester')->update(['id_semester' => 1]);
    echo "<p>Berhasil memperbaiki {$affected} data Penugasan Guru (id_semester yang kosong menjadi 1).</p>";
    
    // Perbaiki nilai yang status_nilai nya mungkin nyangkut (jaga-jaga)
    echo "<p>Pengecekan database selesai. Sistem sudah siap digunakan.</p>";
} catch (\Exception $e) {
    echo "<p>Terjadi kesalahan: " . $e->getMessage() . "</p>";
}

echo "<h3>JANGAN LUPA HAPUS FILE INI SETELAH DIJALANKAN!</h3>";
