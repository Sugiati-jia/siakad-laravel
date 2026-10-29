<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $table = 'guru';

    /** DB siakad-nilai menggunakan PK id_guru (bukan id). */
    protected $primaryKey = 'id_guru';

    /** @var string */
    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_telepon',
        'email',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Mengizinkan kode legacy memakai $guru->id sementara kolom penyimpanan tetap id_guru (DB ini).
     */
    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_guru']) ? (int) $this->attributes['id_guru'] : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guruMapels()
    {
        return $this->hasMany(GuruMapel::class, 'id_guru', 'id_guru');
    }

    public function mapels()
    {
        return $this->belongsToMany(Mapel::class, 'guru_mapel', 'id_guru', 'id_mapel', 'id_guru', 'id_mapel');
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'guru_mapel', 'id_guru', 'id_kelas', 'id_guru', 'id_kelas');
    }

    public function kelasWali()
    {
        return $this->hasOne(Kelas::class, 'id_wali_kelas', 'id_guru');
    }

    public function nilaiTps()
    {
        return $this->hasMany(NilaiTp::class);
    }
}
