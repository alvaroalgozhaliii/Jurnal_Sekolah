@extends('layouts.app')

@section('title', 'Jadwal Waka Bertugas — Jurnal Sekolah')
@section('page-title', 'Jadwal Waka Bertugas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Waka Kurikulum</h1>
        <p class="page-subtitle">Mengatur Waka yang bertugas berdasarkan tanggal</p>
    </div>
</div>

<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">{{ isset($jadwalEdit) ? 'Ubah Jadwal Waka' : 'Tambah Jadwal Waka' }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ isset($jadwalEdit) ? route('waka-kurikulum.jadwal.update', $jadwalEdit->id_jadwal_waka) : route('waka-kurikulum.jadwal.store') }}" method="POST">
            @csrf
            @if(isset($jadwalEdit)) @method('PUT') @endif
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="tanggal">Tanggal <span class="req">*</span></label>
                    <input class="form-control" type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', isset($jadwalEdit) ? $jadwalEdit->tanggal->format('Y-m-d') : '') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="id_user_waka">Waka Bertugas <span class="req">*</span></label>
                    <select class="form-control" id="id_user_waka" name="id_user_waka" required>
                        <option value="">Pilih Waka</option>
                        @foreach($wakas as $waka)
                            <option value="{{ $waka->id_user }}" {{ old('id_user_waka', $jadwalEdit->id_user_waka ?? '') == $waka->id_user ? 'selected' : '' }}>{{ $waka->nama }} ({{ strtoupper(str_replace('_', ' ', $waka->role)) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="keterangan">Keterangan</label>
                <input class="form-control" type="text" id="keterangan" name="keterangan" value="{{ old('keterangan', $jadwalEdit->keterangan ?? '') }}" maxlength="255">
            </div>
            <div class="d-flex gap-8 mt-16">
                <button type="submit" class="btn btn-primary">{{ isset($jadwalEdit) ? 'UPDATE JADWAL' : 'SIMPAN JADWAL' }}</button>
                @if(isset($jadwalEdit))
                    <a href="{{ route('waka-kurikulum.dashboard') }}" class="btn btn-secondary">Batal</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Jadwal Tugas Waka</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($jadwal->count())
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead><tr><th>No</th><th>Tanggal</th><th>Waka Bertugas</th><th>Keterangan</th><th class="action-col">Aksi</th></tr></thead>
                <tbody>
                @foreach($jadwal as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->tanggal->format('d-m-Y') }}</td>
                        <td class="fw-bold text-navy">{{ $item->waka->nama ?? '-' }}<br><span class="text-muted" style="font-size:11px;">{{ strtoupper(str_replace('_', ' ', $item->waka->role ?? '-')) }}</span></td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="action-col">
                            <a href="{{ route('waka-kurikulum.jadwal.edit', $item->id_jadwal_waka) }}" class="btn btn-secondary btn-sm">Ubah</a>
                            <form action="{{ route('waka-kurikulum.jadwal.destroy', $item->id_jadwal_waka) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="empty-state"><div class="empty-state-text">Belum ada jadwal Waka bertugas.</div></div>
        @endif
    </div>
</div>
@endsection