@extends('layouts.app')

@section('title', 'Verifikasi Identitas Satpam — Jurnal Sekolah')
@section('page-title', 'Pemeriksaan Gerbang Satpam')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pemeriksaan Identitas - Petugas Keamanan (Satpam)</h1>
        <p class="page-subtitle">Verifikasi fisik kartu tanda pelajar / identitas sebelum melintas pintu gerbang</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('satpam.dashboard') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="grid-2">
    <!-- DATA IDENTITAS -->
    <div class="card mb-24">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Data Identitas Pemohon
            </h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="info-table">
                <tbody>
                    @if($pengajuan->siswa)
                    <tr>
                        <th>Nama Siswa</th>
                        <td class="fw-bold text-navy" style="font-size:15px;">{{ $pengajuan->siswa->nama }}</td>
                    </tr>
                    <tr>
                        <th>NISN (Kartu Pelajar)</th>
                        <td class="fw-bold">{{ $pengajuan->siswa->NISN }}</td>
                    </tr>
                    <tr>
                        <th>Kelas & Jurusan</th>
                        <td><span class="badge badge-navy">{{ $pengajuan->siswa->kelas->nama_kelas ?? '-' }} ({{ $pengajuan->siswa->kelas->jurusan->nama_jurusan ?? '-' }})</span></td>
                    </tr>
                    @elseif($pengajuan->guru)
                    <tr>
                        <th>Nama Guru</th>
                        <td class="fw-bold text-navy" style="font-size:15px;">{{ $pengajuan->guru->nama }}</td>
                    </tr>
                    <tr>
                        <th>NIP Guru</th>
                        <td class="fw-bold">{{ $pengajuan->guru->nip ?? '-' }}</td>
                    </tr>
                    @else
                    <tr>
                        <th>Nama Pemohon</th>
                        <td class="fw-bold text-navy">{{ $pengajuan->pengaju->nama ?? '-' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Kategori Dispen</th>
                        <td><span class="badge badge-purple">{{ strtoupper(str_replace('_', ' ', $pengajuan->kategori)) }}</span></td>
                    </tr>
                    <tr>
                        <th>Tanggal & Jam</th>
                        <td>
                            <strong>{{ $pengajuan->tanggal }}</strong> | 
                            Jam Keluar: <strong>{{ $pengajuan->jam_mulai ?? '-' }}</strong>
                            @if($pengajuan->perkiraan_kembali)
                                (Perkiraan Kembali: <strong>{{ $pengajuan->perkiraan_kembali }}</strong>)
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Alasan Lengkap</th>
                        <td>{{ $pengajuan->alasan }}</td>
                    </tr>
                    <tr>
                        <th>Persetujuan Waka</th>
                        <td>
                            @if(in_array($pengajuan->status, ['menunggu_satpam', 'disetujui_waka', 'pending_satpam']))
                                <span class="badge badge-success">DISETUJUI WAKA ({{ $pengajuan->wakaApprover->nama ?? 'Waka' }})</span>
                                @if($pengajuan->catatan_waka)
                                    <div class="text-muted mt-8" style="font-size:11px;">Catatan: {{ $pengajuan->catatan_waka }}</div>
                                @endif
                            @else
                                <span class="badge badge-warning">{{ strtoupper(str_replace('_', ' ', $pengajuan->status)) }}</span>
                            @endif
                        </td>
                    </tr>
                    @if($pengajuan->lampiran_foto)
                    <tr>
                        <th>Bukti / Surat</th>
                        <td>
                            <a href="{{ asset('storage/' . $pengajuan->lampiran_foto) }}" target="_blank" class="btn btn-secondary btn-sm">Lihat Lampiran Foto</a>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- FORM KEPUTUSAN VERIFIKASI SATPAM -->
    <div class="card mb-24" style="border: 2px solid var(--navy-primary);">
        <div class="card-header" style="background: var(--badge-navy-bg);">
            <h3 class="card-title" style="color: var(--navy-primary);">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Pemeriksaan Fisik Kartu Identitas & Gerbang
            </h3>
        </div>
        <div class="card-body">
            @if(!in_array($pengajuan->status, ['menunggu_satpam', 'disetujui_waka', 'pending_satpam']))
                <div class="alert alert-warning">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <div>Pengajuan ini belum disetujui Waka. Verifikasi hanya dapat dilakukan setelah persetujuan Waka terbit.</div>
                </div>
            @else
                <form action="{{ route('satpam.verifikasi', $pengajuan->id_pengajuan) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="status_satpam">Hasil Pemeriksaan Fisik Kartu Siswa / Identitas <span class="req">*</span></label>
                        <select id="status_satpam" name="status_satpam" class="form-control" required style="font-weight:700;">
                            <option value="valid" {{ $pengajuan->status_satpam === 'valid' ? 'selected' : '' }}>VALID (Sesuai Kartu Tanda Pelajar / Identitas Guru)</option>
                            <option value="tidak_valid" {{ $pengajuan->status_satpam === 'tidak_valid' ? 'selected' : '' }}>TIDAK VALID (Identitas Tidak Cocok / Ditolak)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="catatan_satpam">Catatan Petugas Satpam (Jam Gerbang / Keterangan)</label>
                        <textarea id="catatan_satpam" name="catatan_satpam" class="form-control" rows="3" placeholder="Contoh: Siswa keluar gerbang pukul 09:15 WIB, kartu pelajar sesuai...">{{ $pengajuan->catatan_satpam }}</textarea>
                    </div>

                    <div class="d-flex gap-8 mt-16">
                        <button type="submit" class="btn btn-primary btn-lg">
                            SIMPAN HASIL VERIFIKASI GERBANG
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
