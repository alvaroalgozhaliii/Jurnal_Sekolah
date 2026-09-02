@extends('layouts.app')

@section('title', 'Dashboard Piket — Jurnal Sekolah')
@section('page-title', 'Dashboard Piket')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Petugas Piket</h1>
        <p class="page-subtitle">Monitoring Jam Pelajaran, Kehadiran Guru, Kelas Kosong & Pengajuan Dispensasi</p>
    </div>
    <div class="page-actions" style="display:flex; gap:8px;">
        <a href="{{ route('piket.pengajuan.create') }}" class="btn btn-primary" style="background:#1e3a8a; color:#ffffff; font-weight:600;">+ Input Dispen Siswa</a>
        <a href="{{ route('pengajuan.create') }}?tipe=guru" class="btn" style="background:#d97706; color:#ffffff; font-weight:600; padding:7px 14px; border-radius:6px; border:none;">+ Input Dispen Guru</a>
        <a href="{{ route('piket.anak-sakit') }}" class="btn btn-secondary" style="font-weight:600;">Catat Anak Sakit</a>
    </div>
</div>

<!-- BANNER WAKA BERTUGAS HARI INI -->
<div style="background:var(--bg-card); border:1px solid #38bdf8; border-radius:8px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:12px;">
        
        <div>
            <div style="font-size:13.5px; font-weight:700; color:#38bdf8;">
                Waka yang Bertugas Hari Ini: {{ $wakaHariIni ? $wakaHariIni->waka->nama . ' (' . strtoupper(str_replace('_', ' ', $wakaHariIni->waka->role)) . ')' : 'Belum Terjadwal (Otomatis diarahkan ke Waka SDM / Kesiswaan)' }}
            </div>
            <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">
                Pengajuan dispensasi siswa & guru yang dibuat hari ini akan otomatis diteruskan ke Waka di atas via WhatsApp.
            </div>
        </div>
    </div>
    @if($wakaHariIni && $wakaHariIni->waka && $wakaHariIni->waka->no_hp)
        <span class="badge" style="background:#10b981; color:#fff; font-size:12px; padding:6px 12px; font-weight:600;">
             WA: {{ $wakaHariIni->waka->no_hp }}
        </span>
    @endif
</div>

<!-- WARNING KELAS KOSONG -->
@if(count($kelasKosong) > 0)
    <div class="alert alert-danger mb-24">
        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        <div>
            <strong class="d-block mb-8">Peringatan Kelas Kosong Saat Ini:</strong>
            <ul style="margin-left: 16px;">
                @foreach($kelasKosong as $kk)
                    <li>{{ $kk['pesan'] }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!-- STATISTIK KEHADIRAN GURU & SISWA -->
<div class="grid-4 mb-24">
    <div class="stat-card" style="border-left: 4px solid #0284c7;">
        <div class="stat-icon-box blue">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #0284c7;">{{ $totalIzinOrtuHariIni }}</div>
            <div class="stat-label">Izin Siswa dari Ortu Hari Ini</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #16a34a;">
        <div class="stat-icon-box green">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #16a34a;">{{ $jumlahGuruHadir }}</div>
            <div class="stat-label">Guru Hadir Hari Ini</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #d97706;">
        <div class="stat-icon-box amber">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #d97706;">{{ $totalPendingWaka }}</div>
            <div class="stat-label">Dispen Menunggu Waka</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #9333ea;">
        <div class="stat-icon-box purple">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #9333ea;">{{ $totalVerifiedSatpam }}</div>
            <div class="stat-label">Dispen Selesai</div>
        </div>
    </div>
</div>

@if(isset($izinOrtuHariIniList) && $izinOrtuHariIniList->count() > 0)
<!-- DAFTAR IZIN ORTU TERBARU -->
<div class="card mb-24">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 class="card-title" style="color: #38bdf8;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            Daftar Izin Siswa dari Orang Tua (Tercatat Otomatis di Kelas)
        </h3>
        <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary btn-sm">Lihat Semua &rarr;</a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tanggal Izin</th>
                        <th>Keterangan / Alasan</th>
                        <th>Status</th>
                        <th>Lampiran</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($izinOrtuHariIniList as $idx => $iz)
                    <tr>
                        <td class="no-col">{{ $idx + 1 }}</td>
                        <td>
                            <span class="badge {{ $iz->kategori === 'sakit' ? 'badge-purple' : 'badge-info' }}">
                                {{ $iz->kategori === 'sakit' ? 'IZIN SAKIT' : 'IZIN' }}
                            </span>
                        </td>
                        <td class="fw-bold text-navy">{{ $iz->siswa->nama ?? '-' }}</td>
                        <td>{{ $iz->siswa->kelas->nama_kelas ?? '-' }}</td>
                        <td>{{ $iz->tanggal }}</td>
                        <td>{{ Str::limit($iz->alasan, 40) }}</td>
                        <td><span class="badge badge-success">TERCATAT DI KELAS</span></td>
                        <td>
                            @if($iz->lampiran_foto)
                                <a href="{{ asset('storage/' . $iz->lampiran_foto) }}" target="_blank" class="btn btn-secondary btn-sm">Lihat Bukti</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="action-col">
                            <a href="{{ route('pengajuan.show', $iz->id_pengajuan) }}" class="btn btn-primary btn-sm">
                                Detail &rarr;
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- TABEL MONITORING JADWAL HARI INI -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
            Jadwal Mengajar KBM Hari Ini ({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }})
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($jadwalHariIni->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">Jam</th>
                        <th>Waktu</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalHariIni as $j)
                    <tr>
                        <td class="no-col fw-bold">{{ $j->jam_ke }}</td>
                        <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
                        <td><span class="badge badge-navy">{{ $j->kelas->nama_kelas ?? '-' }}</span></td>
                        <td class="fw-bold text-navy">{{ $j->mapel }}</td>
                        <td>{{ $j->guru->nama ?? 'Belum ditentukan' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada jadwal KBM untuk hari ini.</div>
        </div>
        @endif
    </div>
</div>
@endsection
