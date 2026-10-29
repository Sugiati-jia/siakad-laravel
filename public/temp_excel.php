<?php
require "../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$files = [
    "raport" => "../RAPORT KELAS 10.xlsx",
    "nilai" => "../NILAI GURU MAPEL_DASAR OTOMOTIF _-X TSM (3).xlsx"
];

// Active selection
$selectedFile = $_GET['file'] ?? 'nilai';
if (!array_key_exists($selectedFile, $files)) {
    $selectedFile = 'nilai';
}
$filePath = $files[$selectedFile];
$selectedSheet = $_GET['sheet'] ?? null;

$error = null;
$sheetNames = [];
$sheetData = [];

if (file_exists($filePath)) {
    try {
        $spreadsheet = IOFactory::load($filePath);
        $sheetNames = $spreadsheet->getSheetNames();
        
        if (empty($selectedSheet) || !in_array($selectedSheet, $sheetNames)) {
            $selectedSheet = $sheetNames[0];
        }
        
        $worksheet = $spreadsheet->getSheetByName($selectedSheet);
        $highestRow = min(50, $worksheet->getHighestRow()); // limit to 50 rows for performance
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = min(20, Coordinate::columnIndexFromString($highestColumn)); // limit to 20 columns

        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = [];
            $hasContent = false;
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellCoordinate = Coordinate::stringFromColumnIndex($col) . $row;
                $cell = $worksheet->getCell($cellCoordinate);
                $value = $cell->getValue();
                
                if ($cell->isFormula()) {
                    try {
                        $value = $cell->getCalculatedValue();
                    } catch (\Exception $e) {
                        // fallback
                    }
                }
                
                $valueStr = $value === null ? "" : (string)$value;
                if (trim($valueStr) !== "") {
                    $hasContent = true;
                }
                
                // Get style metadata
                $isMerged = false;
                foreach ($worksheet->getMergeCells() as $mergeRange) {
                    if ($cell->isInRange($mergeRange)) {
                        $isMerged = true;
                        break;
                    }
                }
                
                $rowData[] = [
                    'value' => $valueStr,
                    'is_merged' => $isMerged,
                    'style' => $cell->getStyle()
                ];
            }
            if ($hasContent) {
                $sheetData[$row] = $rowData;
            }
        }
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
} else {
    $error = "File tidak ditemukan di path: " . realpath($filePath);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Inspector & Preview | SIAKAD</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
        }
        .app-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .file-tab {
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 30px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .file-tab.active {
            background-color: #3b82f6;
            color: white !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .file-tab:not(.active) {
            background-color: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.8) !important;
        }
        .file-tab:not(.active):hover {
            background-color: rgba(255,255,255,0.15);
        }
        .sheet-pill {
            padding: 8px 16px;
            font-weight: 500;
            border-radius: 20px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            margin: 4px;
            border: 1px solid #e5e7eb;
        }
        .sheet-pill.active {
            background-color: #10b981;
            color: white !important;
            border-color: #10b981;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
        }
        .sheet-pill:not(.active) {
            background-color: white;
            color: #4b5563 !important;
        }
        .sheet-pill:not(.active):hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }
        .table-excel {
            border-collapse: collapse;
            font-size: 0.85rem;
            background-color: white;
        }
        .table-excel th {
            background-color: #f9fafb;
            color: #4b5563;
            font-weight: 600;
            text-align: center;
            border: 1px solid #e5e7eb;
            padding: 8px;
        }
        .table-excel td {
            border: 1px solid #e5e7eb;
            padding: 6px 12px;
            min-width: 100px;
            vertical-align: middle;
        }
        .table-excel tr:hover td {
            background-color: #f3f4f6;
        }
        .row-index {
            background-color: #f9fafb;
            text-align: center;
            font-weight: bold;
            color: #9ca3af;
            width: 50px;
            border-right: 2px solid #d1d5db !important;
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.04);
        }
    </style>
</head>
<body>

    <!-- Header Banner -->
    <div class="app-header py-4 mb-4">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="fw-extrabold mb-1"><i class="bi bi-file-earmark-excel me-2 text-success"></i> Excel Template Inspector & Parser</h3>
                    <p class="mb-0 text-white-50">Pratinjau visual template rapor Kurikulum Merdeka dan format nilai pengampu guru.</p>
                </div>
                <div class="mt-3 mt-md-0 d-flex gap-2">
                    <a href="?file=nilai" class="file-tab {{ $selectedFile == 'nilai' ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i> Nilai Guru Mapel
                    </a>
                    <a href="?file=raport" class="file-tab {{ $selectedFile == 'raport' ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Template Rapor Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container-fluid px-4">
        @if ($error)
            <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center mb-4" role="alert" style="border-radius: 12px;">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div>
                    <h6 class="mb-1 fw-bold">Gagal Membaca File Excel</h6>
                    <span>{{ $error }}</span>
                </div>
            </div>
        @else
            <!-- Sheets Selector Card -->
            <div class="card mb-4">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-2 text-muted px-2"><i class="bi bi-collection me-1 text-info"></i> Daftar Worksheet (Silakan Klik untuk Memilih Lembar):</h6>
                    <div class="d-flex flex-wrap">
                        @foreach ($sheetNames as $name)
                            <a href="?file={{ $selectedFile }}&sheet={{ urlencode($name) }}" class="sheet-pill {{ $selectedSheet === $name ? 'active' : '' }}">
                                <i class="bi bi-grid-3x3-gap me-1"></i> {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Excel Grid Data Card -->
            <div class="card">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="mb-0 fw-extrabold text-dark">
                            <i class="bi bi-table me-2 text-success"></i> 
                            Lembar: <span class="text-success">{{ $selectedSheet }}</span>
                        </h5>
                        <small class="text-muted">Menampilkan baris 1 s.d. {{ count($sheetData) }} dengan isi data lengkap.</small>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-info-circle me-1 text-primary"></i> 
                            File: {{ basename($filePath) }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 700px; overflow: auto;">
                        <table class="table table-excel mb-0">
                            <thead>
                                <tr>
                                    <th class="row-index"></th>
                                    <?php for($i = 1; $i <= $highestColumnIndex; $i++): ?>
                                        <th><?php echo Coordinate::stringFromColumnIndex($i); ?></th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sheetData as $rowNum => $cols)
                                    <tr>
                                        <td class="row-index">{{ $rowNum }}</td>
                                        @foreach ($cols as $colData)
                                            <td class="{{ $colData['is_merged'] ? 'text-muted fst-italic' : '' }}">
                                                {{ $colData['value'] }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- App Navigation Helper Back to Dashboard -->
        <div class="text-center mt-5 mb-4">
            <a href="/admin/dashboard" class="btn btn-outline-dark px-4 py-2 rounded-pill fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard Utama SIAKAD
            </a>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
