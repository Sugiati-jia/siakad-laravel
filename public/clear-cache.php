<?php
// Script to clear all Laravel caches and OPCache
require __DIR__.'/../vendor/autoload.php';
\ = require_once __DIR__.'/../bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Http\Kernel::class);

// Run artisan commands
Artisan::call('optimize:clear');
Artisan::call('cache:clear');
Artisan::call('config:clear');
Artisan::call('route:clear');
Artisan::call('view:clear');

// Clear OPCache
if (function_exists('opcache_reset')) {
    opcache_reset();
    \ = 'OPCache berhasil direset.';
} else {
    \ = 'OPCache tidak aktif atau tidak bisa direset.';
}

echo '<h1>Cache & OPCache Berhasil Dihapus!</h1>';
echo '<p>' . \ . '</p>';
echo '<p>Sekarang silakan coba lakukan proses IMPORT EXCEL lagi.</p>';

