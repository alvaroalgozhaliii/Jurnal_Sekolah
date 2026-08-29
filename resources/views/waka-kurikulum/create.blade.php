@extends('layouts.app')

@section('title', 'Tambah Jadwal Piket & Waka Bertugas — Jurnal Sekolah')
@section('page-title', 'Tambah Jadwal Piket & Waka')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Jadwal Piket & Waka Bertugas</h1>
        <p class="page-subtitle">Atur penugasan Waka Piket harian dan Guru Piket untuk tanggal tertentu</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('waka-kurikulum.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 760px;">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Form Penugasan Piket & Waka
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

        <form action="{{ route('waka-kurikulum.jadwal.store') }}" method="POST">
            @csrf
            <div class="form-group mb-16">
                <label class="form-label" for="tanggal">Tanggal Penugasan <span class="req">*</span></label>
                <input class="form-control" type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="id_user_waka">Waka yang Bertugas <span class="req">*</span></label>
                    <select class="form-control select-search" id="id_user_waka" name="id_user_waka" required placeholder="Ketik nama Waka...">
                        <option value="">-- Pilih Waka Bertugas --</option>
                        @foreach($wakas as $waka)
                            <option value="{{ $waka->id_user }}" @selected(old('id_user_waka') == $waka->id_user)>
                                {{ $waka->nama }} ({{ strtoupper(str_replace('_', ' ', $waka->role)) }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted" style="display:block; margin-top:4px;">
                        ℹ️ Pengajuan dispen pada tanggal ini akan otomatis diteruskan ke Waka ini melalui WhatsApp.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="id_guru_piket">Guru Piket yang Bertugas (Opsional)</label>
                    <select class="form-control select-search" id="id_guru_piket" name="id_guru_piket" placeholder="Ketik nama Guru Piket...">
                        <option value="">-- Pilih Guru Piket (Opsional) --</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id_guru }}" @selected(old('id_guru_piket') == $guru->id_guru)>
                                {{ $guru->nama }} ({{ $guru->nip ? 'NIP: '.$guru->nip : ($guru->bidang_studi ?? 'Guru') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group mb-24">
                <label class="form-label" for="keterangan">Keterangan / Catatan Penugasan</label>
                <input class="form-control" type="text" id="keterangan" name="keterangan" value="{{ old('keterangan') }}" placeholder="Contoh: Piket Reguler / Upacara / Ujian Semester" maxlength="255">
            </div>

            <div style="display:flex; gap:12px; align-items:center; padding-top:16px; border-top:1px solid #e2e8f0;">
                <button type="submit" class="btn btn-primary btn-lg" style="padding:10px 20px; font-weight:700;">
                    SIMPAN JADWAL PIKET
                </button>
                <a href="{{ route('waka-kurikulum.index') }}" class="btn btn-secondary btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
