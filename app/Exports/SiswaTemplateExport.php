<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiswaTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'nisn', 
            'nis', 
            'nama', 
            'jenis_kelamin', 
            'tempat_lahir', 
            'tanggal_lahir', 
            'alamat', 
            'no_hp', 
            'agama', 
            'nama_ayah', 
            'nama_ibu', 
            'no_hp_ortu', 
            'email'
        ];
    }

    public function array(): array
    {
        return [
            [
                '1234567890', 
                '1234', 
                'John Doe', 
                'L', 
                'Jakarta', 
                '2005-01-01', 
                'Jl. Sudirman', 
                '08123456789', 
                'Islam', 
                'Budi', 
                'Siti', 
                '08123456780', 
                'john@siswa.com'
            ]
        ];
    }
}
