<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename is_finalized to status_final & drop tingkat in kelas
        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'is_finalized')) {
                $table->renameColumn('is_finalized', 'status_final');
            }
            if (Schema::hasColumn('kelas', 'tingkat')) {
                $table->dropColumn('tingkat');
            }
        });

        // 2. Drop redundant columns in jadwal_pelajaran
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $cols = ['jam_mulai', 'jam_selesai', 'jam_mengajar', 'jam_istirahat', 'ruangan', 'created_at', 'updated_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('jadwal_pelajaran', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // 3. Drop created_at & updated_at from transaction tables
        $transactionTables = ['nilai_tp', 'catatan_wali_kelas', 'nilai_ekstrakurikuler', 'kokurikuler'];
        foreach ($transactionTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'created_at')) $table->dropColumn('created_at');
                    if (Schema::hasColumn($tableName, 'updated_at')) $table->dropColumn('updated_at');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'status_final')) {
                $table->renameColumn('status_final', 'is_finalized');
            }
            $table->enum('tingkat', ['X', 'XI', 'XII'])->nullable();
        });

        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('jam_mengajar', 50)->nullable();
            $table->string('jam_istirahat', 100)->nullable();
            $table->string('ruangan', 50)->nullable();
            $table->timestamps();
        });

        $transactionTables = ['nilai_tp', 'catatan_wali_kelas', 'nilai_ekstrakurikuler', 'kokurikuler'];
        foreach ($transactionTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->timestamps();
                });
            }
        }
    }
};
