@extends('layouts.app')

@section('title', 'Dashboard Administrator — Jurnal Sekolah')
@section('page-title', 'Dashboard Administrator')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Selamat Datang, Administrator</h1>
        <p class="page-subtitle">Ringkasan Sistem Informasi KBM & Presensi Sekolah</p>
    </div>
    @if($tahunAktif)
    <div class="page-actions">
        <span class="badge badge-navy" style="font-size:13px; padding:8px 14px;">
            Tahun Pelajaran: {{ $tahunAktif->tahun_pelajaran }} (Semester {{ $tahunAktif->semester }})
        </span>
    </div>
    @endif
</div>

<!-- STATS MASTER DATA GRID -->
<div class="grid-4 mb-24">
    <div class="stat-card">
        <div class="stat-icon-box navy">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div>
            <div class="stat-num">{{ $jumlahGuru }}</div>
            <div class="stat-label">Total Guru</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-box blue">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
        </div>
        <div>
            <div class="stat-num">{{ $jumlahSiswa }}</div>
            <div class="stat-label">Total Siswa</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-box green">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
        </div>
        <div>
            <div class="stat-num">{{ $jumlahKelas }}</div>
            <div class="stat-label">Total Kelas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-box amber">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
        </div>
        <div>
            <div class="stat-num">{{ $jumlahMapel }}</div>
            <div class="stat-label">Mata Pelajaran</div>
        </div>
    </div>
</div>

<!-- CHARTS ANALYTICS SECTION -->
<div class="grid-2 mb-24">
    <!-- CHART 1: KEHADIRAN SISWA & DISPENSASI -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Statistik Kehadiran & Izin (Hadir, Sakit, Izin, Alpa, Dispen)
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="chartKehadiranSiswa"></canvas>
            </div>
        </div>
    </div>

    <!-- CHART 2: SEBARAN AKUN PENGGUNA (GURU, SISWA, KARYAWAN) -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Sebaran Akun Pengguna (Guru, Siswa, Karyawan)
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="chartSebaranPengguna"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- KETERANGAN SUMMARY HARI INI -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Ringkasan Aktivitas Hari Ini ({{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }})
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Indikator Aktivitas</th>
                        <th>Jumlah Terdata Hari Ini</th>
                        <th>Status Operasional</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold text-navy">Kehadiran Guru (Presensi Masuk)</td>
                        <td class="fw-bold">{{ $kehadiranGuruHariIni }} Guru</td>
                        <td><span class="badge badge-success">Terproses</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-navy">Absensi Siswa (Hadir KBM)</td>
                        <td class="fw-bold">{{ $kehadiranSiswaHariIni }} Siswa</td>
                        <td><span class="badge badge-info">Tercatat</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-navy">Jurnal Mengajar Terisi</td>
                        <td class="fw-bold">{{ $jurnalHariIni }} Jurnal KBM</td>
                        <td><span class="badge badge-purple">Aktif</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // CHART 1: KEHADIRAN (HADIR, SAKIT, IZIN, ALPA, DISPEN)
    const ctx1 = document.getElementById('chartKehadiranSiswa').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alpa', 'Dispensasi'],
            datasets: [{
                label: 'Jumlah Catatan Presensi',
                data: [
                    {{ $siswaHadir ?? 0 }},
                    {{ $siswaSakit ?? 0 }},
                    {{ $siswaIzin ?? 0 }},
                    {{ $siswaAlpa ?? 0 }},
                    {{ $siswaDispen ?? 0 }}
                ],
                backgroundColor: [
                    '#16a34a', // Hadir - Green
                    '#9333ea', // Sakit - Purple
                    '#0284c7', // Izin - Blue
                    '#dc2626', // Alpa - Red
                    '#d97706'  // Dispen - Amber
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

    // CHART 2: SEBARAN PENGGUNA (GURU, SISWA, KARYAWAN)
    const ctx2 = document.getElementById('chartSebaranPengguna').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Guru', 'Siswa / Ortu', 'Karyawan / Staff'],
            datasets: [{
                data: [
                    {{ $userGuru ?? 0 }},
                    {{ $userOrtu ?? 0 }},
                    {{ $userKaryawan ?? 0 }}
                ],
                backgroundColor: [
                    '#1e3a8a', // Guru - Navy
                    '#0284c7', // Siswa - Blue
                    '#d97706'  // Karyawan - Amber
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 16 }
                }
            }
        }
    });
});
</script>
@endpush
