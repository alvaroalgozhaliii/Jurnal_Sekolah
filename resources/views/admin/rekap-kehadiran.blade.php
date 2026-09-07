@extends('layouts.app')

@section('title', 'Rekap Kehadiran — Jurnal Sekolah')
@section('page-title', 'Rekap Kehadiran')

@section('content')
<style>
/* ══════════════════════════════════════════
   COMPACT DASHBOARD GRID — Admin Rekap Kehadiran
══════════════════════════════════════════ */
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
.cal-dot { width: 4px; height: 4px; border-radius: 50%; background: #10b981; }
.cal-selected .cal-dot { background: rgba(255,255,255,.9); }
.cal-day.cal-weekend { color: #ef4444; }
.cal-day.cal-weekend.cal-selected { color: #fff; }

.cal-filter-strip {
    border-top: 1px solid var(--border); background: var(--bg-page);
    padding: 12px 16px; margin-top: auto; display: flex; flex-direction: column; gap: 10px;
    border-radius: 0 0 16px 16px; overflow: visible;
}

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
        <h1 class="page-title">Rekap Kehadiran Siswa & Guru</h1>
        <p class="page-subtitle">Laporan Presensi Harian Guru & Siswa Sekolah</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.rekap-kehadiran.export-csv', request()->query()) }}"
           class="btn btn-secondary">⬇️ Export CSV</a>
    </div>
</div>

@php
    $today    = \Carbon\Carbon::today()->toDateString();
    $selDate  = $tanggal ?: $today;
    $selCarbon = \Carbon\Carbon::parse($selDate);
    $navYear  = (int)request('_y', $selCarbon->year);
    $navMonth = (int)request('_m', $selCarbon->month);

    // Summary counts for chart
    $siswaHadir     = $rekapSiswa->where('status', 'Hadir')->count();
    $siswaTerlambat = $rekapSiswa->where('status', 'Terlambat')->count();
    $siswaIzin      = $rekapSiswa->where('status', 'Izin')->count();
    $siswaSakit     = $rekapSiswa->where('status', 'Sakit')->count();
    $siswaAlpa      = $rekapSiswa->where('status', 'Alpa')->count();
@endphp

<div class="cal-dashboard-grid">
    {{-- COMPACT CALENDAR --}}
    <form action="{{ route('admin.rekap-kehadiran') }}" method="GET" id="calForm" style="margin:0;">
        <input type="hidden" name="tanggal" id="hiddenTanggal" value="{{ $selDate }}">
        <input type="hidden" name="id_kelas" id="hiddenKelas" value="{{ $kelasId }}">
        <input type="hidden" name="id_guru" id="hiddenGuru" value="{{ $guruId }}">

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

            {{-- Filter Strip with Searchable Selects --}}
            <div class="cal-filter-strip">
                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="margin-bottom:4px; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--text-secondary);">🔍 Cari / Pilih Kelas</label>
                    <select name="_kelas_ui" class="form-control select-search" data-searchable="true" id="uiKelas" onchange="document.getElementById('hiddenKelas').value=this.value; document.getElementById('calForm').submit();">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}" {{ $kelasId == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="margin-bottom:4px; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--text-secondary);">🔍 Cari / Pilih Guru</label>
                    <select name="_guru_ui" class="form-control select-search" data-searchable="true" id="uiGuru" onchange="document.getElementById('hiddenGuru').value=this.value; document.getElementById('calForm').submit();">
                        <option value="">Semua Guru</option>
                        @foreach($guru as $g)
                            <option value="{{ $g->id_guru }}" {{ $guruId == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }}</option>
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
                <div class="chart-title">📊 Diagram Presensi Harian</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">
                    Tanggal: {{ \Carbon\Carbon::parse($selDate)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                </div>
            </div>
            <div class="badge badge-info" style="font-size:12px; padding:6px 12px;">
                {{ $rekapGuru->count() }} Guru · {{ $rekapSiswa->count() }} Siswa
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 200px 1fr; gap:24px; align-items:center;">
            <div class="chart-canvas-wrapper">
                <canvas id="adminPresensiChart"></canvas>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; justify-content:space-between; padding:8px 12px; border-radius:8px; background:var(--bg-page); font-size:13px;">
                    <span style="font-weight:600; color:#10b981;">🟢 Siswa Hadir</span>
                    <span style="font-weight:700;">{{ $siswaHadir }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 12px; border-radius:8px; background:var(--bg-page); font-size:13px;">
                    <span style="font-weight:600; color:#f59e0b;">🟠 Siswa Terlambat</span>
                    <span style="font-weight:700;">{{ $siswaTerlambat }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 12px; border-radius:8px; background:var(--bg-page); font-size:13px;">
                    <span style="font-weight:600; color:#3b82f6;">🔵 Siswa Izin / Sakit</span>
                    <span style="font-weight:700;">{{ $siswaIzin + $siswaSakit }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 12px; border-radius:8px; background:var(--bg-page); font-size:13px;">
                    <span style="font-weight:600; color:#ef4444;">🔴 Siswa Alpa</span>
                    <span style="font-weight:700;">{{ $siswaAlpa }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 12px; border-radius:8px; background:var(--bg-page); font-size:13px;">
                    <span style="font-weight:600; color:#1e3a8a;">👨‍🏫 Guru Hadir</span>
                    <span style="font-weight:700;">{{ $rekapGuru->count() }} Guru</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Rekap Guru --}}
<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">Rekap Kehadiran Guru — {{ \Carbon\Carbon::parse($selDate)->format('d/m/Y') }}</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($rekapGuru->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Guru</th>
                        <th>NIP</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapGuru as $index => $rg)
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $rg->user->nama ?? '-' }}</td>
                        <td class="text-muted">{{ $rg->user->nip ?? '-' }}</td>
                        <td><span class="badge badge-success">{{ $rg->jam_masuk }}</span></td>
                        <td>
                            @if($rg->jam_keluar)
                                <span class="badge badge-info">{{ $rg->jam_keluar }}</span>
                            @else
                                <span class="badge badge-gray">Belum keluar</span>
                            @endif
                        </td>
                        <td>{{ $rg->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data presensi guru pada tanggal ini.</div>
        </div>
        @endif
    </div>
</div>

{{-- Rekap Siswa --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rekap Kehadiran Siswa — {{ \Carbon\Carbon::parse($selDate)->format('d/m/Y') }}</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($rekapSiswa->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapSiswa as $index => $rs)
                    @php
                        $st = strtolower($rs->status);
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
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="text-muted fw-bold">{{ $rs->siswa->nisn ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $rs->siswa->nama ?? '-' }}</td>
                        <td><span class="badge badge-navy">{{ $rs->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                        <td>{{ $rs->jurnal->mata_pelajaran ?? '-' }}</td>
                        <td><span class="badge {{ $badgeCls }}">{{ strtoupper($rs->status) }}</span></td>
                        <td>{{ $rs->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data presensi siswa pada tanggal ini.</div>
        </div>
        @endif
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
    const uiK = document.getElementById('uiKelas');
    const uiG = document.getElementById('uiGuru');
    if (uiK) document.getElementById('hiddenKelas').value = uiK.value;
    if (uiG) document.getElementById('hiddenGuru').value  = uiG.value;
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

// Chart Admin Presensi
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('adminPresensiChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Terlambat', 'Izin/Sakit', 'Alpa'],
                datasets: [{
                    data: [
                        {{ $siswaHadir }},
                        {{ $siswaTerlambat }},
                        {{ $siswaIzin + $siswaSakit }},
                        {{ $siswaAlpa }}
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
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
