@extends('layouts.app')

@section('title', 'Detail Pengajuan Izin / Dispen — Jurnal Sekolah')
@section('page-title', 'Detail Pengajuan Izin')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ in_array($pengajuan->kategori, ['sakit', 'acara_keluarga', 'izin_masuk', 'izin_keluar']) ? 'Detail Pengajuan Izin Siswa' : 'Detail Pengajuan Dispensasi / Izin' }}</h1>
        <p class="page-subtitle">Informasi pengajuan, verifikasi identitas, dan riwayat status</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

@php
    $st = strtolower($pengajuan->status);
    $badgeCls = match($st) {
        'verified', 'disetujui_satpam', 'completed', 'disetujui', 'selesai' => 'badge-success',
        'disetujui_waka', 'menunggu_satpam', 'pending_satpam' => 'badge-info',
        'pending_waka', 'menunggu_waka', 'pending_piket' => 'badge-warning',
        default => 'badge-danger'
    };
    $statusLabelShow = match($st) {
        'completed', 'disetujui' => ($pengajuan->pengaju && $pengajuan->pengaju->isOrtu() ? 'DISETUJUI (ORANG TUA)' : 'DISETUJUI'),
        'verified' => 'TERVERIFIKASI',
        'disetujui_satpam' => 'DISETUJUI SATPAM',
        'disetujui_waka' => 'DISETUJUI WAKA',
        'pending_waka' => 'MENUNGGU PERSETUJUAN WAKA',
        'pending_piket' => 'MENUNGGU VERIFIKASI PIKET',
        'pending_satpam' => 'MENUNGGU PEMERIKSAAN SATPAM',
        default => strtoupper(str_replace('_', ' ', $pengajuan->status))
    };
@endphp

@if(($pengajuan->pengaju && $pengajuan->pengaju->isOrtu()) && in_array($st, ['completed', 'disetujui', 'verified']))
<div class="alert alert-success mb-16" style="background:#ecfdf5; border-color:#6ee7b7; color:#065f46;">
    <div style="display:flex; align-items:center; gap:8px;">
        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px; height:18px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span><strong>Izin Telah Disetujui Otomatis:</strong> Pengajuan izin ini diajukan langsung oleh Orang Tua siswa sehingga otomatis disahkan dan langsung dicatat ke data presensi kelas anak.</span>
    </div>
</div>
@endif

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
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>Status Saat Ini</th>
                        <td>
                            <span class="badge {{ $badgeCls }}" style="font-size:12px; padding:5px 12px;">
                                {{ $statusLabelShow }}
                            </span>
                        </td>
                    </tr>
                    @php
                        $katLabelShow = \App\Helpers\DispenHelper::kategoriLabel($pengajuan->kategori);
                        $katBadgeCls = \App\Helpers\DispenHelper::badgeColor($pengajuan->kategori);
                    @endphp
                    <tr>
                        <th>Kategori</th>
                        <td><span class="badge {{ $katBadgeCls }}">{{ strtoupper($katLabelShow) }}</span></td>
                    </tr>
                    @if($pengajuan->siswa)
                    <tr>
                        <th>Nama Siswa</th>
                        <td class="fw-bold text-navy">{{ $pengajuan->siswa->nama }}</td>
                    </tr>
                    <tr>
                        <th>NISN & Kelas</th>
                        <td>NISN: {{ $pengajuan->siswa->nisn ?? $pengajuan->siswa->NISN ?? '-' }} | Kelas: {{ $pengajuan->siswa->kelas->nama_kelas ?? '-' }} ({{ $pengajuan->siswa->kelas->jurusan->nama_jurusan ?? '-' }})</td>
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
                        <th>{{ in_array($pengajuan->kategori, ['sakit', 'acara_keluarga', 'izin_masuk', 'izin_keluar']) ? 'Keperluan / Jenis Izin' : 'Jenis Dispen / Keperluan' }}</th>
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

        @if(in_array($pengajuan->kategori, ['dispensasi','dispen_keluar','dispen_masuk','dispen_lomba','izin_guru','izin_keluar','izin_masuk']))
        <!-- WHATSAPP NOTIFICATION TRIGGER BOX -->
        @php
            $isGuruDispen = ($pengajuan->kategori === 'izin_guru');

            // --- Resolve Waka HP (4-tier fallback, handles empty string no_hp) ---
            $wakaNoHp = null;
            // Tier 1: Waka yang dituju di pengajuan ini
            if (!empty($pengajuan->wakaTujuan?->no_hp)) {
                $wakaNoHp = $pengajuan->wakaTujuan->no_hp;
            }
            // Tier 2: Waka bertugas pada tanggal pengajuan (JadwalWaka)
            if (!$wakaNoHp) {
                $jadwalWaka = \App\Models\JadwalWaka::wakaBertugasPada($pengajuan->tanggal ?? $pengajuan->created_at?->toDateString());
                if ($jadwalWaka && $jadwalWaka->waka && !empty($jadwalWaka->waka->no_hp)) {
                    $wakaNoHp = $jadwalWaka->waka->no_hp;
                }
            }
            // Tier 3: Cari waka mana saja yang punya no_hp tidak kosong
            if (!$wakaNoHp) {
                $wakaUser = \App\Models\User::whereIn('role', ['waka_kesiswaan', 'waka_sdm'])
                    ->where('no_hp', '!=', '')->whereNotNull('no_hp')->first();
                if ($wakaUser) $wakaNoHp = $wakaUser->no_hp;
            }
            // Tier 4: Fallback ke Pengaturan table
            if (!$wakaNoHp) {
                $wakaNoHp = \App\Models\Pengaturan::getVal('wa_waka_kesiswaan')
                    ?? \App\Models\Pengaturan::getVal('wa_waka_sdm')
                    ?? '085707300240';
            }

            // --- Resolve Satpam HP ---
            $satpamUser = \App\Models\User::where('role', 'satpam')
                ->where('no_hp', '!=', '')->whereNotNull('no_hp')->first();
            $satpamNoHp = $satpamUser->no_hp
                ?? \App\Models\Pengaturan::getVal('wa_satpam')
                ?? '081359472399';

            // --- Resolve Kepala Sekolah HP ---
            $kepalaUser = \App\Models\User::where('role', 'kepala_sekolah')
                ->where('no_hp', '!=', '')->whereNotNull('no_hp')->first();
            $kepalaNoHp = $kepalaUser->no_hp
                ?? \App\Models\Pengaturan::getVal('wa_kepala_sekolah')
                ?? '085707300240';

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
                        <a href="{{ $waLinkWaka }}" target="_blank" class="btn btn-primary btn-sm">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            Kirim WA ke Waka ({{ $wakaNoHp }})
                        </a>
                        @if($isGuruDispen && in_array($pengajuan->status, ['pending_kepala', 'disetujui_kepala', 'completed']))
                        <a href="{{ $waLinkKepala }}" target="_blank" class="btn btn-primary btn-sm">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            Kirim WA ke Kepala Sekolah ({{ $kepalaNoHp }})
                        </a>
                        @elseif(!$isGuruDispen && $pengajuan->isDisetujuiWaka())
                        <a href="{{ $waLinkSatpam }}" target="_blank" class="btn btn-primary btn-sm">
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
        @endif
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
     FORM KEPUTUSAN PIKET (Hanya Piket / Admin saat pending_piket)
     ============================================================ --}}
@if((Auth::user()->isPiket() || Auth::user()->isAdmin()) && $pengajuan->status === 'pending_piket')
<div class="card mb-24" style="border: 2px solid #d97706;">
    <div class="card-header" style="background: #fffbeb;">
        <h3 class="card-title" style="color: #d97706;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            Keputusan Guru Piket
        </h3>
    </div>
    <div class="card-body">
        <p class="mb-16" style="font-size:13px; color:#78350f;">Pengajuan izin siswa ini menunggu verifikasi dari Guru Piket. Setelah disetujui, pengajuan akan diteruskan ke <strong>Waka yang bertugas hari ini</strong> untuk persetujuan akhir.</p>
        <form action="{{ route('pengajuan.approve.piket', $pengajuan->id_pengajuan) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="catatan_piket">Catatan Guru Piket (Opsional)</label>
                <textarea id="catatan_piket" name="catatan" class="form-control" rows="3" placeholder="Masukkan catatan atau alasan penolakan..."></textarea>
            </div>
            <div class="d-flex gap-12 mt-16">
                <button type="submit" name="keputusan" value="setujui" class="btn btn-success btn-lg">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    SETUJUI & TERUSKAN KE WAKA
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
     FORM KEPUTUSAN WAKA (Hanya Waka / Admin saat pending_waka)
     ============================================================ --}}
@if((Auth::user()->isWaka() || Auth::user()->isAdmin()) && $pengajuan->status === 'pending_waka')
@php
    // Cari Waka yang berwenang: berdasarkan JadwalWaka pada tanggal pengajuan
    $tanggalPengajuanWaka = $pengajuan->tanggal ?? $pengajuan->created_at?->toDateString() ?? now()->toDateString();
    $jadwalWakaBerhak     = \App\Models\JadwalWaka::wakaBertugasPada($tanggalPengajuanWaka);
    $wakaYangBerhak       = $jadwalWakaBerhak?->waka;

    // Fallback ke id_waka_tujuan jika tidak ada jadwal
    if (!$wakaYangBerhak && $pengajuan->id_waka_tujuan) {
        $wakaYangBerhak = \App\Models\User::find($pengajuan->id_waka_tujuan);
    }

    $isWakaYangBerhak = Auth::user()->isAdmin()
        || !$wakaYangBerhak
        || (int) $wakaYangBerhak->id_user === (int) Auth::user()->id_user;
@endphp
<div class="card card-amber mb-24">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            Keputusan Persetujuan Waka Piket
        </h3>
    </div>
    <div class="card-body">
        {{-- Info Waka yang berwenang --}}
        @if($wakaYangBerhak)
        <div style="background:{{ $isWakaYangBerhak ? '#ecfdf5' : '#fefce8' }}; border:1px solid {{ $isWakaYangBerhak ? '#6ee7b7' : '#fde047' }}; border-radius:8px; padding:10px 14px; margin-bottom:16px; font-size:13px; color:{{ $isWakaYangBerhak ? '#065f46' : '#854d0e' }}; display:flex; align-items:center; gap:8px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;flex-shrink:0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            <span>
                <strong>Waka Piket Berwenang ({{ \Carbon\Carbon::parse($tanggalPengajuanWaka)->isoFormat('dddd, D MMM Y') }}):</strong>
                {{ $wakaYangBerhak->nama }} <span style="font-weight:400;">({{ strtoupper(str_replace('_',' ',$wakaYangBerhak->role)) }})</span>
                @if(!$isWakaYangBerhak)
                    &mdash; <em>Anda tidak berwenang menyetujui pengajuan ini. Silakan teruskan ke Waka yang bertugas.</em>
                @endif
            </span>
        </div>
        @endif

        @if($isWakaYangBerhak)
        <form action="{{ route('pengajuan.approve.waka', $pengajuan->id_pengajuan) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="catatan_waka">Catatan / Alasan Keputusan Waka</label>
                <textarea id="catatan_waka" name="catatan" class="form-control" rows="3" placeholder="Masukkan catatan persetujuan atau alasan penolakan..."></textarea>
            </div>
            <div class="d-flex gap-12 mt-16">
                <button type="submit" name="keputusan" value="setujui" class="btn btn-success btn-lg">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    SETUJUI PENGAJUAN
                </button>
                <button type="submit" name="keputusan" value="tolak" class="btn btn-danger btn-lg" onclick="return confirm('Yakin ingin menolak pengajuan ini?')">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    TOLAK PENGAJUAN
                </button>
            </div>
        </form>
        @else
        <div style="text-align:center; padding:16px 0; color:#92400e;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:32px;height:32px;margin:0 auto 8px;display:block;opacity:0.5;"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
            Persetujuan ini hanya bisa dilakukan oleh <strong>{{ $wakaYangBerhak?->nama ?? 'Waka Piket' }}</strong> yang bertugas pada tanggal tersebut.
        </div>
        @endif
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
