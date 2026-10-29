<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Terlalu Banyak Permintaan | SIAKAD SMK Sunan Giri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .error-card { background: var(--custom-card-bg, #ffffff); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 3rem 2rem; max-width: 600px; width: 90%; text-align: center; animation: fadeIn 0.5s ease-out; }
        .error-code { font-size: 8rem; font-weight: 800; line-height: 1; margin-bottom: 1rem; background: linear-gradient(135deg, #e74c3c, #c0392b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .error-title { font-size: 1.75rem; font-weight: 600; color: #2c3e50; margin-bottom: 1rem; }
        .error-message { color: #5a6c7d; font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem; }
        .btn-custom { padding: 0.6rem 1.5rem; font-weight: 600; border-radius: 50rem; transition: all 0.3s; }
        .btn-primary-custom { background-color: #3498db; color: #fff; border: none; }
        .btn-primary-custom:hover { background-color: #2980b9; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3); }
        .btn-outline-custom { border: 2px solid #2c3e50; color: #2c3e50; background: transparent; }
        .btn-outline-custom:hover { background-color: #2c3e50; color: #fff; transform: translateY(-2px); }
        .footer { margin-top: 2rem; font-size: 0.85rem; color: #95a5a6; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .tips-box { background-color: var(--custom-card-bg, #ffffff)3cd; color: #856404; border-radius: 8px; padding: 1rem; text-align: left; margin-bottom: 1.5rem; font-size: 0.9rem; border-left: 4px solid #ffeeba; }
    </style>
</head>
<body>
    <div class="error-card">
        <i class="bi bi-hourglass-split text-danger" style="font-size: 4rem;"></i>
        <div class="error-code">429</div>
        <h1 class="error-title">Terlalu Banyak Permintaan</h1>
        <p class="error-message">Anda terlalu sering mengirim permintaan ke server. Silakan tunggu beberapa saat dan coba lagi.</p>
        
        

        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button onclick="window.history.back()" class="btn btn-outline-custom btn-custom">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </button>
            <a href="/" class="btn btn-primary-custom btn-custom">
                <i class="bi bi-house-door me-2"></i>Halaman Utama
            </a>
        </div>
        
        <div class="footer">
            &copy; 2026 SIAKAD SMK Sunan Giri - Sistem Informasi Akademik
        </div>
    </div>
</body>
</html>
