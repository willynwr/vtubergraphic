<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Karyawan') - VtuberGraphic</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
            --gradient-blue: linear-gradient(135deg, #7eb8e0, #6dcfcf);
            --gradient-green: linear-gradient(135deg, #8dd4b0, #7eb8e0);
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

        .nav-label:first-child {
            margin-top: 0;
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

        .nav-item .nav-icon svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sidebar-user {
            padding: 16px 8px;
            margin-top: auto;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 12px;
            background: var(--bg-glass);
            border-radius: 14px;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            overflow: hidden;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-footer {
            padding: 8px;
        }

        .sidebar-footer form {
            margin: 0;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            background: transparent;
            border: none;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            padding: 10px 8px;
            border-radius: 10px;
            transition: all 0.2s;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }

        .btn-logout:hover {
            color: var(--accent-red);
            background: rgba(232, 112, 112, 0.08);
        }

        .btn-logout .nav-icon svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 28px 32px;
            position: relative;
        }

        .main-content::before {
            content: '';
            position: fixed;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: var(--accent-pink);
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.06;
            pointer-events: none;
            z-index: -1;
        }

        .main-content::after {
            content: '';
            position: fixed;
            bottom: -100px;
            left: 300px;
            width: 400px;
            height: 400px;
            background: var(--accent-purple);
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.06;
            pointer-events: none;
            z-index: -1;
        }

        /* Top bar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .topbar-left .topbar-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
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

        .live-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(141, 212, 176, 0.12);
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            color: var(--accent-green);
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: var(--accent-green);
            border-radius: 50%;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
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
            border-radius: 20px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
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

        .stat-card.card-pink::after { background: var(--accent-pink); }
        .stat-card.card-blue::after { background: var(--accent-blue); }
        .stat-card.card-orange::after { background: var(--accent-orange); }
        .stat-card.card-green::after { background: var(--accent-green); }
        .stat-card.card-purple::after { background: var(--accent-purple); }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .stat-icon svg {
            width: 22px;
            height: 22px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .card-pink .stat-icon { background: rgba(232, 123, 176, 0.12); color: var(--accent-pink); }
        .card-blue .stat-icon { background: rgba(126, 184, 224, 0.12); color: var(--accent-blue); }
        .card-orange .stat-icon { background: rgba(240, 184, 110, 0.12); color: var(--accent-orange); }
        .card-green .stat-icon { background: rgba(141, 212, 176, 0.12); color: var(--accent-green); }
        .card-purple .stat-icon { background: rgba(179, 136, 217, 0.12); color: var(--accent-purple); }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 4px;
            font-variant-numeric: tabular-nums;
        }

        .card-pink .stat-value { color: var(--accent-pink); }
        .card-blue .stat-value { color: var(--accent-blue); }
        .card-orange .stat-value { color: var(--accent-orange); }
        .card-green .stat-value { color: var(--accent-green); }
        .card-purple .stat-value { color: var(--accent-purple); }

        .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Card */
        .card {
            background: var(--bg-card);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 12px 40px rgba(180, 120, 160, 0.1);
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

        .card-title-icon {
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-pink);
            flex: 0 0 auto;
        }

        .card-title-icon svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .card-body {
            padding: 0 24px 24px;
        }

        .card-body.no-padding {
            padding: 0;
        }

        /* Data Table */
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

        .table-wrapper {
            max-height: 400px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
        }

        .table-wrapper::-webkit-scrollbar { width: 6px; }
        .table-wrapper::-webkit-scrollbar-track { background: transparent; }
        .table-wrapper::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .badge-green { background: rgba(141, 212, 176, 0.15); color: #2f7c57; }
        .badge-blue { background: rgba(126, 184, 224, 0.15); color: #2f6f99; }
        .badge-orange { background: rgba(240, 184, 110, 0.15); color: #a86d2b; }
        .badge-red { background: rgba(232, 112, 112, 0.15); color: #b54e4e; }
        .badge-pink { background: rgba(232, 123, 176, 0.15); color: #c4528a; }
        .badge-purple { background: rgba(179, 136, 217, 0.15); color: #7c5ba5; }

        /* Buttons */
        .btn-primary {
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(232, 123, 176, 0.3);
        }

        .btn-secondary {
            padding: 10px 20px;
            background: var(--bg-glass);
            border: none;
            border-radius: 12px;
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-secondary:hover {
            background: rgba(255, 230, 240, 0.45);
            transform: translateY(-1px);
        }

        .btn-icon-only {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: none;
            background: var(--bg-glass);
            color: var(--text-secondary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-icon-only:hover {
            background: rgba(255, 230, 240, 0.45);
            color: var(--text-primary);
        }

        .btn-icon-only svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Forms */
        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 6px;
            font-weight: 500;
        }

        .form-control {
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

        .form-control:focus {
            border-color: var(--accent-pink);
            box-shadow: 0 0 0 4px rgba(232, 123, 176, 0.1);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 28px;
        }

        .content-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        /* Modal */
        .modal-overlay {
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

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            width: 100%;
            max-width: 560px;
            max-height: 85vh;
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-header {
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
        }

        .modal-header .modal-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .modal-body {
            padding: 0 24px 24px;
            overflow-y: auto;
            flex: 1;
        }

        .btn-close-modal {
            width: 36px; height: 36px;
            border-radius: 10px;
            border: none;
            background: var(--bg-glass);
            color: var(--text-secondary);
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-close-modal:hover {
            background: rgba(255, 230, 240, 0.45);
            color: var(--text-primary);
        }

        .btn-close-modal svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Toast */
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
            background: rgba(141, 212, 176, 0.15);
            color: #2f7c57;
        }

        .alert-toast.error {
            background: rgba(232, 112, 112, 0.15);
            color: #b54e4e;
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Empty State */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
            color: var(--text-muted);
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: var(--bg-glass);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .empty-state-icon svg {
            width: 28px;
            height: 28px;
            stroke: var(--text-muted);
            fill: none;
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .empty-state p {
            font-size: 14px;
            font-weight: 500;
        }

        .empty-state span {
            font-size: 12px;
            margin-top: 4px;
        }

        /* Action Cards (for attendance) */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
        }

        .action-card {
            padding: 20px 16px;
            background: var(--bg-glass);
            border-radius: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(180, 120, 160, 0.1);
        }

        .action-card.selected {
            border-color: var(--accent-pink);
            background: rgba(232, 123, 176, 0.08);
        }

        .action-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 22px;
        }

        .action-card-label {
            font-size: 13px;
            font-weight: 600;
        }

        .action-card-desc {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* GPS Status */
        .gps-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .gps-idle { background: var(--bg-glass); color: var(--text-muted); }
        .gps-loading { background: rgba(240, 184, 110, 0.12); color: var(--accent-orange); }
        .gps-success { background: rgba(141, 212, 176, 0.12); color: #2f7c57; }
        .gps-error { background: rgba(232, 112, 112, 0.12); color: #b54e4e; }

        /* Swap request timeline */
        .timeline-item {
            display: flex;
            gap: 16px;
            padding: 16px;
            background: var(--bg-glass);
            border-radius: 14px;
            margin-bottom: 10px;
            transition: all 0.2s;
        }

        .timeline-item:hover {
            background: rgba(255, 230, 240, 0.35);
        }

        .timeline-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 5px;
            flex-shrink: 0;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .timeline-subtitle {
            font-size: 12px;
            color: var(--text-muted);
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
            color: var(--text-primary);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 45;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .content-grid { grid-template-columns: 1fr; }
            .content-grid-3 { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.active { display: block; }
            .burger-menu { display: flex; }
            .main-content { margin-left: 0; padding: 20px 16px; padding-top: 70px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .topbar { flex-direction: column; align-items: flex-start; gap: 12px; }
            .action-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @yield('styles')
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
            <div class="sidebar-subtitle">Portal Karyawan</div>

            <nav class="sidebar-nav">
                <div class="nav-label">Menu Utama</div>
                <a class="nav-item {{ request()->routeIs('portal.index') ? 'active' : '' }}" href="{{ route('portal.index') }}">
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
                <a class="nav-item {{ request()->routeIs('portal.attendance') ? 'active' : '' }}" href="{{ route('portal.attendance') }}">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 6v6l4 2"></path>
                        </svg>
                    </span>
                    Absensi
                </a>
                <a class="nav-item {{ request()->routeIs('portal.schedule') ? 'active' : '' }}" href="{{ route('portal.schedule') }}">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M8 2v4"></path>
                            <path d="M16 2v4"></path>
                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                            <path d="M3 10h18"></path>
                        </svg>
                    </span>
                    Jadwal Kerja
                </a>
                <a class="nav-item {{ request()->routeIs('portal.swap') ? 'active' : '' }}" href="{{ route('portal.swap') }}">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M7 7h10l-3-3"></path>
                            <path d="M17 17H7l3 3"></path>
                        </svg>
                    </span>
                    Tukar Libur
                </a>

                <div class="nav-label">Lainnya</div>
                <a class="nav-item" href="/">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 7h4l2-3h4l2 3h4v12H4z"></path>
                            <circle cx="12" cy="13" r="3.5"></circle>
                        </svg>
                    </span>
                    Scanner
                </a>
            </nav>

            <div class="sidebar-user">
                <div class="user-card">
                    <div class="user-avatar">{{ strtoupper(substr($employee->name, 0, 2)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ $employee->name }}</div>
                        <div class="user-role">{{ $employee->position ?? 'Karyawan' }}</div>
                    </div>
                </div>
            </div>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('portal.logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                        </span>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `alert-toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }
    </script>
    @yield('scripts')
</body>
</html>
