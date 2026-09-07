@extends('layouts.app')

@section('title', 'Siswa Terlambat Kelas Saya — Jurnal Sekolah')
@section('page-title', 'Data Siswa Terlambat')

@section('content')
<style>
/* ═══════════════════════════════════════
   COMPACT DASHBOARD GRID (Siswa Terlambat)
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
    background: linear-gradient(135deg, rgba(217,119,6,.06), transparent);
}
.cal-month-name {
    font-size: 20px; font-weight: 800; letter-spacing: -.4px;
    background: linear-gradient(135deg, #92400e, #d97706);
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
    background: #d97706; border-color: #d97706; color: #fff;
    transform: scale(1.06);
}
.cal-weekdays {
    display: grid; grid-template-columns: repeat(7, 1fr);
    padding: 10px 14px 2px;
}
.cal-wd {
    text-align: center; font-size: 10.5px; font-weight: 700;
    text-transform: uppercase; color: var(--text-secondary);
}
.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); padding: 4px 14px 14px; gap: 3px; }
.cal-day {
    aspect-ratio: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    border-radius: 8px; font-size: 12.5px; font-weight: 500;
    cursor: pointer !important; user-select: none; pointer-events: auto !important;
    position: relative;
    transition: background .15s, transform .12s;
    color: var(--text-primary); min-height: 36px; gap: 2px;
}
.cal-day:hover:not(.cal-other-month):not(.cal-selected) { background: var(--bg-page); transform: scale(1.06); }
.cal-day.cal-today { box-shadow: inset 0 0 0 2px #d97706; color: #d97706; font-weight: 800; }
.cal-day.cal-selected {
    background: linear-gradient(135deg, #92400e, #d97706) !important;
    color: #fff !important; font-weight: 700;
    box-shadow: 0 4px 12px rgba(217,119,6,.35); transform: scale(1.08);
}
.cal-day.cal-other-month { opacity: .18; pointer-events: none !important; cursor: default !important; }
.cal-dot { width: 4px; height: 4px; border-radius: 50%; background: #ef4444; }
.cal-selected .cal-dot { background: rgba(255,255,255,.9); }
.cal-day.cal-weekend { color: #ef4444; }
.cal-day.cal-weekend.cal-selected { color: #fff; }

.cal-filter-strip {
    border-top: 1px solid var(--border); background: var(--bg-page);
    padding: 12px 16px; margin-top: auto;
    border-radius: 0 0 16px 16px; overflow: visible;
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
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
}
.chart-canvas-wrapper {
    position: relative;
    width: 100%;
    height: 180px;
}
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Siswa Terlambat — Kelas {{ $kelas->nama_kelas ?? '-' }}</h1>
        <p class="page-subtitle">Rekap keterlambatan siswa kelas bimbingan Anda</p>
    </div>
    @if($kelas)
    <div class="page-actions">
        <a href="{{ route('walikelas.siswa-terlambat.export-csv', request()->query()) }}" class="btn btn-secondary">
            ⬇️ Export CSV
        </a>
    </div>
    @endif
</div>

@if(!$kelas)
<div class="alert alert-warning"><div>Anda belum ditugaskan sebagai Wali Kelas.</div></div>
@else

@php
    $today   = \Carbon\Carbon::today()->toDateString();
    $selDate = $tanggal ?: $today;
    $selC    = \Carbon\Carbon::parse($selDate);
    $navYear = (int)($calYear ?? $selC->year);
    $navMonth= (int)($calMonth ?? $selC->month);

    $tanggalAda = $terlambatListAll->pluck('tanggal')->map(fn($d) => substr($d,0,10))->unique()->values()->toArray();

    // Group late students for chart
    $topTerlambat = $terlambatListAll->groupBy('id_siswa')->map(function($items) {
        return [
            'nama' => Str::words($items->first()->siswa->nama ?? 'Siswa', 2, ''),
            'total' => $items->count()
        ];
    })->sortByDesc('total')->take(5)->values();
@endphp

<div class="cal-dashboard-grid">
    {{-- COMPACT CALENDAR --}}
    <form action="{{ route('walikelas.siswa-terlambat') }}" method="GET" id="calForm" style="margin:0;">
        <input type="hidden" name="tanggal" id="hiddenTanggal" value="{{ $selDate }}">
        <input type="hidden" name="id_siswa" id="hiddenSiswa" value="{{ $selectedSiswaId }}">

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
                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="margin-bottom:4px; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--text-secondary);">🔍 Cari / Pilih Siswa</label>
                    <select class="form-control select-search" data-searchable="true" id="uiSiswa" onchange="document.getElementById('hiddenSiswa').value=this.value; document.getElementById('calForm').submit();">
                        <option value="">-- Semua Siswa --</option>
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

    {{-- DIAGRAM & STATISTIK CARD --}}
    <div class="cal-chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">📊 Diagram Keterlambatan Siswa</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">
                    Tren & Top Siswa Sering Terlambat (Bulan Ini)
                </div>
            </div>
            <div class="badge badge-warning" style="font-size:12px; padding:6px 12px;">
                Total: {{ $terlambatListAll->count() }} Keterlambatan
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 180px; gap:20px; align-items:center;">
            <div class="chart-canvas-wrapper">
                <canvas id="terlambatChart"></canvas>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px;">
                <div style="background:var(--bg-page); border:1px solid var(--border); padding:10px 14px; border-radius:10px;">
                    <div style="font-size:11px; color:var(--text-secondary); font-weight:600; text-transform:uppercase;">Terlambat Hari Ini</div>
                    <div style="font-size:20px; font-weight:800; color:#d97706;">{{ $terlambatList->count() }} Siswa</div>
                </div>
                <div style="background:var(--bg-page); border:1px solid var(--border); padding:10px 14px; border-radius:10px;">
                    <div style="font-size:11px; color:var(--text-secondary); font-weight:600; text-transform:uppercase;">Total Siswa Pernah Terlambat</div>
                    <div style="font-size:20px; font-weight:800; color:#1e3a8a;">{{ $terlambatListAll->pluck('id_siswa')->unique()->count() }} Siswa</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">Detail Keterlambatan — {{ \Carbon\Carbon::parse($selDate)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($terlambatList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Siswa</th>
                        <th>Terlambat s.d Jam ke-</th>
                        <th>Alasan</th>
                        <th>Tindakan</th>
                        <th class="action-col">Slip</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($terlambatList as $idx => $t)
                    <tr>
                        <td class="no-col">{{ $idx + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $t->siswa->nama ?? '-' }}</td>
                        <td>
                            <span class="badge" style="background:#fef3c7;color:#d97706;font-weight:700;">
                                Jam ke-{{ $t->terlambat_sampai_jam }}
                            </span>
                            @php
                                $hD = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
                                $hT = $hD[\Carbon\Carbon::parse($t->tanggal)->format('l')] ?? 'Senin';
                                $alk = \App\Services\KbmService::getAlokasiWaktu($hT, (int)$t->terlambat_sampai_jam);
                            @endphp
                            @if($alk)
                                <div style="font-size:11px;color:var(--text-secondary);">{{ $alk['waktu_mulai'] }} – {{ $alk['waktu_selesai'] }}</div>
                            @endif
                        </td>
                        <td>{{ Str::limit($t->alasan, 50) }}</td>
                        <td class="text-muted">{{ Str::limit($t->tindakan_piket ?? '-', 30) }}</td>
                        <td class="action-col">
                            <a href="{{ route('piket.siswa-terlambat.slip', $t->id_terlambat) }}" target="_blank"
                               class="btn btn-secondary btn-sm" title="Cetak Slip">🖨️</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada catatan keterlambatan pada tanggal ini.</div>
        </div>
        @endif
    </div>
</div>

@if($terlambatListAll->count() > 0)
@php
    $rekap = $terlambatListAll->groupBy('id_siswa')->map(function($items) {
        return [
            'nama'   => $items->first()->siswa->nama ?? '-',
            'nisn'   => $items->first()->siswa->NISN ?? '-',
            'jumlah' => $items->count(),
        ];
    })->sortByDesc('jumlah');
@endphp
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ringkasan Bulan Ini — Rekap per Siswa</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Total Terlambat</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekap as $r)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-navy">{{ $r['nama'] }}</td>
                        <td class="text-muted">{{ $r['nisn'] }}</td>
                        <td>
                            <span class="badge {{ $r['jumlah'] >= 5 ? 'badge-danger' : ($r['jumlah'] >= 3 ? 'badge-warning' : 'badge-info') }}">
                                {{ $r['jumlah'] }}x Terlambat
                            </span>
                        </td>
                        <td class="text-muted">
                            @if($r['jumlah'] >= 5) ⚠️ Perlu perhatian khusus
                            @elseif($r['jumlah'] >= 3) Perlu ditindaklanjuti
                            @else - @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endif

@push('scripts')
<script>
const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
let calYear  = {{ $selC->year }};
let calMonth = {{ $selC->month }};
const selectedDate = '{{ $selDate }}';
const todayStr     = '{{ $today }}';
const tanggalAda   = @json($tanggalAda);

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
    const uiS = document.getElementById('uiSiswa');
    if (uiS) document.getElementById('hiddenSiswa').value = uiS.value;
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

// Chart Keterlambatan
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('terlambatChart');
    if (ctx) {
        const topData = @json($topTerlambat);
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: topData.length ? topData.map(item => item.nama) : ['Tidak ada data'],
                datasets: [{
                    label: 'Frekuensi Terlambat',
                    data: topData.length ? topData.map(item => item.total) : [0],
                    backgroundColor: 'rgba(217, 119, 6, 0.8)',
                    borderColor: '#d97706',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { font: { size: 11 } } }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
