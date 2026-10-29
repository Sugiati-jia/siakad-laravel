<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class NilaiTp extends Model
{
    use HasFactory;

    protected $table = 'nilai_tp';
    protected $primaryKey = 'id_nilai_tp';
    public $incrementing = true;
    protected $keyType = 'int';

    // Kolom aktual DB: id_nilai_tp, id_siswa, id_mapel, id_tujuan_pembelajaran,
    //                  nilai_th, nilai_uh, nilai_akhir_tp, created_at, nilai_as
    // Kolom tambahan (migration additive): nilai_th1, nilai_th2, nilai_uh1, nilai_uh2, status_nilai
    const UPDATED_AT = null;

    protected $fillable = [
        'id_siswa',
        'id_mapel',
        'id_guru_mapel',
        'id_semester',
        'id_tujuan_pembelajaran',
        'nilai_th1',     // Tugas Harian 1
        'nilai_th2',     // Tugas Harian 2
        'nilai_uh1',     // Ulangan Harian 1
        'nilai_uh2',     // Ulangan Harian 2
        'nilai_as',      // Asesmen Sumatif / Akhir Semester
        'nilai_akhir_tp',// Nilai Akhir TP (hasil perhitungan)
        'status_nilai',  // draft | submitted | locked
    ];

    protected $casts = [
        'nilai_th1'      => 'decimal:2',
        'nilai_th2'      => 'decimal:2',
        'nilai_uh1'      => 'decimal:2',
        'nilai_uh2'      => 'decimal:2',
        'nilai_as'       => 'decimal:2',
        'nilai_akhir_tp' => 'decimal:2',
    ];

    // ── Accessor/Mutator alias (agar kode lama tetap jalan) ─────────────────

    public function getSemesterAttribute()
    {
        return $this->relasiSemester->nama ?? '-';
    }

    public function getTh1Attribute() { return $this->attributes['nilai_th1'] ?? null; }
    public function getTh2Attribute() { return $this->attributes['nilai_th2'] ?? null; }
    public function getUh1Attribute() { return $this->attributes['nilai_uh1'] ?? null; }
    public function getUh2Attribute() { return $this->attributes['nilai_uh2'] ?? null; }
    public function getAkhirSemesterAttribute() { return $this->attributes['nilai_as'] ?? null; }

    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_nilai_tp']) ? (int) $this->attributes['id_nilai_tp'] : null;
    }

    /** Alias id_siswa ↔ id_siswa */
    public function getSiswaIdAttribute(): ?int
    {
        return isset($this->attributes['id_siswa']) ? (int) $this->attributes['id_siswa'] : null;
    }

    public function setSiswaIdAttribute(mixed $value): void
    {
        $this->attributes['id_siswa'] = $value;
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

    /** Alias tp_id ↔ id_tujuan_pembelajaran */
    public function getTpIdAttribute(): ?int
    {
        return isset($this->attributes['id_tujuan_pembelajaran'])
            ? (int) $this->attributes['id_tujuan_pembelajaran'] : null;
    }

    public function setTpIdAttribute(mixed $value): void
    {
        $this->attributes['id_tujuan_pembelajaran'] = $value;
    }

    /** Alias nilai_tp (nama lama) → nilai_akhir_tp */
    public function getNilaiTpAttribute(): ?float
    {
        return isset($this->attributes['nilai_akhir_tp'])
            ? (float) $this->attributes['nilai_akhir_tp'] : null;
    }

    /**
     * Accessor: hitung rata-rata TH secara virtual (tidak disimpan ke DB).
     */
    public function getNilaiThAttribute(): ?float
    {
        $th1 = $this->attributes['nilai_th1'] ?? null;
        $th2 = $this->attributes['nilai_th2'] ?? null;
        if ($th1 === null && $th2 === null) return null;
        $values = array_filter([$th1, $th2], fn($v) => $v !== null);
        return round(array_sum($values) / count($values), 2);
    }

    /**
     * Accessor: hitung rata-rata UH secara virtual (tidak disimpan ke DB).
     */
    public function getNilaiUhAttribute(): ?float
    {
        $uh1 = $this->attributes['nilai_uh1'] ?? null;
        $uh2 = $this->attributes['nilai_uh2'] ?? null;
        if ($uh1 === null && $uh2 === null) return null;
        $values = array_filter([$uh1, $uh2], fn($v) => $v !== null);
        return round(array_sum($values) / count($values), 2);
    }

    // ── Kalkulasi otomatis ────────────────────────────────────────────────────

    /**
     * Hitung nilai_akhir_tp dari TH dan UH saja (TANPA AS).
     * AS hanya digunakan di level summary siswa (sesuai rumus Excel):
     *   NA = AVERAGE(rerata_semua_TH, rerata_semua_UH, AS)
     *
     * Kolom nilai_akhir_tp menyimpan rerata TH+UH per baris TP.
     */
    protected function hitungNilaiAkhir(): void
    {
        $components = [];

        // Rata-rata TH (dari th1 & th2)
        $th1 = $this->attributes['nilai_th1'] ?? null;
        $th2 = $this->attributes['nilai_th2'] ?? null;
        if ($th1 !== null || $th2 !== null) {
            $thValues = array_filter([$th1, $th2], fn($v) => $v !== null);
            $components[] = array_sum($thValues) / count($thValues);
        }

        // Rata-rata UH (dari uh1 & uh2)
        $uh1 = $this->attributes['nilai_uh1'] ?? null;
        $uh2 = $this->attributes['nilai_uh2'] ?? null;
        if ($uh1 !== null || $uh2 !== null) {
            $uhValues = array_filter([$uh1, $uh2], fn($v) => $v !== null);
            $components[] = array_sum($uhValues) / count($uhValues);
        }

        // nilai_as TIDAK ikut di sini — AS hanya dihitung di summary level

        if (!empty($components)) {
            $this->attributes['nilai_akhir_tp'] = round(array_sum($components) / count($components), 2);
        }
    }

    public function save(array $options = []): bool
    {
        $this->hitungNilaiAkhir();
        return parent::save($options);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    public function tp()
    {
        return $this->belongsTo(TujuanPembelajaran::class, 'id_tujuan_pembelajaran', 'id_tujuan_pembelajaran');
    }

    public function guruMapel()
    {
        return $this->belongsTo(GuruMapel::class, 'id_guru_mapel', 'id_guru_mapel');
    }

    public function semesterRel()
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeDraft($query)
    {
        return $query->where('status_nilai', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status_nilai', 'submitted');
    }

    public function scopeLocked($query)
    {
        return $query->where('status_nilai', 'locked');
    }
}
