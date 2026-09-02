@extends('layouts.app')

@section('title', 'Catat Siswa Sakit — Jurnal Sekolah')
@section('page-title', 'Catat Siswa Sakit')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pencatatan Siswa Sakit (Piket)</h1>
        <p class="page-subtitle">Catat siswa yang sakit/izin pulang saat jam sekolah berlangsung</p>
    </div>
</div>

<div class="card mb-24" style="max-width: 650px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Catat Siswa Sakit</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('piket.anak-sakit.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label" for="id_siswa">Pilih Siswa <span class="req">*</span></label>
                <select id="id_siswa" name="id_siswa" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswas as $s)
                    <option value="{{ $s->id_siswa }}">
                        {{ $s->nama }} (NISN: {{ $s->NISN }} - Kelas {{ $s->kelas->nama_kelas ?? '-' }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="tanggal">Tanggal <span class="req">*</span></label>
                <input type="date" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="alasan">Alasan / Gejala Sakit <span class="req">*</span></label>
                <textarea id="alasan" name="alasan" rows="3" class="form-control" required placeholder="Contoh: Demam tinggi di UKS / Izin pulang sakit"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="lampiran_foto">Upload Bukti Foto (Surat/Foto Sakit)</label>
                <input type="file" id="lampiran_foto" name="lampiran_foto" accept="image/*" class="form-control">
            </div>

            <button type="submit" class="btn btn-amber btn-lg mt-16">
                CATAT SISWA SAKIT
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Pencatatan Anak Sakit</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($riwayatSakit->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Alasan / Gejala</th>
                        <th>Bukti Foto</th>
                        <th>Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatSakit as $r)
                    <tr>
                        <td class="fw-bold">{{ $r->tanggal }}</td>
                        <td class="text-muted">{{ $r->siswa->NISN ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $r->siswa->nama ?? '-' }}</td>
                        <td><span class="badge badge-navy">{{ $r->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                        <td>{{ $r->alasan }}</td>
                        <td>
                            @if($r->lampiran_foto)
                            <a href="{{ asset('storage/' . $r->lampiran_foto) }}" target="_blank" class="btn btn-secondary btn-sm">Lihat Foto</a>
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $r->pengaju->nama ?? 'Piket' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada riwayat pencatatan siswa sakit.</div>
        </div>
        @endif
    </div>
</div>
@endsection
