<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $primaryKey = 'id_tahun_ajaran';

    protected $fillable = [
        'tahun_ajaran',
        'id_semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function guruMapels()
    {
        return $this->hasMany(GuruMapel::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function nilaiTps()
    {
        return $this->hasMany(NilaiTp::class);
    }

    public function nilaiEkstrakurikulers()
    {
        return $this->hasMany(NilaiEkstrakurikuler::class);
    }

    public function catatanWaliKelas()
    {
        return $this->hasMany(CatatanWaliKelas::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Tahun ajaran yang sedang berstatus aktif (jika ada).
     */
    public static function active(): ?self
    {
        return static::query()->aktif()->first();
    }

    /**
     * Tahun ajaran aktif atau hentikan permintaan jika admin belum menetapkannya.
     */
    public static function activeOrAbort(): self
    {
        $ta = static::active();

        abort_if(!$ta, 422, __('Tahun ajaran aktif belum ditetapkan oleh admin.'));

        return $ta;
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }

    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_tahun_ajaran'])
            ? (int) $this->attributes['id_tahun_ajaran']
            : null;
    }

    public function getNamaTahunAjaranAttribute()
    {
        $semesterNama = $this->semester ? $this->semester->nama : $this->id_semester;
        return $this->tahun_ajaran . ' - Semester ' . $semesterNama;
    }
}
