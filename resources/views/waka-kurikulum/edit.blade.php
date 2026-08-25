@extends('layouts.app')

@section('title', 'Ubah Jadwal Waka — Jurnal Sekolah')
@section('page-title', 'Ubah Jadwal Waka')

@section('content')
<div class="page-header"><div><h1 class="page-title">Ubah Jadwal Waka</h1><p class="page-subtitle">Perbarui penugasan Waka pada tanggal ini</p></div><a href="{{ route('waka-kurikulum.index') }}" class="btn btn-secondary">Kembali</a></div>
<div class="card"><div class="card-header"><h3 class="card-title">Form Jadwal</h3></div><div class="card-body">
    @if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form action="{{ route('waka-kurikulum.jadwal.update', $jadwalWaka->id_jadwal_waka) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-row">
            <div class="form-group"><label class="form-label" for="tanggal">Tanggal <span class="req">*</span></label><input class="form-control" type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', $jadwalWaka->tanggal->format('Y-m-d')) }}" required></div>
            <div class="form-group"><label class="form-label" for="id_user_waka">Waka Bertugas <span class="req">*</span></label><select class="form-control" id="id_user_waka" name="id_user_waka" required><option value="">Pilih Waka</option>@foreach($wakas as $waka)<option value="{{ $waka->id_user }}" @selected(old('id_user_waka', $jadwalWaka->id_user_waka) == $waka->id_user)>{{ $waka->nama }} ({{ strtoupper(str_replace('_', ' ', $waka->role)) }})</option>@endforeach</select></div>
        </div>
        <div class="form-group"><label class="form-label" for="keterangan">Keterangan</label><input class="form-control" type="text" id="keterangan" name="keterangan" value="{{ old('keterangan', $jadwalWaka->keterangan) }}" maxlength="255"></div>
        <button type="submit" class="btn btn-primary">UPDATE JADWAL</button>
    </form>
</div></div>
@endsection
