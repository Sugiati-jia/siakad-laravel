<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kokurikuler extends Model
{
    use HasFactory;

    protected $table = 'kokurikuler';
    protected $primaryKey = 'id_kokurikuler';

    protected $fillable = [
        'id_siswa',
        'id_semester',
        'id_tahun_ajaran',
        'deskripsi',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function getSemesterAttribute()
    {
        return $this->semesterRel->nama ?? '-';
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
