@extends('layouts.app')

@section('title', 'Jurnal Harian — Jurnal Sekolah')
@section('page-title', 'Jurnal Harian KBM')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jurnal Harian KBM</h1>
        <p class="page-subtitle">Daftar Pelaksanaan Mengajar & Jurnal Kelas</p>
    </div>
    @if(Auth::user()->isGuru())
    <div class="page-actions">
        <a href="{{ route('jurnal-harian.create') }}" class="btn btn-primary">+ Isi Jurnal Mengajar</a>
        <a href="{{ route('jurnal-harian.trash') }}" class="btn btn-secondary">Lihat Trash</a>
    </div>
    @endif
</div>

{{-- Rekap Excel Bulanan Card --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <strong class="text-navy" style="font-size:14px;">Rekap Excel Jurnal Harian KBM per Bulan:</strong>
                <span class="text-muted" style="font-size:12px;">(Khusus Guru Pengajar Berjadwal)</span>
            </div>
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <select id="bulanJurnal" class="form-control" style="width:auto; padding:4px 10px; font-size:13px; height:auto;">
                    @php $curMonth = date('n'); @endphp
                    @foreach(['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $mVal => $mName)
                        <option value="{{ $mVal }}" {{ $curMonth == $mVal ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
                <input type="number" id="tahunJurnal" value="{{ date('Y') }}" min="2020" max="2099" class="form-control" style="width:80px; padding:4px 8px; font-size:13px; height:auto;">
                <a id="btnDownloadJurnal" href="{{ route('rekap.jurnal-excel', ['bulan' => date('n'), 'tahun' => date('Y')]) }}" class="btn btn-sm" style="background:#2563eb; color:#fff; font-weight:600;">
                    Unduh Rekap Excel (.XLSX)
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const b = document.getElementById('bulanJurnal');
    const t = document.getElementById('tahunJurnal');
    const btn = document.getElementById('btnDownloadJurnal');
    function updateUrl() {
        btn.href = "{{ route('rekap.jurnal-excel') }}?bulan=" + b.value + "&tahun=" + t.value;
    }
    b.addEventListener('change', updateUrl);
    t.addEventListener('input', updateUrl);
});
</script>

<!-- FILTER BAR -->
<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('jurnal-harian.index') }}" method="GET" class="filter-bar">
            <div class="form-group" style="margin:0;">
                <label for="tanggal" class="form-label" style="margin-bottom:2px;">Tanggal Jurnal</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" class="form-control">
            </div>

            @if(Auth::user()->isAdmin() || Auth::user()->isPiket() || Auth::user()->isWaka())
            <div class="form-group" style="margin:0;">
                <label for="id_guru" class="form-label" style="margin-bottom:2px;">Filter Guru</label>
                <select id="id_guru" name="id_guru" class="form-control">
                    <option value="">Semua Guru</option>
                    @foreach($guruList as $g)
                    <option value="{{ $g->id_guru }}" {{ $id_guru == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="form-group" style="margin:0;">
                <label for="id_kelas" class="form-label" style="margin-bottom:2px;">Filter Kelas</label>
                <select id="id_kelas" name="id_kelas" class="form-control">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                    <option value="{{ $k->id_kelas }}" {{ $id_kelas == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="align-self:flex-end;">Filter Data</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($jurnal_harian->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th>Materi Pelajaran</th>
                        <th>Status</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurnal_harian as $item)
                    @php
                        $st = strtolower($item->status_keterlaksanaan);
                        $badgeCls = match($st) {
                            'terlaksana' => 'badge-success',
                            'tidak_terlaksana', 'kosong' => 'badge-danger',
                            'pengganti' => 'badge-amber',
                            default => 'badge-gray'
                        };
                    @endphp
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $item->tanggal }}</td>
                        <td><span class="badge badge-navy">{{ $item->jadwal->kelas->nama_kelas ?? '-' }}</span></td>
                        <td class="fw-bold text-navy">{{ $item->mapel }}</td>
                        <td>{{ $item->guru->nama ?? '-' }}</td>
                        <td>{{ Str::limit($item->materi, 30) }}</td>
                        <td><span class="badge {{ $badgeCls }}">{{ strtoupper(str_replace('_', ' ', $item->status_keterlaksanaan)) }}</span></td>
                        <td class="action-col">
                            <a href="{{ route('jurnal-harian.show', $item->id_jurnal) }}" class="btn btn-secondary btn-sm">Detail</a>
                            @if(Auth::user()->isGuru() && $item->id_guru == Auth::user()->guru?->id_guru)
                            <a href="{{ route('jurnal-harian.edit', $item->id_jurnal) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('jurnal-harian.destroy', $item->id_jurnal) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus jurnal harian ini?')">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data jurnal harian untuk filter ini.</div>
        </div>
        @endif
    </div>
</div>
@endsection
