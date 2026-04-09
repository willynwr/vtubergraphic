<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Access - VtuberGraphic</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-1: #fff5f9;
            --bg-2: #f4ecff;
            --card: rgba(255, 255, 255, 0.9);
            --border: rgba(179, 136, 217, 0.18);
            --text: #3d2b3a;
            --muted: #8a6b80;
            --accent: #e87bb0;
            --accent-2: #b388d9;
            --danger: #e87070;
            --shadow: 0 24px 60px rgba(179, 136, 217, 0.14);
        }

        body {
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(232, 123, 176, 0.22), transparent 34%),
                radial-gradient(circle at bottom right, rgba(179, 136, 217, 0.22), transparent 30%),
                linear-gradient(135deg, var(--bg-1), var(--bg-2));
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 440px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 28px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            padding: 32px;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            inset: auto -30% -35% auto;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(232, 123, 176, 0.18), transparent 65%);
            pointer-events: none;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(232, 123, 176, 0.08);
            color: var(--accent);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        h1 {
            font-size: 30px;
            line-height: 1.05;
            margin-bottom: 10px;
        }

        p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .alert {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(232, 112, 112, 0.08);
            border: 1px solid rgba(232, 112, 112, 0.18);
            color: var(--danger);
            font-size: 13px;
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .field input {
            width: 100%;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1.5px solid var(--border);
            background: rgba(255, 255, 255, 0.72);
            font: inherit;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field input:focus {
            border-color: rgba(232, 123, 176, 0.45);
            box-shadow: 0 0 0 4px rgba(232, 123, 176, 0.12);
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            border: none;
            border-radius: 16px;
            padding: 14px 16px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            box-shadow: 0 12px 24px rgba(232, 123, 176, 0.22);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.75);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .hint {
            margin-top: 18px;
            font-size: 12px;
            color: var(--muted);
            text-align: center;
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="eyebrow">VtuberGraphic - Admin Login</div>
        @if ($errors->any())
            <div class="alert">{{ $errors->first('password') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.password.check') }}">
            @csrf
            <div class="field">
                <label for="password"></label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                    autofocus
                >
            </div>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Login</button>
                <a class="btn btn-secondary" href="{{ url('/') }}" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">Batal</a>
            </div>
        </form>

    </main>
</body>
</html>