<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tujuan_pembelajaran', function (Blueprint $table) {
            // Hapus kolom lama
            if (Schema::hasColumn('tujuan_pembelajaran', 'tingkat')) {
                $table->dropColumn('tingkat');
            }
            if (Schema::hasColumn('tujuan_pembelajaran', 'urutan')) {
                $table->dropColumn('urutan');
            }
            
            // Tambah id_tingkat sebagai FK ke tingkat_kelas
            $table->unsignedBigInteger('id_tingkat')->nullable()->after('kode_tp');
            $table->foreign('id_tingkat')->references('id_tingkat')->on('tingkat_kelas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tujuan_pembelajaran', function (Blueprint $table) {
            $table->dropForeign(['id_tingkat']);
            $table->dropColumn('id_tingkat');
            
            $table->varchar('tingkat', 5)->nullable();
            $table->unsignedTinyInteger('urutan')->default(1);
        });
    }
};
