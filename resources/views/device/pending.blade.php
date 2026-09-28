<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perangkat Baru Terdeteksi — Jurnal Sekolah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-hover: #1e40af;
            --warning: #f59e0b;
            --warning-bg: #fef3c7;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --bg-page: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        body {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .container {
            width: 100%;
            max-width: 520px;
        }
        .card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08), 0 20px 25px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border);
            padding: 36px 32px;
            text-align: center;
        }
        .icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #fffbeb;
            border: 2px solid #fde68a;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #d97706;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            background: #fef3c7;
            color: #92400e;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 10px;
        }
        p {
            font-size: 13.5px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .notice-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 18px;
            text-align: left;
            margin-bottom: 24px;
            font-size: 13px;
        }
        .notice-box ul {
            margin-left: 20px;
            margin-top: 8px;
            color: #475569;
            line-height: 1.5;
        }
        .notice-box li {
            margin-bottom: 6px;
        }
        .alert {
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            text-align: left;
        }
        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .alert-info {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .form-group {
            text-align: left;
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 13.5px;
            font-family: inherit;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            width: 100%;
        }
        .btn-primary {
            background: var(--primary);
            color: #ffffff;
        }
        .btn-primary:hover {
            background: var(--primary-hover);
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            margin-top: 10px;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .footer-note {
            font-size: 11.5px;
            color: #94a3b8;
            margin-top: 20px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="icon-circle">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                <line x1="12" y1="18" x2="12.01" y2="18"></line>
            </svg>
        </div>

        <div class="badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            1 Akun = 1 Perangkat
        </div>

        <h1>Perangkat Baru Terdeteksi</h1>

        <p>
            Akun Anda terikat pada 1 perangkat. Demi keamanan data sekolah dan presensi, login dari perangkat atau browser baru wajib mendapat <strong>persetujuan atasan / Administrator</strong> terlebih dahulu.
        </p>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        <div class="notice-box">
            <strong style="color: #1e293b;">📋 Informasi &amp; Langkah Selanjutnya:</strong>
            <ul>
                <li>Permintaan akses perangkat ini telah diteruskan secara otomatis ke <strong>Administrator Sekolah</strong>.</li>
                <li>Jika Anda baru saja <strong>ganti HP</strong>, Anda dapat mengisi alasan di bawah agar mempercepat verifikasi.</li>
                <li>Setelah disetujui, silakan <strong>login kembali</strong> dari perangkat/browser ini.</li>
            </ul>
        </div>

        <!-- FORM ALASAN PERGANTIAN PERANGKAT -->
        <form action="{{ route('device.keterangan') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="username">Username / NIP / NISN Akun Anda</label>
                <input type="text" id="username" name="username" class="form-control" value="{{ session('device_blocked_username', old('username')) }}" placeholder="Masukkan username akun Anda" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="keterangan">Alasan Pergantian Perangkat (Opsional)</label>
                <textarea id="keterangan" name="keterangan" rows="2" class="form-control" placeholder="Contoh: Ganti HP baru, HP lama rusak, atau login dari laptop guru..." required>{{ old('keterangan') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-bottom: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                Kirim Alasan ke Administrator
            </button>
        </form>

        <a href="{{ route('login') }}" class="btn btn-secondary">
            &larr; Kembali ke Halaman Login
        </a>

        <div class="footer-note">
            Jika Anda tidak merasa login dari perangkat baru ini, segera laporkan ke Admin karena kemungkinan ada orang lain yang mengetahui password akun Anda.
        </div>
    </div>
</div>
</body>
</html>
