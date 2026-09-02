@extends('layouts.app')

@section('content')
<h2>Piket: Absen Siswa</h2>

<div style="background-color: #f9f9f9; padding: 15px; border: 1px solid #ccc; margin-bottom: 20px;">
    <form action="{{ route('piket.absen-siswa') }}" method="GET">
        <label style="font-weight: bold; font-size: 15px;">1. Pilih Kelas * :</label>
        <select name="id_kelas" class="form-control select-search" required style="min-width: 250px;" placeholder="Ketik / Pilih Kelas..." onchange="this.form.submit()">
            <option value="">Pilih Kelas</option>
            @foreach($kelasList as $k)
                <option value="{{ $k->id_kelas }}" {{ $idKelas == $k->id_kelas ? 'selected' : '' }}>
                    {{ $k->nama_kelas }} ({{ $k->tingkat }})
                </option>
            @endforeach
        </select>
        <noscript><button type="submit">Pilih</button></noscript>
    </form>
</div>

@if($kelasSelected)
    <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
        <h3>Daftar Siswa Kelas {{ $kelasSelected->nama_kelas }} (Tanggal: {{ $todayDate }})</h3>
        
        <form action="{{ route('piket.absen-siswa') }}" method="GET" style="display:inline;">
            <input type="hidden" name="id_kelas" value="{{ $idKelas }}">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama atau NISN siswa..." style="padding: 5px; width: 220px;">
            <button type="submit">Cari Siswa</button>
            @if(!empty($search))
                <a href="{{ route('piket.absen-siswa', ['id_kelas' => $idKelas]) }}">Reset Search</a>
            @endif
        </form>
    </div>

    @if($siswaList->count() > 0)
        <form action="{{ route('piket.absen-siswa.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_kelas" value="{{ $idKelas }}">

            <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 15px;">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Kelamin</th>
                        <th>Status Presensi (Hari Ini)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswaList as $s)
                    @php
                        $statusSaatIni = $existingAbsensi[$s->id_siswa]->status ?? 'hadir';
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $s->NISN }}</td>
                        <td><strong>{{ $s->nama }}</strong></td>
                        <td>{{ $s->jenis_kelamin ?? '-' }}</td>
                        <td>
                            @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa', 'terlambat' => 'Terlambat'] as $val => $lbl)
                                <label style="margin-right: 12px; cursor: pointer;">
                                    <input type="radio" name="absensi[{{ $s->id_siswa }}]" value="{{ $val }}" {{ $statusSaatIni === $val ? 'checked' : '' }}>
                                    {{ $lbl }}
                                </label>
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" style="padding: 10px 25px; font-weight: bold; background-color: #0066cc; color: white; border: none; cursor: pointer;">
                SIMPAN ABSENSI KELAS {{ $kelasSelected->nama_kelas }}
            </button>
        </form>
    @else
        <p>Tidak ada siswa ditemukan di kelas ini.</p>
    @endif
@else
    <p style="color: #666; font-style: italic;">Silakan pilih kelas terlebih dahulu untuk menampilkan daftar siswa dan menginput absensi.</p>
@endif

@endsection
