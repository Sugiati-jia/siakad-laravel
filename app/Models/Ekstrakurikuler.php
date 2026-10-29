<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ekstrakurikuler extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $table = 'ekstrakurikuler';
    protected $primaryKey = 'id_ekstrakurikuler';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama',
        'deskripsi',
        'status',
    ];

    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_ekstrakurikuler'])
            ? (int) $this->attributes['id_ekstrakurikuler'] : null;
    }

    public function nilaiEkstrakurikulers()
    {
        return $this->hasMany(NilaiEkstrakurikuler::class, 'id_ekstrakurikuler', 'id_ekstrakurikuler');
    }
}
