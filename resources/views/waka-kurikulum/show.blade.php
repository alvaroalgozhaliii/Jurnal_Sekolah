@extends('layouts.app')

@section('title', 'Detail Jadwal Waka — Jurnal Sekolah')
@section('page-title', 'Detail Jadwal Waka')

@section('content')
<div class="page-header"><div><h1 class="page-title">Detail Jadwal Waka</h1><p class="page-subtitle">Informasi Waka yang bertugas pada tanggal tertentu</p></div><a href="{{ route('waka-kurikulum.index') }}" class="btn btn-secondary">Kembali</a></div>
<div class="card"><div class="card-header"><h3 class="card-title">Jadwal Bertugas</h3></div><div class="card-body"><table class="table"><tbody>
    <tr><th>Tanggal</th><td class="fw-bold">{{ $jadwalWaka->tanggal->format('d-m-Y') }}</td></tr>
    <tr><th>Waka Bertugas</th><td class="fw-bold text-navy">{{ $jadwalWaka->waka->nama ?? '-' }}</td></tr>
    <tr><th>Role</th><td>{{ strtoupper(str_replace('_', ' ', $jadwalWaka->waka->role ?? '-')) }}</td></tr>
    <tr><th>Keterangan</th><td>{{ $jadwalWaka->keterangan ?? '-' }}</td></tr>
</tbody></table><div class="d-flex gap-8 mt-16"><a href="{{ route('waka-kurikulum.jadwal.edit', $jadwalWaka->id_jadwal_waka) }}" class="btn btn-primary">UBAH JADWAL</a></div></div></div>
@endsection
