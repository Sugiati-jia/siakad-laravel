<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIAKAD - SMK Sunan Giri')</title>
    
    <!-- PWA Manifest & Icons -->
    <link rel="manifest" href="/manifest.json?v={{ time() }}">
    <meta name="theme-color" content="#6C82FF">
    <link rel="icon" type="image/png" href="/images/logo-sekolah.png">
    <link rel="apple-touch-icon" href="/images/logo-sekolah.png">

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            height: 100vh;
            background-color: #dce3f9;
            background-image: 
                linear-gradient(rgba(0, 0, 0, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.03) 1px, transparent 1px);
            background-size: 30px 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            margin: 0;
            position: relative;
            overflow: hidden;
        }

        /* Large decorative arcs on body background (like reference) */
        body::before {
            content: '';
            position: fixed;
            top: -120px; right: -120px;
            width: 420px; height: 420px;
            border-radius: 50%;
            border: 60px solid rgba(135,160,255,.18);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -80px; right: -60px;
            width: 280px; height: 280px;
            border-radius: 50%;
            border: 50px solid rgba(135,160,255,.12);
            pointer-events: none;
        }

        /* ── Main card ── */
        .auth-wrap {
            width: 100%;
            max-width: 1000px;
            display: flex;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(80,100,180,.22);
            position: relative;
            z-index: 1;
        }

        /* ═══ LEFT PANEL ═══ */
        .auth-left {
            width: 46%;
            flex-shrink: 0;
            background: #6C82FF;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 48px 44px;
        }

        /* ── Geometric decorations (matching reference) ── */

        /* Dot grid (4x3) */
        .deco-dots {
            position: absolute;
            left: 40px; bottom: 200px;
            display: grid;
            grid-template-columns: repeat(4, 10px);
            gap: 8px;
        }
        .deco-dots span {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: rgba(255,255,255,.55);
        }

        /* Half circle top-left */
        .deco-half {
            position: absolute;
            top: 40px; left: 60px;
            width: 52px; height: 26px;
            border-radius: 52px 52px 0 0;
            background: rgba(255,255,255,.35);
        }

        /* Vertical rounded rects (hanging lines) */
        .deco-lines {
            position: absolute;
            top: 30px; left: 180px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .deco-line {
            width: 8px;
            border-radius: 4px;
            background: rgba(255,255,255,.15);
        }
        .deco-line-1 { height: 90px; }
        .deco-line-2 { height: 130px; background: rgba(255,255,255,.2); }
        .deco-line-3 { height: 70px; }

        /* Small circle with ring (top-right) */
        .deco-ring {
            position: absolute;
            top: 50px; right: 60px;
            width: 24px; height: 24px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,.4);
        }
        .deco-ring::after {
            content: '';
            position: absolute;
            top: 4px; left: 4px;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--custom-card-bg, #ffffff);
        }

        /* Small green dot */
        .deco-green {
            position: absolute;
            top: 48px; right: 40px;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #5CF5A0;
        }

        /* X shape */
        .deco-x {
            position: absolute;
            bottom: 90px; left: 50%;
            width: 18px; height: 18px;
            transform: translateX(-50%);
        }
        .deco-x::before, .deco-x::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 18px; height: 3px;
            background: rgba(255,255,255,.45);
            border-radius: 2px;
        }
        .deco-x::before { transform: translate(-50%,-50%) rotate(45deg); }
        .deco-x::after  { transform: translate(-50%,-50%) rotate(-45deg); }

        /* Large circle outline (bottom-right) */
        .deco-arc {
            position: absolute;
            bottom: -30px; right: -10px;
            width: 200px; height: 200px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,.35);
        }

        /* Teal sphere (bottom-right inside arc) */
        .deco-sphere {
            position: absolute;
            bottom: 16px; right: 30px;
            width: 80px; height: 80px;
            border-radius: 50%;
            background: linear-gradient(160deg, #5CE0D8, #38BFA8);
            box-shadow: 0 8px 24px rgba(56,191,168,.4);
        }
        /* small sphere accent */
        .deco-sphere::after {
            content: '';
            position: absolute;
            top: -14px; right: -10px;
            width: 16px; height: 16px;
            border-radius: 50%;
            background: #5CE0D8;
        }

        /* Teal sphere bottom-left */
        .deco-sphere2 {
            position: absolute;
            bottom: 160px; left: 28px;
            width: 30px; height: 30px;
            border-radius: 50%;
            background: linear-gradient(160deg, #5CE0D8, #38BFA8);
            box-shadow: 0 4px 12px rgba(56,191,168,.4);
        }

        /* Left panel text */
        .left-content {
            position: relative;
            z-index: 2;
        }
        .left-content h2 {
            font-size: 4rem; /* HUGE text like image */
            font-weight: 900;
            color: #fff;
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 24px;
        }
        .left-content p {
            font-size: 1.1rem;
            color: rgba(255,255,255,.85);
            line-height: 1.8;
            max-width: 320px;
        }

        /* Logo on left */
        .left-logo {
            width: 100px; height: 100px;
            background: var(--custom-card-bg, #ffffff); /* Solid white background like image */
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 40px; /* More space below logo */
            padding: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,.15);
        }
        .left-logo img { width:100%; height:100%; object-fit:contain; border-radius:50%; }
        .left-logo .fb { font-size:2.4rem; color:#6C82FF; display:none; }

        /* Extra slot */
        .left-extra { margin-top: 24px; }

        /* Mobile CTA */
        .m-cta { display: none; }

        /* ═══ RIGHT PANEL ═══ */
        .auth-right {
            flex: 1;
            background: #EDF1FA;
            padding: 44px 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Mobile elements */
        .sheet-bar  { display: none; }
        .sheet-back { display: none; }

        /* Small decorative dots on right panel (bottom-right like reference) */
        .right-dots {
            position: absolute;
            bottom: 16px; right: 16px;
            display: grid;
            grid-template-columns: repeat(4, 6px);
            gap: 5px;
        }
        .right-dots span {
            width: 4px; height: 4px;
            border-radius: 50%;
            background: rgba(108,130,255,.25);
        }

        /* ═══ MOBILE ═══ */
        @media (max-width: 768px) {
            body {
                padding: 0;
                overflow: hidden;
                background: #6C82FF;
            }
            body::before, body::after { display: none; }

            .auth-wrap {
                flex-direction: column;
                border-radius: 0;
                box-shadow: none;
                min-height: 100vh;
                min-height: 100dvh;
            }

            .auth-left {
                position: fixed; inset: 0;
                width: 100%; padding: 0 32px;
                z-index: 1;
                justify-content: center;
                align-items: center;
                text-align: center;
            }
            .left-content h2 { font-size: 2.2rem; }
            .left-content p { margin: 0 auto; }
            .left-logo { margin: 0 auto 24px; width: 100px; height: 100px; }

            .m-cta {
                display: inline-flex;
                align-items: center; gap: 10px;
                margin-top: 32px;
                padding: 12px 24px;
                border-radius: 100px;
                background: rgba(255,255,255,.18);
                border: 1.5px solid rgba(255,255,255,.4);
                color: #fff;
                font-size: .94rem; font-weight: 700;
                cursor: pointer; text-decoration: none;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                transition: background .22s, transform .18s;
                width: auto;
            }
            .m-cta:hover, .m-cta:active {
                background: rgba(255,255,255,.28);
                transform: translateY(-2px); color: #fff;
            }
            .m-dot {
                width: 22px; height: 22px; border-radius: 50%;
                background: rgba(255,255,255,.85);
                display: flex; align-items: center; justify-content: center;
                color: #6C82FF; font-size: .76rem;
            }

            .auth-right {
                position: fixed;
                left: 0; right: 0; bottom: 0; z-index: 20;
                height: 100dvh; overflow-y: auto;
                padding: 18px 20px 36px;
                border-radius: 26px 26px 0 0;
                background: #EDF1FA;
                box-shadow: 0 -16px 48px rgba(0,0,0,.16);
                transform: translateY(100%);
                transition: transform .44s cubic-bezier(.22,1,.36,1);
                display: flex; flex-direction: column;
                align-items: center; justify-content: flex-start;
            }
            .auth-right.open { transform: translateY(0); }

            .sheet-bar {
                display: block;
                width: 36px; height: 4px; border-radius: 2px;
                background: #c7d0e8; margin: 0 auto 14px;
            }
            .sheet-back {
                display: flex; align-items: center; gap: 6px;
                background: none; border: none; color: #8899bb;
                font-size: .85rem; font-weight: 600;
                cursor: pointer; padding: 0; margin-bottom: 12px;
                transition: color .2s; align-self: flex-start;
            }
            .sheet-back:hover { color: #6C82FF; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="auth-wrap">

    <!-- ═══ LEFT ═══ -->
    <div class="auth-left">
        <!-- Decorative elements matching reference -->
        <span class="deco-half"></span>
        <div class="deco-lines">
            <span class="deco-line deco-line-1"></span>
            <span class="deco-line deco-line-2"></span>
            <span class="deco-line deco-line-3"></span>
        </div>
        <span class="deco-ring"></span>
        <span class="deco-green"></span>
        <div class="deco-dots">
            <span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span>
        </div>
        <span class="deco-x"></span>
        <span class="deco-arc"></span>
        <span class="deco-sphere"></span>
        <span class="deco-sphere2"></span>

        <div class="left-content">
            <div class="left-logo">
                <img src="/images/logo-sekolah.png" alt="Logo SMK Sunan Giri"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                <i class="bi bi-mortarboard-fill fb"></i>
            </div>
            <h2>Selamat<br>Datang!</h2>
            <p>Sistem Informasi Akademik<br>SMK Sunan Giri</p>
            <div class="left-extra">@yield('left-extra')</div>
            <a href="#" class="m-cta" id="mobileBtn">
                <span>Masuk ke Akun</span>
                <span class="m-dot"><i class="bi bi-chevron-down"></i></span>
            </a>
        </div>
    </div>

    <!-- ═══ RIGHT ═══ -->
    <div class="auth-right" id="authPanel">
        <span class="sheet-bar"></span>
        <button class="sheet-back" id="sheetBack">
            <i class="bi bi-arrow-left-circle-fill" style="font-size:1.05rem;"></i> Kembali
        </button>

        @yield('content')

        <!-- Small dot grid bottom-right like reference -->
        <div class="right-dots" style="position:absolute;">
            <span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span>
        </div>
    </div>
</div>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script>
(function(){
    var btn   = document.getElementById('mobileBtn');
    var panel = document.getElementById('authPanel');
    var back  = document.getElementById('sheetBack');
    function mob(){ return window.innerWidth <= 768; }
    function open(){ panel.classList.add('open'); document.body.style.overflow='hidden'; }
    function close(){ panel.classList.remove('open'); document.body.style.overflow=''; }
    btn  && btn.addEventListener('click', function(e){ e.preventDefault(); mob() && open(); });
    back && back.addEventListener('click', close);
    var ty = 0;
    panel && panel.addEventListener('touchstart', function(e){ ty = e.changedTouches[0].screenY; }, {passive:true});
    panel && panel.addEventListener('touchend', function(e){
        if (e.changedTouches[0].screenY - ty > 72 && panel.scrollTop === 0) close();
    }, {passive:true});
    window.addEventListener('resize', function(){ if (!mob()) close(); });

    // Auto-open panel on mobile if there are validation errors, flash messages, or old input
    @if($errors->any() || session('error') || session('success') || old('email') || old('nisn'))
        if(mob()) open();
    @endif
})();

// Register Service Worker for PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').then(function(registration) {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function(err) {
            console.log('ServiceWorker registration failed: ', err);
        });
    });
}
</script>
@stack('scripts')
</body>
</html>
