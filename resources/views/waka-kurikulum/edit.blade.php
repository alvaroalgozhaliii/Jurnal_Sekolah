@extends('layouts.app')

@section('title', 'Edit Jadwal Piket & Waka — Jurnal Sekolah')
@section('page-title', 'Edit Jadwal Piket & Waka')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Jadwal Piket & Waka Bertugas</h1>
        <p class="page-subtitle">Ubah penugasan Waka Piket dan Guru Piket</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('waka-kurikulum.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 760px;">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            Edit Data Penugasan
        </h3>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger mb-16">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('waka-kurikulum.jadwal.update', $jadwalWaka->id_jadwal_waka) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group mb-16">
                <label class="form-label" for="tanggal">Tanggal Penugasan <span class="req">*</span></label>
                <input class="form-control" type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', $jadwalWaka->tanggal ? $jadwalWaka->tanggal->format('Y-m-d') : '') }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="id_user_waka">Waka yang Bertugas <span class="req">*</span></label>
                    <select class="form-control select-search" id="id_user_waka" name="id_user_waka" required placeholder="Ketik nama Waka...">
                        @foreach($wakas as $waka)
                            <option value="{{ $waka->id_user }}" @selected(old('id_user_waka', $jadwalWaka->id_user_waka) == $waka->id_user)>
                                {{ $waka->nama }} ({{ strtoupper(str_replace('_', ' ', $waka->role)) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="id_guru_piket">Guru Piket yang Bertugas (Opsional)</label>
                    <select class="form-control select-search" id="id_guru_piket" name="id_guru_piket" placeholder="Ketik nama Guru Piket...">
                        <option value="">Pilih Guru Piket (Opsional)</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id_guru }}" @selected(old('id_guru_piket', $jadwalWaka->id_guru_piket) == $guru->id_guru)>
                                {{ $guru->nama }} ({{ $guru->nip ? 'NIP: '.$guru->nip : ($guru->bidang_studi ?? 'Guru') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group mb-24">
                <label class="form-label" for="keterangan">Keterangan / Catatan Penugasan</label>
                <input class="form-control" type="text" id="keterangan" name="keterangan" value="{{ old('keterangan', $jadwalWaka->keterangan) }}" maxlength="255">
            </div>

            <div style="display:flex; gap:12px; align-items:center; padding-top:16px; border-top:1px solid #e2e8f0;">
                <button type="submit" class="btn btn-primary btn-lg" style="padding:10px 20px; font-weight:700;">
                    UPDATE JADWAL PIKET
                </button>
                <a href="{{ route('waka-kurikulum.index') }}" class="btn btn-secondary btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
