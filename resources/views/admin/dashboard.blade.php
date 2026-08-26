<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - VtuberGraphic Absensi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            top: 0;
            left: 0;
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

        .sidebar-footer form {
            margin-top: 8px;
        }

        .sidebar-footer .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            background: transparent;
            border: none;
            text-decoration: none;
            font-size: 13px;
            padding: 10px 8px;
            border-radius: 10px;
            transition: all 0.2s;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            text-align: left;
        }

        .sidebar-footer .logout-btn:hover {
            color: var(--accent-red);
            background: rgba(232, 112, 112, 0.08);
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
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.15;
        }

        .stat-card.card-present::after {
            background: var(--accent-green);
        }

        .stat-card.card-out::after {
            background: var(--accent-blue);
        }

        .stat-card.card-izin::after {
            background: var(--accent-orange);
        }

        .stat-card.card-sakit::after {
            background: var(--accent-red);
        }

        .stat-card.card-absent::after {
            background: var(--accent-pink);
        }

        .stat-card.card-tukar::after {
            background: var(--accent-cyan);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 14px;
        }

        .card-present .stat-icon {
            background: rgba(16, 185, 129, 0.12);
        }

        .card-out .stat-icon {
            background: rgba(59, 130, 246, 0.12);
        }

        .card-izin .stat-icon {
            background: rgba(245, 158, 11, 0.12);
        }

        .card-sakit .stat-icon {
            background: rgba(239, 68, 68, 0.12);
        }

        .card-absent .stat-icon {
            background: rgba(236, 72, 153, 0.12);
        }

        .card-tukar .stat-icon {
            background: rgba(6, 182, 212, 0.12);
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 4px;
            font-variant-numeric: tabular-nums;
        }

        .card-present .stat-value {
            color: var(--accent-green);
        }

        .card-out .stat-value {
            color: var(--accent-blue);
        }

        .card-izin .stat-value {
            color: var(--accent-orange);
        }

        .card-sakit .stat-value {
            color: var(--accent-red);
        }

        .card-absent .stat-value {
            color: var(--accent-pink);
        }

        .card-tukar .stat-value {
            color: var(--accent-cyan);
        }

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
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
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
            width: 10px;
            height: 10px;
            background: var(--accent-green);
            border-radius: 50%;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
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
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(61, 43, 58, 0);
            backdrop-filter: blur(0px);
            z-index: 200;
            justify-content: center;
            align-items: center;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), backdrop-filter 0.3s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }

        .detail-modal.active {
            opacity: 1;
            background: rgba(61, 43, 58, 0.5);
            backdrop-filter: blur(10px);
            pointer-events: auto;
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
            box-shadow: 0 20px 60px rgba(61, 43, 58, 0.15), 0 0 1px rgba(0, 0, 0, 0.1);
            transform: scale(0.92) translateY(20px);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .detail-modal.active .detail-modal-content {
            transform: scale(1) translateY(0);
            box-shadow: 0 25px 70px rgba(61, 43, 58, 0.2), 0 0 1px rgba(0, 0, 0, 0.1);
        }

        .detail-modal-header {
            padding: 28px 32px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, rgba(232, 123, 176, 0.04) 0%, rgba(232, 123, 176, 0.02) 100%);
        }

        .detail-modal-header h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            letter-spacing: -0.3px;
        }

        .btn-close-modal {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid rgba(232, 123, 176, 0);
            background: rgba(232, 123, 176, 0.08);
            color: var(--text-secondary);
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
        }

        .btn-close-modal:hover {
            background: rgba(232, 123, 176, 0.15);
            border-color: rgba(232, 123, 176, 0.2);
            color: var(--accent-pink);
            transform: rotate(90deg);
        }

        .detail-modal-body {
            padding: 32px;
            overflow-y: auto;
            flex: 1;
            animation: slideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s backwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .detail-emp-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 32px;
            padding: 24px;
            background: linear-gradient(135deg, rgba(232, 123, 176, 0.08) 0%, rgba(232, 123, 176, 0.02) 100%);
            border-radius: 16px;
            border: 1px solid rgba(232, 123, 176, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .detail-emp-info:hover {
            border-color: rgba(232, 123, 176, 0.25);
            background: linear-gradient(135deg, rgba(232, 123, 176, 0.12) 0%, rgba(232, 123, 176, 0.04) 100%);
        }

        .detail-emp-avatar {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 8px 20px rgba(232, 123, 176, 0.2);
        }

        .detail-emp-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.2px;
        }

        .detail-emp-sub {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* Mobile burger */
        .burger-menu {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 60;
            width: 44px;
            height: 44px;
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
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
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
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .qr-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(180, 120, 160, 0.14);
        }

        .qr-preview {
            width: 100%;
            aspect-ratio: 1 / 1;
            display: block;
            border-radius: 14px;
            background: #fdf7fb;
            padding: 6px;
            border: 1px solid rgba(179, 136, 217, 0.2);
        }

        .qr-card-label {
            width: 100%;
            font-size: 10.5px;
            font-weight: 600;
            color: var(--text-primary);
            text-align: center;
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
            background: linear-gradient(90deg, var(--accent-pink), #b388d9);
            border-color: transparent;
            color: #fff;
            transform: translateY(-1px);
        }

        .alert-toast {
            display: none;
        }

        /* Notice Modal + Confirm Modal */
        .admin-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(61, 43, 58, 0.42);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 220ms ease;
        }

        .admin-modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .admin-modal-card {
            width: 100%;
            max-width: 400px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.94);
            padding: 24px;
            box-shadow: 0 20px 60px rgba(61, 43, 58, 0.22);
            transform: translateY(10px) scale(0.96);
            opacity: 0;
            transition: transform 260ms cubic-bezier(0.22, 1, 0.36, 1), opacity 220ms ease;
        }

        .admin-modal-overlay.show .admin-modal-card {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .admin-modal-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }

        .admin-modal-icon.icon-error {
            background: linear-gradient(135deg, #e87070, #b388d9);
            color: white;
        }

        .admin-modal-icon.icon-success {
            background: linear-gradient(135deg, #8dd4b0, #57b88b);
            color: white;
        }

        .admin-modal-icon.icon-confirm {
            background: linear-gradient(135deg, #e87bb0, #b388d9);
            color: white;
        }

        .admin-modal-icon svg {
            width: 22px;
            height: 22px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .admin-modal-title {
            font-size: 18px;
            font-weight: 800;
            text-align: center;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .admin-modal-message {
            font-size: 13px;
            text-align: center;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 18px;
        }

        .admin-modal-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .admin-modal-btn {
            flex: 1;
            padding: 12px;
            border-radius: 14px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .admin-modal-btn.btn-cancel-modal {
            background: rgba(255, 247, 251, 1);
            border: 1px solid rgba(234, 217, 228, 1);
            color: var(--text-secondary);
        }

        .admin-modal-btn.btn-cancel-modal:hover {
            background: rgba(255, 238, 247, 1);
        }

        .admin-modal-btn.btn-primary-modal {
            background: var(--gradient-primary);
            border: none;
            color: white;
            font-weight: 700;
        }

        .admin-modal-btn.btn-primary-modal:hover {
            box-shadow: 0 8px 20px rgba(232, 123, 176, 0.3);
        }

        /* Table Toolbar (Search + Filter) */
        .table-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 20px;
            flex-wrap: wrap;
        }

        .table-search {
            width: 260px;
            margin-left: auto;
            position: relative;
        }

        .table-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .table-search-icon svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .table-search input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--bg-glass);
            color: var(--text-primary);
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s;
        }

        .table-search input:focus {
            border-color: var(--accent-pink);
            box-shadow: 0 0 0 3px rgba(232, 123, 176, 0.08);
        }

        .table-search input::placeholder {
            color: var(--text-muted);
        }

        .table-filter {
            position: relative;
        }

        .table-filter select {
            padding: 10px 32px 10px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--bg-glass);
            color: var(--text-primary);
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            outline: none;
            cursor: pointer;
            transition: all 0.3s;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a6b80' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
        }

        .table-filter select:focus {
            border-color: var(--accent-pink);
            box-shadow: 0 0 0 3px rgba(232, 123, 176, 0.08);
        }

        .table-result-count {
            font-size: 11px;
            color: var(--text-muted);
            padding: 0 4px;
            white-space: nowrap;
        }

        /* Top Right Live Clock (matches user portal) */
        .user-portal-clock {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 18px;
            padding: 8px 16px;
            color: #3d2b3a;
            box-shadow: 0 8px 24px rgba(61, 43, 58, 0.12);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            height: 44px;
            /* match button heights */
        }

        .user-portal-clock-time {
            font-size: 18px;
            font-weight: 800;
            color: var(--accent-pink);
            font-variant-numeric: tabular-nums;
        }

        .user-portal-clock-divider {
            width: 1px;
            height: 24px;
            background: #ead9e4;
        }

        .user-portal-clock-date {
            font-size: 11px;
            color: #7e6a79;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.active {
                display: block;
            }

            .burger-menu {
                display: flex;
            }

            .main-content {
                margin-left: 0;
                padding: 20px 16px;
                padding-top: 70px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .table-toolbar {
                padding: 12px 14px;
            }
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
                <a class="nav-item {{ (!isset($activePage) || $activePage === 'dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" id="nav-dashboard">
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
                <a class="nav-item {{ (isset($activePage) && $activePage === 'employees') ? 'active' : '' }}" href="{{ route('admin.employees') }}" id="nav-employees">
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
                <a class="nav-item {{ (isset($activePage) && $activePage === 'locations') ? 'active' : '' }}" href="{{ route('admin.locations') }}" id="nav-locations">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11z"></path>
                            <circle cx="12" cy="10" r="2.5"></circle>
                        </svg>
                    </span>
                    Lokasi Kantor
                </a>
                <a class="nav-item {{ (isset($activePage) && $activePage === 'schedules') ? 'active' : '' }}" href="{{ route('admin.schedules') }}" id="nav-schedules">
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
                <a class="nav-item {{ (isset($activePage) && $activePage === 'calendar') ? 'active' : '' }}" href="{{ route('admin.calendar') }}" id="nav-calendar">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                            <path d="M8 14h.01"></path>
                            <path d="M12 14h.01"></path>
                            <path d="M16 14h.01"></path>
                            <path d="M8 18h.01"></path>
                            <path d="M12 18h.01"></path>
                            <path d="M16 18h.01"></path>
                        </svg>
                    </span>
                    Kalender
                </a>

                <div class="nav-label">Lainnya</div>
                <a class="nav-item {{ (isset($activePage) && $activePage === 'history') ? 'active' : '' }}" href="{{ route('admin.history') }}" id="nav-history">
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


                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                        </span>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">

            @include('admin.dashboard.sections.dashboard')
            @include('admin.dashboard.sections.employees')
            @include('admin.dashboard.sections.locations')
            @include('admin.dashboard.sections.schedules')
            @include('admin.dashboard.sections.calendar')
            @include('admin.dashboard.sections.history')
        </main>
    </div>

    <!-- Employee Detail Modal -->
    <div class="detail-modal" id="detailModal">
        <div class="detail-modal-content">
            <div class="detail-modal-header">
                <h3 id="detailModalTitle">Detail Karyawan</h3>
                <button class="btn-close-modal" onclick="closeDetailModal()" aria-label="Tutup detail">
                    <svg viewBox="0 0 24 24">
                        <path d="M6 6l12 12"></path>
                        <path d="M18 6L6 18"></path>
                    </svg>
                </button>
            </div>
            <div class="detail-modal-body" id="detailModalBody">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>

    <!-- Notice Modal (Success / Error) -->
    <div class="admin-modal-overlay" id="adminNoticeOverlay"
        onclick="if(event.target.id==='adminNoticeOverlay') closeAdminNotice()">
        <div class="admin-modal-card">
            <div class="admin-modal-icon" id="adminNoticeIcon">
                <svg id="adminNoticeIconError" viewBox="0 0 24 24">
                    <path d="M12 8v4"></path>
                    <path d="M12 16h.01"></path>
                    <path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path>
                </svg>
                <svg id="adminNoticeIconSuccess" viewBox="0 0 24 24" style="display:none;">
                    <path d="M20 6 9 17l-5-5"></path>
                </svg>
            </div>
            <div class="admin-modal-title" id="adminNoticeTitle">Error</div>
            <div class="admin-modal-message" id="adminNoticeMessage">-</div>
            <div class="admin-modal-actions">
                <button class="admin-modal-btn btn-primary-modal" onclick="closeAdminNotice()">OK</button>
            </div>
        </div>
    </div>

    <!-- Approve Swap Modal -->
    <div class="admin-modal-overlay" id="approveSwapModal" onclick="if(event.target.id==='approveSwapModal') closeApproveSwapModal()">
        <div class="admin-modal-card" style="max-width: 400px; padding: 25px;">
            <div class="admin-modal-icon icon-confirm">
                <svg viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <path d="M22 4 12 14.01l-3-3"></path>
                </svg>
            </div>
            <div class="admin-modal-title">Setujui Tukar Libur</div>
            <div class="admin-modal-message">Pilih karyawan pengganti dari <b style="color:#e87bb0;">divisi yang sama</b>, dan pastikan karyawan pengganti sedang libur di <b id="lblTargetDateApprove" style="color:#e87bb0;">tanggal target</b>.</div>
            <input type="hidden" id="approveSwapId" value="">
            <div style="margin: 15px 0;">
                <select id="approveSwapWithId" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #e0d0da; font-family: inherit; font-size: 14px; background: #faf5f8; color: #3d2b3a; outline: none; transition: border-color 0.2s;">
                    <option value="">Pilih karyawan</option>
                </select>
            </div>
            <div class="admin-modal-actions">
                <button class="admin-modal-btn btn-cancel-modal" onclick="closeApproveSwapModal()">Batal</button>
                <button class="admin-modal-btn btn-primary-modal" onclick="submitApproveSwap()">Setujui</button>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="admin-modal-overlay" id="adminConfirmOverlay"
        onclick="if(event.target.id==='adminConfirmOverlay') closeAdminConfirm()">
        <div class="admin-modal-card">
            <div class="admin-modal-icon icon-confirm">
                <svg viewBox="0 0 24 24">
                    <path d="M12 8v4"></path>
                    <path d="M12 16h.01"></path>
                    <path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path>
                </svg>
            </div>
            <div class="admin-modal-title" id="adminConfirmTitle">Konfirmasi</div>
            <div class="admin-modal-message" id="adminConfirmMessage">Yakin?</div>
            <div class="admin-modal-actions">
                <button class="admin-modal-btn btn-cancel-modal" onclick="closeAdminConfirm()">Batal</button>
                <button class="admin-modal-btn btn-primary-modal" onclick="executeAdminConfirm()">Ya, lanjut</button>
            </div>
        </div>
    </div>

    <script type="application/json" id="initialSummaryData">@json($summary)</script>
    <script type="application/json" id="initialTodaySummaryData">@json($todaySummary)</script>
    <script type="application/json" id="initialActivePage">@json($activePage ?? 'dashboard')</script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const initialSummary = JSON.parse(document.getElementById('initialSummaryData').textContent);
        const initialTodaySummary = JSON.parse(document.getElementById('initialTodaySummaryData').textContent);
        const initialPage = JSON.parse(document.getElementById('initialActivePage').textContent);

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
            document.getElementById('todayOff').textContent = data.off_today;
            document.getElementById('todayNotPresent').textContent = data.not_present;
        }

        updateTodaySummary(initialTodaySummary);

        // Load data via API
        async function loadData() {
            const month = document.getElementById('selectMonth').value;
            const year = document.getElementById('selectYear').value;

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            };

            try {
                const res = await fetch(`/admin/api/summary?month=${month}&year=${year}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const contentType = (res.headers.get('content-type') || '').toLowerCase();
                const data = contentType.includes('application/json') ? await res.json() : null;

                if (!res.ok) {
                    if (res.status === 401) {
                        showNoticeModal('Sesi admin berakhir. Silakan login ulang.', 'Sesi Berakhir');
                        setTimeout(() => {
                            window.location.href = '{{ route("admin.password.form") }}';
                        }, 800);
                        return;
                    }

                    throw new Error((data && data.message) ? data.message : 'Gagal memuat data dashboard.');
                }

                if (!data || !data.summary || !data.todaySummary || !Array.isArray(data.employeeStats) || !Array.isArray(data.recentAttendances)) {
                    throw new Error('Format data dashboard tidak valid.');
                }

                // Update stats
                setText('statIn', data.summary.total_in);
                setText('statOut', data.summary.total_out);
                setText('statIzin', data.summary.total_izin);
                setText('statSakit', data.summary.total_sakit);
                setText('statAbsen', data.summary.total_absen);
                setText('statTukar', data.summary.total_tukar_libur);

                // Update chart
                initChart(data.summary);

                // Update today
                updateTodaySummary(data.todaySummary);

                // Update employee stats table
                const empBody = document.getElementById('employeeStatsBody');
                if (empBody) {
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
                }

                // Update recent attendance
                const recBody = document.getElementById('recentAttendanceBody');
                if (recBody) {
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
                }

                // Also update history
                const histBody = document.getElementById('historyBody');
                if (histBody) {
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
                }

                showToast('Data berhasil diperbarui', 'success');
            } catch (err) {
                showToast(err.message || 'Gagal memuat data', 'error');
            }
        }

        // Navigation
        function showPage(page) {
            const allowedPages = ['dashboard', 'employees', 'locations', 'schedules', 'calendar', 'history'];
            if (!allowedPages.includes(page)) {
                page = 'dashboard';
            }

            let pageEl = document.getElementById('page-' + page);
            let navEl = document.getElementById('nav-' + page);

            if (!pageEl || !navEl) {
                page = 'dashboard';
                pageEl = document.getElementById('page-dashboard');
                navEl = document.getElementById('nav-dashboard');
            }

            if (!pageEl || !navEl) {
                return;
            }

            document.querySelectorAll('.page-section').forEach(p => {
                p.classList.remove('active');
                p.style.display = 'none';
            });
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            pageEl.classList.add('active');
            pageEl.style.display = 'block';
            navEl.classList.add('active');

            // Close sidebar on mobile
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }

        function getInitialPageFromPath() {
            const path = (window.location.pathname || '').replace(/\/+$/, '');
            const segment = path.split('/').pop();
            const routeMap = {
                'admin': 'dashboard',
                'employees': 'employees',
                'locations': 'locations',
                'schedules': 'schedules',
                'calendar': 'calendar',
                'history': 'history'
            };
            return routeMap[segment] || null;
        }

        showPage(getInitialPageFromPath() || initialPage);

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }

        document.addEventListener('click', function (event) {
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
                showNoticeModal('ID dan Nama wajib diisi.', 'Validasi Gagal');
                return;
            }

            showConfirmModal(
                'Tambah Karyawan',
                `Tambahkan karyawan "${data.name}" (${data.employee_id})?`,
                async () => {
                    try {
                        const res = await fetch('/admin/employees', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify(data),
                        });
                        const result = await res.json();

                        if (result.success) {
                            showNoticeModal(result.message, 'Berhasil', 'success', true);
                        } else {
                            showNoticeModal(result.message || 'Gagal menambahkan.', 'Gagal');
                        }
                    } catch (err) {
                        showNoticeModal('Error: ' + err.message, 'Error');
                    }
                }
            );
        }

        async function deleteEmployee(id) {
            showConfirmModal(
                'Hapus Karyawan',
                'Yakin hapus karyawan ini? Semua data absensi akan ikut terhapus.',
                async () => {
                    try {
                        const res = await fetch(`/admin/employees/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                        });
                        const result = await res.json();

                        if (result.success) {
                            document.getElementById('emp-row-' + id)?.remove();
                            showNoticeModal(result.message, 'Berhasil', 'success');
                        } else {
                            showNoticeModal(result.message || 'Gagal menghapus.', 'Gagal');
                        }
                    } catch (err) {
                        showNoticeModal('Gagal menghapus karyawan.', 'Error');
                    }
                }
            );
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

                document.getElementById('detailModalTitle').textContent = `Detail Karyawan`;
                document.getElementById('detailModalBody').innerHTML = html;
                // Trigger reflow to ensure animations work properly
                void document.getElementById('detailModal').offsetWidth;
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
                showNoticeModal('Nama, Latitude, dan Longitude wajib diisi.', 'Validasi Gagal');
                return;
            }

            showConfirmModal(
                'Tambah Lokasi',
                `Tambahkan lokasi "${data.name}" dengan radius ${data.radius_meters || 1000}m?`,
                async () => {
                    try {
                        const res = await fetch('/admin/locations', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify(data),
                        });
                        const result = await res.json();

                        if (result.success) {
                            showNoticeModal(result.message, 'Berhasil', 'success', true);
                        } else {
                            showNoticeModal(result.message || 'Gagal menambahkan.', 'Gagal');
                        }
                    } catch (err) {
                        showNoticeModal('Gagal menambahkan lokasi.', 'Error');
                    }
                }
            );
        }

        async function deleteLocation(id) {
            showConfirmModal(
                'Hapus Lokasi',
                'Yakin hapus lokasi kantor ini?',
                async () => {
                    try {
                        const res = await fetch(`/admin/locations/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                        });
                        const result = await res.json();
                        if (result.success) {
                            document.getElementById('loc-row-' + id)?.remove();
                            showNoticeModal(result.message, 'Berhasil', 'success');
                        } else {
                            showNoticeModal(result.message || 'Gagal menghapus.', 'Gagal');
                        }
                    } catch (err) {
                        showNoticeModal('Gagal menghapus lokasi.', 'Error');
                    }
                }
            );
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
            return `https://api.qrserver.com/v1/create-qr-code/?size=512x512&margin=10&qzone=1&ecc=H&data=${encodeURIComponent(employeeId)}&bgcolor=ffffff&color=6b3f73`;
        }

        async function buildEmployeeQrImage(employeeId, employeeName) {
            const qrUrl = buildQrUrl(employeeId);
            const response = await fetch(qrUrl, { mode: 'cors' });
            if (!response.ok) {
                throw new Error('QR image request failed');
            }
            const blob = await response.blob();
            const objectUrl = URL.createObjectURL(blob);

            const qrImg = await new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = objectUrl;
            });

            const qrSize = 512;
            const padding = 24;
            const labelHeight = 48;
            const width = qrSize + padding * 2;
            const height = qrSize + padding * 2 + labelHeight;

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, width, height);

            ctx.drawImage(qrImg, padding, padding, qrSize, qrSize);

            ctx.textAlign = 'center';
            ctx.fillStyle = '#3d2b3a';
            ctx.font = '600 26px Poppins, sans-serif';
            let label = `${employeeName} - ${employeeId}`;
            const maxLabelWidth = width - padding * 2;
            while (ctx.measureText(label).width > maxLabelWidth && label.length > 3) {
                label = label.slice(0, -1);
            }
            if (label !== `${employeeName} - ${employeeId}`) {
                label = label.slice(0, -1) + '…';
            }
            ctx.fillText(label, width / 2, padding + qrSize + labelHeight / 2 + 9);

            URL.revokeObjectURL(objectUrl);
            return canvas;
        }

        async function downloadEmployeeQr(employeeId, employeeName) {
            const fileName = `qr-${employeeId}.png`;

            try {
                const canvas = await buildEmployeeQrImage(employeeId, employeeName);
                const imgBlob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/png'));
                const objectUrl = URL.createObjectURL(imgBlob);
                const link = document.createElement('a');
                link.href = objectUrl;
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(objectUrl);

                showToast(`QR ${employeeName} berhasil didownload`, 'success');
            } catch (err) {
                console.error('downloadEmployeeQr failed:', err);
                const qrUrl = buildQrUrl(employeeId);
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
        async function openApproveSwapModal(id, department, targetDateStr) {
            document.getElementById('approveSwapId').value = id;
            document.getElementById('lblTargetDateApprove').innerText = targetDateStr || 'tanggal target';
            
            const selectEl = document.getElementById('approveSwapWithId');
            selectEl.innerHTML = '<option value="">Tanpa Tukar (Hanya Ubah Libur Sendiri)</option>';
            document.getElementById('approveSwapModal').classList.add('show');
            
            try {
                const res = await fetch(`/admin/swap-requests/${id}/eligible-employees`);
                const json = await res.json();

                const eligibleEmployees = (json.success && Array.isArray(json.data)) ? json.data : [];
                eligibleEmployees.forEach(emp => {
                    const opt = document.createElement('option');
                    opt.value = emp.employee_id;
                    opt.text = `Tukar Dengan: ${emp.name} — ${emp.position || '-'}`;
                    selectEl.appendChild(opt);
                });
                selectEl.selectedIndex = 0;
            } catch (e) {
                selectEl.innerHTML = '<option value="">Tanpa Tukar (Hanya Ubah Libur Sendiri)</option>';
            }
        }

        function closeApproveSwapModal() {
            document.getElementById('approveSwapModal').classList.remove('show');
            document.getElementById('approveSwapId').value = '';
            document.getElementById('approveSwapWithId').value = '';
        }

        async function submitApproveSwap() {
            const id = document.getElementById('approveSwapId').value;
            const selectEl = document.getElementById('approveSwapWithId');
            let targetId = selectEl.value;

            if (targetId === '') {
                targetId = null;
            }

            try {
                const res = await fetch(`/admin/swap-requests/${id}/approve`, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ swap_with_employee_id: targetId })
                });
                const data = await res.json();
                if (!res.ok) {
                    showNoticeModal(data.message || 'Gagal menyetujui.', 'Gagal');
                    return;
                }
                closeApproveSwapModal();
                document.getElementById('swap-row-' + id)?.remove();
                showNoticeModal('Request berhasil disetujui!', 'Berhasil', 'success', true);
            } catch (err) {
                showNoticeModal('Error saat menyetujui.', 'Error');
            }
        }

        async function approveSwap(id, departmentStr, targetDateStr) {
            // keep old function definition for compatibility if called directly, or just redirect
            openApproveSwapModal(id, departmentStr, targetDateStr);
        }

        async function rejectSwap(id) {
            showConfirmModal(
                'Tolak Tukar Libur',
                'Tolak permintaan tukar libur ini?',
                async () => {
                    try {
                        const res = await fetch(`/admin/swap-requests/${id}/reject`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                        });
                        const data = await res.json();
                        if (!res.ok) {
                            showNoticeModal(data.message || 'Gagal menolak.', 'Gagal');
                            return;
                        }
                        document.getElementById('swap-row-' + id)?.remove();
                        showNoticeModal('Request berhasil ditolak.', 'Berhasil', 'success', true);
                    } catch (err) {
                        showNoticeModal('Error saat menolak.', 'Error');
                    }
                }
            );
        }

        async function addNewScheduleDay(selectEl, employeeId) {
            const newDay = Number(selectEl.value);
            if (isNaN(newDay)) return;
            showConfirmModal('Tambah Hari Libur', 'Tambahkan hari libur ini?', async () => {
                try {
                    const addRes = await fetch('/admin/schedules', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ employee_id: employeeId, day_of_week: newDay }),
                    });
                    if (!addRes.ok) {
                        showNoticeModal('Gagal menambahkan hari libur baru.', 'Gagal');
                        selectEl.value = '';
                        return;
                    }
                    showNoticeModal('Hari libur baru berhasil ditambahkan.', 'Berhasil', 'success', true);
                } catch (err) {
                    showNoticeModal('Gagal menambahkan hari libur.', 'Error');
                    selectEl.value = '';
                }
            });
        }

        async function updateSingleScheduleDay(selectEl) {
            const scheduleId = Number(selectEl.dataset.scheduleId);
            const employeeId = selectEl.dataset.employeeId;
            const newDay = Number(selectEl.value);
            const oldDay = Number(selectEl.dataset.originalDay);

            if (!scheduleId || !employeeId) {
                showToast('Data jadwal tidak valid.', 'error');
                return;
            }

            if (newDay === oldDay) {
                return;
            }

            const row = selectEl.closest('tr');
            const allValues = Array.from(row.querySelectorAll('.schedule-day-select')).map((el) => Number(el.value));
            if (new Set(allValues).size !== allValues.length) {
                showNoticeModal('Hari libur tidak boleh duplikat.', 'Validasi Gagal');
                selectEl.value = String(oldDay);
                return;
            }

            showConfirmModal(
                'Ubah Hari Libur',
                'Yakin ingin mengubah hari libur karyawan ini?',
                async () => {
                    try {
                        const addRes = await fetch('/admin/schedules', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ employee_id: employeeId, day_of_week: newDay }),
                        });

                        if (!addRes.ok) {
                            showNoticeModal('Gagal menyimpan hari libur baru.', 'Gagal');
                            selectEl.value = String(oldDay);
                            return;
                        }

                        const deleteRes = await fetch(`/admin/schedules/${scheduleId}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                        });

                        if (!deleteRes.ok) {
                            showNoticeModal('Gagal mengganti hari libur lama.', 'Gagal');
                            selectEl.value = String(oldDay);
                            return;
                        }

                        showNoticeModal('Hari libur berhasil diubah.', 'Berhasil', 'success', true);
                    } catch (err) {
                        showNoticeModal('Gagal memperbarui hari libur.', 'Error');
                        selectEl.value = String(oldDay);
                    }
                }
            );
        }

        // showToast replaced by showNoticeModal — kept as alias for compatibility
        function showToast(message, type = 'success') {
            if (type === 'success') {
                showNoticeModal(message, 'Berhasil', 'success');
            } else {
                showNoticeModal(message, 'Gagal', 'error');
            }
        }

        // Auto refresh disabled; data updates on manual actions (month/year change or page reload).

        // ======= Notice Modal (replaces showToast) =======
        let adminNoticeShouldReload = false;

        function showNoticeModal(message, title = 'Error', variant = 'error', shouldReload = false) {
            const titleEl = document.getElementById('adminNoticeTitle');
            const messageEl = document.getElementById('adminNoticeMessage');
            const iconWrap = document.getElementById('adminNoticeIcon');
            const iconError = document.getElementById('adminNoticeIconError');
            const iconSuccess = document.getElementById('adminNoticeIconSuccess');

            if (titleEl) titleEl.textContent = title;
            if (messageEl) messageEl.textContent = message;
            adminNoticeShouldReload = shouldReload;

            if (iconWrap && iconError && iconSuccess) {
                if (variant === 'success') {
                    iconWrap.className = 'admin-modal-icon icon-success';
                    iconError.style.display = 'none';
                    iconSuccess.style.display = '';
                } else {
                    iconWrap.className = 'admin-modal-icon icon-error';
                    iconSuccess.style.display = 'none';
                    iconError.style.display = '';
                }
            }

            const overlay = document.getElementById('adminNoticeOverlay');
            if (overlay) requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function closeAdminNotice() {
            const overlay = document.getElementById('adminNoticeOverlay');
            if (overlay) overlay.classList.remove('show');
            if (adminNoticeShouldReload) {
                adminNoticeShouldReload = false;
                window.location.reload();
            }
        }

        // ======= Confirm Modal (replaces confirm()) =======
        let pendingAdminConfirmAction = null;

        function showConfirmModal(title, message, onConfirm) {
            document.getElementById('adminConfirmTitle').textContent = title;
            document.getElementById('adminConfirmMessage').textContent = message;
            pendingAdminConfirmAction = onConfirm;
            const overlay = document.getElementById('adminConfirmOverlay');
            if (overlay) requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function closeAdminConfirm() {
            const overlay = document.getElementById('adminConfirmOverlay');
            if (overlay) overlay.classList.remove('show');
            pendingAdminConfirmAction = null;
        }

        function executeAdminConfirm() {
            const action = pendingAdminConfirmAction;
            closeAdminConfirm();
            if (typeof action === 'function') action();
        }

        // Escape key closes modals
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAdminNotice();
                closeAdminConfirm();
                closeDetailModal();
                closeApproveSwapModal();
            }
        });

        // ======= Table Search & Filter Functions =======

        /**
         * Generic filter helper: shows/hides table rows based on search + filters
         */
        function filterTableRows(tbodyId, searchInputId, filters, resultCountId) {
            const tbody = document.getElementById(tbodyId);
            if (!tbody) return;

            const searchVal = document.getElementById(searchInputId)?.value?.toLowerCase()?.trim() || '';
            const rows = tbody.querySelectorAll('tr[data-search]');
            let visibleCount = 0;

            rows.forEach(row => {
                const searchMatch = !searchVal || row.dataset.search.includes(searchVal);

                let filterMatch = true;
                for (const f of filters) {
                    const selectVal = document.getElementById(f.selectId)?.value || '';
                    if (selectVal && row.dataset[f.dataAttr] !== selectVal) {
                        filterMatch = false;
                        break;
                    }
                }

                if (searchMatch && filterMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const countEl = document.getElementById(resultCountId);
            if (countEl) {
                countEl.textContent = `${visibleCount} dari ${rows.length} data`;
            }
        }

        // Employee table
        function filterEmployeeTable() {
            filterTableRows('employeeListBody', 'empSearchInput', [
                { selectId: 'empFilterDept', dataAttr: 'dept' }
            ], 'empResultCount');
        }

        // History table
        function filterHistoryTable() {
            const tbody = document.getElementById('historyBody');
            if (!tbody) return;

            const searchVal = (document.getElementById('histSearchInput')?.value || '').toLowerCase().trim();
            const typeVal = document.getElementById('histFilterType')?.value || '';
            const dateVal = document.getElementById('histFilterDate')?.value || '';

            const now = new Date();
            const todayStr = now.toISOString().split('T')[0];
            const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

            const rows = tbody.querySelectorAll('tr[data-search]');
            let visible = 0;

            rows.forEach(row => {
                const searchMatch = !searchVal || row.dataset.search.includes(searchVal);
                const typeMatch = !typeVal || row.dataset.type === typeVal;

                let dateMatch = true;
                if (dateVal && row.dataset.date) {
                    const rowDate = row.dataset.date;
                    if (dateVal === 'today') dateMatch = rowDate === todayStr;
                    else if (dateVal === 'week') dateMatch = rowDate >= weekAgo;
                    else if (dateVal === 'month') dateMatch = rowDate >= monthAgo;
                }

                if (searchMatch && typeMatch && dateMatch) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            const countEl = document.getElementById('histResultCount');
            if (countEl) countEl.textContent = `${visible} dari ${rows.length} data`;
        }

        // Location table
        function filterLocationTable() {
            filterTableRows('locationListBody', 'locSearchInput', [], 'locResultCount');
        }

        // Schedule table
        function filterScheduleTable() {
            filterTableRows('scheduleTableBody', 'schedSearchInput', [
                { selectId: 'schedFilterDept', dataAttr: 'dept' }
            ], 'schedResultCount');
        }

        // Swap request table
        function filterSwapTable() {
            filterTableRows('swapTableBody', 'swapSearchInput', [
                { selectId: 'swapFilterStatus', dataAttr: 'status' }
            ], 'swapResultCount');
        }

        // Employee stats table (dashboard)
        function filterEmpStatsTable() {
            filterTableRows('employeeStatsBody', 'empStatsSearchInput', [], 'empStatsResultCount');
        }

        // Recent attendance table (dashboard)
        function filterRecentAttTable() {
            filterTableRows('recentAttendanceBody', 'recentAttSearchInput', [
                { selectId: 'recentAttFilterType', dataAttr: 'type' },
                { selectId: 'recentAttFilterDept', dataAttr: 'dept' }
            ], 'recentAttResultCount');
        }

        // Live Clock
        function updateSidebarClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

            document.querySelectorAll('.global-clock-time').forEach(el => el.textContent = timeStr);
            document.querySelectorAll('.global-clock-date').forEach(el => el.textContent = dateStr);
        }
        setInterval(updateSidebarClock, 1000);
        updateSidebarClock();

        // Employee Modal Functions
        function openAddEmployeeModal() {
            const overlay = document.getElementById('addEmployeeOverlay');
            if (overlay) requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function closeAddEmployeeModal() {
            const overlay = document.getElementById('addEmployeeOverlay');
            if (overlay) {
                overlay.classList.remove('show');
                // Clear inputs
                document.getElementById('newEmpId').value = '';
                document.getElementById('newEmpName').value = '';
                document.getElementById('newEmpDept').value = '';
                document.getElementById('newEmpPos').value = '';
            }
        }

        // Add Employee function hook
        const originalAddEmployee = addEmployee;
        window.addEmployee = async function () {
            await originalAddEmployee();
            closeAddEmployeeModal();
        }

        // Location Modal Functions
        function openAddLocationModal() {
            const overlay = document.getElementById('addLocationOverlay');
            if (overlay) requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function closeAddLocationModal() {
            const overlay = document.getElementById('addLocationOverlay');
            if (overlay) {
                overlay.classList.remove('show');
                // Clear inputs
                document.getElementById('newLocName').value = '';
                document.getElementById('newLocLat').value = '';
                document.getElementById('newLocLng').value = '';
                document.getElementById('newLocRadius').value = '1000';
            }
        }

        // Add Location function hook
        const originalAddLocation = addLocation;
        window.addLocation = async function () {
            await originalAddLocation();
            closeAddLocationModal();
        }
    </script>
</body>

</html>