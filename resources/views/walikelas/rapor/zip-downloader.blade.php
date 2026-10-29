<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mengunduh {{ $filename }}...</title>
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--custom-header-bg, #f8f9fa);
            color: #495057;
        }
        .container {
            text-align: center;
            background: var(--custom-card-bg, #ffffff);
            padding: 2rem 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e9ecef;
            border-top: 4px solid #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h3 style="margin-top:0;">Mengunduh File Rapor...</h3>
        <p style="margin-bottom:0;">File sedang diproses oleh browser Anda (tanpa Download Manager).</p>
        <p style="font-size: 0.85em; color: #6c757d;">Jendela ini akan kembali otomatis setelah unduhan selesai.</p>
    </div>

    <script>
        // Menggunakan teknik Blob & Base64 untuk mem-bypass Internet Download Manager (IDM)
        window.onload = function() {
            setTimeout(function() {
                try {
                    const base64Data = "{{ $zipContent }}";
                    const byteCharacters = atob(base64Data);
                    const byteNumbers = new Array(byteCharacters.length);
                    for (let i = 0; i < byteCharacters.length; i++) {
                        byteNumbers[i] = byteCharacters.charCodeAt(i);
                    }
                    const byteArray = new Uint8Array(byteNumbers);
                    const blob = new Blob([byteArray], {type: "application/zip"});
                    const blobUrl = URL.createObjectURL(blob);
                    
                    const link = document.createElement('a');
                    link.href = blobUrl;
                    link.download = "{{ $filename }}";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    setTimeout(() => {
                        URL.revokeObjectURL(blobUrl);
                        // Coba tutup tab jika dibuka di tab baru, jika tidak kembali ke halaman sebelumnya
                        if (window.history.length > 1) {
                            window.history.back();
                        } else {
                            window.close();
                        }
                    }, 500);
                } catch (e) {
                    // Fallback to data URI directly if blob fails (less likely)
                    const link = document.createElement('a');
                    link.href = "data:application/zip;base64,{{ $zipContent }}";
                    link.download = "{{ $filename }}";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }, 800); // Sedikit jeda agar UI loading terlihat
        };
    </script>
</body>
</html>
