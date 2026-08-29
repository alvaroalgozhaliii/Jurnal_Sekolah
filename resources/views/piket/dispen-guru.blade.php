@extends('layouts.app')

@section('title', 'Dispensasi Guru — Jurnal Sekolah')
@section('page-title', 'Dispensasi Guru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pencatatan Dispensasi / Izin Guru Piket</h1>
        <p class="page-subtitle">Formulir pencatatan dispensasi, izin keluar, dan tugas pengganti guru KBM hari ini</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('piket.dashboard') }}" class="btn btn-secondary">&larr; Kembali ke Dashboard</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success mb-20">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger mb-20">
    <ul style="margin:0; padding-left:16px;">
        @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card mb-24" style="max-width: 780px;">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            Formulir Pengajuan Dispen Guru
        </h3>
    </div>
    <div class="card-body">
        <form action="{{ route('piket.dispen-guru.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Dispen (Otomatis Sistem)</label>
                    <input type="text" value="{{ $todayDate }}" readonly class="form-control" style="background:#f1f5f9; font-weight:700; color:#1e3a8a;">
                </div>
                <div class="form-group">
                    <label class="form-label" for="status_guru">Status Kehadiran <span class="req">*</span></label>
                    <select id="status_guru" name="status_guru" class="form-control" required>
                        <option value="tidak_hadir" {{ old('status_guru') == 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir / Izin</option>
                        <option value="terlambat" {{ old('status_guru') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="digantikan" {{ old('status_guru') == 'digantikan' ? 'selected' : '' }}>Digantikan</option>
                    </select>
                </div>
            </div>

            <div class="form-group mb-16">
                <label class="form-label" for="id_jadwal">Pilih Jadwal Mengajar Guru <span class="req">*</span></label>
                <select id="id_jadwal" name="id_jadwal" class="form-control select-search" required placeholder="Ketik nama guru / kelas / mapel...">
                    <option value="">-- Pilih Jadwal & Guru Hari Ini --</option>
                    @foreach($jadwalHariIni as $j)
                        <option value="{{ $j->id_jadwal }}" {{ old('id_jadwal') == $j->id_jadwal ? 'selected' : '' }}>
                            {{ $j->guru->nama ?? 'Tanpa Guru' }} — Kelas {{ $j->kelas->nama_kelas ?? '-' }} (Mapel: {{ $j->mapel }}, Jam {{ $j->jam_ke }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-16">
                <label class="form-label" for="keperluan_select">Keperluan / Alasan Izin <span class="req">*</span></label>
                <select name="keperluan_select" id="keperluan_select" class="form-control" required>
                    <option value="">-- Pilih Keperluan / Alasan Izin --</option>
                    @foreach($keperluanOptions as $kop)
                        <option value="{{ $kop }}" {{ old('keperluan_select') == $kop ? 'selected' : '' }}>{{ $kop }}</option>
                    @endforeach
                </select>

                <div id="custom_keperluan_wrap" style="{{ old('keperluan_select') == 'Lainnya' ? 'display:block;' : 'display:none;' }} margin-top:8px;">
                    <input type="text" name="keperluan_custom" value="{{ old('keperluan_custom') }}" placeholder="Tuliskan detail alasan / keperluan lainnya..." class="form-control">
                </div>
            </div>

            <div class="form-group mb-20">
                <label class="form-label" for="pengganti">Guru Pengganti (Jika Ada)</label>
                <input type="text" id="pengganti" name="pengganti" value="{{ old('pengganti') }}" placeholder="Nama Guru yang menggantikan mengajar..." class="form-control">
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">SUBMIT DISPEN GURU</button>
                <a href="{{ route('piket.dashboard') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<div class="page-header" style="margin-top:32px;">
    <div>
        <h2 class="page-title" style="font-size:18px;">Riwayat Dispen Guru Hari Ini</h2>
        <p class="page-subtitle">Daftar dispensasi guru KBM tercatat tanggal: <strong>{{ $todayDate }}</strong></p>
    </div>
</div>

{{-- Search Riwayat --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('piket.dispen-guru.index') }}" class="d-flex gap-8" style="align-items:center;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama guru, keperluan, pengganti..." class="form-control" style="max-width:420px;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if($search ?? false)
                <a href="{{ route('piket.dispen-guru.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($dispenList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Guru</th>
                        <th>Kelas & Mapel</th>
                        <th>Status</th>
                        <th>Keperluan / Alasan</th>
                        <th>Guru Pengganti</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dispenList as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-navy">{{ $item->jadwal->guru->nama ?? '-' }}</td>
                        <td>
                            <span class="badge badge-navy">Kelas {{ $item->jadwal->kelas->nama_kelas ?? '-' }}</span><br>
                            <small class="text-muted">{{ $item->jadwal->mapel ?? '-' }} (Jam {{ $item->jadwal->jam_ke ?? '-' }})</small>
                        </td>
                        <td>
                            @if($item->status_guru === 'tidak_hadir')
                                <span class="badge badge-danger">Tidak Hadir</span>
                            @elseif($item->status_guru === 'terlambat')
                                <span class="badge badge-warning">Terlambat</span>
                            @elseif($item->status_guru === 'digantikan')
                                <span class="badge badge-info">Digantikan</span>
                            @else
                                <span class="badge badge-gray">{{ ucfirst($item->status_guru) }}</span>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $item->keperluan ?? '-' }}</td>
                        <td>
                            @if($item->pengganti)
                                <span class="badge badge-success">{{ $item->pengganti }}</span>
                            @else
                                <span class="text-muted">-</span>
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
                @if($search)
                    Tidak ada data dispen guru yang sesuai pencarian.
                @else
                    Belum ada data dispen guru yang dicatat hari ini.
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    const keperluanSelect = document.getElementById('keperluan_select');
    const customWrap = document.getElementById('custom_keperluan_wrap');

    if (keperluanSelect && customWrap) {
        keperluanSelect.addEventListener('change', function() {
            if (this.value === 'Lainnya') {
                customWrap.style.display = 'block';
            } else {
                customWrap.style.display = 'none';
            }
        });
    }
</script>
@endpush
@endsection
