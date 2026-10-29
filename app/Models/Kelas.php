<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    public $incrementing = true;
    protected $keyType = 'int';

    // kelas tidak punya updated_at di DB lama — tapi setelah migration punya id_tahun_ajaran & tingkat
    public $timestamps = false; // tabel kelas tidak punya timestamps
    protected $fillable = [
        'nama',           // ← nama kolom aktual
        'id_tingkat',     // Relasi ke tabel tingkat_kelas
        'id_wali_kelas',
        'id_konsentrasi_keahlian',
        'id_tahun_ajaran',
        'status_final',
    ];

    // ── Accessor/Mutator alias ───────────────────────────────────────────────

    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_kelas']) ? (int) $this->attributes['id_kelas'] : null;
    }

    /**
     * Alias: $kelas->nama_kelas → kolom 'nama' di DB.
     */
    public function getNamaKelasAttribute(): ?string
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNamaKelasAttribute(mixed $value): void
    {
        $this->attributes['nama'] = $value;
    }

    /** Alias wali_id_kelas ↔ id_wali_kelas */
    public function getWaliKelasIdAttribute(): ?int
    {
        return isset($this->attributes['id_wali_kelas']) ? (int) $this->attributes['id_wali_kelas'] : null;
    }

    public function setWaliKelasIdAttribute(mixed $value): void
    {
        $this->attributes['id_wali_kelas'] = $value;
    }

    /** Alias jurusan_id ↔ id_konsentrasi_keahlian */
    public function getJurusanIdAttribute(): ?int
    {
        return isset($this->attributes['id_konsentrasi_keahlian']) ? (int) $this->attributes['id_konsentrasi_keahlian'] : null;
    }

    public function setJurusanIdAttribute(mixed $value): void
    {
        $this->attributes['id_konsentrasi_keahlian'] = $value;
    }

    /** Alias id_tahun_ajaran ↔ id_tahun_ajaran */
    public function getTahunAjaranIdAttribute(): ?int
    {
        return isset($this->attributes['id_tahun_ajaran']) ? (int) $this->attributes['id_tahun_ajaran'] : null;
    }

    public function setTahunAjaranIdAttribute(mixed $value): void
    {
        $this->attributes['id_tahun_ajaran'] = $value;
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'id_wali_kelas', 'id_guru');
    }

    public function konsentrasiKeahlian()
    {
        return $this->belongsTo(KonsentrasiKeahlian::class, 'id_konsentrasi_keahlian', 'id_konsentrasi_keahlian');
    }

    /** Backward compatibility alias for konsentrasiKeahlian */
    public function jurusan()
    {
        return $this->konsentrasiKeahlian();
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id_kelas');
    }

    public function guruMapels()
    {
        return $this->hasMany(GuruMapel::class, 'id_kelas', 'id_kelas');
    }

    public function mapels()
    {
        return $this->belongsToMany(Mapel::class, 'guru_mapel', 'id_kelas', 'id_mapel', 'id_kelas', 'id_mapel')
            ->withPivot('id_guru');
    }

    public function tingkatKelas()
    {
        return $this->belongsTo(TingkatKelas::class, 'id_tingkat', 'id_tingkat');
    }
}
