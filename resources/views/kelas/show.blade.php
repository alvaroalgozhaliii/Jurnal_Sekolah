@extends('layouts.app')

@section('title', 'Detail Kelas — Jurnal Sekolah')
@section('page-title', 'Detail Kelas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Kelas: {{ $kelas->nama_kelas }}</h1>
        <p class="page-subtitle">Informasi Kelas, Daftar Siswa, Mata Pelajaran & Jadwal Pelajaran</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('kelas.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        <a href="{{ route('kelas.edit', $kelas->id_kelas) }}" class="btn btn-primary">Edit Kelas</a>
    </div>
</div>

{{-- BAGIAN 1: INFORMASI KELAS + DAFTAR SISWA --}}
<div class="grid-2 mb-24">
    {{-- Informasi Kelas --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Kelas</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="info-table">
                <tbody>
                    <tr><th>Nama Kelas</th><td class="fw-bold text-navy">{{ $kelas->nama_kelas }}</td></tr>
                    <tr><th>Tingkat</th><td><span class="badge badge-navy">{{ $kelas->tingkat }}</span></td></tr>
                    <tr><th>Jurusan</th><td>{{ $kelas->jurusan->nama_jurusan ?? '-' }}</td></tr>
                    <tr><th>Wali Kelas</th><td>{{ $kelas->wali_kelas ?? '-' }}</td></tr>
                    <tr><th>Jumlah Siswa</th><td>{{ $kelas->siswa->count() }} Siswa</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Daftar Siswa --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Siswa ({{ $kelas->siswa->count() }})</h3>
        </div>
        <div class="card-body" style="padding:0;">
            @if($kelas->siswa->count() > 0)
            <div class="table-wrapper" style="border:none; border-radius:0; max-height:260px; overflow-y:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="no-col">No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>JK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelas->siswa as $s)
                        <tr>
                            <td class="no-col">{{ $loop->iteration }}</td>
                            <td class="text-muted fw-bold">{{ $s->nisn }}</td>
                            <td class="fw-bold text-navy">{{ $s->nama }}</td>
                            <td>{{ $s->jenis_kelamin }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-text">Belum ada siswa di kelas ini.</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- BAGIAN 2: MATA PELAJARAN KELAS --}}
<div class="card mb-24">
    <div class="card-header d-flex justify-between align-center">
        <h3 class="card-title">Mata Pelajaran Kelas</h3>
        <button type="button" class="btn btn-primary btn-sm" onclick="toggleTambahMapel()">+ Tambah Mapel</button>
    </div>

    {{-- Form Tambah Mapel (tersembunyi awalnya) --}}
    <div id="formTambahMapel" style="display:none; padding:16px; border-bottom:1px solid #e2e8f0;">
        <form action="{{ route('kelas.attach-mapel', $kelas->id_kelas) }}" method="POST" class="d-flex gap-8" style="align-items:center; flex-wrap:wrap;">
            @csrf
            <select name="id_mapel" class="form-control select-search" style="min-width:320px;" required placeholder="Ketik / Pilih Mata Pelajaran">
                <option value="">-- Pilih Mata Pelajaran --</option>
                @foreach($availableMapel as $m)
                <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }} ({{ $m->kode_mapel ?? '-' }})</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-success btn-sm">Tambahkan</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleTambahMapel()">Batal</button>
        </form>
    </div>

    <div class="card-body" style="padding:0;">
        @if($kelas->mataPelajaran->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelas->mataPelajaran as $m)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="text-muted">{{ $m->kode_mapel ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $m->nama_mapel }}</td>
                        <td class="action-col">
                            <form action="{{ route('kelas.detach-mapel', [$kelas->id_kelas, $m->id_mapel]) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus mata pelajaran ini dari kelas?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada mata pelajaran yang ditambahkan ke kelas ini.</div>
        </div>
        @endif
    </div>
</div>

{{-- BAGIAN 3: JADWAL PELAJARAN PER HARI --}}
<div class="card">
    <div class="card-header d-flex justify-between align-center">
        <h3 class="card-title"> Jadwal Pelajaran</h3>
        <a href="{{ route('jadwal.create', ['id_kelas' => $kelas->id_kelas]) }}" class="btn btn-primary btn-sm">+ Tambah Jadwal</a>
    </div>
    <div class="card-body" style="padding:0 0 8px 0;">
        @php
        $urutan = ['Senin','Selasa','Rabu','Kamis','Jumat'];
        $jadwalGrouped = $kelas->jadwal->sortBy('jam_ke')->groupBy('hari');
        $totalJadwal = $kelas->jadwal->count();

        // Mapping istirahat senin-kamis & jumat
        $istirahatSeninKamis = [4 => 'Istirahat 1 (09:40 - 10:00)', 7 => 'Istirahat 2 (11:45 - 13:15)'];
        $istirahatJumat      = [4 => 'Istirahat 1 (09:00 - 09:30)', 8 => 'Istirahat 2 (11:20 - 13:00)'];
        @endphp

        @if($totalJadwal > 0)
            @foreach($urutan as $hari)
                @if(isset($jadwalGrouped[$hari]))
                @php
                $jadwalHari = $jadwalGrouped[$hari]->sortBy('jam_ke');
                $isJumat = ($hari === 'Jumat');
                $mapIstirahat = $isJumat ? $istirahatJumat : $istirahatSeninKamis;
                @endphp
                <div style="margin: 12px 16px;">
                    <div style="background:#1e3a8a; color:#fff; padding:8px 14px; border-radius:6px 6px 0 0; font-weight:700; font-size:13px; letter-spacing:0.5px;">
                        {{ strtoupper($hari) }}
                    </div>
                    <div class="table-wrapper" style="border-radius:0 0 6px 6px; border:1px solid #e2e8f0; margin:0;">
                        <table class="table" style="margin:0;">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th style="width:90px;">Jam Ke</th>
                                    <th style="width:160px;">Jam Pembelajaran</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th style="width:70px;">Ruang</th>
                                    <th class="action-col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalHari as $j)
                                <tr>
                                    <td class="fw-bold text-center">Jam {{ $j->jam_ke }}</td>
                                    <td class="fw-bold" style="color:#1e3a8a;">
                                        {{ \App\Services\KbmService::getLabelWaktu($j->hari, $j->jam_ke) ?: ($j->waktu_mulai . ' - ' . $j->waktu_selesai) }}
                                    </td>
                                    <td class="fw-bold text-navy">{{ $j->mapel }}</td>
                                    <td>{{ $j->guru->nama ?? '<em class="text-muted">-</em>' }}</td>
                                    <td>{{ $j->ruang ?? '-' }}</td>
                                    <td class="action-col">
                                        <a href="{{ route('jadwal.edit', $j->id_jadwal) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <form action="{{ route('jadwal.destroy', $j->id_jadwal) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus jadwal ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                {{-- Tampilkan istirahat setelah jam tertentu --}}
                                @if(isset($mapIstirahat[$j->jam_ke]))
                                <tr style="background:#fff7ed;">
                                    <td colspan="6" style="text-align:center; font-style:italic; color:#92400e; padding:6px; font-size:12px;">
                                         {{ $mapIstirahat[$j->jam_ke] }}
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endforeach
        @else
        <div class="empty-state" style="padding:32px;">
            <div class="empty-state-text">Belum ada jadwal pelajaran untuk kelas ini.</div>
            <a href="{{ route('jadwal.create', ['id_kelas' => $kelas->id_kelas]) }}" class="btn btn-primary mt-16">+ Tambah Jadwal Pertama</a>
        </div>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success mt-16">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger mt-16">{{ session('error') }}</div>
@endif
@endsection

@push('scripts')
<script>
function toggleTambahMapel() {
    const f = document.getElementById('formTambahMapel');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
</script>
@endpush