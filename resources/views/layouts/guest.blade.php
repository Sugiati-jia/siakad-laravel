<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'SIAKAD - SMK Sunan Giri')</title>
    <link rel="icon" type="image/png" href="/images/logo-sekolah.png">
    <link rel="apple-touch-icon" href="/images/logo-sekolah.png">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <!-- Google Font -->
    
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ─── Reset ─── */
        *, *::before, *::after { box-sizing: border-box; }

        /* ─── Full-screen background ─── */
        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            position: relative;
            background-color: #f0f7ff;
            background-image: 
                linear-gradient(rgba(13, 110, 253, 0.15) 1px, transparent 1px),
                linear-gradient(90deg, rgba(13, 110, 253, 0.15) 1px, transparent 1px);
            background-size: 20px 20px;
            background-position: center center;
        }

        /* Subtle radial gradient overlay to fade edges */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at center, transparent 0%, rgba(240, 247, 255, 0.85) 100%);
            z-index: 1;
            pointer-events: none;
        }

        /* ─── Card wrapper ─── */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
        }

        /* ─── Light Glassmorphism Card ─── */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(24px) saturate(1.4);
            -webkit-backdrop-filter: blur(24px) saturate(1.4);
            border: 1px solid rgba(255, 255, 255, 1);
            border-radius: 24px;
            padding: 44px 40px 38px;
            box-shadow:
                0 10px 40px rgba(13, 110, 253, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            animation: slideUp .55s cubic-bezier(.16, 1, .3, 1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ─── Logo / header ─── */
        .login-logo {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-logo-icon {
            width: 64px;
            height: 64px;
            background: rgba(13, 110, 253, 0.1);
            border: 1px solid rgba(13, 110, 253, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            animation: floatIcon 3s ease-in-out infinite;
        }

        .login-logo-icon i {
            font-size: 1.8rem;
            color: #0d6efd;
        }

        @keyframes floatIcon {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .login-logo h3 {
            font-size: 2.1rem;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }

        .login-logo p {
            color: #64748b;
            font-size: 0.88rem;
            margin-bottom: 2px;
            line-height: 1.6;
        }

        /* ─── Form labels ─── */
        .form-label {
            font-weight: 600;
            font-size: 0.84rem;
            color: #334155;
            margin-bottom: 6px;
        }

        /* ─── Input group ─── */
        .input-group {
            background: var(--custom-card-bg, #ffffff);
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            transition: all 0.2s;
            display: flex;
        }

        .input-group:focus-within {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #64748b;
            padding: 12px 14px;
        }

        .input-group:focus-within .input-group-text {
            color: #0d6efd;
        }

        .form-control {
            background: transparent;
            border: none;
            padding: 12px 14px;
            font-size: 0.9rem;
            color: #334155;
            box-shadow: none !important;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control:focus {
            background: transparent;
            border: none;
            box-shadow: none !important;
            color: #1e293b;
        }

        /* Fix browser autofill background mismatch */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover, 
        .form-control:-webkit-autofill:focus, 
        .form-control:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #ffffff inset !important;
            -webkit-text-fill-color: #334155;!important;
            transition: background-color 5000s ease-in-out 0s;
            border-radius: 0;
        }

        /* Eye toggle button */
        .input-group .btn-outline-secondary {
            background: transparent;
            border: none;
            color: #64748b;
            padding: 0 14px;
            transition: all .2s;
        }

        .input-group .btn-outline-secondary:hover {
            background: var(--custom-header-bg, #f8fafc);
            color: #334155;
        }

        /* ─── Remember me & Forgot Password row ─── */
        .remember-forgot-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-check-input {
            background-color: var(--custom-card-bg, #ffffff);
            border: 1.5px solid #cbd5e1;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-check-label {
            font-size: 0.84rem;
            color: #475569;
        }

        .forgot-link {
            color: #0d6efd;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            transition: color .2s;
        }

        .forgot-link:hover {
            color: #0b5ed7;
        }

        /* ─── Login button ─── */
        .btn-login {
            background: #0d6efd;
            border: none;
            padding: 14px;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 50px;
            color: #ffffff;
            letter-spacing: 0.01em;
            transition: all .25s cubic-bezier(.16, 1, .3, 1);
            box-shadow: 0 4px 14px rgba(13, 110, 253, 0.4);
        }

        .btn-login:hover {
            background: #0b5ed7;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
        }

        /* ─── Divider ─── */
        .login-divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #94a3b8;
            font-size: 0.78rem;
        }

        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--custom-header-bg, #e2e8f0);
        }

        .login-divider span {
            padding: 0 14px;
            font-weight: 500;
        }

        /* ─── Footer link ─── */
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: #64748b;
            font-size: 0.84rem;
        }

        .login-footer a {
            color: #0d6efd;
            font-weight: 600;
            text-decoration: none;
            transition: color .2s;
        }

        .login-footer a:hover {
            color: #0b5ed7;
            text-decoration: underline;
        }

        /* ─── Info section ─── */
        .info-section {
            background: rgba(13, 110, 253, 0.04);
            border: 1px solid rgba(13, 110, 253, 0.1);
            border-radius: 14px;
            padding: 16px 20px;
            margin-top: 24px;
            text-align: center;
        }

        .info-section h6 {
            color: #64748b;
            font-weight: 700;
            font-size: 0.75rem;
            margin-bottom: 12px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .info-item {
            display: inline-block;
            margin: 0 12px;
            color: #475569;
        }

        .info-item i {
            display: block;
            font-size: 1.35rem;
            margin-bottom: 4px;
            color: #0d6efd;
        }

        .info-item small {
            font-size: 0.73rem;
            font-weight: 600;
            color: #475569;
        }

        /* ─── Alerts ─── */
        .alert {
            border-radius: 12px;
            border: none;
            font-size: 0.85rem;
            padding: 12px 16px;
            backdrop-filter: blur(12px);
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.2) !important;
            color: #166534;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2) !important;
            color: #991b1b;
        }

        .alert .btn-close {
            /* Remove white inversion since background is light now */
        }

        /* ─── Validation ─── */
        .invalid-feedback {
            font-size: 0.78rem;
            color: #dc2626;
        }

        /* ─── Watermark ─── */
        .watermark {
            margin-top: 24px;
            font-size: 0.75rem;
            color: #94a3b8;
            letter-spacing: 0.05em;
            z-index: 10;
            text-align: center;
            position: relative;
        }

        /* ─── Responsive ─── */
        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px 28px;
                border-radius: 18px;
            }
            .login-logo h3 { font-size: 1.7rem; }
            .info-item { margin: 0 8px; }
            .remember-forgot-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    @yield('content')
</div>

<div class="watermark">© {{ date('Y') }} SIAKAD SMK Sunan Giri</div>

<!-- Bootstrap JS -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

<script>
    // Auto-hide flash messages after 5s
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (el) {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 5000);
</script>

@stack('scripts')
</body>
</html>
