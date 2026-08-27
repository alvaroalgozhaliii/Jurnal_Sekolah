@extends('layouts.app')

@section('content')
<h2>Piket: Dispen Guru</h2>

<div style="background-color: #f9f9f9; padding: 15px; border: 1px solid #ccc; margin-bottom: 20px;">
    <h3>Form Pengajuan Dispen Guru</h3>
    <form action="{{ route('piket.dispen-guru.store') }}" method="POST">
        @csrf
        <table>
            <tr>
                <td><label>Tanggal Dispen (Otomatis)</label></td>
                <td>
                    <input type="text" value="{{ $todayDate }}" readonly style="background-color: #e9ecef; padding: 5px; width: 150px; font-weight: bold;">
                    <small style="color: #6c757d; margin-left: 10px;">Ditentukan otomatis dari sistem saat ini</small>
                </td>
            </tr>
            <tr>
                <td><label>Pilih Jadwal Mengajar Guru *</label></td>
                <td>
                    <select name="id_jadwal" required style="padding: 5px; width: 450px;">
                        <option value="">-- Pilih Jadwal & Guru Hari Ini --</option>
                        @foreach($jadwalHariIni as $j)
                            <option value="{{ $j->id_jadwal }}">
                                {{ $j->guru->nama ?? 'Tanpa Guru' }} - Kelas {{ $j->kelas->nama_kelas ?? '-' }} (Mapel: {{ $j->mapel }}, Jam ke-{{ $j->jam_ke }})
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Status Kehadiran *</label></td>
                <td>
                    <select name="status_guru" required style="padding: 5px; width: 200px;">
                        <option value="tidak_hadir">Tidak Hadir / Izin</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="digantikan">Digantikan</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Keperluan *</label></td>
                <td>
                    <select name="keperluan_select" id="keperluan_select" required style="padding: 5px; width: 250px;">
                        <option value="">-- Pilih Keperluan --</option>
                        @foreach($keperluanOptions as $kop)
                            <option value="{{ $kop }}">{{ $kop }}</option>
                        @endforeach
                    </select>

                    <div id="custom_keperluan_wrap" style="display: none; margin-top: 5px;">
                        <input type="text" name="keperluan_custom" placeholder="Tuliskan keperluan lainnya..." style="padding: 5px; width: 300px;">
                    </div>
                </td>
            </tr>
            <tr>
                <td><label>Guru Pengganti (Jika ada)</label></td>
                <td>
                    <input type="text" name="pengganti" placeholder="Nama Guru Pengganti" style="padding: 5px; width: 250px;">
                </td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" style="padding: 8px 20px; margin-top: 10px;">SUBMIT DISPEN GURU</button></td>
            </tr>
        </table>
    </form>
</div>

<h3>Riwayat Dispen Guru Hari Ini ({{ $todayDate }})</h3>

<form action="{{ route('piket.dispen-guru.index') }}" method="GET" style="margin-bottom: 15px;">
    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama guru atau keperluan..." style="padding: 5px; width: 250px;">
    <button type="submit">Cari</button>
    @if(!empty($search))
        <a href="{{ route('piket.dispen-guru.index') }}">Reset</a>
    @endif
</form>

<table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse; font-size: 13px;">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Guru</th>
            <th>Kelas & Mapel</th>
            <th>Status Guru</th>
            <th>Keperluan</th>
            <th>Guru Pengganti</th>
        </tr>
    </thead>
    <tbody>
        @forelse($dispenList as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->tanggal }}</td>
            <td><strong>{{ $item->jadwal->guru->nama ?? '-' }}</strong></td>
            <td>Kelas {{ $item->jadwal->kelas->nama_kelas ?? '-' }} (Mapel: {{ $item->jadwal->mapel ?? '-' }})</td>
            <td>{{ strtoupper($item->status_guru) }}</td>
            <td>{{ $item->keperluan ?? '-' }}</td>
            <td>{{ $item->pengganti ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center;">Belum ada data dispen guru yang dicatat hari ini.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<script>
    const keperluanSelect = document.getElementById('keperluan_select');
    const customWrap = document.getElementById('custom_keperluan_wrap');

    keperluanSelect.addEventListener('change', function() {
        if (this.value === 'Lainnya') {
            customWrap.style.display = 'block';
        } else {
            customWrap.style.display = 'none';
        }
    });
</script>
@endsection
