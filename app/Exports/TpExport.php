<?php

namespace App\Exports;

use App\Models\TujuanPembelajaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TpExport implements FromCollection, WithHeadings
{
    protected $id_mapel;
    protected $tingkat;
    protected $semester;

    public function __construct($id_mapel, $tingkat, $semester)
    {
        $this->id_mapel = $id_mapel;
        $this->tingkat = $tingkat;
        $this->semester = $semester;
    }

    public function collection()
    {
        return TujuanPembelajaran::where('id_mapel', $this->id_mapel)
            ->where('tingkat', $this->tingkat)
            ->where('id_semester', $this->semester)
            ->select('kode_tp', 'deskripsi')
            ->orderBy('kode_tp')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Kode TP',
            'Deskripsi',
        ];
    }
}
