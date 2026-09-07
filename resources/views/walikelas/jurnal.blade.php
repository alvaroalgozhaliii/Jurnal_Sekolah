@extends('layouts.app')

@section('title', 'Jurnal Kelas Bimbingan — Jurnal Sekolah')
@section('page-title', 'Jurnal Kelas Bimbingan')

@section('content')
<style>
/* ═══════════════════════════════════════
   COMPACT DASHBOARD GRID (Jurnal KBM)
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
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    display: flex;
    flex-direction: column;
    height: 100%;
}
.cal-head {
    padding: 16px 20px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(135deg, rgba(99,102,241,.06), transparent);
}
.cal-month-name {
    font-size: 20px; font-weight: 800; letter-spacing: -.4px;
    background: linear-gradient(135deg, #1e1b4b, #6366f1);
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
    background: #6366f1; border-color: #6366f1; color: #fff;
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
.cal-day.cal-today { box-shadow: inset 0 0 0 2px #6366f1; color: #6366f1; font-weight: 800; }
.cal-day.cal-selected {
    background: linear-gradient(135deg, #1e1b4b, #6366f1) !important;
    color: #fff !important; font-weight: 700;
    box-shadow: 0 4px 12px rgba(99,102,241,.35); transform: scale(1.08);
}
.cal-day.cal-other-month { opacity: .18; pointer-events: none !important; cursor: default !important; }
.cal-dot { width: 4px; height: 4px; border-radius: 50%; background: #6366f1; }
.cal-selected .cal-dot { background: rgba(255,255,255,.9); }
.cal-day.cal-weekend { color: #ef4444; }
.cal-day.cal-weekend.cal-selected { color: #fff; }

.cal-filter-strip {
    border-top: 1px solid var(--border); background: var(--bg-page);
    padding: 12px 16px; margin-top: auto; display: flex; align-items: center; justify-content: space-between;
}

/* Analytics Card */
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
        <h1 class="page-title">Jurnal Harian Kelas Bimbingan</h1>
        <p class="page-subtitle">Monitoring pelaksanaan pengajaran KBM di kelas bimbingan</p>
    </div>
</div>

@if($kelas)
@php
    $today   = \Carbon\Carbon::today()->toDateString();
    $selDate = $tanggal ?: null;
    $selC    = $selDate ? \Carbon\Carbon::parse($selDate) : \Carbon\Carbon::today();
    $navYear  = $selC->year;
    $navMonth = $selC->month;

    $tanggalAda = $jurnalAll->pluck('tanggal')->map(fn($d) => substr($d,0,10))->filter()->unique()->values()->toArray();

    // Mapel distribution for chart
    $mapelStats = $jurnalAll->groupBy('mata_pelajaran')->map->count()->sortDesc()->take(5);
@endphp

<div class="cal-dashboard-grid">
    {{-- COMPACT CALENDAR --}}
    <form action="{{ route('walikelas.jurnal') }}" method="GET" id="calForm" style="margin:0;">
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

            <div class="cal-filter-strip">
                <span style="font-size:11px; font-weight:600; color:var(--text-secondary);">● ada jurnal</span>
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('hiddenTanggal').value=''; document.getElementById('calForm').submit();" style="font-size:11px; padding:4px 10px;">
                    Tampilkan Semua
                </button>
            </div>
        </div>
    </form>

    {{-- DIAGRAM CARD --}}
    <div class="cal-chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">📊 Visualisasi Pelaksanaan KBM</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">
                    Statistik Mata Pelajaran & Sesi Pembelajaran
                </div>
            </div>
            <div class="badge badge-purple" style="font-size:12px; padding:6px 12px;">
                Total: {{ $jurnalAll->count() }} Sesi KBM
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 180px; gap:20px; align-items:center;">
            <div class="chart-canvas-wrapper">
                <canvas id="jurnalChart"></canvas>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px;">
                <div style="background:var(--bg-page); border:1px solid var(--border); padding:10px 14px; border-radius:10px;">
                    <div style="font-size:11px; color:var(--text-secondary); font-weight:600; text-transform:uppercase;">Tercatat Hari Ini</div>
                    <div style="font-size:20px; font-weight:800; color:#6366f1;">
                        {{ $jurnalAll->where('tanggal', $today)->count() }} Sesi
                    </div>
                </div>
                <div style="background:var(--bg-page); border:1px solid var(--border); padding:10px 14px; border-radius:10px;">
                    <div style="font-size:11px; color:var(--text-secondary); font-weight:600; text-transform:uppercase;">Total Mata Pelajaran</div>
                    <div style="font-size:20px; font-weight:800; color:#10b981;">
                        {{ $mapelStats->count() }} Mapel
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top:16px;">
    <div class="card-header">
        <h3 class="card-title">
            Riwayat Jurnal Harian —
            @if($selDate)
                {{ \Carbon\Carbon::parse($selDate)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @else
                Semua Sesi KBM
            @endif
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($jurnalList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Tanggal</th>
                        <th>Jam ke-</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th>Materi Pembelajaran</th>
                        <th>Status Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurnalList as $idx => $j)
                    <tr>
                        <td class="no-col">{{ $idx + 1 }}</td>
                        <td class="fw-bold">{{ \Carbon\Carbon::parse($j->tanggal)->format('d/m/Y') }}</td>
                        <td><span class="badge badge-info">Jam {{ $j->jam_ke }}</span></td>
                        <td class="fw-bold text-navy">{{ $j->mata_pelajaran }}</td>
                        <td>{{ $j->user->nama ?? '-' }}</td>
                        <td>{{ Str::limit($j->materi, 60) }}</td>
                        <td>
                            @if(strtolower($j->status_kelas ?? '') == 'tertib')
                                <span class="badge badge-success">TERTIB</span>
                            @else
                                <span class="badge badge-warning">{{ strtoupper($j->status_kelas ?? 'NORMAL') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">
                @if($selDate)
                    Belum ada jurnal KBM pada tanggal {{ \Carbon\Carbon::parse($selDate)->format('d/m/Y') }}.
                @else
                    Belum ada jurnal harian yang tercatat di kelas ini.
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@else
<div class="alert alert-warning">
    <div>Anda belum ditugaskan sebagai Wali Kelas.</div>
</div>
@endif

@push('scripts')
<script>
const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
let calYear  = {{ $navYear }};
let calMonth = {{ $navMonth }};
const selectedDate = '{{ $selDate ?? "" }}';
const todayStr     = '{{ $today }}';
const tanggalAda   = @json($tanggalAda ?? []);

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

        if (tanggalAda.includes(dateStr)) {
            const dot = document.createElement('div');
            dot.className = 'cal-dot';
            cell.appendChild(dot);
        }

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

// Chart Jurnal Mapel
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('jurnalChart');
    if (ctx) {
        const mapelData = @json($mapelStats);
        const labels = Object.keys(mapelData);
        const values = Object.values(mapelData);

        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels.length ? labels : ['Belum ada data'],
                datasets: [{
                    label: 'Jumlah Sesi KBM',
                    data: values.length ? values : [0],
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderColor: '#6366f1',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
