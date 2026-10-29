<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = \App\Models\User::find(26);
echo "User 26:\n";
print_r($user->toArray());
$guru = $user->guru;
if ($guru) {
    echo "Guru:\n";
    print_r($guru->toArray());
}
