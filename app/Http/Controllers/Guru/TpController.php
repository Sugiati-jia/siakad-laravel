<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruMapel;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Exports\TpExport;
use App\Imports\TpImport;
use Maatwebsite\Excel\Facades\Excel;

class TpController extends Controller
{
    public function selectKelas(): View
    {
        $guru = Auth::user()->guru;

        $guruMapels = GuruMapel::with('kelas', 'mapel')
            ->where('id_guru', $guru->id_guru)
            ->where('status', 'aktif')
            ->get()
            ->unique('id_kelas');

        return view('guru.tp.select-kelas', compact('guruMapels'));
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

        return view('guru.tp.select-mapel', compact('guruMapels', 'kelas'));
    }

    public function input($id_kelas, $mapel_id): View
    {
        $guru = Auth::user()->guru;
        
        $guruMapel = GuruMapel::where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $mapel_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        $kelas = \App\Models\Kelas::findOrFail($id_kelas);
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $tujuanPembelajarans = TujuanPembelajaran::where('id_mapel', $mapel_id)
            ->where('tingkat', $kelas->tingkat)
            ->where('id_semester', $guruMapel->id_semester)
            ->orderBy('kode_tp')
            ->get();

        return view('guru.tp.input', compact(
            'guruMapel',
            'kelas',
            'mapel',
            'tujuanPembelajarans'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'guru_mapel_id' => 'required|exists:guru_mapel,id_guru_mapel',
            'kode_tp' => 'required|string|max:10',
            'deskripsi' => 'required|string|max:500',
        ]);

        $guru = Auth::user()->guru;
        $guruMapel = GuruMapel::with('kelas', 'mapel')->findOrFail($request->guru_mapel_id);

        // Verify this guru owns this assignment
        if ($guruMapel->id_guru != $guru->id_guru) {
            abort(403, 'Unauthorized action.');
        }

        // Check if grades are locked
        $isLocked = \App\Models\NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('status_nilai', 'locked')
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'Tujuan Pembelajaran tidak dapat ditambah karena nilai sudah dikunci oleh Wali Kelas.');
        }

        // Limit Check: Max 15 TPs
        $tpCount = TujuanPembelajaran::where('id_mapel', $guruMapel->id_mapel)
            ->where('tingkat', $guruMapel->kelas->tingkat)
            ->where('id_semester', $guruMapel->id_semester)
            ->count();

        if ($tpCount >= 15) {
            return back()->with('error', 'Jumlah Tujuan Pembelajaran tidak boleh lebih dari 15.')->withInput();
        }

        try {
            $maxUrutan = TujuanPembelajaran::where('id_mapel', $guruMapel->id_mapel)
                ->where('tingkat', $guruMapel->kelas->tingkat)
                ->where('id_semester', $guruMapel->id_semester)
                ->max('urutan');

            TujuanPembelajaran::create([
                'id_mapel' => $guruMapel->id_mapel,
                'kode_tp' => $request->kode_tp,
                'deskripsi' => $request->deskripsi,
                'tingkat' => $guruMapel->kelas->tingkat,
                'id_semester' => $guruMapel->id_semester,
                'urutan' => $maxUrutan ? $maxUrutan + 1 : 1,
            ]);

            // Log Activity
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Tambah TP',
                'description' => 'Menambahkan Tujuan Pembelajaran baru: Kode ' . $request->kode_tp . ' untuk ' . $guruMapel->kelas->nama_kelas . ' - ' . $guruMapel->mapel->nama_mapel,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return back()->with('success', 'Tujuan Pembelajaran berhasil ditambahkan');
        } catch (\Exception $e) {
            // Log Error to laravel.log for Admin's Log Error view
            \Illuminate\Support\Facades\Log::error('Tambah TP Exception: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'guru_mapel_id' => $request->guru_mapel_id
            ]);

            return back()->withInput()->with('sweet_error', 'Gagal menambahkan Tujuan Pembelajaran. Pastikan Anda memasukkan angka yang benar pada Kode TP.')->with('sweet_error_code', 422);
        }
    }

    public function storeAjax(Request $request)
    {
        $request->validate([
            'guru_mapel_id' => 'required|exists:guru_mapel,id_guru_mapel',
            'kode_tp' => 'required|string|max:10',
            'deskripsi' => 'required|string|max:500',
        ]);

        $guru = Auth::user()->guru;
        $guruMapel = GuruMapel::with('kelas')->findOrFail($request->guru_mapel_id);

        if ($guruMapel->id_guru != $guru->id_guru) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Check if grades are locked
        $isLocked = \App\Models\NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('status_nilai', 'locked')
            ->exists();

        if ($isLocked) {
            return response()->json(['success' => false, 'message' => 'Tujuan Pembelajaran tidak dapat ditambah karena nilai sudah dikunci oleh Wali Kelas.'], 403);
        }

        // Limit Check: Max 15 TPs
        $tpCount = TujuanPembelajaran::where('id_mapel', $guruMapel->id_mapel)
            ->where('tingkat', $guruMapel->kelas->tingkat)
            ->where('id_semester', $guruMapel->id_semester)
            ->count();

        if ($tpCount >= 15) {
            return response()->json(['success' => false, 'message' => 'Jumlah Tujuan Pembelajaran tidak boleh lebih dari 15.'], 422);
        }

        try {
            $maxUrutan = TujuanPembelajaran::where('id_mapel', $guruMapel->id_mapel)
                ->where('tingkat', $guruMapel->kelas->tingkat)
                ->where('id_semester', $guruMapel->id_semester)
                ->max('urutan');

            $tp = TujuanPembelajaran::create([
                'id_mapel' => $guruMapel->id_mapel,
                'kode_tp' => $request->kode_tp,
                'deskripsi' => $request->deskripsi,
                'tingkat' => $guruMapel->kelas->tingkat,
                'id_semester' => $guruMapel->id_semester,
                'urutan' => $maxUrutan ? $maxUrutan + 1 : 1,
            ]);

            // Log Activity
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Tambah TP',
                'description' => 'Menambahkan Tujuan Pembelajaran (AJAX): Kode ' . $request->kode_tp . ' untuk ' . $guruMapel->kelas->nama_kelas,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return response()->json(['success' => true, 'message' => 'Tujuan Pembelajaran berhasil ditambahkan!', 'tp' => $tp]);
        } catch (\Exception $e) {
            // Log Error
            \Illuminate\Support\Facades\Log::error('Tambah TP AJAX Exception: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'guru_mapel_id' => $request->guru_mapel_id
            ]);

            throw $e;
        }
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'kode_tp' => 'required|string|max:10',
            'deskripsi' => 'required|string|max:500',
        ]);

        $tp = TujuanPembelajaran::findOrFail($id);

        // Check if grades are locked
        $isLocked = \App\Models\NilaiTp::where('id_mapel', $tp->id_mapel)
            ->where('id_semester', $tp->id_semester)
            ->where('status_nilai', 'locked')
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'Tujuan Pembelajaran tidak dapat diubah karena nilai mapel ini sudah dikunci oleh Wali Kelas.');
        }

        try {
            $tp->update([
                'kode_tp' => $request->kode_tp,
                'deskripsi' => $request->deskripsi,
            ]);

            // Log Activity
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Edit TP',
                'description' => 'Mengubah Tujuan Pembelajaran: Kode ' . $tp->kode_tp . ' untuk mapel ID ' . $tp->id_mapel,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return back()->with('success', 'Tujuan Pembelajaran berhasil diubah');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Edit TP Exception: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'tp_id' => $id
            ]);

            return back()->withInput()->with('sweet_error', 'Gagal mengubah Tujuan Pembelajaran. Pastikan Anda memasukkan angka yang benar pada Kode TP.')->with('sweet_error_code', 422);
        }
    }

    public function destroy($id): RedirectResponse
    {
        $tp = TujuanPembelajaran::findOrFail($id);

        // Check if grades are locked
        $isLocked = \App\Models\NilaiTp::where('id_mapel', $tp->id_mapel)
            ->where('id_semester', $tp->id_semester)
            ->where('status_nilai', 'locked')
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'Tujuan Pembelajaran tidak dapat dihapus karena nilai mapel ini sudah dikunci oleh Wali Kelas.');
        }
        
        // Limit Check: Min 3 TPs
        $tpCount = TujuanPembelajaran::where('id_mapel', $tp->id_mapel)
            ->where('tingkat', $tp->tingkat)
            ->where('id_semester', $tp->id_semester)
            ->count();

        if ($tpCount <= 3) {
            return back()->with('error', 'Jumlah Tujuan Pembelajaran tidak boleh kurang dari 3.');
        }
        
        try {
            $tp->delete();

            // Log Activity
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Hapus TP',
                'description' => 'Menghapus Tujuan Pembelajaran: Kode ' . $tp->kode_tp . ' untuk mapel ID ' . $tp->id_mapel,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return back()->with('success', 'Tujuan Pembelajaran berhasil dihapus');
        } catch (\Exception $e) {
            // Log Error
            \Illuminate\Support\Facades\Log::error('Hapus TP Exception: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'tp_id' => $id
            ]);

            throw $e;
        }
    }

    public function destroyAjax($id)
    {
        $tp = TujuanPembelajaran::findOrFail($id);

        // Check if grades are locked
        $isLocked = \App\Models\NilaiTp::where('id_mapel', $tp->id_mapel)
            ->where('id_semester', $tp->id_semester)
            ->where('status_nilai', 'locked')
            ->exists();

        if ($isLocked) {
            return response()->json(['success' => false, 'message' => 'Tujuan Pembelajaran tidak dapat dihapus karena nilai mapel ini sudah dikunci oleh Wali Kelas.'], 403);
        }

        // Limit Check: Min 3 TPs
        $tpCount = TujuanPembelajaran::where('id_mapel', $tp->id_mapel)
            ->where('tingkat', $tp->tingkat)
            ->where('id_semester', $tp->id_semester)
            ->count();

        if ($tpCount <= 3) {
            return response()->json(['success' => false, 'message' => 'Jumlah Tujuan Pembelajaran tidak boleh kurang dari 3.'], 422);
        }

        try {
            $tp->delete();

            // Log Activity
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Hapus TP',
                'description' => 'Menghapus Tujuan Pembelajaran (AJAX): Kode ' . $tp->kode_tp . ' untuk mapel ID ' . $tp->id_mapel,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return response()->json(['success' => true, 'message' => 'Tujuan Pembelajaran berhasil dihapus!']);
        } catch (\Exception $e) {
            // Log Error
            \Illuminate\Support\Facades\Log::error('Hapus TP AJAX Exception: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'tp_id' => $id
            ]);

            throw $e;
        }
    }

    public function destroyAll($id_kelas, $mapel_id): RedirectResponse
    {
        $guru = Auth::user()->guru;
        $guruMapel = GuruMapel::with('kelas', 'mapel')->where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $mapel_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        // Check if grades are locked
        $isLocked = \App\Models\NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('status_nilai', 'locked')
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'Tujuan Pembelajaran tidak dapat dihapus karena nilai sudah dikunci oleh Wali Kelas.');
        }

        try {
            TujuanPembelajaran::where('id_mapel', $guruMapel->id_mapel)
                ->where('tingkat', $guruMapel->kelas->tingkat)
                ->where('id_semester', $guruMapel->id_semester)
                ->delete();

            // Log Activity
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Hapus Semua TP',
                'description' => 'Menghapus SEMUA Tujuan Pembelajaran untuk kelas ' . $guruMapel->kelas->nama_kelas . ' - ' . $guruMapel->mapel->nama_mapel,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return back()->with('success', 'Semua Tujuan Pembelajaran berhasil dihapus');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Hapus Semua TP Exception: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'guru_mapel_id' => $guruMapel->id_guru_mapel
            ]);

            throw $e;
        }
    }

    public function export($id_kelas, $mapel_id)
    {
        $guru = Auth::user()->guru;
        
        $guruMapel = GuruMapel::with('kelas', 'mapel')->where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $mapel_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        $filename = 'Format_TP_' . str_replace(' ', '_', $guruMapel->mapel->nama_mapel) . '_Tingkat_' . $guruMapel->kelas->tingkat . '_Semester_' . $guruMapel->id_semester . '.xlsx';

        return Excel::download(new TpExport($mapel_id, $guruMapel->kelas->tingkat, $guruMapel->id_semester), $filename);
    }

    public function import(Request $request, $id_kelas, $mapel_id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $guru = Auth::user()->guru;
        
        $guruMapel = GuruMapel::with('kelas', 'mapel')->where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $mapel_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        // Check if grades are locked
        $isLocked = \App\Models\NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('status_nilai', 'locked')
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'Tidak dapat mengimpor TP karena nilai mata pelajaran ini sudah dikunci oleh Wali Kelas.');
        }

        try {
            $import = new TpImport($mapel_id, $guruMapel->kelas->tingkat, $guruMapel->id_semester);
            
            // Load file with PhpSpreadsheet directly to evaluate formulas and find MASTER sheet if available
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->file('file')->getPathname());
            $masterSheet = null;
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $cleanTitle = preg_replace('/[^a-z]/', '', strtolower($sheet->getTitle()));
                if (str_contains($cleanTitle, 'master')) {
                    $masterSheet = $sheet;
                    break;
                }
            }
            
            if ($masterSheet) {
                $data = collect($masterSheet->toArray(null, true, false, false));
                $import->collection($data);
            } else {
                $data = collect($spreadsheet->getActiveSheet()->toArray(null, true, false, false));
                $import->collection($data);
            }
            
            if ($import->importedCount > 0) {
                // Log Activity
                \App\Models\ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Import TP',
                    'description' => 'Mengimpor/memperbarui ' . $import->importedCount . ' Tujuan Pembelajaran untuk kelas ' . $guruMapel->kelas->nama_kelas . ' - ' . $guruMapel->mapel->nama_mapel,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);

                return back()->with('success', 'Berhasil mengimpor/memperbarui ' . $import->importedCount . ' Tujuan Pembelajaran.');
            } elseif ($import->processedCount > 0) {
                // Log Activity
                \App\Models\ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Import TP',
                    'description' => 'Memproses TP Excel. Semua Tujuan Pembelajaran sudah sesuai (tidak ada perubahan).',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);

                return back()->with('success', 'Berhasil memproses file Excel. Semua Tujuan Pembelajaran sudah sesuai (tidak ada perubahan).');
            } else {
                return back()->with('error', 'Tidak ada Tujuan Pembelajaran yang diimpor. Format kolom mungkin tidak cocok atau data kosong.');
            }
        } catch (\Exception $e) {
            // Log to system Laravel logs for Admin's Log Error view
            \Illuminate\Support\Facades\Log::error('TP Import Exception: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'id_kelas' => $id_kelas,
                'mapel_id' => $mapel_id
            ]);

            // Log activity log as well
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Import TP Gagal',
                'description' => 'Gagal mengimpor Tujuan Pembelajaran: ' . $e->getMessage(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            throw $e;
        }
    }
}
