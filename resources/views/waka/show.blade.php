@extends('layouts.app')

@section('title', 'Persetujuan Dispensasi — Waka')
@section('page-title', 'Halaman Persetujuan Waka')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Persetujuan Dispensasi / Izin — Waka {{ $isSdm ? 'SDM' : 'Kesiswaan' }}</h1>
        <p class="page-subtitle">Pemeriksaan detail pengajuan, bukti lampiran & eksekusi keputusan persetujuan</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('waka.dashboard') }}" class="btn btn-secondary">&larr; Kembali ke Dashboard</a>
        <a href="{{ route('waka.persetujuan.index') }}" class="btn btn-secondary">Daftar Pengajuan</a>
    </div>
</div>

<div class="grid-2 mb-24">
    <!-- DETAIL SISWA / GURU PEMOHON -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Data Pemohon & Pengajuan
            </h3>
        </div>
        <div class="card-body" style="padding:0;">
            @php
                $st = strtolower($pengajuan->status);
                $badgeCls = match($st) {
                    'verified', 'disetujui_satpam', 'completed' => 'badge-success',
                    'disetujui_waka', 'pending_satpam' => 'badge-info',
                    'pending_waka', 'pending_piket' => 'badge-warning',
                    default => 'badge-danger'
                };
            @endphp
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>Status Dispen</th>
                        <td>
                            <span class="badge {{ $badgeCls }}" style="font-size:12.5px; padding:5px 12px;">
                                {{ strtoupper(str_replace('_', ' ', $pengajuan->status)) }}
                            </span>
                        </td>
                    </tr>
                    @if($pengajuan->siswa)
                    <tr>
                        <th>Nama Siswa</th>
                        <td class="fw-bold text-navy" style="font-size:15px;">{{ $pengajuan->siswa->nama }}</td>
                    </tr>
                    <tr>
                        <th>NIS & Kelas</th>
                        <td>NIS: <strong>{{ $pengajuan->siswa->nis }}</strong> | Kelas: <span class="badge badge-navy">{{ $pengajuan->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <th>Jurusan</th>
                        <td>{{ $pengajuan->siswa->kelas->jurusan->nama_jurusan ?? '-' }}</td>
                    </tr>
                    @elseif($pengajuan->guru)
                    <tr>
                        <th>Nama Guru</th>
                        <td class="fw-bold text-navy" style="font-size:15px;">{{ $pengajuan->guru->nama }}</td>
                    </tr>
                    <tr>
                        <th>NIP / Bidang</th>
                        <td>NIP: {{ $pengajuan->guru->nip ?? '-' }} | Bidang: {{ $pengajuan->guru->bidang_studi ?? '-' }}</td>
                    </tr>
                    @else
                    <tr>
                        <th>Pemohon</th>
                        <td class="fw-bold text-navy">{{ $pengajuan->pengaju->nama ?? '-' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Kategori & Jenis</th>
                        <td>
                            <span class="badge badge-purple">{{ strtoupper(str_replace('_', ' ', $pengajuan->kategori)) }}</span>
                            @if($pengajuan->jenis_izin)
                                <span class="badge badge-gray">{{ $pengajuan->jenis_izin }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Dispen</th>
                        <td class="fw-bold">{{ $pengajuan->tanggal }}</td>
                    </tr>
                    <tr>
                        <th>Jam Keluar</th>
                        <td>
                            <strong>{{ $pengajuan->jam_mulai ?? 'Seharian' }}</strong>
                            @if($pengajuan->perkiraan_kembali)
                                &mdash; Perkiraan Kembali: <strong>{{ $pengajuan->perkiraan_kembali }}</strong>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Alasan Lengkap</th>
                        <td>{{ $pengajuan->alasan }}</td>
                    </tr>
                    @if($pengajuan->keterangan)
                    <tr>
                        <th>Keterangan Piket</th>
                        <td>{{ $pengajuan->keterangan }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Pengaju (Akun)</th>
                        <td>{{ $pengajuan->pengaju->nama ?? '-' }} (Role: {{ strtoupper($pengajuan->pengaju->role ?? '-') }})</td>
                    </tr>
                    <tr>
                        <th>Waktu Diajukan</th>
                        <td class="text-muted">{{ $pengajuan->created_at ? $pengajuan->created_at->format('d M Y, H:i') : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FORM KEPUTUSAN WAKA & LAMPIRAN BUKTI -->
    <div>
        {{-- FORM EKSEKUSI KEPUTUSAN (JIKA BELUM DIPUTUSKAN / PENDING WAKA) --}}
        @if($pengajuan->status === 'pending_waka')
        <div class="card mb-24" style="border: 2px solid var(--gold-accent);">
            <div class="card-header" style="background: var(--gold-light);">
                <h3 class="card-title" style="color: var(--gold-hover);">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    Form Keputusan Persetujuan Waka
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('waka.persetujuan.proses', $pengajuan->id_pengajuan) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="catatan_waka">Catatan / Arahan Waka (Opsional jika setuju, wajib alasan jika tolak)</label>
                        <textarea id="catatan_waka" name="catatan" class="form-control" rows="3" placeholder="Contoh: Disetujui, harap segera kembali sebelum jam KBM berikutnya selesai..."></textarea>
                    </div>

                    <div class="d-flex gap-12 mt-16">
                        <button type="submit" name="keputusan" value="setujui" class="btn btn-success btn-lg" style="flex:1;">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            SETUJUI DISPEN
                        </button>
                        <button type="submit" name="keputusan" value="tolak" class="btn btn-danger btn-lg" onclick="return confirm('Yakin ingin menolak pengajuan dispensasi ini?')" style="flex:1;">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            TOLAK DISPEN
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        {{-- KEPUTUSAN SUDAH DIAMBIL --}}
        <div class="card mb-24" style="border: 2px solid {{ $pengajuan->isDisetujuiWaka() ? 'var(--badge-success-text)' : 'var(--badge-danger-text)' }};">
            <div class="card-header" style="background: {{ $pengajuan->isDisetujuiWaka() ? 'var(--badge-success-bg)' : 'var(--badge-danger-bg)' }};">
                <h3 class="card-title" style="color: {{ $pengajuan->isDisetujuiWaka() ? 'var(--badge-success-text)' : 'var(--badge-danger-text)' }};">
                    Keputusan Telah Diterbitkan
                </h3>
            </div>
            <div class="card-body">
                <p class="mb-8">Status Keputusan: <strong>{{ strtoupper(str_replace('_', ' ', $pengajuan->status)) }}</strong></p>
                <p class="mb-8">Waka Approver: <strong>{{ $pengajuan->wakaApprover->nama ?? 'Waka' }}</strong></p>
                <p class="mb-8">Waktu Keputusan: <strong>{{ $pengajuan->tgl_waka ?? '-' }}</strong></p>
                <p>Catatan Waka: <strong>{{ $pengajuan->catatan_waka ?? '-' }}</strong></p>
            </div>
        </div>
        @endif

        <!-- LAMPIRAN FOTO BUKTI -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    Lampiran Bukti / Surat Tugas
                </h3>
            </div>
            <div class="card-body text-center">
                @if($pengajuan->lampiran_foto)
                    <a href="{{ asset('storage/' . $pengajuan->lampiran_foto) }}" target="_blank" class="btn btn-secondary btn-sm mb-12">Buka Ukuran Penuh</a><br>
                    <img src="{{ asset('storage/' . $pengajuan->lampiran_foto) }}" alt="Bukti Foto" style="max-width:100%; max-height:220px; border-radius:var(--radius-sm); border:1px solid var(--border);">
                @else
                    <div class="empty-state">
                        <div class="empty-state-text">Tidak Ada Lampiran Bukti Foto</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- RIWAYAT AUDIT TRAIL -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Riwayat Log Status & Pemeriksaan
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($pengajuan->logs && $pengajuan->logs->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Waktu</th>
                        <th>Pelaku</th>
                        <th>Role</th>
                        <th>Status Sebelum</th>
                        <th>Status Sesudah</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuan->logs as $log)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="text-muted">{{ $log->created_at ? $log->created_at->format('d M Y H:i:s') : '-' }}</td>
                        <td class="fw-bold text-navy">{{ $log->user->nama ?? '-' }}</td>
                        <td><span class="badge badge-navy">{{ strtoupper($log->role ?? '-') }}</span></td>
                        <td>{{ $log->status_sebelum ? strtoupper(str_replace('_', ' ', $log->status_sebelum)) : '-' }}</td>
                        <td>
                            <span class="badge {{ str_contains($log->status_sesudah, 'tolak') ? 'badge-danger' : (str_contains($log->status_sesudah, 'setuju') || in_array($log->status_sesudah, ['verified','completed']) ? 'badge-success' : 'badge-warning') }}">
                                {{ strtoupper(str_replace('_', ' ', $log->status_sesudah)) }}
                            </span>
                        </td>
                        <td>{{ $log->catatan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada riwayat tercatat.</div>
        </div>
        @endif
    </div>
</div>
@endsection
