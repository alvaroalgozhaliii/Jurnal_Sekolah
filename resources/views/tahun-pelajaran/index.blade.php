@extends('layouts.app')

@section('title', 'Tahun Pelajaran — Jurnal Sekolah')
@section('page-title', 'Tahun Pelajaran')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Master Tahun Pelajaran</h1>
        <p class="page-subtitle">Kelola Tahun Pelajaran & Semester Aktif Sekolah</p>
    </div>
</div>

<div class="grid-2">
    <!-- TAMPILAN ATAU FORM SEKARANG -->
    <div class="card mb-24">
        <div class="card-header">
            <h3 class="card-title">Atur Tahun Pelajaran Aktif</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('tahun-pelajaran.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="tahun_pelajaran">Tahun Pelajaran <span class="req">*</span></label>
                    <input type="text" id="tahun_pelajaran" name="tahun_pelajaran" placeholder="Contoh: 2025/2026" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="semester">Semester <span class="req">*</span></label>
                    <select id="semester" name="semester" class="form-control" required>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-lg mt-16">
                    SIMPAN & SET AKTIF
                </button>
            </form>
        </div>
    </div>

    <!-- RIWAYAT TAHUN PELAJARAN -->
    <div class="card mb-24">
        <div class="card-header">
            <h3 class="card-title">Daftar Tahun Pelajaran</h3>
        </div>
        <div class="card-body" style="padding:0;">
            @if($tahunPelajaran->count() > 0)
            <div class="table-wrapper" style="border:none; border-radius:0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tahun Pelajaran</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th class="action-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tahunPelajaran as $tp)
                        <tr>
                            <td class="fw-bold text-navy">{{ $tp->tahun_pelajaran }}</td>
                            <td>{{ $tp->semester }}</td>
                            <td>
                                @if($tp->aktif)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-gray">Nonaktif</span>
                                @endif
                            </td>
                            <td class="action-col">
                                @if(!$tp->aktif)
                                <form action="{{ route('tahun-pelajaran.set-aktif', $tp->id_tp) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">Aktifkan</button>
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
                <div class="empty-state-text">Belum ada data tahun pelajaran.</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
