<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel Ruangan
        Schema::create('ruangan', function (Blueprint $table) {
            $table->id('id_ruangan');
            $table->string('nama_ruangan', 50);
            $table->string('kode_ruangan', 20)->nullable();
            $table->timestamps();
        });

        // 2. Tabel Tingkat Kelas
        Schema::create('tingkat_kelas', function (Blueprint $table) {
            $table->id('id_tingkat');
            $table->string('nama_tingkat', 10); // X, XI, XII
            $table->timestamps();
        });

        // Insert Default Tingkat Kelas
        DB::table('tingkat_kelas')->insert([
            ['nama_tingkat' => 'X', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tingkat' => 'XI', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tingkat' => 'XII', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Tabel Jam Pelajaran
        Schema::create('jam_pelajaran', function (Blueprint $table) {
            $table->id('id_jam');
            $table->integer('urutan');
            $table->string('jam_ke', 20); // I, II, ISTIRAHAT
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->enum('jenis', ['belajar', 'istirahat'])->default('belajar');
            $table->timestamps();
        });

        // Insert Default Jam Pelajaran berdasarkan gambar jadwal
        DB::table('jam_pelajaran')->insert([
            ['urutan' => 1, 'jam_ke' => 'I', 'waktu_mulai' => '07:15', 'waktu_selesai' => '07:55', 'jenis' => 'belajar', 'created_at' => now()],
            ['urutan' => 2, 'jam_ke' => 'II', 'waktu_mulai' => '07:55', 'waktu_selesai' => '08:35', 'jenis' => 'belajar', 'created_at' => now()],
            ['urutan' => 3, 'jam_ke' => 'III', 'waktu_mulai' => '08:35', 'waktu_selesai' => '09:15', 'jenis' => 'belajar', 'created_at' => now()],
            ['urutan' => 4, 'jam_ke' => 'IV', 'waktu_mulai' => '09:15', 'waktu_selesai' => '09:55', 'jenis' => 'belajar', 'created_at' => now()],
            ['urutan' => 5, 'jam_ke' => 'ISTIRAHAT', 'waktu_mulai' => '09:55', 'waktu_selesai' => '10:15', 'jenis' => 'istirahat', 'created_at' => now()],
            ['urutan' => 6, 'jam_ke' => 'V', 'waktu_mulai' => '10:15', 'waktu_selesai' => '10:55', 'jenis' => 'belajar', 'created_at' => now()],
            ['urutan' => 7, 'jam_ke' => 'VI', 'waktu_mulai' => '10:55', 'waktu_selesai' => '11:35', 'jenis' => 'belajar', 'created_at' => now()],
            ['urutan' => 8, 'jam_ke' => 'VII', 'waktu_mulai' => '11:35', 'waktu_selesai' => '12:15', 'jenis' => 'belajar', 'created_at' => now()],
            ['urutan' => 9, 'jam_ke' => 'VIII', 'waktu_mulai' => '12:15', 'waktu_selesai' => '12:55', 'jenis' => 'belajar', 'created_at' => now()],
            ['urutan' => 10, 'jam_ke' => 'IX', 'waktu_mulai' => '12:55', 'waktu_selesai' => '13:35', 'jenis' => 'belajar', 'created_at' => now()],
        ]);

        // 4. Update Tabel Kelas
        Schema::table('kelas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tingkat')->nullable()->after('id_kelas');
            $table->foreign('id_tingkat')->references('id_tingkat')->on('tingkat_kelas')->onDelete('set null');
        });

        // Migrate existing tingkat in kelas to id_tingkat
        $kelases = DB::table('kelas')->get();
        foreach ($kelases as $kelas) {
            $tingkat = DB::table('tingkat_kelas')->where('nama_tingkat', $kelas->tingkat)->first();
            if ($tingkat) {
                DB::table('kelas')->where('id_kelas', $kelas->id_kelas)->update(['id_tingkat' => $tingkat->id_tingkat]);
            }
        }

        // 5. Update Tabel Jadwal Pelajaran
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->unsignedBigInteger('id_ruangan')->nullable()->after('id_semester');
            $table->unsignedBigInteger('id_jam_mulai')->nullable()->after('hari');
            $table->unsignedBigInteger('id_jam_selesai')->nullable()->after('id_jam_mulai');
            
            $table->foreign('id_ruangan')->references('id_ruangan')->on('ruangan')->onDelete('set null');
            $table->foreign('id_jam_mulai')->references('id_jam')->on('jam_pelajaran')->onDelete('cascade');
            $table->foreign('id_jam_selesai')->references('id_jam')->on('jam_pelajaran')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropForeign(['id_ruangan']);
            $table->dropForeign(['id_jam_mulai']);
            $table->dropForeign(['id_jam_selesai']);
            $table->dropColumn(['id_ruangan', 'id_jam_mulai', 'id_jam_selesai']);
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['id_tingkat']);
            $table->dropColumn('id_tingkat');
        });

        Schema::dropIfExists('jam_pelajaran');
        Schema::dropIfExists('tingkat_kelas');
        Schema::dropIfExists('ruangan');
    }
};
