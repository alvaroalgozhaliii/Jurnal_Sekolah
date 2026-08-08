@extends('layouts.app')

@section('content')
<h2>Presensi Piket</h2>

<form action="{{ route('piket.presensi') }}" method="GET" style="margin-bottom: 20px;">
    <label for="tanggal">Pilih Tanggal:</label>
    <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()">
    <button type="submit">Filter Tanggal</button>
</form>

<h3>Status Kehadiran Guru Berdasarkan Jadwal ({{ $tanggal }})</h3>
@if($jadwalHariIni->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Jam Ke</th>
                <th>Waktu</th>
                <th>Kelas</th>
                <th>Mata Pelajaran</th>
                <th>Guru Jadwal</th>
                <th>Presensi Masuk Self</th>
                <th>Status Kehadiran Piket</th>
                <th>Aksi Catat Piket</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwalHariIni as $j)
                @php
                    $piketRec = $absensiPiketList[$j->id_jadwal] ?? null;
                    $selfPresensi = $j->guru?->id_user ? ($presensiGuruMasuk[$j->guru->id_user] ?? null) : null;
                @endphp
                <tr>
                    <td>{{ $j->jam_ke }}</td>
                    <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
                    <td>{{ $j->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $j->mapel }}</td>
                    <td>{{ $j->guru->nama ?? 'Belum ditentukan' }}</td>
                    <td>
                        @if($selfPresensi)
                            <span style="color: green;">Hadir ({{ $selfPresensi->jam_masuk }})</span>
                        @else
                            <span style="color: red;">Belum Masuk</span>
                        @endif
                    </td>
                    <td>
                        @if($piketRec)
                            <strong>{{ strtoupper($piketRec->status_guru) }}</strong>
                            @if($piketRec->pengganti)
                                (Pengganti: {{ $piketRec->pengganti }})
                            @endif
                        @else
                            <span style="color: gray;">Belum dicatat</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('piket.presensi-guru.store') }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="id_jadwal" value="{{ $j->id_jadwal }}">
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            
                            <select name="status_guru" required>
                                <option value="hadir" {{ ($piketRec?->status_guru == 'hadir') ? 'selected' : '' }}>Hadir</option>
                                <option value="tidak_hadir" {{ ($piketRec?->status_guru == 'tidak_hadir') ? 'selected' : '' }}>Tidak Hadir / Kosong</option>
                                <option value="terlambat" {{ ($piketRec?->status_guru == 'terlambat') ? 'selected' : '' }}>Terlambat</option>
                                <option value="digantikan" {{ ($piketRec?->status_guru == 'digantikan') ? 'selected' : '' }}>Digantikan</option>
                            </select>

                            <input type="text" name="pengganti" placeholder="Nama Guru Pengganti" value="{{ $piketRec?->pengganti }}" style="width: 130px;">
                            <button type="submit">Simpan</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>Tidak ada jadwal untuk tanggal {{ $tanggal }}.</p>
@endif
@endsection
