<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruMapel;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use App\Models\NilaiTp;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NilaiImport;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ImportController extends Controller
{
    public function selectKelas(): View
    {
        $guru = Auth::user()->guru;
        
        $guruMapels = GuruMapel::with('kelas', 'mapel')
            ->where('id_guru', $guru->id_guru)
            ->where('status', 'aktif')
            ->get()
            ->unique('id_kelas');

        return view('guru.import.select-kelas', compact('guruMapels'));
    }

    public function selectMapel($id_kelas): View
    {
        $guru = Auth::user()->guru;
        
        $guruMapels = GuruMapel::with('mapel')
            ->where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('status', 'aktif')
            ->get();

        $kelas = \App\Models\Kelas::findOrFail($id_kelas);

        return view('guru.import.select-mapel', compact('guruMapels', 'kelas'));
    }

    public function import($id_kelas, $mapel_id): View
    {
        $guru = Auth::user()->guru;
        
        $guruMapel = GuruMapel::where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $mapel_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        $kelas = \App\Models\Kelas::findOrFail($id_kelas);
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $isLocked = NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('id_semester', $guruMapel->id_semester)
            ->where('status_nilai', 'locked')
            ->exists();

        return view('guru.import.import', compact(
            'guruMapel',
            'kelas',
            'mapel',
            'isLocked'
        ));
    }

    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'guru_mapel_id' => 'required|exists:guru_mapel,id_guru_mapel',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'mapel_id' => 'required|exists:mapel,id_mapel',
            'id_semester' => 'required|in:1,2',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'excel_file' => 'required|mimes:xlsx,xls|max:10240'
        ]);

        $guru = Auth::user()->guru;
        $guruMapel = GuruMapel::findOrFail($request->guru_mapel_id);

        // Auto-fix bug lama dimana id_mapel masih kosong di tabel guru_mapel
        if (!$guruMapel->id_mapel && $request->mapel_id) {
            $guruMapel->id_mapel = $request->mapel_id;
            $guruMapel->save();
        }

        // Verify this guru owns this assignment
        if ($guruMapel->id_guru != $guru->id_guru) {
            abort(403, 'Unauthorized action.');
        }

        // Check if grades are locked
        $isLocked = NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('id_semester', $request->id_semester)
            ->where('status_nilai', 'locked')
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'Tidak dapat mengimpor nilai karena nilai mata pelajaran ini sudah dikunci oleh Wali Kelas.');
        }

        try {
            DB::beginTransaction();

            // Load Excel file using PhpSpreadsheet directly to correctly evaluate cross-sheet formulas
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->file('excel_file')->getPathname());
            
            $masterSheet = null;
            $nilaiSheet = null;
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $cleanTitle = preg_replace('/[^a-z]/', '', strtolower($sheet->getTitle()));
                if (str_contains($cleanTitle, 'master')) {
                    $masterSheet = $sheet;
                }
                if (str_contains($cleanTitle, 'nilai')) {
                    $nilaiSheet = $sheet;
                }
            }
            
            if ($masterSheet && $nilaiSheet) {
                // 3-sheet format
                $masterData = collect($masterSheet->toArray(null, true, false, false));
                (new \App\Imports\MasterSheetImport($request->guru_mapel_id, $request->id_semester, $request->id_tahun_ajaran))
                    ->collection($masterData);
                
                $nilaiData = collect($nilaiSheet->toArray(null, true, false, false));
                (new \App\Imports\NilaiSheetImport($request->guru_mapel_id, $request->id_semester, $request->id_tahun_ajaran))
                    ->collection($nilaiData);
            } else {
                // 1-sheet format
                $sheet = $spreadsheet->getActiveSheet();
                $data = collect($sheet->toArray(null, true, false, false));
                (new \App\Imports\NilaiSheetImport($request->guru_mapel_id, $request->id_semester, $request->id_tahun_ajaran))
                    ->collection($data);
            }

            // Validasi apakah TP dan Nilai benar-benar masuk
            $tpCount = \App\Models\TujuanPembelajaran::where('id_mapel', $guruMapel->mapel_id)
                ->where('id_semester', $request->id_semester)
                ->where('tingkat', $guruMapel->kelas->tingkat)
                ->count();
                
            if ($tpCount === 0) {
                $mode = ($masterSheet && $nilaiSheet) ? '3-Sheet' : '1-Sheet';
                throw new \Exception("Mode {$mode}: 0 Tujuan Pembelajaran terbaca! Pastikan judul 'TUJUAN PEMBELAJARAN' dan 'NO.' ada, dan sel tidak tersembunyi.");
            }

            // Validasi apakah Nilai masuk
            $nilaiCount = \App\Models\NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
                ->where('id_semester', $request->id_semester)
                ->count();

            if ($nilaiCount === 0) {
                throw new \Exception("Tujuan Pembelajaran berhasil, tapi 0 Nilai tersimpan! Pastikan nama siswa di Excel SAMA PERSIS dengan di sistem untuk kelas ini.");
            }

            DB::commit();

            // Log Activity
            $importMode = ($masterSheet && $nilaiSheet) ? 'Format 3 Sheet (Manual)' : 'Format 1 Sheet (Template)';
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Import Nilai',
                'description' => 'Mengimpor nilai (' . $importMode . ') untuk ' . $guruMapel->kelas->nama_kelas . ' - ' . $guruMapel->mapel->nama_mapel,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return redirect()->route('guru.nilai.input', [
                'kelas_id' => $request->id_kelas,
                'mapel_id' => $request->mapel_id
            ])->with('success', 'Data nilai berhasil diimpor dari Excel');

        } catch (\Throwable $e) {
            DB::rollBack();

            // Log Error to system logs for Admin's view
            \Illuminate\Support\Facades\Log::error('Nilai Import Exception: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'guru_mapel_id' => $request->guru_mapel_id
            ]);

            // Log Activity Gagal
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Import Nilai Gagal',
                'description' => 'Gagal mengimpor nilai dari Excel: ' . $e->getMessage(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage());
        }
    }

    public function template($id_kelas, $mapel_id): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $guru = Auth::user()->guru;
        
        $guruMapel = GuruMapel::where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $mapel_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        $kelas = \App\Models\Kelas::findOrFail($id_kelas);
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $siswas = Siswa::where('id_kelas', $id_kelas)
            ->orderBy('nama')
            ->get();

        // Get TP from id_mapel directly
        $tujuanPembelajarans = TujuanPembelajaran::where('id_mapel', $mapel_id)
            ->where('tingkat', $kelas->tingkat)
            ->where('id_semester', $guruMapel->id_semester)
            ->orderBy('kode_tp')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // Buat sheet NILAI
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('NILAI');

        // Header Metadata
        $sheet->setCellValue('B5', 'MATA PELAJARAN');
        $sheet->setCellValue('C5', ':');
        $sheet->setCellValue('D5', $mapel->nama_mapel);
        
        $sheet->setCellValue('B6', 'KELAS');
        $sheet->setCellValue('C6', ':');
        $sheet->setCellValue('D6', $kelas->nama_kelas);
        
        $sheet->setCellValue('B7', 'SEMESTER');
        $sheet->setCellValue('C7', ':');
        $sheet->setCellValue('D7', $guruMapel->id_semester == 1 ? '1 (SATU)' : '2 (DUA)');
        
        $sheet->setCellValue('B8', 'GURU MATA PELAJARAN');
        $sheet->setCellValue('C8', ':');
        $sheet->setCellValue('D8', $guru->nama);

        // Header Tabel
        $sheet->setCellValue('A11', 'No');
        $sheet->setCellValue('B11', 'NAMA PESERTA DIDIK');
        $sheet->setCellValue('C11', 'JENIS PENILAIAN');
        
        $sheet->setCellValue('C12', 'KD');
        $sheet->setCellValue('D12', 'TUGAS HARIAN');
        $sheet->setCellValue('G12', 'ULANGAN HARIAN');
        $sheet->setCellValue('J12', 'AKHIR SEMESTER');
        
        $sheet->setCellValue('D13', '1');
        $sheet->setCellValue('E13', '2');
        $sheet->setCellValue('F13', 'Rerata');
        $sheet->setCellValue('G13', '1');
        $sheet->setCellValue('H13', '2');
        $sheet->setCellValue('I13', 'Rerata');

        // Fill student data
        $row = 14;
        foreach ($siswas as $index => $siswa) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $siswa->nama);
            
            if ($tujuanPembelajarans->count() > 0) {
                foreach ($tujuanPembelajarans as $tpIndex => $tp) {
                    $sheet->setCellValue('C' . ($row + $tpIndex), $tp->kode_tp);
                }
                // Hanya set NISN di baris pertama siswa (opsional, tapi bisa diletakkan di kolom akhir)
                $row += $tujuanPembelajarans->count();
            } else {
                // Default jika tidak ada TP
                $sheet->setCellValue('C' . $row, '1');
                $row++;
            }
        }

        // Style the header
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ]
        ];

        $sheet->getStyle('A11:J13')->applyFromArray($headerStyle);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setWidth(8);
        $sheet->getColumnDimension('E')->setWidth(8);
        $sheet->getColumnDimension('G')->setWidth(8);
        $sheet->getColumnDimension('H')->setWidth(8);
        $sheet->getColumnDimension('J')->setWidth(15);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'NILAI_GURU_' . str_replace(' ', '_', $mapel->nama_mapel) . '_' . str_replace(' ', '_', $kelas->nama_kelas) . '.xlsx';

        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
