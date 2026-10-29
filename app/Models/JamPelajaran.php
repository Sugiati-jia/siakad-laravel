<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jam_pelajaran';
    protected $primaryKey = 'id_jam';

    protected $fillable = [
        'urutan',
        'jam_ke',
        'waktu_mulai',
        'waktu_selesai',
        'jenis',
    ];

    public function getFormatJamAttribute()
    {
        if ($this->jenis === 'istirahat') {
            return "ISTIRAHAT (" . \Carbon\Carbon::parse($this->waktu_mulai)->format('H:i') . " - " . \Carbon\Carbon::parse($this->waktu_selesai)->format('H:i') . ")";
        }
        return "Jam ke-{$this->jam_ke} (" . \Carbon\Carbon::parse($this->waktu_mulai)->format('H:i') . " - " . \Carbon\Carbon::parse($this->waktu_selesai)->format('H:i') . ")";
    }
}
