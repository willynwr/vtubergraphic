<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - VtuberGraphic Absensi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-primary: #fef7ff;
            --bg-secondary: #fff5f9;
            --bg-card: rgba(255, 255, 255, 0.9);
            --bg-card-hover: rgba(255, 240, 246, 0.95);
            --bg-glass: rgba(255, 230, 240, 0.25);
            --border: rgba(219, 160, 190, 0.2);
            --border-hover: rgba(219, 160, 190, 0.4);
            --text-primary: #3d2b3a;
            --text-secondary: #8a6b80;
            --text-muted: #b8a0b0;
            --accent-purple: #b388d9;
            --accent-blue: #7eb8e0;
            --accent-cyan: #6dcfcf;
            --accent-green: #8dd4b0;
            --accent-orange: #f0b86e;
            --accent-red: #e87070;
            --accent-pink: #e87bb0;
            --gradient-primary: linear-gradient(135deg, #e87bb0, #b388d9);
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* Layout */
        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 50;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            font-size: 22px;
            font-weight: 900;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            padding: 0 8px;
            margin-bottom: 4px;
        }

        .sidebar-subtitle {
            font-size: 11px;
            color: var(--text-muted);
            padding: 0 8px;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 32px;
        }

        .sidebar-nav {
            flex: 1;
        }

        .nav-label {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 0 12px;
            margin-bottom: 10px;
            margin-top: 24px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            margin-bottom: 4px;
        }

        .nav-item:hover {
            background: var(--bg-glass);
            color: var(--text-primary);
        }

        .nav-item.active {
            background: rgba(232, 123, 176, 0.12);
            color: var(--accent-pink);
        }

        .nav-item .nav-icon {
            width: 24px;
            height: 24px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: currentColor;
        }

        .nav-item .nav-icon svg,
        .stat-icon svg,
        .card-title-icon svg,
        .btn-icon svg,
        .btn-close-modal svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .stat-icon svg {
            width: 22px;
            height: 22px;
        }

        .card-title-icon {
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-pink);
            flex: 0 0 auto;
        }

        .btn-icon {
            width: 16px;
            height: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-footer {
            padding: 16px 8px;
            border-top: 1px solid var(--border);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            padding: 10px 8px;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .sidebar-footer a:hover {
            color: var(--accent-pink);
            background: var(--bg-glass);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 28px 32px;
        }

        /* Top bar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .topbar-title {
            font-size: 26px;
            font-weight: 800;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .month-picker {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 6px;
        }

        .month-picker select {
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 12px;
            cursor: pointer;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
        }

        .month-picker select option {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .btn-refresh {
            padding: 10px 18px;
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            color: white;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-refresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(232, 123, 176, 0.3);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            padding: 22px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(180, 120, 160, 0.12);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.15;
        }

        .stat-card.card-present::after { background: var(--accent-green); }
        .stat-card.card-out::after { background: var(--accent-blue); }
        .stat-card.card-izin::after { background: var(--accent-orange); }
        .stat-card.card-sakit::after { background: var(--accent-red); }
        .stat-card.card-absent::after { background: var(--accent-pink); }
        .stat-card.card-tukar::after { background: var(--accent-cyan); }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 14px;
        }

        .card-present .stat-icon { background: rgba(16, 185, 129, 0.12); }
        .card-out .stat-icon { background: rgba(59, 130, 246, 0.12); }
        .card-izin .stat-icon { background: rgba(245, 158, 11, 0.12); }
        .card-sakit .stat-icon { background: rgba(239, 68, 68, 0.12); }
        .card-absent .stat-icon { background: rgba(236, 72, 153, 0.12); }
        .card-tukar .stat-icon { background: rgba(6, 182, 212, 0.12); }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 4px;
            font-variant-numeric: tabular-nums;
        }

        .card-present .stat-value { color: var(--accent-green); }
        .card-out .stat-value { color: var(--accent-blue); }
        .card-izin .stat-value { color: var(--accent-orange); }
        .card-sakit .stat-value { color: var(--accent-red); }
        .card-absent .stat-value { color: var(--accent-pink); }
        .card-tukar .stat-value { color: var(--accent-cyan); }

        .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Today's Live Card */
        .today-live {
            padding: 24px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }

        .today-live::before {
            content: '';
            position: absolute;
            top: -50px; right: -50px;
            width: 200px; height: 200px;
            background: var(--accent-pink);
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.06;
        }

        .today-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .today-title {
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .live-dot {
            width: 10px; height: 10px;
            background: var(--accent-green);
            border-radius: 50%;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .today-date {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .today-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
        }

        .today-stat {
            text-align: center;
            padding: 16px;
            background: var(--bg-glass);
            border: 1px solid var(--border);
            border-radius: 14px;
        }

        .today-stat-value {
            font-size: 28px;
            font-weight: 800;
        }

        .today-stat-label {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 28px;
        }

        /* Card */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px 16px;
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-body {
            padding: 0 24px 24px;
        }

        /* Chart */
        .chart-container {
            position: relative;
            height: 260px;
        }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .data-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            background: var(--bg-card);
        }

        .data-table tbody tr {
            transition: background 0.2s;
        }

        .data-table tbody tr:hover {
            background: var(--bg-glass);
        }

        .data-table tbody td {
            padding: 14px 16px;
            font-size: 13px;
            border-bottom: 1px solid var(--border);
            color: var(--text-secondary);
        }

        .data-table tbody td:first-child {
            color: var(--text-primary);
            font-weight: 500;
        }

        /* Type Badge */
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .type-badge.type-IN {
            background: rgba(16, 185, 129, 0.12);
            color: var(--accent-green);
        }
        .type-badge.type-OUT {
            background: rgba(59, 130, 246, 0.12);
            color: var(--accent-blue);
        }
        .type-badge.type-IZIN {
            background: rgba(245, 158, 11, 0.12);
            color: var(--accent-orange);
        }
        .type-badge.type-SAKIT {
            background: rgba(239, 68, 68, 0.12);
            color: var(--accent-red);
        }
        .type-badge.type-TUKAR_LIBUR {
            background: rgba(236, 72, 153, 0.12);
            color: var(--accent-pink);
        }

        /* Scrollable table wrapper */
        .table-wrapper {
            max-height: 400px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
        }

        .table-wrapper::-webkit-scrollbar {
            width: 6px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        /* Employee Table */
        .employee-table-full {
            width: 100%;
        }

        .employee-table-full .card-body {
            padding: 0;
        }

        .employee-table-full .table-wrapper {
            max-height: 500px;
        }

        /* Detail Modal */
        .detail-modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(61, 43, 58, 0.45);
            backdrop-filter: blur(10px);
            z-index: 200;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .detail-modal.active {
            display: flex;
        }

        .detail-modal-content {
            width: 100%;
            max-width: 700px;
            max-height: 85vh;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 24px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .detail-modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-modal-header h3 {
            font-size: 18px;
            font-weight: 700;
        }

        .btn-close-modal {
            width: 36px; height: 36px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-secondary);
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-close-modal:hover {
            background: var(--bg-glass);
            color: var(--text-primary);
        }

        .detail-modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .detail-emp-info {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            padding: 16px;
            background: var(--bg-glass);
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .detail-emp-avatar {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
            color: white;
        }

        .detail-emp-name {
            font-size: 18px;
            font-weight: 700;
        }

        .detail-emp-sub {
            font-size: 13px;
            color: var(--text-secondary);
        }

        /* Mobile burger */
        .burger-menu {
            display: none;
            position: fixed;
            top: 16px; left: 16px;
            z-index: 60;
            width: 44px; height: 44px;
            border-radius: 12px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            cursor: pointer;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 45;
        }

        /* Page sections (Employee, Location) */
        .page-section {
            display: none;
        }
        .page-section.active {
            display: block;
        }

        /* Employee/Location forms */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 6px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--bg-glass);
            color: var(--text-primary);
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s;
        }

        .form-group input:focus {
            border-color: var(--accent-pink);
        }

        .form-group input::placeholder {
            color: var(--text-muted);
        }

        .btn-add {
            padding: 12px 24px;
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            color: white;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(232, 123, 176, 0.3);
        }

        .btn-delete {
            padding: 6px 12px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 8px;
            color: var(--accent-red);
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .btn-detail {
            padding: 6px 12px;
            background: rgba(232, 123, 176, 0.1);
            border: 1px solid rgba(232, 123, 176, 0.2);
            border-radius: 8px;
            color: var(--accent-pink);
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-detail:hover {
            background: rgba(232, 123, 176, 0.2);
        }

        .qr-card {
            width: 128px;
            padding: 10px;
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(250, 246, 255, 0.92));
            border: 1px solid rgba(179, 136, 217, 0.16);
            box-shadow: 0 10px 24px rgba(180, 120, 160, 0.08);
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }

        .qr-preview {
            width: 100%;
            aspect-ratio: 1 / 1;
            display: block;
            border-radius: 14px;
            background: #fff;
            padding: 6px;
            border: 1px solid rgba(61, 43, 58, 0.06);
        }

        .qr-card-label {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 11px;
            color: var(--text-secondary);
        }

        .qr-card-code {
            font-weight: 700;
            color: var(--text-primary);
            font-variant-numeric: tabular-nums;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-qr-download {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid rgba(232, 123, 176, 0.18);
            border-radius: 10px;
            background: rgba(232, 123, 176, 0.08);
            color: var(--accent-pink);
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-qr-download:hover {
            background: rgba(232, 123, 176, 0.14);
            transform: translateY(-1px);
        }

        .alert-toast {
            position: fixed;
            top: 20px; right: 20px;
            padding: 16px 24px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 500;
            z-index: 300;
            animation: slideInRight 0.3s ease;
            box-shadow: 0 10px 30px rgba(180, 120, 160, 0.15);
        }

        .alert-toast.success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--accent-green);
        }

        .alert-toast.error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--accent-red);
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .content-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.active { display: block; }
            .burger-menu { display: flex; }
            .main-content { margin-left: 0; padding: 20px 16px; padding-top: 70px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .topbar { flex-direction: column; align-items: flex-start; gap: 12px; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- Burger menu -->
        <button class="burger-menu" onclick="toggleSidebar()">☰</button>
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">VtuberGraphic</div>
            <div class="sidebar-subtitle">Admin Panel</div>

            <nav class="sidebar-nav">
                <div class="nav-label">Menu</div>
                <a class="nav-item active" onclick="showPage('dashboard')" id="nav-dashboard">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 20h16"></path>
                            <path d="M6 20V10"></path>
                            <path d="M12 20V4"></path>
                            <path d="M18 20v-7"></path>
                        </svg>
                    </span>
                    Dashboard
                </a>
                <a class="nav-item" onclick="showPage('employees')" id="nav-employees">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M20 21v-2a3 3 0 0 0-2-2.82"></path>
                            <path d="M16 3.5a4 4 0 0 1 0 7.5"></path>
                        </svg>
                    </span>
                    Karyawan
                </a>
                <a class="nav-item" onclick="showPage('locations')" id="nav-locations">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11z"></path>
                            <circle cx="12" cy="10" r="2.5"></circle>
                        </svg>
                    </span>
                    Lokasi Kantor
                </a>
                <a class="nav-item" onclick="showPage('schedules')" id="nav-schedules">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M8 2v4"></path>
                            <path d="M16 2v4"></path>
                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                            <path d="M3 10h18"></path>
                        </svg>
                    </span>
                    Jadwal & Tukar Libur
                </a>

                <div class="nav-label">Lainnya</div>
                <a class="nav-item" onclick="showPage('history')" id="nav-history">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-4"></path>
                            <path d="M9 2v4"></path>
                            <path d="M15 2v4"></path>
                            <path d="M7 10h10"></path>
                            <path d="M7 14h6"></path>
                        </svg>
                    </span>
                    Riwayat Absensi
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="/">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 7h4l2-3h4l2 3h4v12H4z"></path>
                            <circle cx="12" cy="13" r="3.5"></circle>
                        </svg>
                    </span>

                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @include('admin.dashboard.sections.dashboard')
            @include('admin.dashboard.sections.employees')
            @include('admin.dashboard.sections.locations')
            @include('admin.dashboard.sections.schedules')
            @include('admin.dashboard.sections.history')
        </main>
    </div>

    <!-- Employee Detail Modal -->
    <div class="detail-modal" id="detailModal">
        <div class="detail-modal-content">
            <div class="detail-modal-header">
                <h3 id="detailModalTitle">Detail Karyawan</h3>
                <button class="btn-close-modal" onclick="closeDetailModal()" aria-label="Tutup detail">
                    <svg viewBox="0 0 24 24"><path d="M6 6l12 12"></path><path d="M18 6L6 18"></path></svg>
                </button>
            </div>
            <div class="detail-modal-body" id="detailModalBody">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>

    <script type="application/json" id="initialSummaryData">@json($summary)</script>
    <script type="application/json" id="initialTodaySummaryData">@json($todaySummary)</script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const initialSummary = JSON.parse(document.getElementById('initialSummaryData').textContent);
        const initialTodaySummary = JSON.parse(document.getElementById('initialTodaySummaryData').textContent);

        // Set today's date
        document.getElementById('todayDateText').textContent = new Date().toLocaleDateString('id-ID', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        });

        // Initialize Chart
        let pieChart = null;
        function initChart(data) {
            const ctx = document.getElementById('pieChart').getContext('2d');

            if (pieChart) pieChart.destroy();

            pieChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Pulang', 'Izin', 'Sakit', 'Absen', 'Tukar Libur'],
                    datasets: [{
                        data: [
                            data.total_in, data.total_out, data.total_izin,
                            data.total_sakit, data.total_absen, data.total_tukar_libur
                        ],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(6, 182, 212, 0.8)',
                        ],
                        borderWidth: 0,
                        hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#8888aa',
                                padding: 16,
                                font: { family: 'Inter', size: 12 },
                                usePointStyle: true,
                                pointStyleWidth: 12,
                            }
                        }
                    }
                }
            });
        }

        // Initialize with server data
        initChart(initialSummary);

        // Load today summary
        function updateTodaySummary(data) {
            document.getElementById('todayPresent').textContent = data.present;
            document.getElementById('todayOut').textContent = data.out;
            document.getElementById('todayIzin').textContent = data.izin;
            document.getElementById('todaySakit').textContent = data.sakit;
            document.getElementById('todayNotPresent').textContent = data.not_present;
        }

        updateTodaySummary(initialTodaySummary);

        // Load data via API
        async function loadData() {
            const month = document.getElementById('selectMonth').value;
            const year = document.getElementById('selectYear').value;

            try {
                const res = await fetch(`/admin/api/summary?month=${month}&year=${year}`);
                const data = await res.json();

                // Update stats
                document.getElementById('statIn').textContent = data.summary.total_in;
                document.getElementById('statOut').textContent = data.summary.total_out;
                document.getElementById('statIzin').textContent = data.summary.total_izin;
                document.getElementById('statSakit').textContent = data.summary.total_sakit;
                document.getElementById('statAbsen').textContent = data.summary.total_absen;
                document.getElementById('statTukar').textContent = data.summary.total_tukar_libur;

                // Update chart
                initChart(data.summary);

                // Update today
                updateTodaySummary(data.todaySummary);

                // Update employee stats table
                const empBody = document.getElementById('employeeStatsBody');
                empBody.innerHTML = data.employeeStats.map(s => `
                    <tr>
                        <td>${s.name}</td>
                        <td>${s.total_in}</td>
                        <td>${s.total_out}</td>
                        <td>${s.total_izin}</td>
                        <td>${s.total_sakit}</td>
                        <td>${s.avg_work_duration}</td>
                    </tr>
                `).join('');

                // Update recent attendance
                const recBody = document.getElementById('recentAttendanceBody');
                recBody.innerHTML = data.recentAttendances.map(a => `
                    <tr>
                        <td>${a.employee_name}</td>
                        <td>${a.employee_id}</td>
                        <td>${a.department || '-'}</td>
                        <td><span class="type-badge type-${a.type}">${a.type_label}</span></td>
                        <td>${a.date}</td>
                        <td>${a.time}</td>
                        <td>${a.distance ? Math.round(a.distance) + 'm' : '-'}</td>
                    </tr>
                `).join('');

                // Also update history
                const histBody = document.getElementById('historyBody');
                histBody.innerHTML = data.recentAttendances.map(a => `
                    <tr>
                        <td>${a.employee_name}</td>
                        <td>${a.employee_id}</td>
                        <td><span class="type-badge type-${a.type}">${a.type_label}</span></td>
                        <td>${a.date}</td>
                        <td>${a.time}</td>
                        <td>${a.distance ? Math.round(a.distance) + 'm' : '-'}</td>
                        <td>${a.note || '-'}</td>
                    </tr>
                `).join('');

                showToast('Data berhasil diperbarui', 'success');
            } catch (err) {
                showToast('Gagal memuat data', 'error');
            }
        }

        // Navigation
        function showPage(page) {
            document.querySelectorAll('.page-section').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            document.getElementById('page-' + page).classList.add('active');
            document.getElementById('nav-' + page).classList.add('active');

            // Close sidebar on mobile
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }

        document.addEventListener('click', function(event) {
            const detailButton = event.target.closest('.js-employee-detail');
            if (detailButton) {
                showEmployeeDetail(detailButton.dataset.employeeId);
                return;
            }

            const deleteEmployeeButton = event.target.closest('.js-employee-delete');
            if (deleteEmployeeButton) {
                deleteEmployee(deleteEmployeeButton.dataset.employeeId);
                return;
            }

            const deleteLocationButton = event.target.closest('.js-location-delete');
            if (deleteLocationButton) {
                deleteLocation(deleteLocationButton.dataset.locationId);
            }
        });

        // Employee CRUD
        async function addEmployee() {
            const data = {
                employee_id: document.getElementById('newEmpId').value,
                name: document.getElementById('newEmpName').value,
                department: document.getElementById('newEmpDept').value,
                position: document.getElementById('newEmpPos').value,
            };

            if (!data.employee_id || !data.name) {
                showToast('ID dan Nama wajib diisi', 'error');
                return;
            }

            try {
                const res = await fetch('/admin/employees', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(data),
                });
                const result = await res.json();

                if (result.success) {
                    showToast(result.message, 'success');
                    location.reload();
                } else {
                    showToast(result.message || 'Gagal menambahkan', 'error');
                }
            } catch (err) {
                showToast('Error: ' + err.message, 'error');
            }
        }

        async function deleteEmployee(id) {
            if (!confirm('Yakin hapus karyawan ini? Semua data absensi akan ikut terhapus.')) return;

            try {
                const res = await fetch(`/admin/employees/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                });
                const result = await res.json();

                if (result.success) {
                    document.getElementById('emp-row-' + id)?.remove();
                    showToast(result.message, 'success');
                }
            } catch (err) {
                showToast('Gagal menghapus', 'error');
            }
        }

        async function showEmployeeDetail(id) {
            const month = document.getElementById('selectMonth').value;
            const year = document.getElementById('selectYear').value;

            try {
                const res = await fetch(`/admin/employees/${id}/detail?month=${month}&year=${year}`);
                const data = await res.json();

                const emp = data.employee;
                const initials = emp.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

                let html = `
                    <div class="detail-emp-info">
                        <div class="detail-emp-avatar">${initials}</div>
                        <div>
                            <div class="detail-emp-name">${emp.name}</div>
                            <div class="detail-emp-sub">${emp.position || ''} · ${emp.department || ''} · ${emp.employee_id}</div>
                        </div>
                    </div>
                `;

                if (data.daily_records.length > 0) {
                    html += `<div class="table-wrapper" style="max-height:400px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Masuk</th>
                                    <th>Pulang</th>
                                    <th>Durasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>`;

                    data.daily_records.forEach(r => {
                        const statusColor = r.is_sufficient ? 'var(--accent-green)' : 'var(--accent-orange)';
                        const statusText = r.work_minutes > 0 ? (r.is_sufficient ? '✅ Cukup' : '⚠️ Kurang') : '-';

                        // Check for non-IN/OUT records
                        let specialBadges = '';
                        r.records.forEach(rec => {
                            if (['IZIN', 'SAKIT', 'TUKAR_LIBUR'].includes(rec.type)) {
                                specialBadges += `<span class="type-badge type-${rec.type}">${rec.type_label}</span> `;
                            }
                        });

                        html += `<tr>
                            <td>${r.date}</td>
                            <td>${r.day}</td>
                            <td>${r.in_time === '-' ? '-' : r.in_time.substring(0, 5)}</td>
                            <td>${r.out_time === '-' ? '-' : r.out_time.substring(0, 5)}</td>
                            <td>${r.work_duration}</td>
                            <td>${specialBadges || `<span style="color:${statusColor}">${statusText}</span>`}</td>
                        </tr>`;
                    });

                    html += `</tbody></table></div>`;
                } else {
                    html += `<p style="color:var(--text-muted);text-align:center;padding:40px;">Tidak ada data absensi untuk periode ini.</p>`;
                }

                document.getElementById('detailModalTitle').textContent = `Detail: ${emp.name}`;
                document.getElementById('detailModalBody').innerHTML = html;
                document.getElementById('detailModal').classList.add('active');
            } catch (err) {
                showToast('Gagal memuat detail', 'error');
            }
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('active');
        }

        // Location CRUD
        async function addLocation() {
            const data = {
                name: document.getElementById('newLocName').value,
                latitude: parseFloat(document.getElementById('newLocLat').value),
                longitude: parseFloat(document.getElementById('newLocLng').value),
                radius_meters: parseInt(document.getElementById('newLocRadius').value),
            };

            if (!data.name || isNaN(data.latitude) || isNaN(data.longitude)) {
                showToast('Semua field wajib diisi', 'error');
                return;
            }

            try {
                const res = await fetch('/admin/locations', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(data),
                });
                const result = await res.json();

                if (result.success) {
                    showToast(result.message, 'success');
                    location.reload();
                }
            } catch (err) {
                showToast('Gagal menambahkan', 'error');
            }
        }

        async function deleteLocation(id) {
            if (!confirm('Yakin hapus lokasi ini?')) return;

            try {
                const res = await fetch(`/admin/locations/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                });
                const result = await res.json();
                if (result.success) {
                    document.getElementById('loc-row-' + id)?.remove();
                    showToast(result.message, 'success');
                }
            } catch (err) {
                showToast('Gagal menghapus', 'error');
            }
        }

        function getMyLocation() {
            if (!navigator.geolocation) {
                showToast('GPS tidak didukung', 'error');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    document.getElementById('newLocLat').value = pos.coords.latitude.toFixed(7);
                    document.getElementById('newLocLng').value = pos.coords.longitude.toFixed(7);
                    showToast('Lokasi berhasil diambil', 'success');
                },
                () => showToast('Gagal mengambil lokasi', 'error'),
                { enableHighAccuracy: true }
            );
        }

        function buildQrUrl(employeeId) {
            return `https://api.qrserver.com/v1/create-qr-code/?size=512x512&margin=12&data=${encodeURIComponent(employeeId)}&bgcolor=ffffff&color=111111`;
        }

        async function downloadEmployeeQr(employeeId, employeeName) {
            const qrUrl = buildQrUrl(employeeId);
            const fileName = `qr-${employeeId}.png`;

            try {
                const response = await fetch(qrUrl, { mode: 'cors' });
                if (!response.ok) {
                    throw new Error('QR image request failed');
                }

                const blob = await response.blob();
                const objectUrl = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = objectUrl;
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(objectUrl);

                showToast(`QR ${employeeName} berhasil didownload`, 'success');
            } catch (err) {
                const link = document.createElement('a');
                link.href = qrUrl;
                link.target = '_blank';
                link.rel = 'noopener';
                document.body.appendChild(link);
                link.click();
                link.remove();

                showToast('QR dibuka di tab baru. Silakan simpan gambar tersebut.', 'success');
            }
        }

        // Swap request approval
        async function approveSwap(id) {
            if (!confirm('Setujui permintaan tukar libur ini?')) return;
            try {
                const res = await fetch(`/admin/swap-requests/${id}/approve`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                });
                const data = await res.json();
                if (!res.ok) { showToast(data.message || 'Gagal menyetujui.', 'error'); return; }
                document.getElementById('swap-row-' + id)?.remove();
                showToast('Request berhasil disetujui!', 'success');
            } catch (err) { showToast('Error saat menyetujui.', 'error'); }
        }

        async function rejectSwap(id) {
            if (!confirm('Tolak permintaan tukar libur ini?')) return;
            try {
                const res = await fetch(`/admin/swap-requests/${id}/reject`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                });
                const data = await res.json();
                if (!res.ok) { showToast(data.message || 'Gagal menolak.', 'error'); return; }
                document.getElementById('swap-row-' + id)?.remove();
                showToast('Request berhasil ditolak.', 'success');
            } catch (err) { showToast('Error saat menolak.', 'error'); }
        }

        // Toast
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `alert-toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(30px)';
                toast.style.transition = 'all 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Auto refresh disabled; data updates on manual actions (month/year change or page reload).
    </script>
</body>
</html>
