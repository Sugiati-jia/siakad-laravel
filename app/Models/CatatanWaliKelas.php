<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanWaliKelas extends Model
{
    use HasFactory;

    protected $table = 'catatan_wali_kelas';
    protected $primaryKey = 'id_catatan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_siswa',
        'id_kelas',
        'id_tahun_ajaran',
        'id_semester',
        'catatan',
        'sakit',
        'izin',
        'alpa',
        'jenis_catatan',
        'status_kenaikan',
        'status',
    ];

    protected $casts = [
        'sakit' => 'integer',
        'izin'  => 'integer',
        'alpa'  => 'integer',
    ];

    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_catatan'])
            ? (int) $this->attributes['id_catatan'] : null;
    }

    /** Total hari tidak hadir */
    public function getTotalAbsenAttribute(): int
    {
        return ($this->sakit ?? 0) + ($this->izin ?? 0) + ($this->alpa ?? 0);
    }

    public function getSemesterAttribute()
    {
        return $this->semesterRel->nama ?? '-';
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
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
