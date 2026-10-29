<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('jadwal_pelajaran');
print_r($columns);
