@extends('layouts.app')

@section('title', 'Dashboard Orang Tua — Jurnal Sekolah')
@section('page-title', 'Dashboard Orang Tua')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Selamat Datang, Orang Tua / Wali</h1>
        <p class="page-subtitle">Pantau Presensi, Jadwal, dan Kehadiran KBM Anak Anda</p>
    </div>
</div>

@if($anakList->count() > 1)
<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('ortu.dashboard') }}" method="GET" class="d-flex align-center gap-12">
            <label class="form-label" style="margin:0; white-space:nowrap;">Pilih Siswa / Anak:</label>
            <select name="id_siswa" onchange="this.form.submit()" class="form-control" style="max-width:350px;">
                @foreach($anakList as $a)
                <option value="{{ $a->id_siswa }}" {{ ($selectedSiswa && $selectedSiswa->id_siswa == $a->id_siswa) ? 'selected' : '' }}>
                    {{ $a->nama }} (NISN: {{ $a->NISN }})
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>
@endif

@if($selectedSiswa)
<div class="grid-2 mb-24">
    <!-- INFORMASI PROFIL ANAK -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Profil Siswa: {{ $selectedSiswa->nama }}
            </h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="info-table">
                <tbody>
                    <tr><th>NISN</th><td class="fw-bold text-navy">{{ $selectedSiswa->NISN }}</td></tr>
                    <tr><th>Nama Lengkap</th><td class="fw-bold">{{ $selectedSiswa->nama }}</td></tr>
                    <tr><th>Kelas</th><td><span class="badge badge-navy">{{ $selectedSiswa->kelas->nama_kelas ?? '-' }}</span></td></tr>
                    <tr><th>Jurusan</th><td>{{ $selectedSiswa->kelas->jurusan->nama_jurusan ?? '-' }}</td></tr>
                    <tr><th>Status Kehadiran Hari Ini</th><td>
                        @if($statusPresensi->count() > 0)
                            @foreach($statusPresensi as $sp)
                                @php
                                    $st = strtolower($sp->status);
                                    $bCls = match($st) {
                                        'hadir' => 'badge-success',
                                        'izin' => 'badge-info',
                                        'sakit' => 'badge-purple',
                                        'alpa' => 'badge-danger',
                                        'terlambat' => 'badge-warning',
                                        default => 'badge-gray'
                                    };
                                @endphp
                                <span class="badge {{ $bCls }}">{{ strtoupper($sp->status) }}</span>
                            @endforeach
                        @else
                            <span class="badge badge-gray">Belum ada catatan presensi hari ini</span>
                        @endif
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- GRAFIK PRESENSI KEHADIRAN ANAK -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Grafik Rekap Kehadiran Anak
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="chartPresensiAnak"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- JADWAL HARI INI -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
            Jadwal Pelajaran Hari Ini ({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }})
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
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th>Ruang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalHariIni as $j)
                    <tr>
                        <td class="no-col fw-bold">{{ $j->jam_ke }}</td>
                        <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
                        <td class="fw-bold text-navy">{{ $j->mapel }}</td>
                        <td>{{ $j->guru->nama ?? '-' }}</td>
                        <td>{{ $j->ruang ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada jadwal pelajaran untuk hari ini.</div>
        </div>
        @endif
    </div>
</div>
@else
<div class="alert alert-warning">
    <div>Data anak tidak ditemukan atau belum terhubung dengan akun ini.</div>
</div>
@endif
@endsection

@push('scripts')
@if($selectedSiswa)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chartPresensiAnak').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alpa', 'Dispensasi'],
            datasets: [{
                data: [
                    {{ $summaryAnak['hadir'] ?? 0 }},
                    {{ $summaryAnak['sakit'] ?? 0 }},
                    {{ $summaryAnak['izin'] ?? 0 }},
                    {{ $summaryAnak['alpa'] ?? 0 }},
                    {{ $summaryAnak['dispen'] ?? 0 }}
                ],
                backgroundColor: [
                    '#16a34a', // Hadir - Green
                    '#9333ea', // Sakit - Purple
                    '#0284c7', // Izin - Blue
                    '#dc2626', // Alpa - Red
                    '#d97706'  // Dispen - Amber
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
                    labels: { boxWidth: 12, padding: 14 }
                }
            }
        }
    });
});
</script>
@endif
@endpush
