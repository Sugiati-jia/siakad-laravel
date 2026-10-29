<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TingkatKelas extends Model
{
    use HasFactory;

    protected $table = 'tingkat_kelas';
    protected $primaryKey = 'id_tingkat';

    protected $fillable = [
        'nama_tingkat',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_tingkat', 'id_tingkat');
    }
}
