<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $table = 'mapel';
    protected $primaryKey = 'id_mapel';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kode_mapel',
        'nama',           // ← nama kolom aktual di DB
        'kelompok_mapel',
        'urutan',
        'kkm',
        'status',
    ];

    // ── Accessor/Mutator alias ───────────────────────────────────────────────

    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_mapel']) ? (int) $this->attributes['id_mapel'] : null;
    }

    /**
     * Alias: $mapel->nama_mapel → kolom 'nama' di DB.
     * Menjaga kompatibilitas kode controller lama yang masih pakai nama_mapel.
     */
    public function getNamaMapelAttribute(): ?string
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNamaMapelAttribute(mixed $value): void
    {
        $this->attributes['nama'] = $value;
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function guruMapels()
    {
        return $this->hasMany(GuruMapel::class, 'id_mapel', 'id_mapel');
    }

    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'guru_mapel', 'id_mapel', 'id_guru', 'id_mapel', 'id_guru')
            ->withPivot('id_kelas');
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'guru_mapel', 'id_mapel', 'id_kelas', 'id_mapel', 'id_kelas')
            ->withPivot('id_guru');
    }

    public function tujuanPembelajarans()
    {
        return $this->hasMany(TujuanPembelajaran::class, 'id_mapel', 'id_mapel');
    }

    public function nilaiTps()
    {
        return $this->hasMany(NilaiTp::class, 'id_mapel', 'id_mapel');
    }
}
