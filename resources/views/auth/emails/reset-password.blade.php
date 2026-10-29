<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: var(--custom-card-bg, #ffffff); border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="color: #333; text-align: center;">Reset Kata Sandi</h2>
        <p>Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda. Berikut adalah kode OTP Anda:</p>
        <div style="text-align: center; margin: 25px 0;">
            <div style="background-color: var(--custom-header-bg, #f1f5f9); border: 2px dashed #0284c7; color: #0284c7; padding: 15px 30px; display: inline-block; border-radius: 8px; font-weight: bold; font-size: 24px; letter-spacing: 5px;">
                {{ $token }}
            </div>
        </div>
        <p style="font-size: 14px; color: #555;">Silakan masukkan kode 6 digit tersebut di halaman verifikasi. Kode ini bersifat rahasia.</p>
        <p style="font-size: 13px; color: #777;">Jika Anda tidak meminta reset kata sandi, abaikan email ini.</p>
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #999; text-align: center;">SIAKAD SMK Sunan Giri</p>
    </div>
</body>
</html>
