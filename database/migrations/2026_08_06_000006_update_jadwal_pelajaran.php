<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            // Drop old foreign keys and columns
            $table->dropForeign(['id_jam_mulai']);
            $table->dropForeign(['id_jam_selesai']);
            $table->dropColumn('id_jam_mulai');
            $table->dropColumn('id_jam_selesai');

            // Add new columns
            $table->unsignedBigInteger('id_jam')->nullable()->after('hari');
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif')->after('id_ruangan');

            // Add new foreign key
            $table->foreign('id_jam')->references('id_jam')->on('jam_pelajaran')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropForeign(['id_jam']);
            $table->dropColumn('id_jam');
            $table->dropColumn('status');

            $table->unsignedBigInteger('id_jam_mulai')->nullable();
            $table->unsignedBigInteger('id_jam_selesai')->nullable();

            $table->foreign('id_jam_mulai')->references('id_jam')->on('jam_pelajaran')->onDelete('cascade');
            $table->foreign('id_jam_selesai')->references('id_jam')->on('jam_pelajaran')->onDelete('cascade');
        });
    }
};
