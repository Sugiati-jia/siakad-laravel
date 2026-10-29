<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonsentrasiKeahlian extends Model
{
    use HasFactory;

    protected $table = 'konsentrasi_keahlian';
    protected $primaryKey = 'id_konsentrasi_keahlian';

    protected $fillable = [
        'id_program_keahlian',
        'kode_konsentrasi',
        'nama_konsentrasi',
        'deskripsi',
        'status',
    ];

    public function programKeahlian()
    {
        return $this->belongsTo(ProgramKeahlian::class, 'id_program_keahlian', 'id_program_keahlian');
    }

    public function bidangKeahlian()
    {
        return $this->belongsTo(BidangKeahlian::class, 'bidang_keahlian_id');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_konsentrasi_keahlian');
    }
}
