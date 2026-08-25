@extends('layouts.app')

@section('title', 'Monitoring Siswa — Jurnal Sekolah')
@section('page-title', 'Monitoring Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Monitoring Kehadiran Siswa</h1>
        <p class="page-subtitle">Rekap izin, sakit, alpa, terlambat, hadir, dan dispen</p>
    </div>
</div>

<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('waka.monitoring-siswa') }}" method="GET" class="d-flex gap-8 align-items-end">
            <div class="form-group" style="margin:0;">
                <label class="form-label" for="tanggal">Tanggal</label>
                <input class="form-control" type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}">
            </div>
            <button class="btn btn-primary" type="submit">Tampilkan</button>
        </form>
    </div>
</div>

<div class="grid-3 mb-24">
    @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa', 'terlambat' => 'Terlambat', 'dispen' => 'Dispen'] as $key => $label)
    <div class="stat-card"><div class="stat-icon-box navy"><svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M8 12l2.5 2.5L16 9"></path></svg></div><div><div class="stat-num">{{ $ringkasan[$key] }}</div><div class="stat-label">{{ $label }}</div></div></div>
    @endforeach
</div>

<div class="card mb-24">
    <div class="card-header"><h3 class="card-title">Detail Presensi Siswa</h3></div>
    <div class="card-body" style="padding:0;">
        @if($absensi->count())
        <div class="table-wrapper" style="border:none; border-radius:0;"><table class="table"><thead><tr><th>No</th><th>Siswa</th><th>Kelas</th><th>Status</th><th>Menit Terlambat</th><th>Keterangan</th></tr></thead><tbody>
        @foreach($absensi as $index => $item)<tr><td>{{ $index + 1 }}</td><td class="fw-bold text-navy">{{ $item->siswa->nama ?? '-' }}</td><td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td><td>{{ strtoupper($item->status) }}</td><td>{{ $item->menit_terlambat ?? 0 }}</td><td>{{ $item->keterangan ?? '-' }}</td></tr>@endforeach
        </tbody></table></div>
        @else <div class="empty-state"><div class="empty-state-text">Belum ada data presensi pada tanggal ini.</div></div> @endif
    </div>
</div>

<div class="card"><div class="card-header"><h3 class="card-title">Pengajuan Izin dan Dispen Siswa</h3></div><div class="card-body" style="padding:0;">
    @if($pengajuan->count())<div class="table-wrapper" style="border:none; border-radius:0;"><table class="table"><thead><tr><th>No</th><th>Siswa</th><th>Kategori</th><th>Jenis</th><th>Status</th><th>Alasan</th></tr></thead><tbody>
    @foreach($pengajuan as $index => $item)<tr><td>{{ $index + 1 }}</td><td class="fw-bold text-navy">{{ $item->siswa->nama ?? '-' }}</td><td>{{ strtoupper(str_replace('_', ' ', $item->kategori)) }}</td><td>{{ $item->jenis_izin ?? '-' }}</td><td>{{ strtoupper(str_replace('_', ' ', $item->status)) }}</td><td>{{ $item->alasan }}</td></tr>@endforeach
    </tbody></table></div>@else <div class="empty-state"><div class="empty-state-text">Belum ada pengajuan siswa pada tanggal ini.</div></div>@endif
</div></div>
@endsection