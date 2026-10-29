<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\NilaiTp;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KenaikanKelasController extends Controller
{
    /**
     * Preview daftar siswa yang akan naik / lulus / tinggal kelas
     * berdasarkan rata-rata nilai akhir TP.
     */
    public function index(): View
    {
        $activeTahun = TahunAjaran::active();

        abort_if(!$activeTahun, 422, 'Tahun ajaran aktif belum ditetapkan.');

        // Hanya bisa diproses di semester genap (2)
        $isSemesterGenap = (int) $activeTahun->id_semester === 2;

        // Ambil semua kelas aktif dengan siswa, urutkan tingkat
        $kelasList = Kelas::where('id_tahun_ajaran', $activeTahun->id_tahun_ajaran)
            ->with(['siswas'])
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();

        // Hitung rata-rata nilai tiap siswa di tahun ajaran ini
        $siswaDenganNilai = [];

        foreach ($kelasList as $kelas) {
            foreach ($kelas->siswas as $siswa) {
                // Ambil rata-rata nilai_akhir_tp dari semua mapel semester genap
                $rataRata = NilaiTp::where('id_siswa', $siswa->id_siswa)
                    ->whereNotNull('nilai_akhir_tp')
                    ->where('nilai_akhir_tp', '>', 0)
                    ->avg('nilai_akhir_tp');

                $rataRata = round($rataRata ?? 0, 1);

                // Tentukan status
                if ($kelas->tingkat === 'XII') {
                    // Kelas XII: lulus jika rata-rata >= 65
                    $statusBaru = $rataRata >= 65 ? 'lulus' : 'tinggal_kelas';
                } else {
                    // Kelas X / XI: naik jika rata-rata >= 65
                    $statusBaru = $rataRata >= 65 ? 'naik_kelas' : 'tinggal_kelas';
                }

                $siswaDenganNilai[] = [
                    'siswa'       => $siswa,
                    'kelas'       => $kelas,
                    'rata_rata'   => $rataRata,
                    'status_baru' => $statusBaru,
                ];
            }
        }

        // Kelompokkan berdasarkan status
        $grouped = collect($siswaDenganNilai)->groupBy('status_baru');

        return view('admin.kenaikan-kelas.index', compact(
            'activeTahun',
            'isSemesterGenap',
            'grouped',
            'siswaDenganNilai'
        ));
    }

    /**
     * Proses kenaikan kelas:
     * - Kelas XII yang lulus → status 'lulus', id_kelas = null
     * - Kelas X / XI yang naik → id_kelas diupdate ke kelas tahun baru
     * - Yang tinggal → id_kelas tetap (di kelas yang sama untuk tahun baru)
     */
    public function proses(Request $request): RedirectResponse
    {
        $activeTahun = TahunAjaran::active();
        abort_if(!$activeTahun, 422, 'Tahun ajaran aktif belum ditetapkan.');
        abort_if((int) $activeTahun->id_semester !== 2, 422, 'Proses kenaikan kelas hanya bisa dilakukan di Semester Genap.');

        $mapTingkat = ['X' => 'XI', 'XI' => 'XII']; // X -> XI -> XII

        // Cari ID Tahun Ajaran Depan
        $tahunAjaranDepan = TahunAjaran::where('tahun_ajaran', '>', $activeTahun->tahun_ajaran)
            ->orderBy('tahun_ajaran', 'asc')
            ->first();
        $idTahunAjaranDepan = $tahunAjaranDepan ? $tahunAjaranDepan->id_tahun_ajaran : null;

        DB::beginTransaction();
        try {
            $kelasList = Kelas::where('id_tahun_ajaran', $activeTahun->id_tahun_ajaran)
                ->with('siswas')
                ->get();

            foreach ($kelasList as $kelas) {
                foreach ($kelas->siswas as $siswa) {
                    $rataRata = NilaiTp::where('id_siswa', $siswa->id_siswa)
                        ->whereNotNull('nilai_akhir_tp')
                        ->where('nilai_akhir_tp', '>', 0)
                        ->avg('nilai_akhir_tp');

                    $rataRata = round($rataRata ?? 0, 1);

                    if ($kelas->tingkat === 'XII') {
                        // Kelas XII: lulus / tinggal
                        if ($rataRata >= 65) {
                            $siswa->update([
                                'status'   => 'lulus',
                                'id_kelas' => null,
                            ]);
                        }
                        // Yang tidak lulus: status tetap aktif di kelas yang sama (tidak dihapus)
                    } else {
                        // Kelas X / XI: cari kelas tujuan tahun baru
                        if ($rataRata >= 65) {
                            $tingkatBaru = $mapTingkat[$kelas->tingkat] ?? null;
                            if ($tingkatBaru) {
                                // Cari kelas yang nama-nya sama tapi tingkat berikutnya
                                // (mis: "X RPL 1" → "XI RPL 1") — ambil bagian setelah huruf tingkat
                                $namaKelasLama = $kelas->nama; // mis: X RPL 1
                                // Hapus prefix tingkat lama dari nama
                                $namaKelasBase = trim(preg_replace('/^(X{1,3}I{0,3})\s+/', '', $namaKelasLama));
                                $namaKelasTargetKandung = $namaKelasBase; // mis: RPL 1

                                // Cari kelas tahun depan dengan tingkat baru
                                $queryKelasTujuan = Kelas::where('tingkat', $tingkatBaru)
                                    ->where('nama', 'like', '%' . $namaKelasTargetKandung . '%')
                                    ->where('id_konsentrasi_keahlian', $kelas->id_konsentrasi_keahlian);
                                    
                                if ($idTahunAjaranDepan) {
                                    $queryKelasTujuan->where('id_tahun_ajaran', $idTahunAjaranDepan);
                                }
                                
                                $kelasTujuan = $queryKelasTujuan->first();

                                if ($kelasTujuan) {
                                    $siswa->update(['id_kelas' => $kelasTujuan->id_kelas]);
                                }
                                // Jika kelas tujuan belum ada, biarkan admin buat dulu
                            }
                        }
                        // Yang tinggal kelas: id_kelas tetap tidak berubah
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.kenaikan-kelas.index')
                ->with('success', 'Proses kenaikan kelas dan kelulusan berhasil dijalankan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
