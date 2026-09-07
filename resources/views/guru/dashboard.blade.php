@extends('layouts.app')

@section('title', 'Dashboard Guru — Jurnal Sekolah')
@section('page-title', 'Dashboard Guru')

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-12">
    <div>
        <h1 class="page-title">Selamat Datang, {{ $guru->nama ?? Auth::user()->nama }}</h1>
        <p class="page-subtitle">Sistem Monitoring Mengajar KBM & Presensi Harian</p>
    </div>
    <div class="alert alert-info py-8 px-16 m-0 d-flex align-center gap-8" style="border-radius: 20px;">
        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px; height:18px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        <span id="dashboard-laptop-clock" class="fw-bold" style="font-size: 13px;">Memuat jam laptop...</span>
    </div>
</div>

@if(isset($error))
    <div class="alert alert-danger">
        <div>{{ $error }}</div>
    </div>
@endif

<!-- PENGINGAT MENGISI JURNAL -->
@if(count($pengingatJurnal) > 0)
    <div class="alert alert-warning mb-24">
        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        <div>
            <strong class="d-block mb-8">Pengingat Jurnal Harian Belum Terisi:</strong>
            <ul style="margin-left: 16px;">
                @foreach($pengingatJurnal as $p)
                    <li>{{ $p }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if($waliMonitoring)
<!-- MONITORING KELAS BIMBINGAN WALI KELAS -->
<div class="card mb-24" style="border-left: 4px solid #10b981; overflow:hidden;">
    <div class="card-header" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(59, 130, 246, 0.05)); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:14px 20px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:42px; height:42px; border-radius:10px; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center; font-size:20px;">
                🎓
            </div>
            <div>
                <h3 class="card-title" style="margin:0; font-size:16px; color:#10b981; font-weight:700;">
                    Monitoring Siswa Kelas {{ $waliMonitoring['kelas']->nama_kelas }} (Wali Kelas)
                </h3>
                <p style="margin:2px 0 0; font-size:12px; color:var(--text-muted);">
                    Tingkat {{ $waliMonitoring['kelas']->tingkat }} &bull; {{ $waliMonitoring['kelas']->jurusan->nama_jurusan ?? 'Reguler' }} &bull; Total {{ $waliMonitoring['totalSiswa'] }} Siswa
                </p>
            </div>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('walikelas.data-kelas') }}" class="btn btn-secondary btn-sm">👥 Data Siswa</a>
            <a href="{{ route('walikelas.rekap-presensi') }}" class="btn btn-secondary btn-sm">📋 Rekap Presensi</a>
            <a href="{{ route('walikelas.siswa-terlambat') }}" class="btn btn-secondary btn-sm">⏰ Terlambat</a>
            <a href="{{ route('walikelas.dashboard') }}" class="btn btn-primary btn-sm" style="background:#10b981; border-color:#10b981;">Portal Wali Kelas &rarr;</a>
        </div>
    </div>
    <div class="card-body">
        <!-- STATS PRESENSI HARI INI -->
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap:12px; margin-bottom:16px;">
            <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:12px; text-align:center;">
                <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Total Siswa</div>
                <div style="font-size:22px; font-weight:800; color:var(--navy-primary); margin-top:4px;">{{ $waliMonitoring['totalSiswa'] }}</div>
            </div>
            <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:12px; text-align:center;">
                <div style="font-size:11px; font-weight:700; color:#10b981; text-transform:uppercase;">Hadir Hari Ini</div>
                <div style="font-size:22px; font-weight:800; color:#10b981; margin-top:4px;">{{ $waliMonitoring['hadir'] }}</div>
            </div>
            <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:12px; text-align:center;">
                <div style="font-size:11px; font-weight:700; color:#f59e0b; text-transform:uppercase;">Terlambat</div>
                <div style="font-size:22px; font-weight:800; color:#f59e0b; margin-top:4px;">{{ $waliMonitoring['terlambat'] + $waliMonitoring['siswaTerlambatList']->count() }}</div>
            </div>
            <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:12px; text-align:center;">
                <div style="font-size:11px; font-weight:700; color:#3b82f6; text-transform:uppercase;">Izin / Sakit</div>
                <div style="font-size:22px; font-weight:800; color:#3b82f6; margin-top:4px;">{{ $waliMonitoring['izin'] + $waliMonitoring['sakit'] }}</div>
            </div>
            <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:12px; text-align:center;">
                <div style="font-size:11px; font-weight:700; color:#ef4444; text-transform:uppercase;">Alpa / Absen</div>
                <div style="font-size:22px; font-weight:800; color:#ef4444; margin-top:4px;">{{ $waliMonitoring['alpa'] }}</div>
            </div>
        </div>

        @if($waliMonitoring['siswaTerlambatList']->count() > 0 || $waliMonitoring['siswaIzinList']->count() > 0)
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:12px;">
                @if($waliMonitoring['siswaTerlambatList']->count() > 0)
                    <div style="background:rgba(245, 158, 11, 0.08); border:1px solid rgba(245, 158, 11, 0.3); border-radius:8px; padding:12px;">
                        <strong style="font-size:13px; color:#d97706; display:flex; align-items:center; gap:6px; margin-bottom:8px;">
                            ⏰ Siswa Kelas Terlambat Hari Ini ({{ $waliMonitoring['siswaTerlambatList']->count() }}):
                        </strong>
                        <ul style="margin:0; padding-left:18px; font-size:12.5px; line-height:1.6;">
                            @foreach($waliMonitoring['siswaTerlambatList'] as $st)
                                <li>
                                    <strong>{{ $st->siswa->nama ?? '-' }}</strong> (Pukul {{ $st->jam_kedatangan }}, Jam ke-{{ $st->terlambat_sampai_jam }}) - <em>{{ $st->alasan }}</em>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($waliMonitoring['siswaIzinList']->count() > 0)
                    <div style="background:rgba(59, 130, 246, 0.08); border:1px solid rgba(59, 130, 246, 0.3); border-radius:8px; padding:12px;">
                        <strong style="font-size:13px; color:#2563eb; display:flex; align-items:center; gap:6px; margin-bottom:8px;">
                            📝 Siswa Izin / Sakit Hari Ini ({{ $waliMonitoring['siswaIzinList']->count() }}):
                        </strong>
                        <ul style="margin:0; padding-left:18px; font-size:12.5px; line-height:1.6;">
                            @foreach($waliMonitoring['siswaIzinList'] as $si)
                                <li>
                                    <strong>{{ $si->siswa->nama ?? '-' }}</strong> - <span class="badge {{ $si->kategori === 'sakit' ? 'badge-purple' : 'badge-info' }}">{{ strtoupper($si->kategori) }}</span>: {{ Str::limit($si->alasan, 35) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endif

<div class="grid-3 mb-24">
    <!-- PRESENSI MASUK/KELUAR STAT CARD -->
    <div class="stat-card">
        <div class="stat-icon-box {{ $presensiHariIni ? 'green' : 'amber' }}">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="font-size: 18px;">
                @if($presensiHariIni)
                    {{ $presensiHariIni->jam_masuk }}
                @else
                    Belum Masuk
                @endif
            </div>
            <div class="stat-label">Presensi Masuk Hari Ini</div>
        </div>
    </div>

    <!-- TOTAL JURNAL TERLAKSANA -->
    <div class="stat-card">
        <div class="stat-icon-box navy">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
        </div>
        <div>
            <div class="stat-num">{{ $jurnalTerlaksana ?? 0 }}</div>
            <div class="stat-label">Jurnal KBM Terlaksana</div>
        </div>
    </div>

    <!-- PRESENSI SAYA ACTION BUTTON -->
    <div class="stat-card justify-between" style="background: linear-gradient(135deg, #1e3a8a, #1e293b); color: #ffffff;">
        <div>
            <div style="font-size: 15px; font-weight: 700;">Presensi Harian Guru</div>
            <div style="font-size: 12px; color: #cbd5e1; margin-top: 2px;">Catat Waktu Masuk & Keluar</div>
        </div>
        <a href="{{ route('guru.presensi-saya') }}" class="btn btn-amber btn-sm">Buka Presensi</a>
    </div>
</div>

<!-- GRAFIK MENGAJAR GURU -->
<div class="grid-2 mb-24">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Grafik Statistik Keterlaksanaan Mengajar
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="chartAktivitasMengajar"></canvas>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                Aksi Cepat Jurnal & Pengajuan Izin
            </h3>
        </div>
        <div class="card-body">
            <p class="mb-16 text-muted">Akses langsung formulir pengisian jurnal mengajar otomatis dan pengajuan izin:</p>
            <div class="d-flex gap-12 flex-wrap mb-16">
                <a href="{{ route('jurnal-harian.create') }}" class="btn btn-primary btn-lg">Form Isi Jurnal Mengajar</a>
                <a href="{{ route('pengajuan.create') }}" class="btn btn-amber btn-lg">+ Buat Pengajuan Izin</a>
                <a href="{{ route('jurnal-harian.index', ['tab' => 'pengajuan']) }}" class="btn btn-secondary btn-lg">Lihat Pengajuan Saya</a>
            </div>
            <div class="alert alert-info" style="margin: 0;">
                <div>Jurnal otomatis mencocokkan jam & hari dari laptop Anda. Pengajuan izin kini digabung di menu Jurnal.</div>
            </div>
        </div>
    </div>
</div>

<!-- JADWAL MENGAJAR HARI INI -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
            Jadwal Mengajar Hari Ini ({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }})
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($jadwalHariIni->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:90px;">Jam Ke</th>
                        <th style="width:160px;">Jam Pembelajaran</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Ruang</th>
                        <th>Status Jurnal</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalHariIni as $j)
                    @php $sudahIsi = $jurnalHariIni->has($j->id_jadwal); @endphp
                    <tr>
                        <td class="fw-bold text-center">Jam {{ $j->jam_ke }}</td>
                        <td class="fw-bold" style="color:#1e3a8a;">
                            {{ \App\Services\KbmService::getLabelWaktu($j->hari, $j->jam_ke) ?: ($j->waktu_mulai . ' - ' . $j->waktu_selesai) }}
                        </td>
                        <td><span class="badge badge-navy">{{ $j->kelas->nama_kelas ?? '-' }}</span></td>
                        <td class="fw-bold text-navy">{{ $j->mapel }}</td>
                        <td>{{ $j->ruang ?? '-' }}</td>
                        <td>
                            @if($sudahIsi)
                                <span class="badge badge-success">Sudah Diisi</span>
                            @else
                                <span class="badge badge-warning">Belum Diisi</span>
                            @endif
                        </td>
                        <td class="action-col">
                            @if($sudahIsi)
                                <a href="{{ route('jurnal-harian.show', $jurnalHariIni[$j->id_jadwal]->id_jurnal) }}" class="btn btn-secondary btn-sm">Lihat Jurnal</a>
                            @else
                                <a href="{{ route('jurnal-harian.create', ['id_jadwal' => $j->id_jadwal]) }}" class="btn btn-primary btn-sm">Isi Jurnal</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada jadwal mengajar untuk hari ini.</div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const monthsIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function updateDashboardClock() {
        const now = new Date();
        const dayName = daysIndo[now.getDay()];
        const dateNum = String(now.getDate()).padStart(2, '0');
        const monthName = monthsIndo[now.getMonth()];
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        const clockEl = document.getElementById('dashboard-laptop-clock');
        if (clockEl) {
            clockEl.textContent = `${dayName}, ${dateNum} ${monthName} ${year} (${hours}:${minutes}:${seconds})`;
        }
    }
    updateDashboardClock();
    setInterval(updateDashboardClock, 1000);

    const ctx = document.getElementById('chartAktivitasMengajar').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Terlaksana', 'Pengganti', 'Tidak Terlaksana / Kosong'],
            datasets: [{
                label: 'Jumlah Jurnal KBM',
                data: [
                    {{ $jurnalTerlaksana ?? 0 }},
                    {{ $jurnalPengganti ?? 0 }},
                    {{ $jurnalTidakTerlaksana ?? 0 }}
                ],
                backgroundColor: [
                    '#16a34a', // Terlaksana - Green
                    '#d97706', // Pengganti - Amber
                    '#dc2626'  // Tidak Terlaksana - Red
                ],
                borderRadius: 6,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
});
</script>
@endpush
