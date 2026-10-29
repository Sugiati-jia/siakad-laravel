<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tablesWithBoth = ['guru', 'siswa', 'mapel', 'tahun_ajaran', 'ekstrakurikuler', 'ruangan'];
        foreach ($tablesWithBoth as $table) {
            if (Schema::hasColumn($table, 'created_at') || Schema::hasColumn($table, 'updated_at')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    if (Schema::hasColumn($table, 'created_at')) $t->dropColumn('created_at');
                    if (Schema::hasColumn($table, 'updated_at')) $t->dropColumn('updated_at');
                });
            }
        }

        $tablesWithCreatedOnly = ['kelas', 'semester'];
        foreach ($tablesWithCreatedOnly as $table) {
            if (Schema::hasColumn($table, 'created_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('created_at');
                });
            }
        }
    }

    public function down(): void
    {
        // Reverting this means adding timestamps back.
        // We'll just add nullable timestamps.
        $tablesWithBoth = ['guru', 'siswa', 'mapel', 'tahun_ajaran', 'ekstrakurikuler', 'ruangan'];
        foreach ($tablesWithBoth as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->timestamps();
            });
        }

        $tablesWithCreatedOnly = ['kelas', 'semester'];
        foreach ($tablesWithCreatedOnly as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->timestamp('created_at')->nullable();
            });
        }
    }
};
