@extends('layouts.app')

@section('title', 'Buat Jadwal Waka — Jurnal Sekolah')
@section('page-title', 'Buat Jadwal Waka')

@section('content')
<div class="page-header"><div><h1 class="page-title">Buat Jadwal Waka</h1><p class="page-subtitle">Pilih Waka yang bertugas pada tanggal tertentu</p></div><a href="{{ route('waka-kurikulum.index') }}" class="btn btn-secondary">Kembali</a></div>
<div class="card"><div class="card-header"><h3 class="card-title">Form Jadwal</h3></div><div class="card-body">
    @if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form action="{{ route('waka-kurikulum.jadwal.store') }}" method="POST">
        @csrf
        <div class="form-row">
            <div class="form-group"><label class="form-label" for="tanggal">Tanggal <span class="req">*</span></label><input class="form-control" type="date" id="tanggal" name="tanggal" value="{{ old('tanggal') }}" required></div>
            <div class="form-group"><label class="form-label" for="id_user_waka">Waka Bertugas <span class="req">*</span></label><select class="form-control" id="id_user_waka" name="id_user_waka" required><option value="">Pilih Waka</option>@foreach($wakas as $waka)<option value="{{ $waka->id_user }}" @selected(old('id_user_waka') == $waka->id_user)>{{ $waka->nama }} ({{ strtoupper(str_replace('_', ' ', $waka->role)) }})</option>@endforeach</select></div>
        </div>
        <div class="form-group"><label class="form-label" for="keterangan">Keterangan</label><input class="form-control" type="text" id="keterangan" name="keterangan" value="{{ old('keterangan') }}" maxlength="255"></div>
        <button type="submit" class="btn btn-primary">SIMPAN JADWAL</button>
    </form>
</div></div>
@endsection
