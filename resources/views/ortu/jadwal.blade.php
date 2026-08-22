@extends('layouts.app')

@section('title', 'Jadwal Anak — Jurnal Sekolah')
@section('page-title', 'Jadwal Anak')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jadwal Pelajaran Anak</h1>
        <p class="page-subtitle">Jadwal Pelajaran Mingguan Kelas Siswa</p>
    </div>
</div>

@if($anakList->count() > 1)
<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('ortu.jadwal-anak') }}" method="GET" class="d-flex align-center gap-12">
            <label class="form-label" style="margin:0; white-space:nowrap;">Pilih Anak:</label>
            <select name="id_siswa" onchange="this.form.submit()" class="form-control" style="max-width:350px;">
                @foreach($anakList as $a)
                <option value="{{ $a->id_siswa }}" {{ ($selectedSiswa && $selectedSiswa->id_siswa == $a->id_siswa) ? 'selected' : '' }}>
                    {{ $a->nama }} (NIS: {{ $a->nis }})
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>
@endif

@if($selectedSiswa)
<div class="alert alert-info mb-24">
    <div>Anak: <strong>{{ $selectedSiswa->nama }}</strong> | Kelas: <strong>{{ $selectedSiswa->kelas->nama_kelas ?? '-' }}</strong></div>
</div>

@if($jadwal->count() > 0)
    <div class="grid-2">
    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
        @if(isset($jadwal[$hari]))
        <div class="card mb-16">
            <div class="card-header">
                <h3 class="card-title">{{ $hari }}</h3>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrapper" style="border:none; border-radius:0;">
                    <table class="table">
                        <thead><tr><th class="no-col">Jam</th><th>Waktu</th><th>Mapel</th><th>Guru</th><th>Ruang</th></tr></thead>
                        <tbody>
                        @foreach($jadwal[$hari] as $j)
                        <tr>
                            <td class="no-col fw-bold">{{ $j->jam_ke }}</td>
                            <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
                            <td class="fw-bold text-navy">{{ $j->mapel }}</td>
                            <td>{{ $j->guru->nama ?? '-' }}</td>
                            <td>{{ $j->ruang ?? '-' }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @endforeach
    </div>
@else
    <div class="empty-state">
        <div class="empty-state-text">Belum ada jadwal pelajaran untuk kelas ini.</div>
    </div>
@endif

@endif
@endsection
