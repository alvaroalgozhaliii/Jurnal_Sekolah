@extends('layouts.app')

@section('title', 'Monitoring Siswa — Jurnal Sekolah')
@section('page-title', 'Monitoring Siswa')

@section('content')
<style>
/* ═══════════════════════════════════════
   COMPACT DASHBOARD GRID (Monitoring Waka)
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

.cal-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: visible;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
    z-index: 10;
}
.cal-head {
    padding: 16px 20px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    border-radius: 16px 16px 0 0;
    background: linear-gradient(135deg, rgba(30,58,138,.06), transparent);
}
.cal-month-name {
    font-size: 20px; font-weight: 800; letter-spacing: -.4px;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    line-height: 1.1;
}
.cal-year-label { font-size: 12px; color: var(--text-secondary); font-weight: 600; }
.cal-nav-arrow {
    width: 34px; height: 34px; border-radius: 10px;
    background: var(--bg-page); border: 1px solid var(--border);
    color: var(--text-primary); cursor: pointer; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    transition: all .18s ease;
}
.cal-nav-arrow:hover {
    background: #3b82f6; border-color: #3b82f6; color: #fff;
    transform: scale(1.06);
}
.cal-weekdays { display: grid; grid-template-columns: repeat(7, 1fr); padding: 10px 14px 2px; }
.cal-wd {
    text-align: center; font-size: 10.5px; font-weight: 700;
    text-transform: uppercase; color: var(--text-secondary);
}
.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); padding: 4px 14px 14px; gap: 3px; }
.cal-day {
    aspect-ratio: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center; border-radius: 8px;
    font-size: 12.5px; font-weight: 500; cursor: pointer !important; position: relative;
    user-select: none; pointer-events: auto !important;
    transition: background .15s, transform .12s;
    color: var(--text-primary); min-height: 36px; gap: 2px;
}
.cal-day:hover:not(.cal-other-month):not(.cal-selected) { background: var(--bg-page); transform: scale(1.06); }
.cal-day.cal-today { box-shadow: inset 0 0 0 2px #3b82f6; color: #3b82f6; font-weight: 800; }
.cal-day.cal-selected {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;
    color: #fff !important; font-weight: 700;
    box-shadow: 0 4px 12px rgba(59,130,246,.35); transform: scale(1.08);
}
.cal-day.cal-other-month { opacity: .18; pointer-events: none !important; cursor: default !important; }

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
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;
}
.chart-title { font-size: 16px; font-weight: 700; color: var(--text-primary); }
.chart-canvas-wrapper { position: relative; width: 100%; height: 180px; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Monitoring Kehadiran Siswa</h1>
        <p class="page-subtitle">Rekap izin, sakit, alpa, terlambat, hadir, dan dispen</p>
    </div>
</div>

@php
    $today   = \Carbon\Carbon::today()->toDateString();
    $selDate = $tanggal ?: $today;
    $selC    = \Carbon\Carbon::parse($selDate);
    $navYear  = $selC->year;
    $navMonth = $selC->month;
@endphp

<div class="cal-dashboard-grid">
    {{-- COMPACT CALENDAR --}}
    <form action="{{ route('waka.monitoring-siswa') }}" method="GET" id="calForm" style="margin:0;">
        <input type="hidden" name="tanggal" id="hiddenTanggal" value="{{ $selDate }}">

        <div class="cal-card">
            <div class="cal-head">
                <div>
                    <span class="cal-month-name" id="calMonthName"></span>
                    <span class="cal-year-label" id="calYearLabel"></span>
                </div>
                <div style="display:flex; gap:6px;">
                    <button type="button" class="cal-nav-arrow" onclick="navigateCal(-1)" title="Bulan sebelumnya">&#8592;</button>
                    <button type="button" class="cal-nav-arrow" onclick="navigateCal(1)" title="Bulan berikutnya">&#8594;</button>
                </div>
            </div>
            <div class="cal-weekdays">
                @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $wd)
                    <div class="cal-wd">{{ $wd }}</div>
                @endforeach
            </div>
            <div class="cal-grid" id="calGrid"></div>
        </div>
    </form>

    {{-- DIAGRAM CARD --}}
    <div class="cal-chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">📊 Visualisasi Presensi Siswa</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">
                    Tanggal: {{ \Carbon\Carbon::parse($selDate)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                </div>
            </div>
            <div class="badge badge-navy" style="font-size:12px; padding:6px 12px;">
                Total: {{ $absensi->count() }} Record Presensi
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 200px 1fr; gap:24px; align-items:center;">
            <div class="chart-canvas-wrapper">
                <canvas id="wakaChart"></canvas>
            </div>

            <div style="display:flex; flex-direction:column; gap:6px;">
                <div style="display:flex; justify-content:space-between; padding:6px 10px; border-radius:6px; background:var(--bg-page); font-size:12.5px;">
                    <span style="font-weight:600; color:#10b981;">🟢 Hadir</span>
                    <span style="font-weight:700;">{{ $ringkasan['hadir'] }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 10px; border-radius:6px; background:var(--bg-page); font-size:12.5px;">
                    <span style="font-weight:600; color:#f59e0b;">🟠 Terlambat</span>
                    <span style="font-weight:700;">{{ $ringkasan['terlambat'] }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 10px; border-radius:6px; background:var(--bg-page); font-size:12.5px;">
                    <span style="font-weight:600; color:#3b82f6;">🔵 Izin / Dispen</span>
                    <span style="font-weight:700;">{{ $ringkasan['izin'] + $ringkasan['dispen'] }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 10px; border-radius:6px; background:var(--bg-page); font-size:12.5px;">
                    <span style="font-weight:600; color:#8b5cf6;">🟣 Sakit</span>
                    <span style="font-weight:700;">{{ $ringkasan['sakit'] }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 10px; border-radius:6px; background:var(--bg-page); font-size:12.5px;">
                    <span style="font-weight:600; color:#ef4444;">🔴 Alpa</span>
                    <span style="font-weight:700;">{{ $ringkasan['alpa'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-24">
    <div class="card-header"><h3 class="card-title">Detail Presensi Siswa — {{ \Carbon\Carbon::parse($selDate)->format('d/m/Y') }}</h3></div>
    <div class="card-body" style="padding:0;">
        @if($absensi->count())
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr><th>No</th><th>Siswa</th><th>Kelas</th><th>Status</th><th>Menit Terlambat</th><th>Keterangan</th></tr>
                </thead>
                <tbody>
                @foreach($absensi as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $item->siswa->nama ?? '-' }}</td>
                        <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                        <td>
                            @php
                                $st = strtolower($item->status);
                                $badge = match($st) {
                                    'hadir' => 'badge-success',
                                    'terlambat' => 'badge-warning',
                                    'izin' => 'badge-info',
                                    'dispen' => 'badge-info',
                                    'sakit' => 'badge-purple',
                                    'alpa' => 'badge-danger',
                                    default => 'badge-gray'
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ strtoupper($item->status) }}</span>
                        </td>
                        <td>{{ $item->menit_terlambat ? $item->menit_terlambat . ' menit' : '-' }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else <div class="empty-state"><div class="empty-state-text">Belum ada data presensi pada tanggal ini.</div></div> @endif
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Pengajuan Izin dan Dispen Siswa</h3></div>
    <div class="card-body" style="padding:0;">
        @if($pengajuan->count())
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr><th>No</th><th>Siswa</th><th>Kategori</th><th>Jenis</th><th>Status</th><th>Alasan</th></tr>
                </thead>
                <tbody>
                @foreach($pengajuan as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $item->siswa->nama ?? '-' }}</td>
                        <td>{{ strtoupper(str_replace('_', ' ', $item->kategori)) }}</td>
                        <td>{{ $item->jenis_izin ?? '-' }}</td>
                        <td>{{ strtoupper(str_replace('_', ' ', $item->status)) }}</td>
                        <td>{{ $item->alasan }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else <div class="empty-state"><div class="empty-state-text">Belum ada pengajuan siswa pada tanggal ini.</div></div> @endif
    </div>
</div>

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

        const numEl = document.createElement('span');
        numEl.textContent = d;
        cell.appendChild(numEl);

        cell.addEventListener('click', () => selectDate(dateStr));
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

function selectDate(dateStr) {
    document.getElementById('hiddenTanggal').value = dateStr;
    document.getElementById('calForm').submit();
}

function navigateCal(delta) {
    calMonth += delta;
    if (calMonth > 12) { calMonth = 1; calYear++; }
    if (calMonth < 1)  { calMonth = 12; calYear--; }
    const firstDate = calYear + '-' + String(calMonth).padStart(2,'0') + '-01';
    selectDate(firstDate);
}

renderCalendar();

// Chart Waka Monitoring
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('wakaChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Terlambat', 'Izin/Dispen', 'Sakit', 'Alpa'],
                datasets: [{
                    data: [
                        {{ $ringkasan['hadir'] }},
                        {{ $ringkasan['terlambat'] }},
                        {{ $ringkasan['izin'] + $ringkasan['dispen'] }},
                        {{ $ringkasan['sakit'] }},
                        {{ $ringkasan['alpa'] }}
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ef4444'],
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