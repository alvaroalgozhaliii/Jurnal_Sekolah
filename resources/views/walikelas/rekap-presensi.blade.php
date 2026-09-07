@extends('layouts.app')

@section('title', 'Rekap Presensi — Jurnal Sekolah')
@section('page-title', 'Rekap Presensi Kelas')

@section('content')
<style>
/* ═══════════════════════════════════════════════
   COMPACT DASHBOARD GRID (Calendar + Diagram)
═══════════════════════════════════════════════ */
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

/* Compact Calendar Card */
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
    background: linear-gradient(135deg, rgba(16,185,129,.05), transparent);
}
.cal-month-name {
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -.4px;
    background: linear-gradient(135deg, #065f46, #10b981);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1.1;
}
.cal-year-label {
    font-size: 12px;
    color: var(--text-secondary);
    font-weight: 600;
}
.cal-nav-arrow {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: var(--bg-page);
    border: 1px solid var(--border);
    color: var(--text-primary);
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: justify;
    justify-content: center;
    transition: all .18s ease;
}
.cal-nav-arrow:hover {
    background: #10b981;
    border-color: #10b981;
    color: #fff;
    transform: scale(1.06);
}

.cal-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    padding: 10px 14px 2px;
}
.cal-wd {
    text-align: center;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-secondary);
}

.cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    padding: 4px 14px 14px;
    gap: 3px;
}
.cal-day {
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 500;
    position: relative;
    color: var(--text-primary);
    min-height: 36px;
    gap: 2px;
    cursor: pointer !important;
    user-select: none;
    pointer-events: auto !important;
    transition: all .15s ease;
}
.cal-day:hover:not(.cal-other-month) {
    background: rgba(16,185,129,.2) !important;
    transform: scale(1.08);
}
.cal-day.cal-other-month { opacity: .18; pointer-events: none !important; cursor: default !important; }
.cal-day.cal-weekend { color: #ef4444; }
.cal-day.cal-today { box-shadow: inset 0 0 0 2px #10b981; color: #10b981; font-weight: 800; }
.cal-day.cal-has-data { background: rgba(16,185,129,.12); font-weight: 700; }
.cal-day.cal-selected {
    background: linear-gradient(135deg, #065f46, #10b981) !important;
    color: #fff !important; font-weight: 700;
    box-shadow: 0 4px 12px rgba(16,185,129,.35); transform: scale(1.08);
}

.cal-dot { width: 4px; height: 4px; border-radius: 50%; background: #10b981; }
.cal-selected .cal-dot { background: rgba(255,255,255,.9); }

.cal-filter-strip {
    border-top: 1px solid var(--border);
    background: var(--bg-page);
    padding: 12px 16px;
    margin-top: auto;
    border-radius: 0 0 16px 16px;
    overflow: visible;
}

/* Diagram / Analytics Card */
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
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
}
.chart-body {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 24px;
    align-items: center;
}
@media (max-width: 640px) {
    .chart-body { grid-template-columns: 1fr; }
}
.chart-canvas-wrapper {
    position: relative;
    width: 100%;
    height: 180px;
}

.stat-pills {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.stat-pill-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 14px;
    border-radius: 10px;
    background: var(--bg-page);
    border: 1px solid var(--border);
    font-size: 13px;
}
.stat-pill-left {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}
.stat-pill-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
.stat-pill-count {
    font-weight: 700;
    font-size: 14px;
}
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Rekap Presensi Kelas Bimbingan</h1>
        <p class="page-subtitle">Laporan bulanan kehadiran siswa kelas bimbingan</p>
    </div>
    @if(isset($kelas) && $kelas)
    <div class="page-actions">
        <a href="{{ route('walikelas.rekap-presensi.export-csv', array_merge(request()->query(), [])) }}"
           class="btn btn-secondary">⬇️ Export CSV</a>
    </div>
    @endif
</div>

@if($kelas)
<<<<<<< HEAD
@php
    $today     = \Carbon\Carbon::today()->toDateString();
    $navCarbon = \Carbon\Carbon::create($tahun, $bulan, 1);

    $MONTHS_ID_PHP = ['','Januari','Februari','Maret','April','Mei','Juni',
                      'Juli','Agustus','September','Oktober','November','Desember'];

    $tanggalAda = $rekapData->map(fn($r) => substr($r->jurnal->tanggal ?? '', 0, 10))
        ->filter()->unique()->values()->toArray();
    $totalPresensi = array_sum($summary);
@endphp

<div class="cal-dashboard-grid">
    {{-- COMPACT MONTHLY CALENDAR --}}
    <form action="{{ route('walikelas.rekap-presensi') }}" method="GET" id="calForm" style="margin:0;">
        <input type="hidden" name="bulan"    id="hiddenBulan" value="{{ $bulan }}">
        <input type="hidden" name="tahun"    id="hiddenTahun" value="{{ $tahun }}">
        <input type="hidden" name="tanggal"  id="hiddenTanggal" value="{{ request('tanggal') }}">
        <input type="hidden" name="id_siswa" id="hiddenSiswa" value="{{ $selectedSiswaId }}">

        <div class="cal-card">
            {{-- Header --}}
            <div class="cal-head">
                <div>
                    <span class="cal-month-name" id="calMonthName">{{ $MONTHS_ID_PHP[$bulan] }}</span>
                    <span class="cal-year-label" id="calYearLabel">{{ $tahun }}</span>
                </div>
                <div style="display:flex; gap:6px;">
                    <button type="button" class="cal-nav-arrow" onclick="navigateCal(-1)" title="Bulan sebelumnya">&#8592;</button>
                    <button type="button" class="cal-nav-arrow" onclick="navigateCal(1)" title="Bulan berikutnya">&#8594;</button>
                </div>
=======
<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('walikelas.rekap-presensi') }}" method="GET" class="filter-bar">
            <div class="form-group" style="margin:0;">
                <label class="form-label" style="margin-bottom:2px;">Filter Siswa</label>
                <select name="id_siswa" class="form-control">
                    <option value="">Semua Siswa Kelas {{ $kelas->nama_kelas }}</option>
                    @foreach($siswaList as $s)
                    <option value="{{ $s->id_siswa }}" {{ $selectedSiswaId == $s->id_siswa ? 'selected' : '' }}>
                        {{ $s->nama }} (NISN: {{ $s->NISN }})
                    </option>
                    @endforeach
                </select>
>>>>>>> 5a2cadca71a8b6ed9ed1939d196668009226b51f
            </div>

            {{-- Weekday labels --}}
            <div class="cal-weekdays">
                @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $wd)
                    <div class="cal-wd">{{ $wd }}</div>
                @endforeach
            </div>

            {{-- Day grid --}}
            <div class="cal-grid" id="calGrid"></div>

            {{-- Searchable Filter Siswa --}}
            <div class="cal-filter-strip">
                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="margin-bottom:4px; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--text-secondary);">🔍 Cari / Pilih Siswa</label>
                    <select class="form-control select-search" data-searchable="true" id="uiSiswa" onchange="document.getElementById('hiddenSiswa').value=this.value; document.getElementById('calForm').submit();">
                        <option value="">Semua Siswa (Kelas {{ $kelas->nama_kelas }})</option>
                        @foreach($siswaList as $s)
                        <option value="{{ $s->id_siswa }}" {{ $selectedSiswaId == $s->id_siswa ? 'selected' : '' }}>
                            {{ $s->nama }} (NISN: {{ $s->NISN }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>

    {{-- DIAGRAM & ANALYTICS CARD --}}
    <div class="cal-chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">📊 Diagram Presensi Siswa</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">
                    {{ $MONTHS_ID_PHP[$bulan] }} {{ $tahun }} · Kelas {{ $kelas->nama_kelas }}
                </div>
            </div>
            <div class="badge badge-success" style="font-size:12px; padding:6px 12px;">
                Total: {{ $totalPresensi }} Record
            </div>
        </div>

        <div class="chart-body">
            <div class="chart-canvas-wrapper">
                <canvas id="presensiChart"></canvas>
            </div>

            <div class="stat-pills">
                <div class="stat-pill-item">
                    <div class="stat-pill-left"><span class="stat-pill-dot" style="background:#10b981;"></span> Hadir</div>
                    <div class="stat-pill-count" style="color:#10b981;">{{ $summary['hadir'] }}</div>
                </div>
                <div class="stat-pill-item">
                    <div class="stat-pill-left"><span class="stat-pill-dot" style="background:#f59e0b;"></span> Terlambat</div>
                    <div class="stat-pill-count" style="color:#f59e0b;">{{ $summary['terlambat'] }}</div>
                </div>
                <div class="stat-pill-item">
                    <div class="stat-pill-left"><span class="stat-pill-dot" style="background:#3b82f6;"></span> Izin</div>
                    <div class="stat-pill-count" style="color:#3b82f6;">{{ $summary['izin'] }}</div>
                </div>
                <div class="stat-pill-item">
                    <div class="stat-pill-left"><span class="stat-pill-dot" style="background:#8b5cf6;"></span> Sakit</div>
                    <div class="stat-pill-count" style="color:#8b5cf6;">{{ $summary['sakit'] }}</div>
                </div>
                <div class="stat-pill-item">
                    <div class="stat-pill-left"><span class="stat-pill-dot" style="background:#ef4444;"></span> Alpa</div>
                    <div class="stat-pill-count" style="color:#ef4444;">{{ $summary['alpa'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Rekap Presensi</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($rekapData->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Keterlambatan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapData as $r)
                    @php
                        $tgl = $r->jurnal->tanggal ?? '';
                        $hariIndo = $tgl ? \Carbon\Carbon::parse($tgl)->locale('id')->isoFormat('dddd') : '-';
                        $st = strtolower($r->status);
                        $badgeCls = match($st) {
                            'hadir'     => 'badge-success',
                            'izin'      => 'badge-info',
                            'sakit'     => 'badge-purple',
                            'alpa'      => 'badge-danger',
                            'terlambat' => 'badge-warning',
                            default     => 'badge-gray'
                        };
                    @endphp
                    <tr>
                        <td class="fw-bold">{{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $hariIndo }}</td>
                        <td class="text-muted">{{ $r->siswa->NISN ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $r->siswa->nama ?? '-' }}</td>
                        <td><span class="badge {{ $badgeCls }}">{{ strtoupper($r->status) }}</span></td>
                        <td>{{ $r->jam_masuk ?? '-' }}</td>
                        <td>{{ $r->menit_terlambat ? $r->menit_terlambat . ' menit' : '-' }}</td>
                        <td>{{ $r->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data rekap presensi pada periode ini.</div>
        </div>
        @endif
    </div>
</div>
@else
<div class="alert alert-warning"><div>Anda belum ditugaskan sebagai Wali Kelas.</div></div>
@endif

@push('scripts')
<script>
const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni',
                   'Juli','Agustus','September','Oktober','November','Desember'];
let calYear  = {{ $tahun }};
let calMonth = {{ $bulan }};
const todayStr   = '{{ \Carbon\Carbon::today()->toDateString() }}';
const tanggalAda = @json($tanggalAda ?? []);

const selectedDate = '{{ request('tanggal') ?? '' }}';

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
        const dow  = new Date(calYear, calMonth - 1, d).getDay();
        const cell = document.createElement('div');

        let cls = 'cal-day';
        if (dow === 0 || dow === 6)            cls += ' cal-weekend';
        if (dateStr === todayStr)               cls += ' cal-today';
        if (dateStr === selectedDate)           cls += ' cal-selected';
        if (tanggalAda.includes(dateStr))       cls += ' cal-has-data';
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
            document.getElementById('hiddenBulan').value = calMonth;
            document.getElementById('hiddenTahun').value = calYear;
            const uiS = document.getElementById('uiSiswa');
            if (uiS) document.getElementById('hiddenSiswa').value = uiS.value;
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
    document.getElementById('hiddenBulan').value = calMonth;
    document.getElementById('hiddenTahun').value = calYear;
    document.getElementById('hiddenSiswa').value = document.getElementById('uiSiswa').value;
    document.getElementById('calForm').submit();
}

renderCalendar();

// Render Presensi Donut Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('presensiChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa'],
                datasets: [{
                    data: [
                        {{ $summary['hadir'] }},
                        {{ $summary['terlambat'] }},
                        {{ $summary['izin'] }},
                        {{ $summary['sakit'] }},
                        {{ $summary['alpa'] }}
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '72%'
            }
        });
    }
});
</script>
@endpush
@endsection
