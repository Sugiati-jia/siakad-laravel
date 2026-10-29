<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidangKeahlian extends Model
{
    use HasFactory;

    protected $table = 'bidang_keahlian';
    protected $primaryKey = 'id_bidang_keahlian';

    protected $fillable = [
        'kode_bidang',
        'nama_bidang',
        'deskripsi',
        'status',
    ];

    public function programKeahlians()
    {
        return $this->hasMany(ProgramKeahlian::class, 'id_bidang_keahlian', 'id_bidang_keahlian');
    }

    public function konsentrasiKeahlians()
    {
        return $this->hasMany(KonsentrasiKeahlian::class);
    }
}
