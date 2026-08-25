@extends('layouts.app')

@section('title', 'Detail Pengajuan Dispen / Izin — Jurnal Sekolah')
@section('page-title', 'Detail Pengajuan Dispen')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Pengajuan Dispensasi / Izin</h1>
        <p class="page-subtitle">Informasi pengajuan, verifikasi identitas, dan riwayat status</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="grid-2 mb-24">
    <!-- INFORMASI PENGAJUAN -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                Informasi Pengajuan
            </h3>
        </div>
        <div class="card-body" style="padding:0;">
            @php
                $st = strtolower($pengajuan->status);
                $badgeCls = match($st) {
                    'verified', 'disetujui_satpam', 'completed', 'selesai' => 'badge-success',
                    'disetujui_waka', 'menunggu_satpam', 'pending_satpam' => 'badge-info',
                    'pending_waka', 'menunggu_waka', 'pending_piket' => 'badge-warning',
                    default => 'badge-danger'
                };
            @endphp
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>Status Saat Ini</th>
                        <td>
                            <span class="badge {{ $badgeCls }}" style="font-size:12px; padding:5px 12px;">
                                {{ strtoupper(str_replace('_', ' ', $pengajuan->status)) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td><span class="badge badge-navy">{{ strtoupper(str_replace('_', ' ', $pengajuan->kategori)) }}</span></td>
                    </tr>
                    @if($pengajuan->siswa)
                    <tr>
                        <th>Nama Siswa</th>
                        <td class="fw-bold text-navy">{{ $pengajuan->siswa->nama }}</td>
                    </tr>
                    <tr>
                        <th>NIS & Kelas</th>
                        <td>NIS: {{ $pengajuan->siswa->nis }} | Kelas: {{ $pengajuan->siswa->kelas->nama_kelas ?? '-' }} ({{ $pengajuan->siswa->kelas->jurusan->nama_jurusan ?? '-' }})</td>
                    </tr>
                    @elseif($pengajuan->guru)
                    <tr>
                        <th>Nama Guru</th>
                        <td class="fw-bold text-navy">{{ $pengajuan->guru->nama }} (NIP: {{ $pengajuan->guru->nip ?? '-' }})</td>
                    </tr>
                    @else
                    <tr>
                        <th>Pemohon</th>
                        <td class="fw-bold text-navy">{{ $pengajuan->pengaju->nama ?? '-' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Pengaju (Akun)</th>
                        <td>{{ $pengajuan->pengaju->nama ?? '-' }} <span class="badge badge-gray">{{ strtoupper($pengajuan->pengaju->role ?? '-') }}</span></td>
                    </tr>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <td class="fw-bold">{{ $pengajuan->tanggal }}</td>
                    </tr>
                    @if($pengajuan->wakaTujuan)
                    <tr>
                        <th>Waka Tujuan</th>
                        <td class="fw-bold text-navy">{{ $pengajuan->wakaTujuan->nama }} ({{ strtoupper(str_replace('_', ' ', $pengajuan->wakaTujuan->role)) }})</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Jam Keluar / Mulai</th>
                        <td>{{ $pengajuan->jam_mulai ? $pengajuan->jam_mulai : 'Seharian' }}</td>
                    </tr>
                    @if($pengajuan->perkiraan_kembali)
                    <tr>
                        <th>Perkiraan Jam Kembali</th>
                        <td>{{ $pengajuan->perkiraan_kembali }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Jenis Dispen / Keperluan</th>
                        <td>{{ $pengajuan->jenis_izin ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alasan Lengkap</th>
                        <td>{{ $pengajuan->alasan }}</td>
                    </tr>
                    @if($pengajuan->keterangan)
                    <tr>
                        <th>Keterangan Tambahan</th>
                        <td>{{ $pengajuan->keterangan }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Waktu Dibuat</th>
                        <td class="text-muted">{{ $pengajuan->created_at ? $pengajuan->created_at->format('d M Y, H:i') : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- LAMPIRAN & NOTIFIKASI WHATSAPP -->
    <div>
        <div class="card mb-24">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    Lampiran Bukti / Surat
                </h3>
            </div>
            <div class="card-body text-center">
                @if($pengajuan->lampiran_foto)
                    <a href="{{ asset('storage/' . $pengajuan->lampiran_foto) }}" target="_blank" class="btn btn-secondary btn-sm mb-12">Buka Ukuran Penuh</a><br>
                    <img src="{{ asset('storage/' . $pengajuan->lampiran_foto) }}" alt="Bukti Foto" style="max-width:100%; max-height:220px; border-radius:var(--radius-sm); border:1px solid var(--border);">
                @else
                    <div class="empty-state">
                        <div class="empty-state-text">Tidak Ada Lampiran Foto</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- WHATSAPP NOTIFICATION TRIGGER BOX -->
        @php
            $isGuruDispen = ($pengajuan->kategori === 'izin_guru');
            $wakaUser = \App\Models\User::whereIn('role', ['waka_kesiswaan', 'waka_sdm'])->whereNotNull('no_hp')->first();
            $satpamUser = \App\Models\User::where('role', 'satpam')->whereNotNull('no_hp')->first();
            $kepalaUser = \App\Models\User::where('role', 'kepala_sekolah')->whereNotNull('no_hp')->first();

            $wakaNoHp = $wakaUser->no_hp ?? '085707300240';
            $satpamNoHp = $satpamUser->no_hp ?? '081359472399';
            $kepalaNoHp = $kepalaUser->no_hp ?? '085707300240';

            $waLinkWaka = \App\Services\WhatsAppService::getDirectWaLinkWaka($pengajuan, $wakaNoHp);
            $waLinkSatpam = \App\Services\WhatsAppService::getDirectWaLinkSatpam($pengajuan, $satpamNoHp);
            $waLinkKepala = \App\Services\WhatsAppService::getDirectWaLinkKepala($pengajuan, $kepalaNoHp);
        @endphp

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    Pemberitahuan WhatsApp
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-16">
                    <strong class="d-block mb-4" style="font-size:12.5px;">1. Buka Chat WhatsApp Langsung (1-Klik):</strong>
                    <p class="text-muted mb-8" style="font-size:11.5px;">Buka WhatsApp dengan format pesan dan link approval resmi yang sudah terisi otomatis:</p>
                    <div class="d-flex gap-8 flex-wrap">
                        <a href="{{ $waLinkWaka }}" target="_blank" class="btn btn-success btn-sm">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            Kirim WA ke Waka ({{ $wakaNoHp }})
                        </a>
                        @if($isGuruDispen && in_array($pengajuan->status, ['pending_kepala', 'disetujui_kepala', 'completed']))
                        <a href="{{ $waLinkKepala }}" target="_blank" class="btn btn-success btn-sm">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            Kirim WA ke Kepala Sekolah ({{ $kepalaNoHp }})
                        </a>
                        @elseif(!$isGuruDispen && $pengajuan->isDisetujuiWaka())
                        <a href="{{ $waLinkSatpam }}" target="_blank" class="btn btn-success btn-sm">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            Kirim WA ke Satpam ({{ $satpamNoHp }})
                        </a>
                        @endif
                    </div>
                </div>

                <hr style="border:0; border-top:1px solid var(--border); margin:12px 0;">

                <div>
                    <strong class="d-block mb-4" style="font-size:12.5px;">2. Kirim Ulang via Gateway Server (Otomatis):</strong>
                    <p class="text-muted mb-8" style="font-size:11.5px;">Kirim otomatis via API Gateway Fonnte:</p>
                    <div class="d-flex gap-8 flex-wrap">
                        <form action="{{ route('pengajuan.resend-wa', $pengajuan->id_pengajuan) }}" method="POST">
                            @csrf
                            <input type="hidden" name="target" value="waka">
                            <button type="submit" class="btn btn-secondary btn-sm">Trigger API WA Waka</button>
                        </form>
                        @if($isGuruDispen && in_array($pengajuan->status, ['pending_kepala', 'disetujui_kepala', 'completed']))
                        <form action="{{ route('pengajuan.resend-wa', $pengajuan->id_pengajuan) }}" method="POST">
                            @csrf
                            <input type="hidden" name="target" value="kepala">
                            <button type="submit" class="btn btn-secondary btn-sm">Trigger API WA Kepsek</button>
                        </form>
                        @elseif(!$isGuruDispen && $pengajuan->isDisetujuiWaka())
                        <form action="{{ route('pengajuan.resend-wa', $pengajuan->id_pengajuan) }}" method="POST">
                            @csrf
                            <input type="hidden" name="target" value="satpam">
                            <button type="submit" class="btn btn-secondary btn-sm">Trigger API WA Satpam</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RIWAYAT AUDIT TRAIL / LOG STATUS -->
<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Riwayat Perjalanan Dispen (Audit Log)
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
                        <th>Catatan / Keterangan</th>
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
            <div class="empty-state-text">Belum ada riwayat perubahan status tercatat.</div>
        </div>
        @endif
    </div>
</div>

{{-- ============================================================
     FORM KEPUTUSAN WAKA (Hanya Waka / Admin saat pending_waka)
     ============================================================ --}}
@if((Auth::user()->isWaka() || Auth::user()->isAdmin()) && $pengajuan->status === 'pending_waka')
<div class="card" style="border: 2px solid var(--gold-accent);">
    <div class="card-header" style="background: var(--gold-light);">
        <h3 class="card-title" style="color: var(--gold-hover);">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            Keputusan Persetujuan Waka
        </h3>
    </div>
    <div class="card-body">
        <form action="{{ route('pengajuan.approve.waka', $pengajuan->id_pengajuan) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="catatan_waka">Catatan / Alasan Keputusan Waka</label>
                <textarea id="catatan_waka" name="catatan" class="form-control" rows="3" placeholder="Masukkan catatan persetujuan atau alasan penolakan..."></textarea>
            </div>
            <div class="d-flex gap-12 mt-16">
                <button type="submit" name="keputusan" value="setujui" class="btn btn-success btn-lg">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    SETUJUI PENGAJUAN (KIRIM NOTIFIKASI SATPAM)
                </button>
                <button type="submit" name="keputusan" value="tolak" class="btn btn-danger btn-lg" onclick="return confirm('Yakin ingin menolak pengajuan ini?')">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    TOLAK PENGAJUAN
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ============================================================
     TOMBOL NAVIGASI CEPAT SATPAM (Jika Satpam login)
     ============================================================ --}}
@if((Auth::user()->isSatpam() || Auth::user()->isAdmin()) && $pengajuan->status === 'disetujui_waka')
<div class="card" style="border: 2px solid var(--navy-primary);">
    <div class="card-header" style="background: var(--badge-navy-bg);">
        <h3 class="card-title" style="color: var(--navy-primary);">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            Verifikasi Identitas di Gerbang Sekolah
        </h3>
    </div>
    <div class="card-body">
        <p class="mb-16">Pengajuan ini telah disetujui Waka. Silakan lakukan pemeriksaan fisik Kartu Tanda Pelajar pada halaman verifikasi Satpam:</p>
        <a href="{{ route('satpam.show', $pengajuan->id_pengajuan) }}" class="btn btn-primary btn-lg">
            BUKA FORM VERIFIKASI IDENTITAS SATPAM &rarr;
        </a>
    </div>
</div>
@endif

@endsection
