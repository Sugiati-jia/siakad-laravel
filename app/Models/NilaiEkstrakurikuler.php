<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiEkstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'nilai_ekstrakurikuler';
    protected $primaryKey = 'id_nilai_ekstra';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_siswa',
        'id_ekstrakurikuler',
        'id_tahun_ajaran',
        'id_semester',
        'nilai',
        'keterangan',   // A/B/C/D
        'deskripsi',
    ];

    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_nilai_ekstra'])
            ? (int) $this->attributes['id_nilai_ekstra'] : null;
    }

    public function getSemesterAttribute()
    {
        return $this->semesterRel->nama ?? '-';
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'id_ekstrakurikuler', 'id_ekstrakurikuler');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function semesterRel()
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }
}
