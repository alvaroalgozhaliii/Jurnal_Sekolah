@extends('layouts.app')

@section('title', 'Data Kelas Saya — Jurnal Sekolah')
@section('page-title', 'Data Kelas Saya')

@section('content')
<style>
/* ═══════════════════════════════════════
   COMPACT DASHBOARD GRID (Data Kelas)
═══════════════════════════════════════ */
.cal-dashboard-grid {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 20px;
    margin-bottom: 24px;
    align-items: stretch;
}
@media (max-width: 991px) {
    .cal-dashboard-grid {
        grid-template-columns: 1fr;
    }
}

.mini-cal-wrapper {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    display: flex;
    flex-direction: column;
    height: 100%;
}
.mini-cal-header {
    padding: 16px 20px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(135deg, #0f172a, #1e3a8a);
    color: #fff;
}
.cal-nav-btn {
    width: 32px; height: 32px; border-radius: 8px;
    background: rgba(255,255,255,.18); border: none; color: #fff;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: background .18s;
}
.cal-nav-btn:hover { background: rgba(255,255,255,.35); }

.mini-cal-body { padding: 12px 14px; }
.cal-weekdays { display: grid; grid-template-columns: repeat(7, 1fr); margin-bottom: 4px; }
.cal-weekday {
    text-align: center; font-size: 10.5px; font-weight: 700;
    color: var(--text-secondary); text-transform: uppercase; padding: 4px 0;
}
.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px; }
.cal-day {
    aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
    border-radius: 8px; font-size: 12px; font-weight: 500; cursor: pointer !important;
    user-select: none; pointer-events: auto !important;
    transition: background .15s, transform .10s;
    color: var(--text-primary); min-height: 34px;
}
.cal-day:hover:not(.cal-other-month):not(.cal-selected) { background: var(--bg-page); transform: scale(1.06); }
.cal-day.cal-today { box-shadow: inset 0 0 0 2px #3b82f6; color: #3b82f6; font-weight: 800; }
.cal-day.cal-selected {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;
    color: #fff !important; font-weight: 700;
    box-shadow: 0 4px 12px rgba(59,130,246,.4); transform: scale(1.08);
}
.cal-day.cal-other-month { opacity: .18; pointer-events: none !important; cursor: default !important; }
.cal-day.cal-weekend { color: #ef4444; }
.cal-day.cal-weekend.cal-selected { color: #fff; }

/* Diagram Card */
.cal-chart-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}
.chart-title {
    font-size: 16px; font-weight: 700; color: var(--text-primary);
}
.chart-canvas-wrapper {
    position: relative; width: 100%; height: 180px;
}
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Data Siswa Kelas Bimbingan</h1>
        <p class="page-subtitle">Daftar siswa terdaftar di kelas bimbingan wali kelas</p>
    </div>
</div>

@if($kelas)
@php
    $today   = \Carbon\Carbon::today()->toDateString();
    $selDate = $tanggal ?: $today;
    $selC    = \Carbon\Carbon::parse($selDate);
    $navYear  = $selC->year;
    $navMonth = $selC->month;

    // Absensi counts for chart
    $cHadir = 0; $cSakit = 0; $cIzin = 0; $cAlpa = 0; $cTerlambat = 0;
    foreach($siswaList as $s) {
        $ab = $statusTanggal[$s->id_siswa] ?? null;
        $st = $ab ? strtolower($ab->status) : null;
        if($st == 'hadir') $cHadir++;
        elseif($st == 'sakit') $cSakit++;
        elseif($st == 'izin') $cIzin++;
        elseif($st == 'alpa') $cAlpa++;
        elseif($st == 'terlambat') $cTerlambat++;
    }
    $cBelum = $siswaList->count() - ($cHadir + $cSakit + $cIzin + $cAlpa + $cTerlambat);
@endphp

<div class="cal-dashboard-grid">
    {{-- COMPACT MINI CALENDAR --}}
    <form action="{{ route('walikelas.data-kelas') }}" method="GET" id="calForm" style="margin:0;">
        <input type="hidden" name="tanggal" id="hiddenTanggal" value="{{ $selDate }}">

        <div class="mini-cal-wrapper">
            <div class="mini-cal-header">
                <div>
                    <div style="font-size:14px; font-weight:700;">📅 Filter Tanggal</div>
                    <div style="font-size:11px; opacity:.8;">Status Absensi Siswa</div>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="cal-nav-btn" onclick="navigateCal(-1)" title="Bulan Sebelumnya">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div style="text-align:center; min-width:110px;">
                        <span id="calMonthName" style="font-size:14px; font-weight:800; display:block; line-height:1.2; background:linear-gradient(135deg, #fff, #93c5fd); -webkit-background-clip:text; -webkit-text-fill-color:transparent;"></span>
                        <span id="calYearLabel" style="font-size:10px; opacity:.7; font-weight:600; text-transform:uppercase;"></span>
                    </div>
                    <button type="button" class="cal-nav-btn" onclick="navigateCal(1)" title="Bulan Berikutnya">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <div class="mini-cal-body">
                <div class="cal-weekdays">
                    @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $wd)
                        <div class="cal-weekday">{{ $wd }}</div>
                    @endforeach
                </div>
                <div class="cal-grid" id="calGrid"></div>
            </div>
        </div>
    </form>

    {{-- DIAGRAM CARD --}}
    <div class="cal-chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">📊 Diagram Status Absensi Siswa</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">
                    {{ \Carbon\Carbon::parse($selDate)->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · Kelas {{ $kelas->nama_kelas }}
                </div>
            </div>
            <div class="badge badge-navy" style="font-size:12px; padding:6px 12px;">
                Total: {{ $siswaList->count() }} Siswa
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 200px 1fr; gap:24px; align-items:center;">
            <div class="chart-canvas-wrapper">
                <canvas id="kelasAbsensiChart"></canvas>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; justify-content:space-between; padding:6px 12px; border-radius:8px; background:var(--bg-page); font-size:12.5px;">
                    <span style="font-weight:600; color:#10b981;">🟢 Hadir</span>
                    <span style="font-weight:700;">{{ $cHadir }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 12px; border-radius:8px; background:var(--bg-page); font-size:12.5px;">
                    <span style="font-weight:600; color:#f59e0b;">🟠 Terlambat</span>
                    <span style="font-weight:700;">{{ $cTerlambat }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 12px; border-radius:8px; background:var(--bg-page); font-size:12.5px;">
                    <span style="font-weight:600; color:#3b82f6;">🔵 Izin / Sakit</span>
                    <span style="font-weight:700;">{{ $cIzin + $cSakit }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 12px; border-radius:8px; background:var(--bg-page); font-size:12.5px;">
                    <span style="font-weight:600; color:#ef4444;">🔴 Alpa</span>
                    <span style="font-weight:700;">{{ $cAlpa }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 12px; border-radius:8px; background:var(--bg-page); font-size:12.5px;">
                    <span style="font-weight:600; color:var(--text-secondary);">⚪ Belum Absen</span>
                    <span style="font-weight:700;">{{ $cBelum }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">Informasi Kelas: {{ $kelas->nama_kelas }}</h3>
    </div>
    <div class="card-body">
        <div class="grid-3">
            <div><span class="text-muted">Kelas:</span> <strong>{{ $kelas->nama_kelas }}</strong></div>
            <div><span class="text-muted">Jurusan:</span> <strong>{{ $kelas->jurusan->nama_jurusan ?? '-' }}</strong></div>
            <div><span class="text-muted">Total Siswa:</span> <strong><span class="badge badge-navy">{{ $siswaList->count() }} Siswa</span></strong></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Siswa Kelas {{ $kelas->nama_kelas }}</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($siswaList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Kelamin</th>
                        <th>Status {{ \Carbon\Carbon::parse($selDate)->format('d/m/Y') }}</th>
                        <th>Akun User Ortu</th>
                        <th>No Telp Ortu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswaList as $index => $s)
                    @php
                        $absen = $statusTanggal[$s->id_siswa] ?? null;
                        $st = $absen ? strtolower($absen->status) : null;
                        $stBadge = match($st) {
                            'hadir'     => 'badge-success',
                            'sakit'     => 'badge-purple',
                            'izin'      => 'badge-info',
                            'alpa'      => 'badge-danger',
                            'terlambat' => 'badge-warning',
                            default     => 'badge-gray'
                        };
                        $stText = match($st) {
                            'hadir'     => 'HADIR',
                            'sakit'     => 'IZIN SAKIT',
                            'izin'      => 'IZIN',
                            'alpa'      => 'ALPA',
                            'terlambat' => 'TERLAMBAT',
                            default     => 'Belum Absen'
                        };
                    @endphp
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="text-muted fw-bold">{{ $s->NISN }}</td>
                        <td class="fw-bold text-navy">{{ $s->nama }}</td>
                        <td>
                            @if($s->jenis_kelamin == 'L')
                                <span class="badge badge-info">Laki-laki</span>
                            @elseif($s->jenis_kelamin == 'P')
                                <span class="badge badge-purple">Perempuan</span>
                            @else -
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $stBadge }}">{{ $stText }}</span>
                            @if($absen && $absen->keterangan)
                                <div class="text-muted" style="font-size:11px; margin-top:2px;">{{ Str::limit($absen->keterangan, 30) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($s->user)
                                <span class="badge badge-success">{{ $s->user->username }}</span>
                            @else
                                <span class="badge badge-gray">Belum ada</span>
                            @endif
                        </td>
                        <td>{{ $s->no_telp_ortu ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada siswa di kelas ini.</div>
        </div>
        @endif
    </div>
</div>
@else
<div class="alert alert-warning">
    <div>Anda belum terdaftar sebagai Wali Kelas.</div>
</div>
@endif

@push('scripts')
<script>
const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
let calYear  = {{ $navYear }};
let calMonth = {{ $navMonth }};
const selectedDate = '{{ $selDate }}';
const todayStr     = '{{ $today }}';

function renderCalendar() {
    const grid   = document.getElementById('calGrid');
    const mName  = document.getElementById('calMonthName');
    const yLabel = document.getElementById('calYearLabel');
    grid.innerHTML = '';
    mName.textContent  = MONTHS_ID[calMonth - 1];
    yLabel.textContent = calYear;

    const firstDay    = new Date(calYear, calMonth - 1, 1).getDay();
    const daysInMonth = new Date(calYear, calMonth, 0).getDate();
    const prevDays    = new Date(calYear, calMonth - 1, 0).getDate();

    for (let i = firstDay - 1; i >= 0; i--) {
        const d = document.createElement('div');
        d.className = 'cal-day cal-other-month';
        d.textContent = prevDays - i;
        grid.appendChild(d);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = calYear + '-' + String(calMonth).padStart(2,'0') + '-' + String(d).padStart(2,'0');
        const dow = new Date(calYear, calMonth - 1, d).getDay();
        const cell = document.createElement('div');
        let cls = 'cal-day';
        if (dow === 0 || dow === 6) cls += ' cal-weekend';
        if (dateStr === todayStr)     cls += ' cal-today';
        if (dateStr === selectedDate) cls += ' cal-selected';
        cell.className = cls;
        cell.textContent = d;
        cell.addEventListener('click', () => {
            document.getElementById('hiddenTanggal').value = dateStr;
            document.getElementById('calForm').submit();
        });
        grid.appendChild(cell);
    }

    const total = firstDay + daysInMonth;
    const rem   = total % 7 === 0 ? 0 : 7 - (total % 7);
    for (let d = 1; d <= rem; d++) {
        const el = document.createElement('div');
        el.className = 'cal-day cal-other-month';
        el.textContent = d;
        grid.appendChild(el);
    }
}

function navigateCal(delta) {
    calMonth += delta;
    if (calMonth > 12) { calMonth = 1; calYear++; }
    if (calMonth < 1)  { calMonth = 12; calYear--; }
    const firstDate = calYear + '-' + String(calMonth).padStart(2,'0') + '-01';
    document.getElementById('hiddenTanggal').value = firstDate;
    document.getElementById('calForm').submit();
}

renderCalendar();

// Chart Data Kelas Absensi
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('kelasAbsensiChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Terlambat', 'Izin/Sakit', 'Alpa', 'Belum Absen'],
                datasets: [{
                    data: [
                        {{ $cHadir }},
                        {{ $cTerlambat }},
                        {{ $cIzin + $cSakit }},
                        {{ $cAlpa }},
                        {{ $cBelum }}
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#cbd5e1'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
        });
    }
});
</script>
@endpush
@endsection
