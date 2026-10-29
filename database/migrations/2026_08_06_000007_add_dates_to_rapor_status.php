<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapor_status', function (Blueprint $table) {
            $table->dateTime('tanggal_cetak')->nullable()->after('dikirim_oleh');
            $table->dateTime('tanggal_kirim')->nullable()->after('tanggal_cetak');
        });
    }

    public function down(): void
    {
        Schema::table('rapor_status', function (Blueprint $table) {
            $table->dropColumn(['tanggal_cetak', 'tanggal_kirim']);
        });
    }
};
