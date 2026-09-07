<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Masuk Kelas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; background: #fff; color: #000; }
        .container { width: 148mm; min-height: 95mm; margin: 10mm auto; padding: 8mm 10mm; border: 2px solid #000; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 6px; margin-bottom: 10px; }
        .header h2 { font-size: 14pt; letter-spacing: 1px; text-transform: uppercase; }
        .header p { font-size: 10pt; }
        .slip-title { text-align: center; font-size: 13pt; font-weight: bold; text-decoration: underline; margin: 10px 0 14px; text-transform: uppercase; }
        table.info { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.info td { padding: 3px 4px; font-size: 11.5pt; vertical-align: top; }
        table.info td:first-child { width: 38%; }
        table.info td:nth-child(2) { width: 5%; text-align: center; }
        .isi-tindakan { margin: 10px 0; font-size: 11.5pt; }
        .ttd-section { margin-top: 16px; display: flex; justify-content: space-between; }
        .ttd-box { text-align: center; width: 45%; }
        .ttd-box .ttd-space { height: 40px; border-bottom: 1px solid #000; margin: 0 10px 4px; }
        .ttd-box p { font-size: 10.5pt; }
        .no-print { display: block; text-align: center; margin-top: 16px; }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .container { margin: 0; border: 2px solid #000; width: 100%; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>SURAT IZIN MASUK KELAS</h2>
        <p>Petugas Piket — {{ config('app.name', 'SMK / SMA') }}</p>
    </div>

    <div class="slip-title">Surat Keterangan Terlambat</div>

    <table class="info">
        <tr>
            <td>Nomor</td>
            <td>:</td>
            <td>{{ str_pad($terlambat->id_terlambat, 4, '0', STR_PAD_LEFT) }}/PIKET/{{ \Carbon\Carbon::parse($terlambat->tanggal)->format('Y') }}</td>
        </tr>
        <tr>
            <td>Hari / Tanggal</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($terlambat->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</td>
        </tr>
        <tr>
            <td>Nama Siswa</td>
            <td>:</td>
            <td><strong>{{ $terlambat->siswa->nama ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td>NISN</td>
            <td>:</td>
            <td>{{ $terlambat->siswa->NISN ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $terlambat->kelas->nama_kelas ?? ($terlambat->siswa->kelas->nama_kelas ?? '-') }}</td>
        </tr>
        <tr>
            <td>Jam Kedatangan</td>
            <td>:</td>
            <td>Pukul <strong>{{ $terlambat->jam_kedatangan }}</strong> WIB</td>
        </tr>
        <tr>
            <td>Melewatkan Pelajaran</td>
            <td>:</td>
            <td>Jam ke-1 s.d Jam ke-<strong>{{ $terlambat->terlambat_sampai_jam }}</strong></td>
        </tr>
        <tr>
            <td>Alasan</td>
            <td>:</td>
            <td>{{ $terlambat->alasan }}</td>
        </tr>
    </table>

    <div class="isi-tindakan">
        <strong>Tindakan Piket:</strong> {{ $terlambat->tindakan_piket ?? 'Diberikan surat izin masuk kelas' }}
    </div>

    <p style="font-size:11pt;">Surat ini sebagai izin masuk kelas bagi siswa yang bersangkutan.</p>

    <div class="ttd-section">
        <div class="ttd-box">
            <p>Mengetahui,</p>
            <p>Petugas Piket</p>
            <div class="ttd-space"></div>
            <p><strong>{{ $terlambat->petugasPiket->nama ?? '...........................' }}</strong></p>
        </div>
        <div class="ttd-box">
            <p>{{ \Carbon\Carbon::parse($terlambat->tanggal)->locale('id')->isoFormat('D MMMM YYYY') }}</p>
            <p>Siswa Bersangkutan</p>
            <div class="ttd-space"></div>
            <p><strong>{{ $terlambat->siswa->nama ?? '...........................' }}</strong></p>
        </div>
    </div>
</div>

<div class="no-print" style="margin-top:20px;">
    <button onclick="window.print()" style="padding:8px 24px; background:#1e3a8a; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px;">🖨️ Cetak</button>
    <a href="{{ route('piket.siswa-terlambat.index') }}" style="margin-left:12px; color:#1e3a8a;">← Kembali ke Daftar</a>
</div>
<script>
    window.addEventListener('load', () => setTimeout(() => window.print(), 400));
</script>
</body>
</html>
