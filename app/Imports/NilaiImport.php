<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class NilaiImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function __construct(
        protected int $guruMapelId,
        protected string|int $semester,
        protected int $tahunAjaranId
    ) {}

    public function sheets(): array
    {
        return [
            // Sheet MASTER akan diproses terlebih dahulu jika ada, untuk membuat TP
            'MASTER' => new MasterSheetImport($this->guruMapelId, $this->semester, $this->tahunAjaranId),
            
            // Sheet NILAI akan memproses nilai (dan TP jika tidak ada sheet MASTER)
            'NILAI'  => new NilaiSheetImport($this->guruMapelId, $this->semester, $this->tahunAjaranId),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        // Abaikan sheet lain yang tidak terdaftar, seperti 'SETOR WALAS'
    }
}

