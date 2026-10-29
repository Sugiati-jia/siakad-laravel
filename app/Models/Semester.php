<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'semester';
    protected $primaryKey = 'id_semester';
    public $timestamps = false;

    protected $fillable = [
        'nama',
    ];

    public function jadwalPelajarans()
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_semester', 'id_semester');
    }

    public function nilaiTps()
    {
        return $this->hasMany(NilaiTp::class, 'id_semester', 'id_semester');
    }

    public function catatanWaliKelas()
    {
        return $this->hasMany(CatatanWaliKelas::class, 'id_semester', 'id_semester');
    }

    public function nilaiEkstrakurikulers()
    {
        return $this->hasMany(NilaiEkstrakurikuler::class, 'id_semester', 'id_semester');
    }

    public function kokurikulers()
    {
        return $this->hasMany(Kokurikuler::class, 'id_semester', 'id_semester');
    }

    public function raporStatuses()
    {
        return $this->hasMany(RaporStatus::class, 'id_semester', 'id_semester');
    }
}
