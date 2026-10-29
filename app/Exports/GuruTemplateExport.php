<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GuruTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'nip', 
            'nama', 
            'jenis_kelamin', 
            'tempat_lahir', 
            'tanggal_lahir', 
            'alamat', 
            'no_telepon', 
            'email'
        ];
    }

    public function array(): array
    {
        return [
            [
                '198001012005011001', 
                'Budi Santoso', 
                'L', 
                'Surabaya', 
                '1980-01-01', 
                'Jl. Pahlawan', 
                '08123456789', 
                'budi@guru.com'
            ]
        ];
    }
}
