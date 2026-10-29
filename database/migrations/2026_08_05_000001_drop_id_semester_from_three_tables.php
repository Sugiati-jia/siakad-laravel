<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. tujuan_pembelajaran — drop FK lalu kolom
        Schema::table('tujuan_pembelajaran', function (Blueprint $table) {
            $table->dropForeign('tujuan_pembelajaran_id_semester_foreign');
            $table->dropIndex('tujuan_pembelajaran_id_semester_foreign');
            $table->dropColumn('id_semester');
        });

        // 2. jadwal_pelajaran — drop FK lalu kolom
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropForeign('jadwal_pelajaran_id_semester_foreign');
            $table->dropIndex('jadwal_pelajaran_id_semester_foreign');
            $table->dropColumn('id_semester');
        });

        // 3. guru_mapel — drop FK lalu kolom
        Schema::table('guru_mapel', function (Blueprint $table) {
            $table->dropForeign('guru_mapel_ibfk_5');
            $table->dropIndex('id_semester');
            $table->dropColumn('id_semester');
        });
    }

    public function down(): void
    {
        Schema::table('tujuan_pembelajaran', function (Blueprint $table) {
            $table->integer('id_semester')->nullable();
            $table->foreign('id_semester', 'tujuan_pembelajaran_id_semester_foreign')
                ->references('id_semester')->on('semester')->onDelete('cascade');
        });

        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->integer('id_semester')->nullable();
            $table->foreign('id_semester', 'jadwal_pelajaran_id_semester_foreign')
                ->references('id_semester')->on('semester')->onDelete('cascade');
        });

        Schema::table('guru_mapel', function (Blueprint $table) {
            $table->integer('id_semester')->nullable();
            $table->foreign('id_semester', 'guru_mapel_ibfk_5')
                ->references('id_semester')->on('semester')->onDelete('cascade');
        });
    }
};
