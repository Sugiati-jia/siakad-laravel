<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'nisn',
        'nis',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',         // ← kolom aktual di DB
        'agama',
        'nama_ayah',
        'nama_ibu',
        'no_hp_ortu',
        'id_kelas',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // ── Accessor/Mutator alias ───────────────────────────────────────────────

    /** Alias $siswa->id → id_siswa */
    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_siswa']) ? (int) $this->attributes['id_siswa'] : null;
    }

    /** Alias id_kelas ↔ id_kelas */
    public function getKelasIdAttribute(): ?int
    {
        return isset($this->attributes['id_kelas']) ? (int) $this->attributes['id_kelas'] : null;
    }

    public function setKelasIdAttribute(mixed $value): void
    {
        $this->attributes['id_kelas'] = $value;
    }

    /** Alias no_telepon → no_hp (nama kolom DB yang benar) */
    public function getNoTeleponAttribute(): ?string
    {
        return $this->attributes['no_hp'] ?? null;
    }

    public function setNoTeleponAttribute(mixed $value): void
    {
        $this->attributes['no_hp'] = $value;
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function nilaiTps()
    {
        return $this->hasMany(NilaiTp::class, 'id_siswa', 'id_siswa');
    }

    public function nilaiEkstrakurikulers()
    {
        return $this->hasMany(NilaiEkstrakurikuler::class, 'id_siswa', 'id_siswa');
    }

    public function catatanWaliKelas()
    {
        return $this->hasMany(CatatanWaliKelas::class, 'id_siswa', 'id_siswa');
    }

    public function orangTua()
    {
        return $this->hasMany(User::class, 'id_siswa');
    }
}
