<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TujuanPembelajaran extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $table = 'tujuan_pembelajaran';
    protected $primaryKey = 'id_tujuan_pembelajaran';
    public $incrementing = true;
    protected $keyType = 'int';

    // Hanya 'created_at', tidak ada 'updated_at' di tabel asli
    const UPDATED_AT = null;

    protected $fillable = [
        'id_mapel',
        'kode_tp',
        'id_tingkat',
        'deskripsi',    // ← nama kolom aktual di DB (bukan deskripsi_tp)
    ];

    // ── Accessor/Mutator alias ───────────────────────────────────────────────

    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_tujuan_pembelajaran'])
            ? (int) $this->attributes['id_tujuan_pembelajaran'] : null;
    }

    /** Alias: $tp->deskripsi_tp → kolom 'deskripsi' di DB */
    public function getDeskripsiTpAttribute(): ?string
    {
        return $this->attributes['deskripsi'] ?? null;
    }

    public function setDeskripsiTpAttribute(mixed $value): void
    {
        $this->attributes['deskripsi'] = $value;
    }

    /** Alias mapel_id ↔ id_mapel */
    public function getMapelIdAttribute(): ?int
    {
        return isset($this->attributes['id_mapel']) ? (int) $this->attributes['id_mapel'] : null;
    }

    public function setMapelIdAttribute(mixed $value): void
    {
        $this->attributes['id_mapel'] = $value;
    }

    /** Deskripsi singkat untuk tampilan list */
    public function getDeskripsiSingkatAttribute(): string
    {
        return \Illuminate\Support\Str::limit($this->attributes['deskripsi'] ?? '', 60, '...');
    }


    // ── Relationships ─────────────────────────────────────────────────────────

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    public function tingkatKelas()
    {
        return $this->belongsTo(TingkatKelas::class, 'id_tingkat', 'id_tingkat');
    }

    public function nilaiTps()
    {
        return $this->hasMany(NilaiTp::class, 'id_tujuan_pembelajaran', 'id_tujuan_pembelajaran');
    }

}
