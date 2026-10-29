<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $table = 'ruangan';
    protected $primaryKey = 'id_ruangan';

    protected $fillable = [
        'nama_ruangan',
        'kode_ruangan',
    ];

    public function jadwal()
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_ruangan', 'id_ruangan');
    }
}
