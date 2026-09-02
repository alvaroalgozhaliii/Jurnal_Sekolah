@extends('layouts.app')

@section('title', 'Jurnal & Pengajuan Izin — Jurnal Sekolah')
@section('page-title', 'Jurnal & Pengajuan Izin')

@section('content')
<div class="page-header mb-16">
    <div>
        <h1 class="page-title">Jurnal Mengajar & Pengajuan Izin</h1>
        <p class="page-subtitle">Pusat Pengelolaan Jurnal Harian KBM & Pengajuan Izin Guru / Siswa</p>
    </div>
</div>

<!-- TAB NAVIGATION & ACTION BUTTONS -->
<div class="d-flex align-center justify-between flex-wrap gap-12 mb-20">
    <div class="d-flex gap-8">
        <a href="{{ route('jurnal-harian.index', array_merge(request()->except('tab'), ['tab' => 'jurnal'])) }}" 
           class="btn {{ ($activeTab !== 'pengajuan') ? 'btn-primary' : 'btn-secondary' }}">
            📖 Jurnal Mengajar KBM ({{ $jurnal_harian->count() }})
        </a>
        <a href="{{ route('jurnal-harian.index', array_merge(request()->except('tab'), ['tab' => 'pengajuan'])) }}" 
           class="btn {{ ($activeTab === 'pengajuan') ? 'btn-primary' : 'btn-secondary' }}">
            📝 Pengajuan Izin & Dispensasi ({{ $pengajuanList->count() }})
        </a>
    </div>
    <div class="d-flex gap-8 flex-wrap">
        <a href="{{ route('jurnal-harian.create') }}" class="btn btn-primary">+ Isi Jurnal Mengajar</a>
        <a href="{{ route('pengajuan.create') }}" class="btn btn-amber">+ Buat Pengajuan Izin</a>
        @if(Auth::user()->isGuru())
            <a href="{{ route('jurnal-harian.trash') }}" class="btn btn-secondary">Trash Jurnal</a>
        @endif
    </div>
</div>

@if($activeTab !== 'pengajuan')
    <!-- FILTER BAR JURNAL -->
    <div class="card mb-24">
        <div class="card-body">
            <form action="{{ route('jurnal-harian.index') }}" method="GET" class="filter-bar">
                <input type="hidden" name="tab" value="jurnal">
                <div class="form-group" style="margin:0;">
                    <label for="tanggal" class="form-label" style="margin-bottom:2px;">Tanggal Jurnal</label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" class="form-control">
                </div>

                @if(Auth::user()->isAdmin() || Auth::user()->isPiket() || Auth::user()->isWaka())
                <div class="form-group" style="margin:0;">
                    <label for="id_guru" class="form-label" style="margin-bottom:2px;">Filter Guru</label>
                    <select id="id_guru" name="id_guru" class="form-control">
                        <option value="">-- Semua Guru --</option>
                        @foreach($guruList as $g)
                        <option value="{{ $g->id_guru }}" {{ $id_guru == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="form-group" style="margin:0;">
                    <label for="id_kelas" class="form-label" style="margin-bottom:2px;">Filter Kelas</label>
                    <select id="id_kelas" name="id_kelas" class="form-control">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList as $k)
                        <option value="{{ $k->id_kelas }}" {{ $id_kelas == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="align-self:flex-end;">Filter Data</button>
            </form>
        </div>
    </div>

    <!-- TABLE JURNAL HARIAN -->
    <div class="card">
        <div class="card-body" style="padding:0;">
            @if($jurnal_harian->count() > 0)
            <div class="table-wrapper" style="border:none; border-radius:0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="no-col">No</th>
                            <th>Tanggal</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru Pengajar</th>
                            <th>Materi Pelajaran</th>
                            <th>Status</th>
                            <th class="action-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jurnal_harian as $item)
                        @php
                            $st = strtolower($item->status_keterlaksanaan);
                            $badgeCls = match($st) {
                                'terlaksana' => 'badge-success',
                                'tidak_terlaksana', 'kosong' => 'badge-danger',
                                'pengganti' => 'badge-amber',
                                default => 'badge-gray'
                            };
                        @endphp
                        <tr>
                            <td class="no-col">{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $item->tanggal }}</td>
                            <td><span class="badge badge-navy">{{ $item->jadwal->kelas->nama_kelas ?? '-' }}</span></td>
                            <td class="fw-bold text-navy">{{ $item->mapel }}</td>
                            <td>{{ $item->guru->nama ?? '-' }}</td>
                            <td>{{ Str::limit($item->materi, 30) }}</td>
                            <td><span class="badge {{ $badgeCls }}">{{ strtoupper(str_replace('_', ' ', $item->status_keterlaksanaan)) }}</span></td>
                            <td class="action-col">
                                <a href="{{ route('jurnal-harian.show', $item->id_jurnal) }}" class="btn btn-secondary btn-sm">Detail</a>
                                @if(Auth::user()->isGuru() && $item->id_guru == Auth::user()->guru?->id_guru)
                                <a href="{{ route('jurnal-harian.edit', $item->id_jurnal) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('jurnal-harian.destroy', $item->id_jurnal) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus jurnal harian ini?')">Hapus</button>
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
                <div class="empty-state-text">Tidak ada data jurnal harian untuk filter ini.</div>
            </div>
            @endif
        </div>
    </div>

@else

    <!-- TABLE PENGAJUAN IZIN & DISPENSASI -->
    <div class="card">
        <div class="card-header d-flex justify-between align-center">
            <h3 class="card-title">Daftar Pengajuan Izin & Dispensasi</h3>
            <a href="{{ route('pengajuan.create') }}" class="btn btn-primary btn-sm">+ Buat Pengajuan Baru</a>
        </div>
        <div class="card-body" style="padding:0;">
            @if($pengajuanList->count() > 0)
            <div class="table-wrapper" style="border:none; border-radius:0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="no-col">No</th>
                            <th>Kategori</th>
                            <th>Pemohon / Subjek</th>
                            <th>Tanggal</th>
                            <th>Waktu / Jam</th>
                            <th>Alasan</th>
                            <th>Status Saat Ini</th>
                            <th>Lampiran</th>
                            <th class="action-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengajuanList as $index => $p)
                        @php
                            $st = strtolower($p->status);
                            $badgeCls = match($st) {
                                'verified', 'disetujui_satpam', 'completed', 'selesai' => 'badge-success',
                                'disetujui_waka', 'menunggu_satpam', 'pending_satpam' => 'badge-info',
                                'pending_waka', 'menunggu_waka', 'pending_piket' => 'badge-warning',
                                default => 'badge-danger'
                            };
                            $katLabel = match($p->kategori) {
                                'sakit' => 'IZIN SAKIT',
                                'izin' => 'IZIN',
                                'izin_guru' => 'DISPENSASI GURU',
                                default => strtoupper(str_replace('_', ' ', $p->kategori))
                            };
                        @endphp
                        <tr>
                            <td class="no-col">{{ $index + 1 }}</td>
                            <td><span class="badge badge-navy">{{ $katLabel }}</span></td>
                            <td class="fw-bold text-navy">
                                {{ $p->siswa ? $p->siswa->nama . ' (Kelas ' . ($p->siswa->kelas->nama_kelas ?? '-') . ')' : ($p->guru ? $p->guru->nama . ' (Guru)' : ($p->pengaju->nama ?? '-')) }}
                            </td>
                            <td>{{ $p->tanggal }}</td>
                            <td>
                                {{ $p->jam_mulai ? $p->jam_mulai : 'Seharian' }}
                                @if($p->perkiraan_kembali)
                                    <span class="text-muted" style="font-size:11px;">(Kembali: {{ $p->perkiraan_kembali }})</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($p->alasan, 35) }}</td>
                            <td><span class="badge {{ $badgeCls }}">{{ strtoupper(str_replace('_', ' ', $p->status)) }}</span></td>
                            <td>
                                @if($p->lampiran_foto)
                                    <a href="{{ asset('storage/' . $p->lampiran_foto) }}" target="_blank" class="btn btn-secondary btn-sm">Lihat Bukti</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="action-col">
                                <a href="{{ route('pengajuan.show', $p->id_pengajuan) }}" class="btn btn-primary btn-sm">Detail & Status</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-text">Belum ada pengajuan izin atau dispensasi terdaftar.</div>
            </div>
            @endif
        </div>
    </div>
@endif

@endsection

