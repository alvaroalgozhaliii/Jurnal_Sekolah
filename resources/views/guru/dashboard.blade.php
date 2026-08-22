@extends('layouts.app')

@section('title', 'Dashboard Guru — Jurnal Sekolah')
@section('page-title', 'Dashboard Guru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Selamat Datang, {{ $guru->nama ?? Auth::user()->nama }}</h1>
        <p class="page-subtitle">Sistem Monitoring Mengajar KBM & Presensi Harian</p>
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
                Aksi Cepat Jurnal & KBM
            </h3>
        </div>
        <div class="card-body">
            <p class="mb-16 text-muted">Akses langsung formulir pengisian jurnal harian dan presensi siswa KBM hari ini:</p>
            <div class="d-flex gap-12 flex-wrap mb-16">
                <a href="{{ route('jurnal-harian.create') }}" class="btn btn-primary btn-lg">Form Isi Jurnal Mengajar</a>
                <a href="{{ route('absensi-siswa.create') }}" class="btn btn-secondary btn-lg">Absensi Siswa Batch</a>
            </div>
            <div class="alert alert-info" style="margin: 0;">
                <div>Isi jurnal harian tepat waktu sesuai jadwal mata pelajaran yang diampu.</div>
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
                        <th class="no-col">Jam</th>
                        <th>Waktu</th>
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
                        <td class="no-col fw-bold">{{ $j->jam_ke }}</td>
                        <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
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
                                <a href="{{ route('jurnal-harian.create', ['id_jadwal' => $j->id_jadwal]) }}" class="btn btn-primary btn-sm">Isi Jurnal Now</a>
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
