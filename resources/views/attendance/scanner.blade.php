<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VtuberGraphic - Absensi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-primary: #fef7ff;
            --bg-secondary: #fff0f6;
            --bg-card: rgba(255, 255, 255, 0.85);
            --bg-glass: rgba(255, 230, 240, 0.35);
            --border-glass: rgba(219, 160, 190, 0.25);
            --text-primary: #3d2b3a;
            --text-secondary: #8a6b80;
            --text-muted: #b8a0b0;
            --accent-pink: #e87bb0;
            --accent-purple: #b388d9;
            --accent-lavender: #c9a0dc;
            --accent-peach: #f4a9c0;
            --accent-mint: #8dd4b0;
            --accent-blue: #7eb8e0;
            --accent-orange: #f0b86e;
            --accent-red: #e87070;
            --accent-lilac: #d4a5e5;
            --gradient-primary: linear-gradient(135deg, #e87bb0, #b388d9, #8bb8e8);
            --gradient-pink: linear-gradient(135deg, #f4a9c0, #e87bb0);
            --gradient-soft: linear-gradient(135deg, #fce4ec, #f3e5f5, #e8eaf6);
            --gradient-success: linear-gradient(135deg, #8dd4b0, #7eb8e0);
            --gradient-danger: linear-gradient(135deg, #e87070, #e87bb0);
            --shadow-soft: 0 8px 32px rgba(180, 120, 160, 0.12);
            --shadow-glow: 0 0 40px rgba(232, 123, 176, 0.15);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gradient-soft);
            background-attachment: fixed;
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated background */
        .bg-animation {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .bg-animation .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.25;
            animation: float 20s infinite ease-in-out;
        }

        .bg-animation .orb:nth-child(1) {
            width: 350px; height: 350px;
            background: #f4a9c0;
            top: -80px; left: -60px;
            animation-delay: 0s;
        }

        .bg-animation .orb:nth-child(2) {
            width: 280px; height: 280px;
            background: #b388d9;
            top: 40%; right: -60px;
            animation-delay: -5s;
        }

        .bg-animation .orb:nth-child(3) {
            width: 320px; height: 320px;
            background: #8bb8e8;
            bottom: -80px; left: 25%;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -30px) scale(1.05); }
            50% { transform: translate(-20px, 20px) scale(0.95); }
            75% { transform: translate(15px, 10px) scale(1.02); }
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 480px;
            margin: 0 auto;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 24px 0 16px;
        }

        .header .logo-img {
            width: 160px;
            height: auto;
            margin-bottom: 2px;
            filter: drop-shadow(0 4px 12px rgba(232, 123, 176, 0.25));
        }

        .header .subtitle {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 500;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .header .date-time {
            margin-top: 14px;
            padding: 12px 20px;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 18px;
            display: inline-flex;
            gap: 16px;
            align-items: center;
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow-soft);
        }

        .header .current-time {
            font-size: 22px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: var(--accent-pink);
        }

        .header .current-date {
            font-size: 11px;
            color: var(--text-secondary);
            text-align: left;
            line-height: 1.4;
        }

        /* Scanner Section */
        .scanner-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }

        .scanner-wrapper {
            width: 100%;
            max-width: 350px;
            position: relative;
        }

        .scanner-frame {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            background: var(--bg-card);
            border: 2px solid var(--border-glass);
            box-shadow: var(--shadow-glow);
        }

        .scanner-frame::before {
            content: '';
            position: absolute;
            top: -2px; left: -2px;
            right: -2px; bottom: -2px;
            background: var(--gradient-primary);
            border-radius: 26px;
            z-index: -1;
            opacity: 0.4;
            animation: pulse-border 3s infinite;
        }

        @keyframes pulse-border {
            0%, 100% { opacity: 0.25; }
            50% { opacity: 0.55; }
        }

        #qr-reader {
            width: 100%;
            border-radius: 24px;
            overflow: hidden;
        }

        #qr-reader video {
            border-radius: 24px;
        }

        #qr-reader__scan_region {
            min-height: 280px;
        }

        #qr-reader__dashboard {
            display: none !important;
        }

        .scanner-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 5;
        }

        .scanner-corners {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 180px; height: 180px;
        }

        .scanner-corners .corner {
            position: absolute;
            width: 30px; height: 30px;
            border-color: var(--accent-pink);
            border-style: solid;
        }

        .corner.tl { top: 0; left: 0; border-width: 3px 0 0 3px; border-radius: 8px 0 0 0; }
        .corner.tr { top: 0; right: 0; border-width: 3px 3px 0 0; border-radius: 0 8px 0 0; }
        .corner.bl { bottom: 0; left: 0; border-width: 0 0 3px 3px; border-radius: 0 0 0 8px; }
        .corner.br { bottom: 0; right: 0; border-width: 0 3px 3px 0; border-radius: 0 0 8px 0; }

        .scan-line {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 180px; height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent-pink), transparent);
            animation: scan-move 2s infinite ease-in-out;
            box-shadow: 0 0 15px rgba(232, 123, 176, 0.4);
        }

        @keyframes scan-move {
            0%, 100% { margin-top: -90px; }
            50% { margin-top: 90px; }
        }

        .scanner-hint {
            text-align: center;
            color: var(--text-secondary);
            font-size: 13px;
            padding: 12px;
        }

        .scanner-hint .icon {
            font-size: 18px;
            margin-bottom: 2px;
        }

        /* Close Camera Button */
        .btn-close-camera {
            position: absolute;
            top: 12px; right: 12px;
            z-index: 10;
            width: 40px; height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid var(--border-glass);
            color: var(--accent-red);
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-close-camera:hover {
            background: var(--accent-red);
            color: white;
            transform: scale(1.1);
        }

        /* Buttons */
        .btn-start-scanner {
            width: 100%;
            max-width: 350px;
            padding: 18px;
            border: none;
            border-radius: 20px;
            background: var(--gradient-pink);
            color: white;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(232, 123, 176, 0.3);
        }

        .btn-start-scanner:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(232, 123, 176, 0.4);
        }

        .btn-start-scanner:active {
            transform: translateY(0);
        }

        /* Upload QR Section */
        .upload-section {
            width: 100%;
            max-width: 350px;
        }

        .btn-upload-qr {
            width: 100%;
            padding: 16px;
            border: 2px dashed var(--border-glass);
            border-radius: 18px;
            background: var(--bg-card);
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-soft);
        }

        .btn-upload-qr:hover {
            border-color: var(--accent-pink);
            color: var(--accent-pink);
            background: rgba(255, 230, 240, 0.5);
            transform: translateY(-2px);
        }

        .upload-preview {
            display: none;
            margin-top: 12px;
            padding: 16px;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 18px;
            text-align: center;
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-soft);
        }

        .upload-preview.active {
            display: block;
            animation: slideUp 0.3s ease;
        }

        .upload-preview img {
            max-width: 180px;
            max-height: 180px;
            border-radius: 12px;
            border: 1px solid var(--border-glass);
            margin-bottom: 12px;
        }

        .upload-preview .upload-status {
            font-size: 13px;
            color: var(--accent-pink);
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .upload-preview .upload-status.scanning {
            color: var(--accent-purple);
        }

        .upload-preview .upload-status.error {
            color: var(--accent-red);
        }

        #fileInput {
            display: none;
        }

        /* Manual Input */
        .manual-input-section {
            width: 100%;
            max-width: 350px;
            margin-top: 4px;
        }

        .manual-toggle {
            width: 100%;
            padding: 14px;
            border: 1px solid var(--border-glass);
            border-radius: 16px;
            background: var(--bg-card);
            color: var(--text-secondary);
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-soft);
        }

        .manual-toggle:hover {
            border-color: var(--accent-purple);
            color: var(--accent-purple);
        }

        .manual-form {
            display: none;
            margin-top: 12px;
            padding: 20px;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-soft);
        }

        .manual-form.active {
            display: block;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-group {
            display: flex;
            gap: 10px;
        }

        .input-group input {
            flex: 1;
            padding: 13px 18px;
            border: 1.5px solid var(--border-glass);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.6);
            color: var(--text-primary);
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: var(--accent-pink);
            box-shadow: 0 0 20px rgba(232, 123, 176, 0.12);
        }

        .input-group input::placeholder {
            color: var(--text-muted);
        }

        .input-group button {
            padding: 13px 20px;
            border: none;
            border-radius: 14px;
            background: var(--gradient-pink);
            color: white;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
        }

        .input-group button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(232, 123, 176, 0.3);
        }

        /* OR Divider */
        .or-divider {
            width: 100%;
            max-width: 350px;
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 4px 0;
        }

        .or-divider::before, .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-glass);
        }

        .or-divider span {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        /* Action Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(61, 43, 58, 0.45);
            backdrop-filter: blur(12px);
            z-index: 100;
            justify-content: center;
            align-items: flex-end;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border: 1px solid var(--border-glass);
            border-radius: 28px 28px 20px 20px;
            padding: 28px 24px 24px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUpModal 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 -10px 50px rgba(180, 120, 160, 0.15);
        }

        @keyframes slideUpModal {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        .modal-handle {
            width: 40px; height: 4px;
            background: #ddd;
            border-radius: 2px;
            margin: 0 auto 22px;
        }

        .employee-info {
            text-align: center;
            margin-bottom: 22px;
        }

        .employee-avatar {
            width: 70px; height: 70px;
            border-radius: 22px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 800;
            color: white;
            margin: 0 auto 12px;
            box-shadow: 0 8px 25px rgba(232, 123, 176, 0.25);
        }

        .employee-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .employee-detail {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .employee-id-badge {
            display: inline-block;
            padding: 5px 14px;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 10px;
            font-size: 12px;
            color: var(--accent-purple);
            margin-top: 8px;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        /* Today's status */
        .today-status {
            padding: 14px;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 16px;
            margin-bottom: 18px;
        }

        .today-status-title {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .today-records {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .today-record-item {
            padding: 5px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .today-record-item.type-IN {
            background: rgba(141, 212, 176, 0.15);
            color: #4a9a72;
        }

        .today-record-item.type-OUT {
            background: rgba(126, 184, 224, 0.15);
            color: #4a7fa8;
        }

        .today-record-item.type-IZIN,
        .today-record-item.type-SAKIT {
            background: rgba(240, 184, 110, 0.15);
            color: #b08940;
        }

        .work-duration-badge {
            padding: 8px 14px;
            background: rgba(179, 136, 217, 0.1);
            border: 1px solid rgba(179, 136, 217, 0.2);
            border-radius: 10px;
            font-size: 13px;
            color: var(--accent-purple);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
        }

        /* Action Buttons */
        .action-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }

        .action-btn {
            padding: 18px 14px;
            border: 2px solid transparent;
            border-radius: 18px;
            background: #fff;
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .action-btn:hover {
            transform: translateY(-3px);
        }

        .action-btn.selected {
            transform: translateY(-3px);
        }

        .action-btn .action-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7), 0 8px 18px rgba(61, 43, 58, 0.08);
            color: currentColor;
        }

        .action-btn .action-icon svg {
            width: 24px;
            height: 24px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .action-btn.btn-in {
            border-color: rgba(141, 212, 176, 0.35);
            background: rgba(141, 212, 176, 0.06);
            color: #4a9a72;
        }
        .action-btn.btn-in.selected, .action-btn.btn-in:hover {
            border-color: var(--accent-mint);
            background: rgba(141, 212, 176, 0.12);
            box-shadow: 0 8px 25px rgba(141, 212, 176, 0.2);
        }

        .action-btn.btn-out {
            border-color: rgba(126, 184, 224, 0.35);
            background: rgba(126, 184, 224, 0.06);
            color: #4a7fa8;
        }
        .action-btn.btn-out.selected, .action-btn.btn-out:hover {
            border-color: var(--accent-blue);
            background: rgba(126, 184, 224, 0.12);
            box-shadow: 0 8px 25px rgba(126, 184, 224, 0.2);
        }

        .action-btn.btn-izin {
            border-color: rgba(240, 184, 110, 0.35);
            background: rgba(240, 184, 110, 0.06);
            color: #b08940;
        }
        .action-btn.btn-izin.selected, .action-btn.btn-izin:hover {
            border-color: var(--accent-orange);
            background: rgba(240, 184, 110, 0.12);
            box-shadow: 0 8px 25px rgba(240, 184, 110, 0.2);
        }

        .action-btn.btn-sakit {
            border-color: rgba(232, 112, 112, 0.35);
            background: rgba(232, 112, 112, 0.06);
            color: #c44e5a;
        }
        .action-btn.btn-sakit.selected, .action-btn.btn-sakit:hover {
            border-color: var(--accent-red);
            background: rgba(232, 112, 112, 0.12);
            box-shadow: 0 8px 25px rgba(232, 112, 112, 0.2);
        }

        .action-btn.btn-tukar {
            border-color: rgba(212, 165, 229, 0.35);
            background: rgba(212, 165, 229, 0.06);
            grid-column: span 2;
            color: #9a73c2;
        }
        .action-btn.btn-tukar.selected, .action-btn.btn-tukar:hover {
            border-color: var(--accent-lilac);
            background: rgba(212, 165, 229, 0.12);
            box-shadow: 0 8px 25px rgba(212, 165, 229, 0.2);
        }

        .action-btn.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Note input */
        .note-section {
            display: none;
            margin-bottom: 14px;
        }

        .note-section.active {
            display: block;
            animation: slideUp 0.3s ease;
        }

        .note-section textarea {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid var(--border-glass);
            border-radius: 16px;
            background: rgba(255,255,255,0.6);
            color: var(--text-primary);
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            resize: none;
            min-height: 76px;
            transition: all 0.3s;
        }

        .note-section textarea:focus {
            border-color: var(--accent-pink);
            box-shadow: 0 0 16px rgba(232, 123, 176, 0.1);
        }

        .note-section textarea::placeholder {
            color: var(--text-muted);
        }

        .note-label {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 8px;
            font-weight: 500;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 18px;
            background: var(--gradient-primary);
            color: white;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 25px rgba(232, 123, 176, 0.25);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(232, 123, 176, 0.35);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-submit .spinner {
            display: none;
            width: 20px; height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        .btn-submit.loading .spinner { display: block; }
        .btn-submit.loading .btn-text { display: none; }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-cancel {
            width: 100%;
            padding: 13px;
            border: 1px solid var(--border-glass);
            border-radius: 14px;
            background: transparent;
            color: var(--text-secondary);
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background: var(--bg-glass);
            color: var(--text-primary);
        }

        /* Result Modal */
        .result-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(61, 43, 58, 0.6);
            backdrop-filter: blur(12px);
            z-index: 200;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .result-overlay.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        .result-card {
            width: 100%;
            max-width: 400px;
            text-align: center;
            padding: 40px 30px;
            background: #fff;
            border: 1px solid var(--border-glass);
            border-radius: 28px;
            animation: scaleIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 20px 60px rgba(180, 120, 160, 0.2);
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .result-icon {
            width: 80px; height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            margin: 0 auto 18px;
        }

        .result-icon.success {
            background: rgba(141, 212, 176, 0.15);
            box-shadow: 0 0 40px rgba(141, 212, 176, 0.15);
        }

        .result-icon.error {
            background: rgba(232, 112, 112, 0.12);
            box-shadow: 0 0 40px rgba(232, 112, 112, 0.12);
        }

        .result-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .result-message {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .result-detail {
            padding: 16px;
            background: var(--bg-glass);
            border-radius: 14px;
            margin: 14px 0;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .result-detail .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .result-detail .detail-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .btn-result-close {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 16px;
            background: var(--gradient-pink);
            color: white;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(232, 123, 176, 0.25);
        }

        .btn-result-close:hover {
            transform: translateY(-2px);
        }

        /* GPS Status */
        .gps-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 14px;
        }

        .gps-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--accent-orange);
            animation: blink 1.5s infinite;
        }

        .gps-dot.active {
            background: var(--accent-mint);
            animation: none;
        }

        .gps-dot.error {
            background: var(--accent-red);
            animation: none;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Admin link */
        .admin-link {
            text-align: center;
            padding: 16px 0;
        }

        .admin-link a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 12px;
            padding: 10px 20px;
            border: 1px solid var(--border-glass);
            border-radius: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-soft);
        }

        .admin-link a:hover {
            color: var(--accent-pink);
            border-color: var(--accent-pink);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 380px) {
            .container { padding: 12px; }
            .modal-content { padding: 22px 16px 16px; }
            .action-grid { gap: 8px; }
        }
    </style>
</head>
<body>
    <div class="bg-animation">
        <div class="orb"></div>
        <div class="orb"></div>
        <div class="orb"></div>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=1x1&data=logo" class="logo-img" style="display:none;" alt="">
            <div style="font-size:32px;font-weight:900;margin-bottom:2px;">
                <span style="background:var(--gradient-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">VtuberGraphic</span>
            </div>
            <div class="subtitle">office</div>
            <div class="date-time">
                <div class="current-time" id="currentTime">--:--:--</div>
                <div class="current-date" id="currentDate">Loading...</div>
            </div>
        </div>

        <!-- Scanner -->
        <div class="scanner-section">
            <div class="scanner-wrapper" id="scannerContainer" style="display:none;">
                <div class="scanner-frame">
                    <button class="btn-close-camera" onclick="closeCamera()" title="Tutup Kamera">✕</button>
                    <div id="qr-reader"></div>
                    <div class="scanner-overlay">
                        <div class="scanner-corners">
                            <div class="corner tl"></div>
                            <div class="corner tr"></div>
                            <div class="corner bl"></div>
                            <div class="corner br"></div>
                        </div>
                        <div class="scan-line"></div>
                    </div>
                </div>
                <div class="scanner-hint">
                    <div class="icon">📷</div>
                    Arahkan kamera ke QR Code karyawan
                </div>
            </div>

            <button class="btn-start-scanner" id="btnStartScanner" onclick="startScanner()">
                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" style="stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;">
                    <path d="M4 8V6a2 2 0 0 1 2-2h2"></path>
                    <path d="M20 8V6a2 2 0 0 0-2-2h-2"></path>
                    <path d="M4 16v2a2 2 0 0 0 2 2h2"></path>
                    <path d="M20 16v2a2 2 0 0 1-2 2h-2"></path>
                    <path d="M8 12h8"></path>
                </svg>
                <span>Login</span>
            </button>

            <!-- <div class="or-divider"><span>atau</span></div> -->

            <!-- Upload QR -->
            <!-- <div class="upload-section">
                <input type="file" id="fileInput" accept="image/*" onchange="handleFileUpload(event)">
                <button class="btn-upload-qr" onclick="document.getElementById('fileInput').click()">
                    <span>🖼️</span>
                    <span>Upload Gambar QR Code</span>
                </button>
                <div class="upload-preview" id="uploadPreview">
                    <img id="uploadImage" src="" alt="QR Preview">
                    <div class="upload-status" id="uploadStatus">
                        <span>⏳</span> Memindai QR Code...
                    </div>
                </div>
            </div> -->

            <!-- Manual Input -->
            <!-- <div class="manual-input-section">
                <button class="manual-toggle" onclick="toggleManualInput()">
                    ⌨️ Masukkan ID karyawan manual
                </button>
                <div class="manual-form" id="manualForm">
                    <div class="input-group">
                        <input type="text" id="manualEmployeeId" placeholder="Contoh: VTG-001" autocomplete="off">
                        <button onclick="submitManualId()">Cari</button>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Admin Link -->
        <!-- <div class="admin-link">
            <a href="/admin">
                <span>⚙️</span>
                Admin Dashboard
            </a>
        </div> -->
    </div>

    <!-- Action Modal -->
    <div class="modal-overlay" id="actionModal">
        <div class="modal-content">
            <div class="modal-handle"></div>

            <div class="employee-info">
                <div class="employee-avatar" id="employeeAvatar">A</div>
                <div class="employee-name" id="employeeName">-</div>
                <div class="employee-detail" id="employeePosition">-</div>
                <div class="employee-id-badge" id="employeeIdBadge">-</div>
            </div>

            <!-- Today Status -->
            <div class="today-status" id="todayStatus" style="display:none;">
                <div class="today-status-title">📋 Status Hari Ini</div>
                <div class="today-records" id="todayRecords"></div>
                <div class="work-duration-badge" id="workDuration" style="display:none;">
                    <span>⏱️</span>
                    <span id="workDurationText">-</span>
                </div>
            </div>

            <!-- GPS Status -->
            <div class="gps-status" id="gpsStatus">
                <div class="gps-dot" id="gpsDot"></div>
                <span id="gpsText">Mengambil lokasi GPS...</span>
            </div>

            <!-- Action Buttons -->
            <div class="action-title">Pilih Aksi Absensi</div>
            <div class="action-grid">
                <button class="action-btn btn-in" onclick="selectAction('IN')" id="btnIN">
                    <span class="action-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M10 17l5-5-5-5"></path>
                            <path d="M15 12H4"></path>
                            <path d="M20 4v16"></path>
                        </svg>
                    </span>
                    Masuk
                </button>
                <button class="action-btn btn-out" onclick="selectAction('OUT')" id="btnOUT">
                    <span class="action-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 7l-5 5 5 5"></path>
                            <path d="M9 12h11"></path>
                            <path d="M4 4v16"></path>
                        </svg>
                    </span>
                    Pulang
                </button>
                <button class="action-btn btn-izin" onclick="selectAction('IZIN')" id="btnIZIN">
                    <span class="action-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M7 4h6l4 4v12H7z"></path>
                            <path d="M13 4v4h4"></path>
                            <path d="M9 13h6"></path>
                            <path d="M9 17h4"></path>
                        </svg>
                    </span>
                    Izin
                </button>
                <button class="action-btn btn-sakit" onclick="selectAction('SAKIT')" id="btnSAKIT">
                    <span class="action-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M20.8 8.6c0 5.2-8.8 11.4-8.8 11.4S3.2 13.8 3.2 8.6A4.6 4.6 0 0 1 7.8 4c1.7 0 3.1.8 4.2 2.2C13.1 4.8 14.5 4 16.2 4a4.6 4.6 0 0 1 4.6 4.6z"></path>
                            <path d="M12 8v4"></path>
                            <path d="M10 10h4"></path>
                        </svg>
                    </span>
                    Sakit
                </button>
                <button class="action-btn btn-tukar" onclick="selectAction('TUKAR_LIBUR')" id="btnTUKAR_LIBUR">
                    <span class="action-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M16 3l4 4-4 4"></path>
                            <path d="M20 7H8a4 4 0 0 0-4 4v1"></path>
                            <path d="M8 21l-4-4 4-4"></path>
                            <path d="M4 17h12a4 4 0 0 0 4-4v-1"></path>
                        </svg>
                    </span>
                    Tukar Libur
                </button>
            </div>

            <!-- Note -->
            <div class="note-section" id="noteSection">
                <div class="note-label">Catatan (opsional)</div>
                <textarea id="noteInput" placeholder="Tuliskan keterangan..."></textarea>
            </div>

            <!-- Submit -->
            <button class="btn-submit" id="btnSubmit" onclick="submitAttendance()" disabled>
                <span class="spinner"></span>
                <span class="btn-text">Pilih aksi terlebih dahulu</span>
            </button>
            <button class="btn-cancel" onclick="closeModal()">Batal</button>
        </div>
    </div>

    <!-- Result Modal -->
    <div class="result-overlay" id="resultModal">
        <div class="result-card">
            <div class="result-icon" id="resultIcon">✅</div>
            <div class="result-title" id="resultTitle">Berhasil!</div>
            <div class="result-message" id="resultMessage">-</div>
            <div class="result-detail" id="resultDetail"></div>
            <button class="btn-result-close" onclick="closeResult()">Selesai</button>
        </div>
    </div>

    <script>
        // State
        let currentEmployee = null;
        let selectedAction = null;
        let gpsPosition = null;
        let html5QrcodeScanner = null;
        let scannerRunning = false;

        // CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Clock
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const date = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            document.getElementById('currentTime').textContent = time;
            document.getElementById('currentDate').textContent = date;
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Scanner
        async function startScanner() {
            try {
                document.getElementById('btnStartScanner').style.display = 'none';
                document.getElementById('scannerContainer').style.display = 'block';

                html5QrcodeScanner = new Html5Qrcode("qr-reader");
                await html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 200, height: 200 }, aspectRatio: 1.0 },
                    onScanSuccess,
                    () => {}
                );
                scannerRunning = true;
            } catch (err) {
                console.error('Camera error:', err);
                showResult(false, 'Kamera Error', 'Tidak bisa mengakses kamera. Pastikan izin kamera sudah diberikan.');
                closeCamera();
            }
        }

        async function stopScanner() {
            if (html5QrcodeScanner && scannerRunning) {
                try {
                    await html5QrcodeScanner.stop();
                    scannerRunning = false;
                } catch (e) {}
            }
        }

        function closeCamera() {
            stopScanner();
            document.getElementById('scannerContainer').style.display = 'none';
            document.getElementById('btnStartScanner').style.display = 'flex';
        }

        function onScanSuccess(decodedText) {
            stopScanner();
            validateEmployee(decodedText.trim());
        }

        // Upload QR Image
        async function handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Show preview
            const preview = document.getElementById('uploadPreview');
            const img = document.getElementById('uploadImage');
            const status = document.getElementById('uploadStatus');

            preview.classList.add('active');
            img.src = URL.createObjectURL(file);
            status.className = 'upload-status scanning';
            status.innerHTML = '<span>⏳</span> Memindai QR Code...';

            try {
                const html5Qr = new Html5Qrcode("qr-upload-temp");
                const result = await html5Qr.scanFile(file, true);
                html5Qr.clear();

                status.className = 'upload-status';
                status.innerHTML = '<span>✅</span> QR Code ditemukan: <strong>' + result + '</strong>';

                // Validate employee
                setTimeout(() => {
                    validateEmployee(result.trim());
                }, 800);
            } catch (err) {
                status.className = 'upload-status error';
                status.innerHTML = '<span>❌</span> Gagal membaca QR Code. Pastikan gambar berisi QR Code yang valid.';
            }

            // Reset file input
            event.target.value = '';
        }

        // Manual input
        function toggleManualInput() {
            const form = document.getElementById('manualForm');
            form.classList.toggle('active');
            if (form.classList.contains('active')) {
                document.getElementById('manualEmployeeId').focus();
            }
        }

        function submitManualId() {
            const id = document.getElementById('manualEmployeeId').value.trim();
            if (id) {
                validateEmployee(id);
            }
        }

        document.getElementById('manualEmployeeId')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') submitManualId();
        });

        // Validate Employee
        async function validateEmployee(employeeId) {
            try {
                const response = await fetch('/attendance/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ employee_id: employeeId }),
                });

                const data = await response.json();

                if (!response.ok) {
                    showResult(false, 'Tidak Ditemukan', data.message);
                    return;
                }

                currentEmployee = data.employee;
                if (data.portal_url) {
                    window.location.href = data.portal_url;
                    return;
                }

                showActionModal(data);
            } catch (err) {
                showResult(false, 'Error', 'Gagal menghubungi server. Periksa koneksi internet.');
            }
        }

        // Show action modal
        function showActionModal(data) {
            const emp = data.employee;
            const initials = emp.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

            document.getElementById('employeeAvatar').textContent = initials;
            document.getElementById('employeeName').textContent = emp.name;
            document.getElementById('employeePosition').textContent = `${emp.position || ''} · ${emp.department || ''}`;
            document.getElementById('employeeIdBadge').textContent = emp.employee_id;

            // Today status
            const today = data.today;
            const statusContainer = document.getElementById('todayStatus');
            const recordsContainer = document.getElementById('todayRecords');

            if (today.records && today.records.length > 0) {
                statusContainer.style.display = 'block';
                recordsContainer.innerHTML = today.records.map(r =>
                    `<div class="today-record-item type-${r.type}">
                        <span>${r.type_label}</span>
                        <span>${r.time.substring(0, 5)}</span>
                    </div>`
                ).join('');

                if (today.work_duration_minutes) {
                    const h = Math.floor(today.work_duration_minutes / 60);
                    const m = today.work_duration_minutes % 60;
                    document.getElementById('workDuration').style.display = 'flex';
                    document.getElementById('workDurationText').textContent = `Durasi kerja: ${h} jam ${m} menit`;
                } else {
                    document.getElementById('workDuration').style.display = 'none';
                }
            } else {
                statusContainer.style.display = 'none';
            }

            // Disable buttons based on today's records
            document.getElementById('btnIN').classList.toggle('disabled', today.has_checked_in);
            document.getElementById('btnOUT').classList.toggle('disabled', today.has_checked_out || !today.has_checked_in);

            // Reset selection
            selectedAction = null;
            document.querySelectorAll('.action-btn').forEach(b => b.classList.remove('selected'));
            document.getElementById('btnSubmit').disabled = true;
            document.getElementById('btnSubmit').querySelector('.btn-text').textContent = 'Pilih aksi terlebih dahulu';
            document.getElementById('noteSection').classList.remove('active');
            document.getElementById('noteInput').value = '';

            // GPS is requested only when user selects IN/OUT.
            gpsPosition = null;
            document.getElementById('gpsDot').className = 'gps-dot';
            document.getElementById('gpsText').textContent = 'GPS hanya diperlukan untuk absen IN/OUT.';

            // Show modal
            document.getElementById('actionModal').classList.add('active');
        }

        // Select action
        function selectAction(type) {
            selectedAction = type;
            document.querySelectorAll('.action-btn').forEach(b => b.classList.remove('selected'));
            document.getElementById('btn' + type).classList.add('selected');

            const typeLabels = { IN: 'Masuk', OUT: 'Pulang', IZIN: 'Izin', SAKIT: 'Sakit', TUKAR_LIBUR: 'Tukar Libur' };
            document.getElementById('btnSubmit').disabled = false;
            document.getElementById('btnSubmit').querySelector('.btn-text').textContent = `Submit Absen ${typeLabels[type]}`;

            const needsNote = ['IZIN', 'SAKIT', 'TUKAR_LIBUR'].includes(type);
            document.getElementById('noteSection').classList.toggle('active', needsNote);

            const requiresGps = ['IN', 'OUT'].includes(type);
            if (requiresGps) {
                requestGPS();
            } else {
                gpsPosition = null;
                document.getElementById('gpsDot').className = 'gps-dot active';
                document.getElementById('gpsText').textContent = 'GPS tidak diperlukan untuk aksi ini.';
            }
        }

        // GPS
        function requestGPS() {
            const dot = document.getElementById('gpsDot');
            const text = document.getElementById('gpsText');

            if (!navigator.geolocation) {
                dot.className = 'gps-dot error';
                text.textContent = 'GPS tidak didukung browser ini';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    gpsPosition = {
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude,
                    };
                    dot.className = 'gps-dot active';
                    text.textContent = `Lokasi ditemukan (${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)})`;
                },
                (err) => {
                    gpsPosition = null;
                    dot.className = 'gps-dot error';
                    if (err.code === err.PERMISSION_DENIED) {
                        text.textContent = 'Akses lokasi ditolak. Aktifkan izin lokasi di browser.';
                    } else if (err.code === err.POSITION_UNAVAILABLE) {
                        text.textContent = 'Lokasi tidak tersedia. Pastikan GPS perangkat aktif.';
                    } else if (err.code === err.TIMEOUT) {
                        text.textContent = 'Pengambilan lokasi timeout. Coba lagi.';
                    } else {
                        text.textContent = 'Gagal mengambil lokasi. Izinkan akses GPS.';
                    }
                },
                { enableHighAccuracy: true, timeout: 15000 }
            );
        }

        // Submit attendance
        async function submitAttendance() {
            if (!selectedAction || !currentEmployee) return;

            const requiresGps = ['IN', 'OUT'].includes(selectedAction);

            if (requiresGps && !gpsPosition) {
                showBlockingError(
                    'Lokasi Tidak Terdeteksi',
                    'Lokasi GPS belum didapatkan. Mohon aktifkan GPS dan izinkan akses lokasi untuk melanjutkan absen IN/OUT.'
                );
                requestGPS();
                return;
            }

            const btn = document.getElementById('btnSubmit');
            btn.classList.add('loading');
            btn.disabled = true;

            try {
                const response = await fetch('/attendance/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        employee_id: currentEmployee.employee_id,
                        type: selectedAction,
                        ...(requiresGps && gpsPosition ? {
                            latitude: gpsPosition.latitude,
                            longitude: gpsPosition.longitude,
                        } : {}),
                        note: document.getElementById('noteInput').value,
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.distance) {
                        showBlockingError('Di Luar Radius', data.message || 'Anda berada di luar radius kantor.');
                    } else {
                        showBlockingError('Gagal', data.message || 'Absensi gagal diproses.');
                    }
                } else {
                    let details = `
                        <div class="detail-row"><span>Tanggal</span><span class="detail-value">${data.attendance.date}</span></div>
                        <div class="detail-row"><span>Waktu</span><span class="detail-value">${data.attendance.time}</span></div>
                        <div class="detail-row"><span>Tipe</span><span class="detail-value">${data.attendance.type_label}</span></div>
                    `;
                    if (data.attendance.distance_meters) {
                        details += `<div class="detail-row"><span>Jarak</span><span class="detail-value">${data.attendance.distance_meters}m</span></div>`;
                    }
                    if (data.work_duration) {
                        details += `<div class="detail-row"><span>Durasi Kerja</span><span class="detail-value">${data.work_duration}</span></div>`;
                    }
                    showResult(true, data.message, `Absensi ${currentEmployee.name}`, details);
                }
            } catch (err) {
                showResult(false, 'Error', 'Gagal mengirim data. Periksa koneksi internet.');
            }

            btn.classList.remove('loading');
            btn.disabled = false;
            closeModal();
        }

        // Close modal
        function closeModal() {
            document.getElementById('actionModal').classList.remove('active');
            selectedAction = null;
            currentEmployee = null;
            gpsPosition = null;
        }

        // Show result
        function showResult(success, title, message, detailHtml = '') {
            const icon = document.getElementById('resultIcon');
            icon.className = 'result-icon ' + (success ? 'success' : 'error');
            icon.textContent = success ? '✅' : '❌';
            document.getElementById('resultTitle').textContent = title;
            document.getElementById('resultMessage').textContent = message;
            document.getElementById('resultDetail').innerHTML = detailHtml;
            document.getElementById('resultDetail').style.display = detailHtml ? 'block' : 'none';
            document.getElementById('resultModal').classList.add('active');
        }

        function showBlockingError(title, message) {
            closeModal();
            setTimeout(() => {
                showResult(false, title, message);
            }, 120);
        }

        function closeResult() {
            window.location.reload();
        }
    </script>
    <!-- Hidden div for QR upload scanning -->
    <div id="qr-upload-temp" style="display:none;"></div>
</body>
</html>
