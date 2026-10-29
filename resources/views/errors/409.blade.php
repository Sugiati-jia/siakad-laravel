<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>409 - Conflict | SIAKAD SMK Sunan Giri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .error-card { background: var(--custom-card-bg, #ffffff); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 3rem 2rem; max-width: 600px; width: 90%; text-align: center; animation: fadeIn 0.5s ease-out; }
        .error-code { font-size: 4rem; font-weight: 800; line-height: 1; margin-bottom: 0.5rem; color: #2c3e50; }
        .error-title { font-size: 1.5rem; font-weight: 600; color: #e74c3c; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 1px; }
        .error-message { color: #5a6c7d; font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem; }
        .error-image { max-width: 280px; height: auto; margin-bottom: 1.5rem; }
        .btn-custom { padding: 0.8rem 3rem; font-weight: 600; border-radius: 50rem; transition: all 0.3s; font-size: 1.1rem; }
        .btn-primary-custom { background-color: #3498db; color: #fff; border: none; }
        .btn-primary-custom:hover { background-color: #2980b9; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">409</div>
        <div class="error-title">Conflict</div>
        <img src="/images/error_422.png" alt="Error Illustration" class="error-image">
        <p class="error-message">
            <strong>Nilai sudah pernah diinput.</strong><br>
            {{ $exception->getMessage() ?: 'Terjadi konflik data.' }}
        </p>
        <button onclick="window.history.back()" class="btn btn-primary-custom btn-custom">
            OK Mengerti
        </button>
    </div>
</body>
</html>
