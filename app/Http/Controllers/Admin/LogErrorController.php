<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;

class LogErrorController extends Controller
{
    public function index(): View
    {
        $logFile = storage_path('logs/laravel.log');
        $parsedLogs = [];
        
        if (File::exists($logFile)) {
            $logs = File::get($logFile);
            if(strlen($logs) > 500000) {
                $logs = substr($logs, -500000); 
                $pos = strpos($logs, '[' . date('Y'));
                if ($pos !== false) {
                    $logs = substr($logs, $pos);
                }
            }

            $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(.*?)\.(ERROR|WARNING|INFO|DEBUG|CRITICAL|EMERGENCY|ALERT|NOTICE):\s+(.*?)(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|\z)/s';
            
            if (preg_match_all($pattern, $logs, $matches, PREG_SET_ORDER)) {
                foreach (array_reverse($matches) as $match) {
                    $date = $match[1];
                    $env = $match[2];
                    $level = $match[3];
                    $fullMessage = $match[4];

                    $lines = explode("\n", trim($fullMessage));
                    $shortMessage = $lines[0];

                    $file = '-';
                    if (preg_match('/at\s+(.*?\.php:\d+)/', $fullMessage, $fileMatch)) {
                        $file = $fileMatch[1];
                    } elseif (preg_match('/in\s+(.*?\.php)\s*\(line\s+(\d+)\)/i', $fullMessage, $fileMatch)) {
                        $file = $fileMatch[1] . ':' . $fileMatch[2];
                    }

                    $solution = 'Tidak ada saran perbaikan spesifik. Silakan cek Stack Trace untuk detail lebih lanjut.';
                    if (stripos($shortMessage, 'Incorrect integer value') !== false) {
                        $solution = 'User memasukkan data teks ke kolom angka di database. Periksa validasi input pada form terkait (pastikan rules integer/numeric ada).';
                    } elseif (stripos($shortMessage, 'Duplicate entry') !== false) {
                        $solution = 'Data duplikat terdeteksi. Periksa apakah user menginputkan nilai yang seharusnya unik (seperti NIS, NIP) yang sudah ada di database.';
                    } elseif (stripos($shortMessage, 'SQLSTATE[HY000] [2002]') !== false || stripos($shortMessage, 'Connection refused') !== false) {
                        $solution = 'Koneksi database gagal. Pastikan MySQL/MariaDB berjalan dan kredensial di file .env sudah benar.';
                    } elseif (stripos($shortMessage, 'Unknown column') !== false || stripos($shortMessage, 'SQLSTATE[42S22]') !== false) {
                        $solution = 'Terdapat kolom yang tidak ditemukan di database. Pastikan nama kolom pada kodingan sesuai dengan database, atau jalankan migration jika ada yang kurang.';
                    } elseif (stripos($shortMessage, 'Integrity constraint violation') !== false) {
                        $solution = 'Pelanggaran constraint FK atau Data. Data yang sedang diakses mungkin berelasi dengan data lain yang salah/sudah dihapus.';
                    } elseif (stripos($shortMessage, 'Base table or view not found') !== false) {
                        $solution = 'Tabel database tidak ditemukan. Jalankan perintah `php artisan migrate` atau pastikan nama tabel di kodingan benar.';
                    } elseif (stripos($shortMessage, 'Class') !== false && stripos($shortMessage, 'not found') !== false) {
                        $solution = 'Class PHP tidak ditemukan. Periksa nama class, namespace, atau jalankan perintah `composer dump-autoload` di terminal server.';
                    } elseif (stripos($shortMessage, 'TokenMismatchException') !== false) {
                        $solution = 'CSRF token kadaluarsa. User biasanya terlalu lama diam di halaman atau session telah terhapus. (Sistem sekarang otomatis menanganinya).';
                    } elseif (stripos($shortMessage, 'MethodNotAllowedHttpException') !== false) {
                        $solution = 'Metode HTTP salah. Route mengharapkan POST tapi menerima GET (atau sebaliknya). Periksa method form di file blade.';
                    } elseif (stripos($shortMessage, 'ModelNotFoundException') !== false) {
                        $solution = 'Data tidak ditemukan di database. Kemungkinan ID yang dicari tidak ada atau data sudah dihapus.';
                    } elseif (stripos($shortMessage, 'Maximum execution time') !== false) {
                        $solution = 'Proses PHP terlalu lama (Timeout). Optimalkan query database, gunakan queueing, atau naikkan max_execution_time di php.ini.';
                    } elseif (stripos($shortMessage, 'Allowed memory size exhausted') !== false) {
                        $solution = 'Skrip kehabisan memori. Kurangi jumlah data yang dimuat ke memori (gunakan chunk() pada DB) atau naikkan memory_limit di php.ini.';
                    } elseif (stripos($shortMessage, 'Data too long') !== false) {
                        $solution = 'Data yang dikirim user terlalu panjang melebihi kapasitas kolom tabel. Beri atribut maxlength pada input form.';
                    } elseif (stripos($shortMessage, 'View') !== false && stripos($shortMessage, 'not found') !== false) {
                        $solution = 'File View (Blade) tidak ditemukan. Pastikan file ada di direktori resources/views dan penamaan huruf besar-kecilnya cocok.';
                    } elseif (stripos($shortMessage, 'Method') !== false && stripos($shortMessage, 'does not exist') !== false) {
                        $solution = 'Method/fungsi tidak ditemukan di dalam class. Periksa typo penamaan fungsi di controller/model.';
                    } elseif (stripos($shortMessage, 'Route') !== false && stripos($shortMessage, 'not defined') !== false) {
                        $solution = 'Route (URL) tidak terdaftar. Periksa penamaan route() pada file blade dan cocokkan dengan routes/web.php.';
                    } elseif (stripos($shortMessage, 'Too few arguments') !== false) {
                        $solution = 'Fungsi atau controller kekurangan parameter. Pastikan parameter URL (seperti ID) dikirim dengan lengkap.';
                    } elseif (stripos($shortMessage, 'syntax error') !== false) {
                        $solution = 'Kesalahan penulisan kode PHP (kurang titik koma, kurung tutup, dsb). Buka file yang tertera di pesan error dan perbaiki.';
                    }

                    $parsedLogs[] = [
                        'date' => $date,
                        'env' => $env,
                        'level' => $level,
                        'short_message' => $shortMessage,
                        'full_message' => $fullMessage,
                        'file' => $file,
                        'solution' => $solution
                    ];

                    if(count($parsedLogs) >= 150) break; // Limit 150 latest logs
                }
            }
        }

        return view('admin.log-error.index', compact('parsedLogs'));
    }

    public function clear()
    {
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        return redirect()->route('admin.log-error.index')->with('success', 'Log error berhasil dibersihkan.');
    }
}
