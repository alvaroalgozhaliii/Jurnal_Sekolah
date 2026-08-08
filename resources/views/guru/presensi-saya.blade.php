@extends('layouts.app')

@section('content')
<h2>Presensi Saya (Guru)</h2>

<div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
    <h3>Presensi Hari Ini ({{ date('Y-m-d') }})</h3>
    
    @if(!$presensiHariIni)
        <p>Anda belum melakukan presensi masuk hari ini.</p>
        <form action="{{ route('guru.presensi-masuk') }}" method="POST">
            @csrf
            <label>Keterangan (Opsional):</label><br>
            <input type="text" name="keterangan" placeholder="Contoh: Hadir Tepat Waktu" style="padding: 5px; width: 300px;"><br><br>
            <button type="submit" style="background-color: green; color: white; padding: 8px 15px;">PRESENSI MASUK</button>
        </form>
    @else
        <p>Waktu Masuk: <strong>{{ $presensiHariIni->jam_masuk }}</strong></p>
        @if(!$presensiHariIni->jam_keluar)
            <form action="{{ route('guru.presensi-keluar') }}" method="POST">
                @csrf
                <button type="submit" style="background-color: red; color: white; padding: 8px 15px;">PRESENSI KELUAR</button>
            </form>
        @else
            <p>Waktu Keluar: <strong>{{ $presensiHariIni->jam_keluar }}</strong></p>
            <p style="color: green; font-weight: bold;">Anda telah menyelesaikan presensi masuk dan keluar hari ini.</p>
        @endif
    @endif
</div>

<h3>Riwayat Presensi Guru</h3>
@if($riwayatPresensi->count() > 0)
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayatPresensi as $index => $rp)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $rp->tanggal }}</td>
                    <td>{{ $rp->jam_masuk }}</td>
                    <td>{{ $rp->jam_keluar ?? 'Belum keluar' }}</td>
                    <td>{{ $rp->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>Belum ada riwayat presensi.</p>
@endif
@endsection
