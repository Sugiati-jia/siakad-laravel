<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruMapel extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $table = 'guru_mapel';
    protected $primaryKey = 'id_guru_mapel';
    public $incrementing = true;
    protected $keyType = 'int';

    // Kolom aktual: id_guru_mapel, id_guru, id_mapel, id_kelas, id_tahun_ajaran, created_at, status
    const UPDATED_AT = null;

    protected $fillable = [
        'id_guru',
        'id_mapel',
        'id_kelas',
        'id_tahun_ajaran',
        'status'
    ];


    // ── Accessor/Mutator alias ───────────────────────────────────────────────

    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_guru_mapel']) ? (int) $this->attributes['id_guru_mapel'] : null;
    }

    public function getGuruIdAttribute(): ?int
    {
        return isset($this->attributes['id_guru']) ? (int) $this->attributes['id_guru'] : null;
    }

    public function setGuruIdAttribute(mixed $value): void
    {
        $this->attributes['id_guru'] = $value;
    }

    public function getMapelIdAttribute(): ?int
    {
        return isset($this->attributes['id_mapel']) ? (int) $this->attributes['id_mapel'] : null;
    }

    public function setMapelIdAttribute(mixed $value): void
    {
        $this->attributes['id_mapel'] = $value;
    }

    public function getKelasIdAttribute(): ?int
    {
        return isset($this->attributes['id_kelas']) ? (int) $this->attributes['id_kelas'] : null;
    }

    public function setKelasIdAttribute(mixed $value): void
    {
        $this->attributes['id_kelas'] = $value;
    }

    public function getTahunAjaranIdAttribute(): ?int
    {
        return isset($this->attributes['id_tahun_ajaran']) ? (int) $this->attributes['id_tahun_ajaran'] : null;
    }

    public function setTahunAjaranIdAttribute(mixed $value): void
    {
        $this->attributes['id_tahun_ajaran'] = $value;
    }

    public function newEloquentBuilder($query)
    {
        return new \App\Database\Eloquent\Builders\GuruMapelBuilder($query);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function nilaiTps()
    {
        // NilaiTp tidak menyimpan id_guru_mapel — join via id_mapel + id_kelas
        return $this->hasMany(NilaiTp::class, 'id_mapel', 'id_mapel')
            ->whereIn('id_siswa', function ($q) {
                $q->select('id_siswa')->from('siswa')->where('id_kelas', $this->id_kelas);
            });
    }
}
