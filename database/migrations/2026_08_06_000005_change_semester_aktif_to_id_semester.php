<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah tipe ENUM menjadi INT dan ubah nama kolom jadi id_semester
        DB::statement('ALTER TABLE tahun_ajaran CHANGE semester_aktif id_semester INT NULL');

        // Tambahkan Foreign Key
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->dropForeign(['id_semester']);
        });

        DB::statement("ALTER TABLE tahun_ajaran CHANGE id_semester semester_aktif ENUM('1','2') NOT NULL DEFAULT '1'");
    }
};
