<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIAKAD - SMK Sunan Giri') | SMK Sunan Giri</title>

    <!-- PWA Manifest & Favicon -->
    <link rel="manifest" href="/manifest.json?v={{ time() }}">
    <meta name="theme-color" content="#1e293b">
    <link rel="icon" type="image/png" href="/images/logo-sekolah.png">
    <link rel="apple-touch-icon" href="/images/logo-sekolah.png">

    <!-- DNS Prefetch & Preconnect untuk percepatan CDN -->
    
    
    

    <!-- Dark Mode Init -->
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark-mode'); // fallback
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
    </script>

    <!-- Bootstrap -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

    <style>
        :root {
            --primary-color: #1e293b;
            --secondary-color: #334155;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-color: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            background-image: 
                linear-gradient(rgba(59, 130, 246, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
            background-position: center center;
            background-attachment: fixed;
        }

        /* ── Sidebar ── */
        .sidebar {
            background-color: var(--primary-color);
            transition: all 0.3s ease;
            /* Fixed sidebar — stays in place while main content scrolls */
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;  /* No horizontal scroll */
            z-index: 100;
            width: 220px;        /* Fixed pixel width, more reliable */
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 8px 14px;
            border-radius: 8px;
            margin: 2px 6px;
            transition: 0.25s;
            font-size: 0.85rem;
            /* Allow text to wrap naturally */
            white-space: normal;
            word-break: break-word;
            display: flex;
            align-items: flex-start;
            gap: 6px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--accent-color);
            color: white;
        }

        .sidebar .nav-link[aria-expanded="true"] {
            color: rgba(255,255,255,0.9);
        }

        .sidebar .nav-link i {
            width: 18px;
            flex-shrink: 0;
            text-align: center;
            padding-top: 2px;
            color: inherit !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* Sidebar section labels */
        .sidebar .sidebar-label {
            font-size: 0.65rem;
            letter-spacing: 1px;
            padding: 6px 14px 2px;
            white-space: normal;
            word-break: break-word;
        }

        /* Submenu (collapse) */
        .sidebar .sub-menu .nav-link {
            padding: 5px 12px;
            font-size: 0.80rem;
            color: rgba(255,255,255,0.6);
            margin: 1px 0;
            border-radius: 6px;
        }

        .sidebar .sub-menu .nav-link:hover {
            color: #fff;
            background-color: rgba(52,152,219,0.35);
        }

        /* Chevron rotation on collapse toggle */
        .sidebar .collapse-toggle .chevron-icon {
            transition: transform 0.3s ease;
        }

        .sidebar .collapse-toggle[aria-expanded="true"] .chevron-icon {
            transform: rotate(180deg);
        }

        /* ── Modern Header & Footer ── */
        .app-header {
            position: sticky;
            top: 0;
            z-index: 1025;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 24px;
            /* Negative margins to pull it flush against the edges of the main-content */
            margin: -20px -1.5rem 24px -1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* On mobile: make header truly fixed so it doesn't scroll away */
        @media (max-width: 767px) {
            .app-header {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                margin: 0 !important;
                z-index: 1025 !important;
                padding: 12px 16px;
                transform: translateZ(0);
                -webkit-transform: translateZ(0);
            }
            /* Push main content down so it's not hidden behind fixed header */
            .main-content {
                padding-top: 70px !important;
            }
        }

        .app-header-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }

        .header-search {
            border-radius: 50px;
            border: 1px solid #cbd5e1;
            padding: 6px 16px;
            font-size: 0.85rem;
            width: 220px;
            outline: none;
            color: #334155;
        }
        @media (max-width: 767px) {
            .header-search { display: none !important; }
            .app-header-title { font-size: 1rem !important; }
        }
        
        .header-search:focus {
            border-color: #94a3b8;
        }

        .header-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #64748b;
            cursor: pointer;
            transition: 0.2s;
        }

        .header-icon:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .app-footer {
            margin-top: 40px;
            padding: 20px 0;
            text-align: center;
            color: #64748b;
            font-size: 0.85rem;
            border-top: 1px dashed rgba(0,0,0,0.1);
        }
        
        .app-footer a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
        }

        html.dark-mode .bottom-nav {
            background-color: #1e293b;!important;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.5) !important;
        }
        html.dark-mode .bottom-nav-item {
            color: #94a3b8 !important;
        }
        html.dark-mode .bottom-nav-item.active {
            color: #38bdf8 !important;
        }

        /* ── Orang Tua Module Dark Mode ── */
        html.dark-mode .ot-menu-card,
        html.dark-mode .ot-notif-item,
        html.dark-mode .ot-card,
        html.dark-mode .ot-info-card,
        html.dark-mode .ot-page-header,
        html.dark-mode .ot-nilai-card,
        html.dark-mode .ot-perm-card {
            background-color: #1e293b;!important;
            color: #f8fafc !important;
            border-color: rgba(255,255,255,0.1) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }
        
        html.dark-mode .ot-page-icon,
        html.dark-mode .ot-nilai-icon,
        html.dark-mode .ot-perm-icon,
        html.dark-mode .icon-green,
        html.dark-mode .icon-amber,
        html.dark-mode .icon-blue,
        html.dark-mode .icon-gray,
        html.dark-mode .ot-chip {
            background-color: #334155;!important;
        }

        html.dark-mode .ot-notif-title,
        html.dark-mode .ot-notif-body,
        html.dark-mode .ot-page-title,
        html.dark-mode .ot-menu-label,
        html.dark-mode .ot-row-label,
        html.dark-mode .ot-row-val,
        html.dark-mode .ot-nilai-mapel,
        html.dark-mode .ot-empty i,
        html.dark-mode .ot-empty p {
            color: #f8fafc !important;
        }
        
        html.dark-mode .ot-notif-time,
        html.dark-mode .ot-page-sub,
        html.dark-mode .ot-nilai-kode,
        html.dark-mode .ot-empty {
            color: #94a3b8 !important;
        }
        
        /* ── Dark Mode Form ── */
        html.dark-mode {
            --bs-primary-bg-subtle: rgba(59, 130, 246, 0.15) !important;
            --bs-secondary-bg-subtle: rgba(255, 255, 255, 0.05) !important;
            --bs-success-bg-subtle: rgba(22, 163, 74, 0.15) !important;
            --bs-info-bg-subtle: rgba(6, 182, 212, 0.15) !important;
            --bs-warning-bg-subtle: rgba(245, 158, 11, 0.15) !important;
            --bs-danger-bg-subtle: rgba(239, 68, 68, 0.15) !important;
            --bs-light-bg-subtle: rgba(255, 255, 255, 0.03) !important;
            --bs-dark-bg-subtle: rgba(255, 255, 255, 0.15) !important;
            
            --bs-light: #1e293b !important;
            --bs-white: #0f172a !important;
            
            --custom-card-bg: #1e293b !important;
            --custom-header-bg: #0f172a !important;
            --custom-text: #cbd5e1 !important;
            --custom-border: rgba(255, 255, 255, 0.1) !important;
        }

        /* Override non-adapting Bootstrap utilities */
        html.dark-mode .bg-white { background-color: #0f172a !important; }
        html.dark-mode .bg-light { background-color: #1e293b !important; }
        html.dark-mode .text-dark { color: #f8fafc !important; }
        html.dark-mode .table-light,
        html.dark-mode .table-light > td,
        html.dark-mode .table-light > th { background-color: #1e293b !important; color: #f8fafc !important; }
        html.dark-mode .border { border-color: rgba(255,255,255,0.1) !important; }
        html.dark-mode .text-muted { color: #94a3b8 !important; }
        html.dark-mode .btn-light, html.dark-mode .btn-white { background-color: rgba(255, 255, 255, 0.1) !important; color: #f8fafc !important; border-color: rgba(255, 255, 255, 0.2) !important; }
        html.dark-mode .btn-light:hover, html.dark-mode .btn-white:hover { background-color: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important; }
        
        /* BRUTE FORCE DARK MODE (As requested) */
        [data-bs-theme="dark"] .card, 
        [data-bs-theme="dark"] .bg-white, 
        [data-bs-theme="dark"] .bg-light,
        [data-bs-theme="dark"] .table, 
        [data-bs-theme="dark"] .table-light,
        html.dark-mode .card,
        html.dark-mode .bg-white,
        html.dark-mode .table,
        html.dark-mode .table-light {
            background-color: #1e293b !important;
            color: #ffffff !important;
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] body, 
        [data-bs-theme="dark"] h1, 
        [data-bs-theme="dark"] h2, 
        [data-bs-theme="dark"] h3, 
        [data-bs-theme="dark"] h4, 
        [data-bs-theme="dark"] h5, 
        [data-bs-theme="dark"] h6, 
        [data-bs-theme="dark"] p, 
        [data-bs-theme="dark"] span, 
        [data-bs-theme="dark"] td, 
        [data-bs-theme="dark"] th,
        html.dark-mode body,
        html.dark-mode h1,
        html.dark-mode h2,
        html.dark-mode h3,
        html.dark-mode h4,
        html.dark-mode h5,
        html.dark-mode h6,
        html.dark-mode p,
        html.dark-mode span,
        html.dark-mode td,
        html.dark-mode th {
            color: #f1f5f9 !important;
        }

        /* Prevent buttons and specific colored badges from losing their color */
        html.dark-mode .btn, [data-bs-theme="dark"] .btn,
        html.dark-mode .badge, [data-bs-theme="dark"] .badge,
        html.dark-mode i, [data-bs-theme="dark"] i {
            color: inherit; /* Let Bootstrap or specific classes handle these */
        }
        
        /* Specific overrides to prevent icons from turning white */
        html.dark-mode .text-warning, [data-bs-theme="dark"] .text-warning { color: #fbbf24 !important; }
        html.dark-mode .text-danger, [data-bs-theme="dark"] .text-danger { color: #f87171 !important; }
        html.dark-mode .text-success, [data-bs-theme="dark"] .text-success { color: #4ade80 !important; }
        html.dark-mode .text-info, [data-bs-theme="dark"] .text-info { color: #38bdf8 !important; }
        html.dark-mode .text-primary, [data-bs-theme="dark"] .text-primary { color: #60a5fa !important; }

        html.dark-mode body {
            background-color: #0f172a;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            color: #f8fafc;
        }
        html.dark-mode .app-header {
            background: #1e293b;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        html.dark-mode .app-header-title {
            color: #f8fafc;
        }
        html.dark-mode .card,
        html.dark-mode .app-footer,
        html.dark-mode .dropdown-menu {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border-color: rgba(255,255,255,0.1) !important;
        }
        html.dark-mode .card-header,
        html.dark-mode .dropdown-menu .bg-light {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            border-color: rgba(255,255,255,0.1) !important;
        }
        html.dark-mode .table,
        html.dark-mode .table td,
        html.dark-mode .table th,
        html.dark-mode .table tbody tr {
            background-color: transparent !important;
            color: #e2e8f0 !important;
            border-color: rgba(255,255,255,0.1) !important;
        }
        html.dark-mode .table-light,
        html.dark-mode .table-light th,
        html.dark-mode .table-light td {
            background-color: #0f172a;!important;
            color: #f8fafc !important;
            border-color: rgba(255,255,255,0.1) !important;
        }
        html.dark-mode .table-secondary,
        html.dark-mode .table-secondary th,
        html.dark-mode .table-secondary td {
            background-color: #1e293b;!important;
            color: #f8fafc !important;
            border-color: rgba(255,255,255,0.1) !important;
        }
        html.dark-mode .table-striped>tbody>tr:nth-of-type(odd)>* {
            color: #e2e8f0 !important;
        }
        html.dark-mode div.dataTables_wrapper div.dataTables_length, 
        html.dark-mode div.dataTables_wrapper div.dataTables_filter, 
        html.dark-mode div.dataTables_wrapper div.dataTables_info, 
        html.dark-mode div.dataTables_wrapper div.dataTables_paginate {
            color: #f8fafc !important;
        }
        html.dark-mode .page-link {
            background-color: #334155;!important;
            border-color: rgba(255,255,255,0.1) !important;
            color: #f8fafc !important;
        }
        html.dark-mode .page-item.disabled .page-link {
            background-color: #1e293b;!important;
            color: #64748b !important;
        }
        html.dark-mode .page-item.active .page-link {
            background-color: var(--accent-color) !important;
            border-color: var(--accent-color) !important;
            color: #fff !important;
        }
        html.dark-mode .table-responsive {
            background-color: transparent !important;
        }
        html.dark-mode .table-hover tbody tr:hover,
        html.dark-mode .table-hover tbody tr:hover td {
            background-color: #334155;!important;
            color: #ffffff !important;
        }
        html.dark-mode .text-dark,
        html.dark-mode .text-secondary {
            color: #f8fafc !important;
        }
        html.dark-mode .btn-light,
        html.dark-mode .profile-btn {
            background-color: #334155;!important;
            color: #f8fafc !important;
            border-color: #334155;!important;
        }
        html.dark-mode .text-muted {
            color: #94a3b8 !important;
        }
        html.dark-mode .bg-light,
        html.dark-mode .bg-white {
            background-color: #1e293b;!important;
            color: #f8fafc !important;
        }
        
        /* Global override for bright inline styles in dark mode */
        html.dark-mode [style*="background:#f1f5f9"], html.dark-mode [style*="background-color:#f1f5f9"],
        html.dark-mode [style*="background:#f8fafc"], html.dark-mode [style*="background-color:#f8fafc"],
        html.dark-mode [style*="background: #ffffff"], html.dark-mode [style*="background-color: #ffffff"],
        html.dark-mode [style*="background:#ffffff"], html.dark-mode [style*="background-color:#ffffff"],
        html.dark-mode [style*="background:#ffffff"], html.dark-mode [style*="background-color:#ffffff"] {
            background-color: #1e293b;!important;
            color: #f8fafc !important;
            border-color: rgba(255,255,255,0.1) !important;
        }

        html.dark-mode [style*="background:#e2e8f0"], html.dark-mode [style*="background-color:#e2e8f0"] {
            background-color: #334155;!important;
            color: #cbd5e1 !important;
            border-color: rgba(255,255,255,0.1) !important;
        }

        /* Blue badges/buttons */
        html.dark-mode [style*="background:#dbeafe"], html.dark-mode [style*="background-color:#dbeafe"],
        html.dark-mode [style*="background:#eff6ff"], html.dark-mode [style*="background-color:#eff6ff"] {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #60a5fa !important;
            border-color: rgba(96, 165, 250, 0.2) !important;
        }

        /* Green badges/buttons */
        html.dark-mode [style*="background:#f0fdf4"], html.dark-mode [style*="background-color:#f0fdf4"],
        html.dark-mode [style*="background:#dcfce7"], html.dark-mode [style*="background-color:#dcfce7"] {
            background-color: rgba(22, 163, 74, 0.15) !important;
            color: #4ade80 !important;
            border-color: rgba(74, 222, 128, 0.2) !important;
        }

        /* Red badges/buttons */
        html.dark-mode [style*="background:#ffffff1f2"], html.dark-mode [style*="background-color:#ffffff1f2"],
        html.dark-mode [style*="background:#fef2f2"], html.dark-mode [style*="background-color:#fef2f2"] {
            background-color: rgba(225, 29, 72, 0.15) !important;
            color: #fb7185 !important;
            border-color: rgba(251, 113, 133, 0.2) !important;
        }

        /* Yellow badges/buttons */
        html.dark-mode [style*="background:#fefce8"], html.dark-mode [style*="background-color:#fefce8"],
        html.dark-mode [style*="background:#ffffffbeb"], html.dark-mode [style*="background-color:#ffffffbeb"] {
            background-color: rgba(202, 138, 4, 0.15) !important;
            color: #facc15 !important;
            border-color: rgba(250, 204, 21, 0.2) !important;
        }

        /* Fix inline hardcoded text colors */
        html.dark-mode [style*="color:#1d4ed8"], html.dark-mode [style*="color:#3b82f6"] { color: #60a5fa !important; }
        html.dark-mode [style*="color:#15803d"], html.dark-mode [style*="color:#16a34a"] { color: #4ade80 !important; }
        html.dark-mode [style*="color:#b91c1c"], html.dark-mode [style*="color:#e11d48"], html.dark-mode [style*="color:#ef4444"] { color: #fb7185 !important; }
        html.dark-mode [style*="color:#ca8a04"], html.dark-mode [style*="color:#d97706"], html.dark-mode [style*="color:#f59e0b"] { color: #facc15 !important; }
        html.dark-mode [style*="color: #475569;"], html.dark-mode [style*="color:#64748b"] { color: #cbd5e1 !important; }
        html.dark-mode [style*="color: #1e293b;"], html.dark-mode [style*="color: #0f172a;"], html.dark-mode [style*="color:#1a2340"] { color: #f8fafc !important; }
        html.dark-mode .header-icon {
            color: #cbd5e1 !important;
            background-color: #334155;!important;
        }
        html.dark-mode .header-icon:hover {
            background-color: #475569;!important;
        }
        html.dark-mode .dropdown-menu .dropdown-item {
            color: #cbd5e1;
        }
        html.dark-mode .dropdown-menu .dropdown-item:hover {
            background-color: #334155;
            color: #ffffff;
        }
        html.dark-mode .dropdown-divider {
            border-top-color: rgba(255,255,255,0.1);
        }
        html.dark-mode .border-light {
            border-color: rgba(255,255,255,0.1) !important;
        }
        html.dark-mode .modal-content {
            background-color: #1e293b;
            color: #f8fafc;
        }
        html.dark-mode .modal-header, html.dark-mode .modal-footer {
            border-color: rgba(255,255,255,0.1);
        }
        html.dark-mode .form-control, html.dark-mode .form-select {
            background-color: #334155;
            border-color: rgba(255,255,255,0.1);
            color: #f8fafc;
        }
        html.dark-mode .form-control:focus, html.dark-mode .form-select:focus {
            background-color: #475569;
            color: #ffffff;
            border-color: var(--accent-color);
        }

        /* ── Main Content ── */
        .main-content {
            padding: 20px;
        }

        /* Push main content to the right since sidebar is fixed (220px wide) */
        @media (min-width: 768px) {
            .main-content-wrapper {
                margin-left: 220px;
                width: calc(100% - 220px) !important;
            }
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-1px);
        }

        .card-header {
            background-color: var(--light-color);
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .table th {
            background-color: var(--light-color);
            font-weight: 600;
        }

        .alert {
            border: none;
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52,152,219,0.25);
        }

        /* Stats cards */
        .stats-card {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            border-radius: 12px;
            padding: 20px;
        }

        .stats-card .stats-number { font-size: 2rem; font-weight: 700; }
        .stats-card .stats-label  { font-size: 0.9rem; opacity: 0.9; }

        /* Grade inputs */
        .grade-input {
            width: 80px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 4px 8px;
        }

        .grade-input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52,152,219,0.25);
        }

        /* Excel table */
        .excel-like-table { border-collapse: separate; border-spacing: 0; }
        .excel-like-table th, .excel-like-table td { border: 1px solid #dee2e6; padding: 8px; vertical-align: middle; }
        .excel-like-table th { background-color: var(--light-color); font-weight: 600; text-align: center; }
        .excel-like-table tr:hover td { background-color: #f8f9fa; }

        /* Loading overlay */
        .loading { opacity: 0.6; pointer-events: none; }

        /* Detail modal */
        .detail-modal-body .table { margin-bottom: 0; }

        /* ── Action Buttons (Konsisten di semua tabel) ── */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.18s ease;
            font-size: 0.88rem;
            text-decoration: none;
            flex-shrink: 0;
        }
        .btn-action:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        .btn-action:active { transform: translateY(0); }

        .btn-action-view   { background: #e0f2fe; color: #0369a1; }
        .btn-action-view:hover  { background: #0ea5e9; color: #fff; }
        .btn-action-edit   { background: #e0e7ff; color: #4338ca; }
        .btn-action-edit:hover  { background: #6366f1; color: #fff; }
        .btn-action-delete { background: #fee2e2; color: #dc2626; }
        .btn-action-delete:hover { background: #ef4444; color: #fff; }
        .btn-action-custom { background: #dcfce7; color: #16a34a; }
        .btn-action-custom:hover { background: #22c55e; color: #fff; }

        /* Dark Mode Overrides for Action Buttons */
        html.dark-mode .btn-action-view { background: rgba(14, 165, 233, 0.15) !important; color: #38bdf8 !important; }
        html.dark-mode .btn-action-view:hover { background: #0ea5e9 !important; color: #fff !important; }
        
        html.dark-mode .btn-action-edit { background: rgba(99, 102, 241, 0.15) !important; color: #818cf8 !important; }
        html.dark-mode .btn-action-edit:hover { background: #6366f1 !important; color: #fff !important; }
        
        html.dark-mode .btn-action-delete { background: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; }
        html.dark-mode .btn-action-delete:hover { background: #ef4444 !important; color: #fff !important; }
        
        html.dark-mode .btn-action-custom { background: rgba(34, 197, 94, 0.15) !important; color: #4ade80 !important; }
        html.dark-mode .btn-action-custom:hover { background: #22c55e !important; color: #fff !important; }

        .action-group { display: flex; align-items: center; gap: 6px; justify-content: center; }

        /* ── Top Page Progress Bar ── */
        #page-progress {
            position: fixed;
            top: 0; left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            z-index: 9999;
            transition: width 0.25s ease, opacity 0.4s ease;
            border-radius: 0 3px 3px 0;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar {
                display: none !important;
            }

            .sidebar-overlay {
                display: none !important;
            }

            .sidebar-overlay.show {
                display: none !important;
            }

            .main-content-wrapper {
                margin-left: 0 !important;
            }

            .main-content {
                padding: 10px;
            }

            .stats-card .stats-number {
                font-size: 1.5rem;
            }

            .bottom-nav { 
                display: flex !important; 
                justify-content: flex-start; 
                align-items: center; 
                overflow-x: auto;
                scrollbar-width: none; /* Firefox */
                padding: 0 10px;
                scroll-behavior: smooth;
                -webkit-overflow-scrolling: touch;
            }
            .bottom-nav::-webkit-scrollbar { display: none; /* Chrome/Safari */ }
            body { padding-bottom: 70px; } /* Space for bottom nav */
            .d-md-none.bg-dark { display: none !important; } /* Hide top header */
            /* #sidebar is naturally hidden by left: -260px, do not use display: none so it can animate */
        }

        @media (max-width: 576px) {
            .main-content { padding: 8px; }
        }

        /* ── Bottom Navigation Bar (Mobile Only) ── */
        .bottom-nav {
            display: none;
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: #ffffff;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            z-index: 9999 !important; /* Highest z-index so nothing overlaps */
            padding: 8px 0;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            /* Prevent any parent transform/overflow from breaking fixed position */
            will-change: auto;
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
        }
        
        .bottom-nav-item {
            text-align: center;
            color: #64748b;
            text-decoration: none;
            font-size: 0.7rem;
            flex: 1 0 70px;
            padding: 8px 4px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .bottom-nav-item i { font-size: 1.3rem; margin-bottom: 2px; }
        .bottom-nav-item.active { color: #0284c7; font-weight: 700; }
        .bottom-nav-item.active i { transform: scale(1.1); }
        .bottom-nav-badge {
            position: absolute; top: -2px; right: 20%;
            background: #ef4444; color: white; border-radius: 50%;
            font-size: 0.6rem; font-weight: bold; width: 16px; height: 16px;
            display: flex; align-items: center; justify-content: center;
        }

        /* Guest layout */
        .guest-layout { padding: 20px; }
        @media (max-width: 576px) { .guest-layout { padding: 15px; } }
        @media (min-width: 1400px) { .guest-layout .col-xl-5 { max-width: 450px; } }

        /* Universal mobile table responsiveness */
        @media (max-width: 768px) {
            .table-responsive, 
            .card-body, 
            .dataTables_wrapper {
                max-width: 100%;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            /* Reset table layout if it was set to fixed or breaking */
            table.table {
                width: 100% !important;
            }
        }
    </style>
</head>

<body class="{{ auth()->check() && auth()->user()->role === 'orang_tua' ? 'is-orangtua' : '' }}">

<!-- Page Loading Progress Bar -->
<div id="page-progress"></div>

@auth

{{-- Mobile sidebar overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="container-fluid">
    <div class="row">

        {{-- Mobile toggle button --}}
        <div class="d-md-none bg-dark text-white p-2 d-flex align-items-center">
            <button class="btn btn-sm btn-outline-light me-2" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <strong>SIAKAD</strong> <small class="ms-2 text-white-50">SMK Sunan Giri</small>
        </div>

        <!-- ========== SIDEBAR ========== -->
        <nav class="d-md-block sidebar" id="sidebar">
            {{-- Mobile Close Button --}}
            <button class="btn text-white d-md-none position-absolute top-0 end-0 mt-2 me-3 border-0" onclick="$('#sidebar').removeClass('show'); $('#sidebarOverlay').removeClass('show');" style="z-index: 1050; font-size: 1.2rem; background: transparent; padding: 5px;">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="position-sticky pt-3">

                <div class="text-center mb-4 d-none d-md-block pt-4 pb-3">
                    <h4 class="text-white mb-1 fw-bold" style="letter-spacing: 1px;">SIAKAD</h4>
                    <small class="text-white-50 d-block" style="font-size: 0.85rem;">SMK Sunan Giri</small>
                </div>

                <ul class="nav flex-column">

                    {{-- ═══════════════ ADMIN & KURIKULUM ═══════════════ --}}
                    @if(auth()->user()->isAdmin() || auth()->user()->role === 'kurikulum')

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>

                        <!-- 1. KELOLA DATA MASTER -->
                        <li class="nav-item mt-2">
                            <a class="nav-link collapsed d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#collapseDataMaster" role="button" aria-expanded="false" aria-controls="collapseDataMaster">
                                <div><i class="bi bi-database me-2"></i> Data Master</div>
                                <i class="bi bi-chevron-down" style="font-size: 0.85rem;"></i>
                            </a>
                            <div class="collapse {{ request()->is('admin/guru*') || request()->is('admin/siswa*') || request()->is('admin/kelas*') || request()->is('admin/mapel*') || request()->is('admin/tahun-ajaran*') || request()->is('admin/wali-kelas*') || request()->is('admin/konsentrasi-keahlian*') || request()->is('admin/ekstrakurikuler*') || request()->is('admin/ruangan*') ? 'show' : '' }}" id="collapseDataMaster">
                                <ul class="nav flex-column ms-3" style="border-left: 1px solid rgba(255,255,255,0.1);">
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}" href="{{ route('admin.guru.index') }}">Guru</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}" href="{{ route('admin.siswa.index') }}">Siswa</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}" href="{{ route('admin.kelas.index') }}">Kelas</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.ruangan.*') ? 'active' : '' }}" href="{{ route('admin.ruangan.index') }}">Ruangan</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.mapel.*') ? 'active' : '' }}" href="{{ route('admin.mapel.index') }}">Mata Pelajaran</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.tahun-ajaran.*') ? 'active' : '' }}" href="{{ route('admin.tahun-ajaran.index') }}">Tahun Ajaran</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.wali-kelas.*') ? 'active' : '' }}" href="{{ route('admin.wali-kelas.index') }}">Wali Kelas</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.konsentrasi-keahlian.*') ? 'active' : '' }}" href="{{ route('admin.konsentrasi-keahlian.index') }}">Jurusan</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.ekstrakurikuler.*') ? 'active' : '' }}" href="{{ route('admin.ekstrakurikuler.index') }}">Ekstrakurikuler</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- 2. KELOLA PEMBELAJARAN -->
                        <li class="nav-item mt-2">
                            <a class="nav-link collapsed d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#collapsePembelajaran" role="button" aria-expanded="false" aria-controls="collapsePembelajaran">
                                <div><i class="bi bi-book me-2"></i> Pembelajaran</div>
                                <i class="bi bi-chevron-down" style="font-size: 0.85rem;"></i>
                            </a>
                            <div class="collapse {{ request()->is('admin/jadwal*') || request()->is('admin/guru-mapel*') ? 'show' : '' }}" id="collapsePembelajaran">
                                <ul class="nav flex-column ms-3" style="border-left: 1px solid rgba(255,255,255,0.1);">
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}" href="{{ route('admin.jadwal.index') }}">Jadwal Pelajaran</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.guru-mapel.*') ? 'active' : '' }}" href="{{ route('admin.guru-mapel.index') }}">Penugasan Guru Mapel</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- KELOLA RAPOR (ADMIN ACCESS) -->
                        <li class="nav-item mt-2">
                            <a class="nav-link {{ request()->routeIs('admin.rapor.*') ? 'active' : '' }}" href="{{ route('admin.rapor.index') }}">
                                <i class="bi bi-file-earmark-pdf"></i> E-Rapor Siswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.leger.*') ? 'active' : '' }}" href="{{ route('admin.leger.select-kelas') }}">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Leger Nilai
                            </a>
                        </li>

                        <!-- KENAIKAN KELAS -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.kenaikan-kelas.*') ? 'active' : '' }}" href="{{ route('admin.kenaikan-kelas.index') }}">
                                <i class="bi bi-arrow-up-circle"></i> Kenaikan Kelas
                            </a>
                        </li>

                        <!-- LOG & SISTEM -->
                        <li class="nav-item mt-4">
                            <small class="text-white-50 sidebar-label text-uppercase fw-bold">
                                <i class="bi bi-shield-lock me-1"></i> Log & Sistem
                            </small>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.setting.*') ? 'active' : '' }}" href="{{ route('admin.setting.index') }}">
                                <i class="bi bi-gear"></i> Pengaturan Sistem
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.log-activity.*') ? 'active' : '' }}" href="{{ route('admin.log-activity.index') }}">
                                <i class="bi bi-clock-history"></i> Riwayat Aktivitas
                            </a>
                        </li>
                        @if(auth()->user()->role === 'superadmin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.log-error.*') ? 'active' : '' }}" href="{{ route('admin.log-error.index') }}">
                                <i class="bi bi-bug"></i> Log Error
                            </a>
                        </li>
                        @endif

                    {{-- ═══════════════ WALI KELAS ═══════════════ --}}
                    @elseif(auth()->user()->isWaliKelas())

                        @php
                            $guruData  = auth()->user()->guru;
                            $wkKelas   = $guruData ? \App\Models\Kelas::where('id_wali_kelas', $guruData->id_guru)->first() : null;
                            $kelasList = $guruData ? \App\Models\GuruMapel::with('kelas')->where('id_guru', $guruData->id_guru)->where('status', 'aktif')->get()->pluck('kelas')->unique('id_kelas')->sortBy('nama_kelas') : collect();
                        @endphp

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('walikelas.dashboard') ? 'active' : '' }}" href="{{ route('walikelas.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>

                        @if($wkKelas)
                            <!-- 4. KELOLA RAPOR -->
                            <li class="nav-item mt-2">
                                <a class="nav-link collapsed d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#collapseKelolaRapor" role="button" aria-expanded="false" aria-controls="collapseKelolaRapor">
                                    <div><i class="bi bi-star-fill me-2"></i> Kelola Rapor ({{ $wkKelas->nama_kelas }})</div>
                                    <i class="bi bi-chevron-down" style="font-size: 0.85rem;"></i>
                                </a>
                                <div class="collapse {{ request()->is('walikelas/nilai*') || request()->is('walikelas/kokurikuler*') || request()->is('walikelas/ekstrakurikuler*') || request()->is('walikelas/catatan*') || request()->is('walikelas/rapor*') || request()->is('walikelas/leger*') || request()->is('walikelas/ranking*') ? 'show' : '' }}" id="collapseKelolaRapor">
                                    <ul class="nav flex-column ms-3" style="border-left: 1px solid rgba(255,255,255,0.1);">
                                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('walikelas.nilai.*') ? 'active' : '' }}" href="{{ route('walikelas.nilai.index', $wkKelas->id_kelas) }}">Rekap Nilai</a></li>
                                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('walikelas.kokurikuler.*') ? 'active' : '' }}" href="{{ route('walikelas.kokurikuler.index', $wkKelas->id_kelas) }}">Kokurikuler (P5)</a></li>
                                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('walikelas.ekstrakurikuler.*') ? 'active' : '' }}" href="{{ route('walikelas.ekstrakurikuler.index', $wkKelas->id_kelas) }}">Ekstrakurikuler</a></li>
                                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('walikelas.catatan.*') ? 'active' : '' }}" href="{{ route('walikelas.catatan.index', $wkKelas->id_kelas) }}">Catatan Wali Kelas</a></li>
                                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('walikelas.rapor.*') ? 'active' : '' }}" href="{{ route('walikelas.rapor.select-kelas') }}">E-Rapor</a></li>
                                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('walikelas.leger.*') ? 'active' : '' }}" href="{{ route('walikelas.leger.select-kelas') }}">Leger Nilai</a></li>
                                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('walikelas.ranking.*') ? 'active' : '' }}" href="{{ route('walikelas.ranking.select-kelas') }}">Ranking</a></li>
                                    </ul>
                                </div>
                            </li>
                        @endif

                        <!-- 2. KELOLA PEMBELAJARAN (SEBAGAI GURU) -->
                        <li class="nav-item mt-2">
                            <a class="nav-link collapsed d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#collapseGuruPemb" role="button" aria-expanded="false" aria-controls="collapseGuruPemb">
                                <div><i class="bi bi-book me-2"></i> Pembelajaran</div>
                                <i class="bi bi-chevron-down" style="font-size: 0.85rem;"></i>
                            </a>
                            <div class="collapse {{ request()->is('guru/tp*') ? 'show' : '' }}" id="collapseGuruPemb">
                                <ul class="nav flex-column ms-3" style="border-left: 1px solid rgba(255,255,255,0.1);">
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('guru.tp.*') ? 'active' : '' }}" href="{{ route('guru.tp.select-kelas') }}">Tujuan Pembelajaran (TP)</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- 3. KELOLA PENILAIAN (SEBAGAI GURU) -->
                        @if($kelasList->count() > 0)
                        <li class="nav-item mt-2">
                            <a class="nav-link collapsed d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#collapseGuruPenilaian" role="button" aria-expanded="false" aria-controls="collapseGuruPenilaian">
                                <div><i class="bi bi-pencil-square me-2"></i> Kelola Penilaian</div>
                                <i class="bi bi-chevron-down" style="font-size: 0.85rem;"></i>
                            </a>
                            <div class="collapse {{ request()->is('guru/nilai*') ? 'show' : '' }}" id="collapseGuruPenilaian">
                                <ul class="nav flex-column ms-3" style="border-left: 1px solid rgba(255,255,255,0.1);">
                                    @foreach($kelasList as $kls)
                                        <li class="nav-item"><a class="nav-link {{ (request()->is('guru/nilai/select-mapel/'.$kls->id_kelas) || request()->is('guru/nilai/input/'.$kls->id_kelas.'/*') || request()->is('guru/nilai/summary/'.$kls->id_kelas.'/*')) ? 'active' : '' }}" href="{{ route('guru.nilai.select-mapel', $kls->id_kelas) }}"><i class="bi bi-mortarboard" style="font-size: 0.85rem;"></i> Nilai {{ $kls->nama_kelas }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                        @endif

                    {{-- ═══════════════ GURU MAPEL ═══════════════ --}}
                    @elseif(auth()->user()->isGuru())

                        @php
                            $guruData      = auth()->user()->guru;
                            $guruKelasList = $guruData ? \App\Models\GuruMapel::with('kelas')->where('id_guru', $guruData->id_guru)->where('status', 'aktif')->get()->pluck('kelas')->unique('id_kelas')->sortBy('nama_kelas') : collect();
                        @endphp

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" href="{{ route('guru.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>

                        <li class="nav-item mt-1">
                            <a class="nav-link {{ request()->routeIs('guru.tp.*') ? 'active' : '' }}" href="{{ route('guru.tp.select-kelas') }}">
                                <i class="bi bi-book"></i> Tujuan Pembelajaran (TP)
                            </a>
                        </li>

                        @if($guruKelasList->count() > 0)
                            <li class="nav-item mt-2 mb-1 px-3">
                                <small class="text-white-50 fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">KELOLA PENILAIAN</small>
                            </li>
                            @foreach($guruKelasList as $kls)
                                <li class="nav-item">
                                    <a class="nav-link {{ (request()->is('guru/nilai/select-mapel/'.$kls->id_kelas) || request()->is('guru/nilai/input/'.$kls->id_kelas.'/*') || request()->is('guru/nilai/summary/'.$kls->id_kelas.'/*')) ? 'active' : '' }}" href="{{ route('guru.nilai.select-mapel', $kls->id_kelas) }}">
                                        <i class="bi bi-mortarboard"></i> Nilai {{ $kls->nama_kelas }}
                                    </a>
                                </li>
                            @endforeach
                        @endif

                    {{-- ═══════════════ ORANG TUA ═══════════════ --}}
                    @elseif(auth()->user()->isOrangTua())

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('orangtua.dashboard') ? 'active' : '' }}" href="{{ route('orangtua.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>

                        <li class="nav-item mt-2">
                            <a class="nav-link {{ request()->routeIs('orangtua.rapor.*') ? 'active' : '' }}" href="{{ route('orangtua.rapor.index') }}">
                                <i class="bi bi-journal-bookmark-fill"></i> E-Rapor
                            </a>
                        </li>
                        
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link flex-grow-1 {{ request()->routeIs('orangtua.notifications.*') ? 'active' : '' }}" href="{{ route('orangtua.notifications.index') }}">
                                <i class="bi bi-bell"></i> Notifikasi
                                @php $notifUnread = auth()->check() ? \App\Models\UserNotification::where('user_id', auth()->id())->where('is_read', false)->count() : 0; @endphp
                                @if($notifUnread > 0)
                                    <span class="badge bg-danger rounded-pill ms-2" style="font-size:0.65rem;">{{ $notifUnread }}</span>
                                @endif
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('orangtua.nilai.*') ? 'active' : '' }}" href="{{ route('orangtua.nilai.index') }}">
                                <i class="bi bi-award"></i> Nilai
                            </a>
                        </li>

                    @endif

                </ul>

                <hr class="text-white-50">

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('logout') }}"
                           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>

            </div>

        </nav>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="col-12 px-md-4 main-content main-content-wrapper">

            <!-- ── Professional Header ── -->
            <header class="app-header">
                <div class="header-left d-flex align-items-center">
                    <h3 class="app-header-title fw-semibold text-dark m-0">@yield('title', 'Dashboard')</h3>
                </div>
                <div class="header-right d-flex align-items-center gap-2">
                    
                    <div class="header-icon bg-light me-2" id="darkModeToggle" title="Toggle Dark/Light Mode">
                        <i class="bi bi-moon-fill" id="darkModeIcon" style="font-size: 0.9rem;"></i>
                    </div>
                    
                    <div class="dropdown dropdown-profile-container">
                        <button class="btn bg-white dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1 profile-btn" type="button" data-bs-toggle="dropdown" style="border: 1px solid #cbd5e1;">
                            @if(auth()->user()->foto)
                                <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="Foto Profil" class="rounded-circle border" style="width: 24px; height: 24px; object-fit: cover;">
                            @else
                                <i class="bi bi-person-circle fs-5 text-secondary"></i>
                            @endif
                            <span class="text-dark d-none d-md-inline" style="font-size: 0.85rem;">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="border-radius: 12px; min-width: 200px;">
                            <li>
                                <div class="px-4 py-3 mb-2 bg-light rounded-top text-center" style="border-bottom: 1px solid #e2e8f0;">
                                    @if(auth()->user()->foto)
                                        <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="Foto Profil" class="rounded-circle border border-2 border-white shadow-sm mb-2" style="width: 56px; height: 56px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center shadow-sm mb-2" style="width: 56px; height: 56px;">
                                            <i class="bi bi-person fs-3"></i>
                                        </div>
                                    @endif
                                    <strong class="d-block text-dark" style="font-size: 0.95rem;">{{ auth()->user()->name }}</strong>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ ucfirst(auth()->user()->role) }}</small>
                                </div>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile.index') }}"><i class="bi bi-person me-2 text-primary"></i> Profile Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger fw-bold" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')

            <!-- ── Professional Footer ── -->
            <footer class="app-footer mt-5">
                &copy; {{ date('Y') }} <strong>SIAKAD SMK Sunan Giri</strong>. 
                <span class="d-none d-sm-inline">Sistem Informasi Akademik Sekolah. Hak Cipta Dilindungi.</span>
            </footer>

        </main>

    </div>
</div>

{{-- Universal Dynamic Bottom Navigation (Mobile Only) --}}
@auth
@php
    $showLainnya = false;
    if(auth()->user()->isAdmin() || auth()->user()->role === 'kurikulum') $showLainnya = true;
    elseif(auth()->user()->isWaliKelas()) $showLainnya = true;
    
    $guruKelasList = collect();
    $needsLainnyaGuru = false;
    if(auth()->user()->isGuru() && !auth()->user()->isWaliKelas() && !auth()->user()->isAdmin() && auth()->user()->role !== 'kurikulum') {
        $guruData = auth()->user()->guru;
        $guruKelasList = $guruData ? \App\Models\GuruMapel::with('kelas')->where('id_guru', $guruData->id_guru)->where('status', 'aktif')->get()->pluck('kelas')->unique('id_kelas')->sortBy('nama_kelas') : collect();
        if($guruKelasList->count() > 3) {
            $showLainnya = true;
            $needsLainnyaGuru = true;
        }
    }
@endphp
<nav class="bottom-nav d-md-none" id="dynamicBottomNav">
    @if(auth()->user()->isAdmin() || auth()->user()->role === 'kurikulum')
        <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
        <a href="{{ route('admin.guru.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i><span>Guru</span></a>
        <a href="{{ route('admin.siswa.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}"><i class="bi bi-people"></i><span>Siswa</span></a>
        <a href="{{ route('admin.rapor.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.rapor.*') ? 'active' : '' }}"><i class="bi bi-journal-check"></i><span>E-Rapor</span></a>
    @elseif(auth()->user()->isWaliKelas())
        @php
            $guruData = auth()->user()->guru;
            $wkKelas = $guruData ? \App\Models\Kelas::where('id_wali_kelas', $guruData->id_guru)->first() : null;
        @endphp
        <a href="{{ route('walikelas.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('walikelas.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
        <a href="{{ route('walikelas.rapor.select-kelas') }}" class="bottom-nav-item {{ request()->routeIs('walikelas.rapor.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i><span>E-Rapor</span></a>
        @if($wkKelas)
            <a href="{{ route('walikelas.kokurikuler.index', $wkKelas->id_kelas) }}" class="bottom-nav-item {{ request()->routeIs('walikelas.kokurikuler.*') ? 'active' : '' }}"><i class="bi bi-award"></i><span>Kokurikuler</span></a>
            <a href="{{ route('walikelas.ekstrakurikuler.index', $wkKelas->id_kelas) }}" class="bottom-nav-item {{ request()->routeIs('walikelas.ekstrakurikuler.*') ? 'active' : '' }}"><i class="bi bi-activity"></i><span>Ekstra</span></a>
            <a href="{{ route('walikelas.catatan.index', $wkKelas->id_kelas) }}" class="bottom-nav-item {{ request()->routeIs('walikelas.catatan.*') ? 'active' : '' }}"><i class="bi bi-journal-richtext"></i><span>Catatan</span></a>
        @endif
    @elseif(auth()->user()->isGuru())
        <a href="{{ route('guru.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
        <a href="{{ route('guru.tp.select-kelas') }}" class="bottom-nav-item {{ request()->routeIs('guru.tp.*') ? 'active' : '' }}"><i class="bi bi-book"></i><span>TP</span></a>
        @foreach($guruKelasList->take(3) as $kls)
            <a href="{{ route('guru.nilai.select-mapel', $kls->id_kelas) }}" class="bottom-nav-item {{ request()->is('guru/nilai/select-mapel/'.$kls->id_kelas) ? 'active' : '' }}"><i class="bi bi-mortarboard"></i><span>Nilai {{ $kls->nama_kelas }}</span></a>
        @endforeach
        @if(!$needsLainnyaGuru)
            <a href="#" class="bottom-nav-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right text-danger"></i><span class="text-danger">Keluar</span></a>
        @endif
    @elseif(auth()->user()->isOrangTua())
        <a href="{{ route('orangtua.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('orangtua.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
        <a href="{{ route('orangtua.rapor.index') }}" class="bottom-nav-item {{ request()->routeIs('orangtua.rapor.*') ? 'active' : '' }}"><i class="bi bi-journal-bookmark-fill"></i><span>E-Rapor</span></a>
        <a href="{{ route('orangtua.notifications.index') }}" class="bottom-nav-item {{ request()->routeIs('orangtua.notifications.*') ? 'active' : '' }}"><i class="bi bi-bell"></i><span>Notif</span></a>
        <a href="{{ route('orangtua.nilai.index') }}" class="bottom-nav-item {{ request()->routeIs('orangtua.nilai.*') ? 'active' : '' }}"><i class="bi bi-award"></i><span>Nilai</span></a>
        <a href="#" class="bottom-nav-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right text-danger"></i><span class="text-danger">Keluar</span></a>
    @endif
    
    @if($showLainnya)
    <a href="#offcanvasLainnya" class="bottom-nav-item" data-bs-toggle="offcanvas" role="button" aria-controls="offcanvasLainnya">
        <i class="bi bi-list"></i><span>Lainnya</span>
    </a>
    @endif
</nav>

@if($showLainnya)
<!-- Offcanvas Bottom Sheet for Lainnya Menu -->
<div class="offcanvas offcanvas-bottom shadow-lg border-0" tabindex="-1" id="offcanvasLainnya" aria-labelledby="offcanvasLainnyaLabel" style="height: auto; max-height: 85vh; border-top-left-radius: 24px; border-top-right-radius: 24px; z-index: 1055;">
    <div class="offcanvas-header border-bottom px-4 py-3">
        <h5 class="offcanvas-title fw-bold" id="offcanvasLainnyaLabel"><i class="bi bi-grid-fill me-2 text-primary"></i>Menu Lainnya</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body px-4 py-3" style="overflow-y: auto;">
        <div class="d-flex flex-column gap-1">
            @if(auth()->user()->isAdmin() || auth()->user()->role === 'kurikulum')
                <h6 class="fw-bold text-primary mb-2 mt-2"><i class="bi bi-database me-1"></i> Data Master</h6>
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('admin.kelas.index') }}"><i class="bi bi-bank me-3 text-secondary"></i>Kelas</a>
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('admin.mapel.index') }}"><i class="bi bi-book me-3 text-secondary"></i>Mata Pelajaran</a>
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('admin.tahun-ajaran.index') }}"><i class="bi bi-calendar3 me-3 text-secondary"></i>Tahun Ajaran</a>
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('admin.wali-kelas.index') }}"><i class="bi bi-person-video3 me-3 text-secondary"></i>Wali Kelas</a>
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('admin.konsentrasi-keahlian.index') }}"><i class="bi bi-tools me-3 text-secondary"></i>Jurusan</a>
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('admin.ekstrakurikuler.index') }}"><i class="bi bi-activity me-3 text-secondary"></i>Ekstrakurikuler</a>
                
                <hr class="my-3 opacity-10">
                <h6 class="fw-bold text-primary mb-2"><i class="bi bi-book me-1"></i> Pembelajaran</h6>
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('admin.jadwal.index') }}"><i class="bi bi-calendar-event me-3 text-secondary"></i>Jadwal Pelajaran</a>
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('admin.guru-mapel.index') }}"><i class="bi bi-person-lines-fill me-3 text-secondary"></i>Penugasan Guru</a>
                
                <hr class="my-3 opacity-10">
                <h6 class="fw-bold text-primary mb-2"><i class="bi bi-shield-lock me-1"></i> Sistem</h6>
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('admin.setting.index') }}"><i class="bi bi-gear me-3 text-secondary"></i>Pengaturan Sistem</a>
            @elseif(auth()->user()->isWaliKelas())
                <h6 class="fw-bold text-primary mb-2 mt-2"><i class="bi bi-book me-1"></i> Pembelajaran & Nilai</h6>
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('guru.tp.select-kelas') }}"><i class="bi bi-bullseye me-3 text-secondary"></i>Tujuan Pembelajaran</a>
                @foreach(\App\Models\GuruMapel::with('kelas')->where('id_guru', auth()->user()->guru->id_guru)->where('status', 'aktif')->get()->pluck('kelas')->unique('id_kelas')->sortBy('nama_kelas') as $kls)
                    <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('guru.nilai.select-mapel', $kls->id_kelas) }}"><i class="bi bi-mortarboard me-3 text-secondary"></i>Nilai {{ $kls->nama_kelas }}</a>
                @endforeach
                
                <hr class="my-3 opacity-10">
                <h6 class="fw-bold text-primary mb-2"><i class="bi bi-journal-check me-1"></i> Rapor Lanjutan</h6>
                @php
                    $guruData = auth()->user()->guru;
                    $wkKelas = $guruData ? \App\Models\Kelas::where('id_wali_kelas', $guruData->id_guru)->first() : null;
                @endphp
                @if($wkKelas)
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('walikelas.nilai.index', $wkKelas->id_kelas) }}"><i class="bi bi-calculator me-3 text-secondary"></i>Rekap Nilai</a>
                @endif
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('walikelas.leger.select-kelas') }}"><i class="bi bi-file-earmark-spreadsheet me-3 text-secondary"></i>Leger Nilai</a>
                <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('walikelas.ranking.select-kelas') }}"><i class="bi bi-trophy me-3 text-secondary"></i>Ranking</a>
            @elseif(auth()->user()->isGuru() && $needsLainnyaGuru)
                <h6 class="fw-bold text-primary mb-2 mt-2"><i class="bi bi-mortarboard me-1"></i> Nilai Kelas Lainnya</h6>
                @foreach($guruKelasList->skip(3) as $kls)
                    <a class="text-decoration-none text-dark py-2 px-3 rounded text-hover-bg" href="{{ route('guru.nilai.select-mapel', $kls->id_kelas) }}"><i class="bi bi-mortarboard me-3 text-secondary"></i>Nilai {{ $kls->nama_kelas }}</a>
                @endforeach
            @endif
            
            <hr class="my-3 opacity-10">
            <a class="text-decoration-none text-danger fw-bold py-2 px-3 rounded text-hover-bg" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right me-3 text-danger"></i>Keluar</a>
        </div>
    </div>
</div>
<style>
    .text-hover-bg:hover { background-color: #f1f5f9; transition: background-color 0.2s; }
</style>
@endif
</nav>
@endauth

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
</form>

@else

<!-- Guest Layout -->
<div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100 justify-content-center guest-layout">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">

            {{-- PWA install notice --}}
            <div class="alert alert-info mb-3">
                <strong><i class="bi bi-phone me-1"></i> Install Aplikasi:</strong><br>
                Untuk Orang Tua, aplikasi ini bisa dipasang di layar utama HP.<br>
                Klik menu browser <b>⋮</b> lalu pilih <b>Tambahkan ke layar utama</b>.
            </div>

            @yield('content')

        </div>
    </div>
</div>

@endauth

<!-- Bootstrap JS -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

<!-- jQuery -->
<script src="{{ asset('js/jquery.min.js') }}"></script>

<!-- SweetAlert2 -->
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>

@if(session('sweet_error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            html: `
                <img src="/images/error_422.png" alt="Error" style="max-width: 250px; margin-bottom: 20px;">
                <h3 class="fw-bold mb-1" style="color: #dc3545;">Terjadi Kesalahan ({{ session('sweet_error_code') }})</h3>
                <p class="text-muted fs-6 px-3">{{ session('sweet_error') }}</p>
            `,
            confirmButtonText: 'OK Mengerti',
            confirmButtonColor: '#4f46e5',
            customClass: {
                popup: 'rounded-4 shadow-lg border-0',
                confirmButton: 'rounded-pill px-4 fw-bold'
            },
            buttonsStyling: true
        });
    });
</script>
@endif

<script>
    // ── Page Loading Progress Bar ──
    (function() {
        const bar = document.getElementById('page-progress');
        if (!bar) return;
        let timer;
        function start() {
            bar.style.width = '0%';
            bar.style.opacity = '1';
            let w = 5;
            clearInterval(timer);
            timer = setInterval(() => {
                if (w < 80) { w += Math.random() * 8; bar.style.width = w + '%'; }
            }, 200);
        }
        function done() {
            clearInterval(timer);
            bar.style.width = '100%';
            setTimeout(() => { bar.style.opacity = '0'; bar.style.width = '0%'; }, 400);
        }
        // Trigger on link clicks (excluding modal triggers, anchors, logout)
        document.addEventListener('click', function(e) {
            const a = e.target.closest('a[href]');
            if (!a) return;
            const href = a.getAttribute('href');
            if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript') || a.dataset.bsToggle) return;
            if (a.getAttribute('target') === '_blank') return;
            start();
        });
        // Trigger on form submit
        document.addEventListener('submit', function(e) {
            const action = e.target.getAttribute('action') || '';
            // Don't show for logout or delete (they redirect anyway)
            if (!e.defaultPrevented) start();
        });
        window.addEventListener('pageshow', done);
        window.addEventListener('load', done);
    })();

    // Auto-hide flash messages
    setTimeout(() => {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Mobile sidebar toggle
    $(document).ready(function() {
        $('#sidebarToggle').on('click', function() {
            $('#sidebar').toggleClass('show');
            $('#sidebarOverlay').toggleClass('show');
        });

        $('#sidebarOverlay').on('click', function() {
            $('#sidebar').removeClass('show');
            $(this).removeClass('show');
        });

        // Sidebar click handling is kept, dynamic JS nav builder has been removed

        // ── SweetAlert2 Delete Confirmation (Global) ──
        // Intercept form submit on data-confirm-delete forms
        $(document).on('submit', 'form[data-confirm]', function(e) {
            e.preventDefault();
            const form = this;
            const msg = $(form).data('confirm') || 'Apakah Anda yakin ingin menghapus data ini?';
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-pill px-4 fw-bold',
                    cancelButton: 'rounded-pill px-4'
                },
                buttonsStyling: true
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // Auto-calculate grades (for guru input pages)
    $(document).on('input', '.grade-input', function() {
        const row = $(this).closest('tr');
        const th1 = parseFloat(row.find('input[name*="[th1]"]').val()) || 0;
        const th2 = parseFloat(row.find('input[name*="[th2]"]').val()) || 0;
        const uh1 = parseFloat(row.find('input[name*="[uh1]"]').val()) || 0;
        const uh2 = parseFloat(row.find('input[name*="[uh2]"]').val()) || 0;
        const as  = parseFloat(row.find('input[name*="[akhir_semester]"]').val()) || 0;

        const rerataTh = th1 && th2 ? (th1 + th2) / 2 : (th1 || th2 || 0);
        const rerataUh = uh1 && uh2 ? (uh1 + uh2) / 2 : (uh1 || uh2 || 0);

        let nilaiTp = 0, count = 0;
        if (rerataTh > 0) { nilaiTp += rerataTh; count++; }
        if (rerataUh > 0) { nilaiTp += rerataUh; count++; }
        if (as > 0)       { nilaiTp += as;       count++; }
        if (count > 0) nilaiTp /= count;

        row.find('.rerata-th-display').text(rerataTh.toFixed(2));
        row.find('.rerata-uh-display').text(rerataUh.toFixed(2));
        row.find('.nilai-tp-display').text(nilaiTp.toFixed(2));
    });
</script>

<!-- FCM Global: Register SW + Auto-sync token for orang_tua -->
<script type="module">
import { initializeApp }          from 'https://www.gstatic.com/firebasejs/10.7.0/firebase-app.js';
import { getMessaging, getToken, onMessage } from 'https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging.js';

const FCM_CONFIG = {
  apiKey:            'AIzaSyDsvRDC2OKIUhyQhE0eEYmCddF92_WNud8',
  authDomain:        'siakad-6d044.firebaseapp.com',
  projectId:         'siakad-6d044',
  storageBucket:     'siakad-6d044.firebasestorage.app',
  messagingSenderId: '65444849835',
  appId:             '1:65444849835:web:8f7f0f2d190572de2e2fee'
};
const VAPID = 'BIQdKkVUDZ81IICLCxOdAsXY7DK-lEWIX-wqvE-KnhRAdpuqvdK7UqtdCgKMsNEeRAGMBkzpirJ5p0bm2sIsa64';

async function fcmAutoRegister() {
    // Hanya jalan untuk role orang_tua
    @if(auth()->check() && auth()->user()->role === 'orang_tua')
    try {
        if (!('serviceWorker' in navigator) || !('Notification' in window)) return;
        
        let perm = Notification.permission;
        if (perm === 'default') {
            perm = await Notification.requestPermission();
        }
        if (perm !== 'granted') return;

        // Register / update service worker
        const reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' });
        await navigator.serviceWorker.ready;

        const app       = initializeApp(FCM_CONFIG);
        const messaging = getMessaging(app);
        
        // Listen to foreground notifications
        onMessage(messaging, (payload) => {
            console.log('[FCM] Foreground notification received:', payload);
            const { title, body } = payload.notification;
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: title,
                text: body,
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            // Update unread count badge if it exists
            const badge = document.querySelector('.ot-badge');
            if (badge) {
                badge.textContent = parseInt(badge.textContent || '0') + 1;
            } else {
                const iconContainer = document.querySelector('.ot-menu-icon');
                if (iconContainer && iconContainer.innerHTML.includes('bi-bell')) {
                    iconContainer.innerHTML += '<span class="ot-badge">1</span>';
                }
            }
        });

        const token     = await getToken(messaging, { vapidKey: VAPID, serviceWorkerRegistration: reg });

        if (!token) return;

        // Simpan ke server (upsert — aman dipanggil berkali-kali)
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const ua   = navigator.userAgent;
        let   os   = 'Device', br = 'Browser';
        if (/Android/i.test(ua))      os = 'Android';
        else if (/iPhone/i.test(ua))  os = 'iOS';
        else if (/Windows/i.test(ua)) os = 'Windows';
        if (/Chrome/i.test(ua))       br = 'Chrome';
        else if (/Firefox/i.test(ua)) br = 'Firefox';
        else if (/Safari/i.test(ua))  br = 'Safari';
        else if (/Edge/i.test(ua))    br = 'Edge';

        await fetch('/orangtua/notifications/subscribe', {
            method : 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body   : JSON.stringify({ endpoint: token, p256dh_key: null, auth_token: null, device_name: `${br} on ${os}` })
        });

        console.log('[FCM] Token synced:', token.substring(0, 20) + '...');
    } catch (e) {
        console.warn('[FCM] Auto-register error:', e.message);
    }
    @endif
}

document.addEventListener('DOMContentLoaded', () => {
    fcmAutoRegister();

    @if(auth()->check() && auth()->user()->role === 'orang_tua')
    // Polling untuk update notifikasi secara real-time via AJAX tanpa reload halaman
    setInterval(() => {
        fetch('/orangtua/notifications/unread-count')
            .then(res => res.json())
            .then(data => {
                if (data.count > 0) {
                    const badge = document.querySelector('.ot-badge');
                    if (badge) {
                        badge.textContent = data.count;
                    } else {
                        const iconContainer = document.querySelector('.ot-menu-icon .bi-bell');
                        if (iconContainer) {
                            iconContainer.parentElement.innerHTML = '<i class="bi bi-bell"></i><span class="ot-badge">' + data.count + '</span>';
                        }
                    }
                }
            })
            .catch(err => console.error('Polling notif error:', err));
    }, 15000); // Polling setiap 15 detik
    @endif
});

/* ── Dark Mode Logic ── */
function initDarkMode() {
    const toggleBtn = document.getElementById('darkModeToggle');
    const icon = document.getElementById('darkModeIcon');
    if(!toggleBtn) return;
    
    // Update icon on load
    if(document.documentElement.classList.contains('dark-mode')) {
        icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
        icon.classList.add('text-warning');
    }

    // Toggle event
    toggleBtn.addEventListener('click', function() {
        document.documentElement.classList.toggle('dark-mode');
        const isDark = document.documentElement.classList.contains('dark-mode');
        
        if (isDark) {
            localStorage.setItem('theme', 'dark');
            icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            icon.classList.add('text-warning');
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        } else {
            localStorage.setItem('theme', 'light');
            icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
            icon.classList.remove('text-warning');
            document.documentElement.removeAttribute('data-bs-theme');
        }
    });
}

// Run on normal load and turbo load
document.addEventListener('DOMContentLoaded', initDarkMode);
document.addEventListener('turbo:load', initDarkMode);

</script>

@yield('scripts')

</body>
</html>
