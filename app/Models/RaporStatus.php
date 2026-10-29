<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaporStatus extends Model
{
    use HasFactory;

    protected $table = 'rapor_status';
    protected $primaryKey = 'id_rapor_status';

    protected $fillable = [
        'id_siswa',
        'id_semester',
        'id_tahun_ajaran',
        'status', // draft, dikirim, diverifikasi
        'dikirim_oleh',
        'tanggal_cetak',
        'tanggal_kirim'
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
