@extends('layouts.app')

@section('title', 'Persetujuan Final Dispen Guru — Kepala Sekolah')
@section('page-title', 'Persetujuan Final Kepala Sekolah')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Persetujuan Final Dispensasi Guru</h1>
        <p class="page-subtitle">Verifikasi Dokumen & Pengambilan Keputusan Resmi Kepala Sekolah</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('kepala.persetujuan.index') }}" class="btn btn-secondary">&larr; Kembali ke Daftar</a>
    </div>
</div>

<div class="grid-3" style="grid-template-columns: 2fr 1fr; align-items: start;">
    
    <!-- KOLOM KIRI: DETAIL LENGKAP PENGAJUAN -->
    <div>
        <div class="card mb-24">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    Informasi Dispensasi / Izin Guru
                </h3>
                @php
                    $status = strtolower($pengajuan->status);
                    $statusBadge = match($status) {
                        'pending_kepala' => ['class' => 'badge-amber', 'label' => 'MENUNGGU KEPUTUSAN KEPSEK'],
                        'disetujui_kepala', 'completed' => ['class' => 'badge-success', 'label' => 'DISETUJUI KEPALA SEKOLAH'],
                        'ditolak_kepala' => ['class' => 'badge-danger', 'label' => 'DITOLAK KEPALA SEKOLAH'],
                        default => ['class' => 'badge-navy', 'label' => strtoupper(str_replace('_', ' ', $pengajuan->status))]
                    };
                @endphp
                <span class="badge {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
            </div>
            <div class="card-body">
                <table class="table" style="border:none;">
                    <tbody>
                        <tr>
                            <td style="width:180px; font-weight:600; color:var(--clr-navy);">Nama Guru</td>
                            <td class="fw-bold text-navy" style="font-size:16px;">
                                {{ $pengajuan->guru?->nama ?? $pengajuan->pengaju?->nama ?? 'Guru' }}
                            </td>
                        </tr>
                        @if($pengajuan->guru?->nip)
                        <tr>
                            <td style="font-weight:600; color:var(--clr-navy);">NIP</td>
                            <td>{{ $pengajuan->guru->nip }}</td>
                        </tr>
                        @endif
                        @if($pengajuan->guru?->bidang_studi)
                        <tr>
                            <td style="font-weight:600; color:var(--clr-navy);">Mata Pelajaran / Bidang</td>
                            <td>{{ $pengajuan->guru->bidang_studi }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="font-weight:600; color:var(--clr-navy);">Tanggal Dispen</td>
                            <td><strong>{{ date('d F Y', strtotime($pengajuan->tanggal)) }}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--clr-navy);">Waktu Meninggalkan Tugas</td>
                            <td>
                                <strong>{{ $pengajuan->jam_mulai ?? '-' }}</strong>
                                @if($pengajuan->perkiraan_kembali)
                                    <span class="text-muted">s/d <strong>{{ $pengajuan->perkiraan_kembali }}</strong> (Kembali ke Sekolah)</span>
                                @else
                                    <span class="text-muted">(Sampai Jam Mengajar Selesai)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--clr-navy);">Keperluan / Jenis Izin</td>
                            <td><span class="badge badge-navy">{{ $pengajuan->jenis_izin ?? 'Dispen Guru' }}</span></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--clr-navy);">Alasan Lengkap</td>
                            <td style="line-height:1.6; white-space: pre-line;">{{ $pengajuan->alasan }}</td>
                        </tr>
                        @if($pengajuan->keterangan)
                        <tr>
                            <td style="font-weight:600; color:var(--clr-navy);">Keterangan Tambahan</td>
                            <td class="text-muted">{{ $pengajuan->keterangan }}</td>
                        </tr>
                        @endif
                        @if($pengajuan->lampiran_foto)
                        <tr>
                            <td style="font-weight:600; color:var(--clr-navy);">Lampiran / Surat Tugas</td>
                            <td>
                                <a href="{{ asset('storage/' . $pengajuan->lampiran_foto) }}" target="_blank" class="btn btn-secondary btn-sm">
                                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Lihat Lampiran Foto / Surat
                                </a>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PERSETUJUAN DARI WAKA SDM -->
        <div class="card mb-24">
            <div class="card-header" style="background:#f0fdf4; border-bottom:1px solid #dcfce7;">
                <h3 class="card-title text-green">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Persetujuan Waka SDM (Telah Diverifikasi)
                </h3>
            </div>
            <div class="card-body">
                <table class="table" style="border:none;">
                    <tbody>
                        <tr>
                            <td style="width:180px; font-weight:600;">Pejabat Waka SDM</td>
                            <td><strong>{{ $pengajuan->wakaApprover?->nama ?? 'Waka SDM' }}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600;">Waktu Disetujui Waka</td>
                            <td>{{ $pengajuan->tgl_waka ? date('d F Y - H:i', strtotime($pengajuan->tgl_waka)) . ' WIB' : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:600;">Catatan / Rekomendasi Waka</td>
                            <td style="font-style:italic;">"{{ $pengajuan->catatan_waka ?? 'Disetujui Waka SDM' }}"</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIWAYAT LOG AUDIT -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Audit Trail Riwayat Proses
                </h3>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrapper" style="border:none; border-radius:0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Aktor / Role</th>
                                <th>Perubahan Status</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuan->logs as $log)
                            <tr>
                                <td style="font-size:12px; white-space:nowrap;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $log->user?->nama ?? 'Sistem' }}</strong>
                                    <div class="text-muted" style="font-size:11px;">{{ strtoupper($log->role_actor) }}</div>
                                </td>
                                <td><span class="badge badge-navy" style="font-size:11px;">{{ strtoupper(str_replace('_', ' ', $log->status_sesudah)) }}</span></td>
                                <td style="font-size:12px;">{{ $log->keterangan }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted" style="padding:16px;">Belum ada catatan log.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: FORM KEPUTUSAN KEPALA SEKOLAH -->
    <div>
        @if($pengajuan->status === 'pending_kepala' || Auth::user()->isAdmin())
        <div class="card mb-24" style="border-top:4px solid var(--clr-primary);">
            <div class="card-header">
                <h3 class="card-title text-navy">
                    <svg class="svg-icon text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    Keputusan Kepala Sekolah
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('kepala.persetujuan.proses', $pengajuan->id_pengajuan) }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-16">
                        <label class="form-label" for="catatan">Catatan / Arahan Kepala Sekolah (Opsional):</label>
                        <textarea id="catatan" name="catatan" rows="3" class="form-control" placeholder="Contoh: Disetujui, harap tugas pengganti disiapkan untuk siswa.">{{ old('catatan') }}</textarea>
                    </div>

                    <div class="d-flex flex-column gap-12">
                        <button type="submit" name="keputusan" value="setujui" class="btn btn-success btn-lg" style="width:100%; justify-content:center;" onclick="return confirm('Apakah Anda yakin menyetujui dispensasi guru ini?')">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            SETUJUI DISPEN GURU
                        </button>

                        <button type="submit" name="keputusan" value="tolak" class="btn btn-danger btn-lg" style="width:100%; justify-content:center;" onclick="return confirm('Apakah Anda yakin MENOLAK dispensasi guru ini?')">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            TOLAK DISPEN
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <!-- STATUS KEPUTUSAN KEPSEK SAAT INI -->
        <div class="card mb-24">
            <div class="card-header">
                <h3 class="card-title">Status Keputusan</h3>
            </div>
            <div class="card-body">
                <div style="padding:12px; background:var(--clr-bg-subtle); border-radius:6px; margin-bottom:12px;">
                    <div class="text-muted" style="font-size:12px;">Keputusan Oleh:</div>
                    <strong>{{ $pengajuan->kepalaApprover?->nama ?? 'Kepala Sekolah' }}</strong>
                    <div class="text-muted" style="font-size:11px; margin-top:4px;">{{ $pengajuan->tgl_kepala ? date('d M Y H:i', strtotime($pengajuan->tgl_kepala)) . ' WIB' : '-' }}</div>
                </div>
                <div style="padding:12px; background:#f8fafc; border-radius:6px;">
                    <div class="text-muted" style="font-size:12px;">Catatan Kepala Sekolah:</div>
                    <div style="font-weight:600; margin-top:4px;">{{ $pengajuan->catatan_kepala ?? '-' }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- HUBUNGI VIA WHATSAPP (DIRECT WA) -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="svg-icon text-green" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    Aksi Cepat WhatsApp
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted" style="font-size:12px; margin-bottom:12px;">
                    Buka WhatsApp untuk berkomunikasi langsung terkait pengajuan ini.
                </p>
                <div class="d-flex flex-column gap-8">
                    @php
                        $nomorGuru = $pengajuan->guru?->no_telp ?? $pengajuan->pengaju?->no_hp ?? '085707300240';
                        $cleanGuru = App\Services\WhatsAppService::formatNomor($nomorGuru);
                    @endphp
                    <a href="https://api.whatsapp.com/send?phone={{ $cleanGuru }}" target="_blank" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                        💬 Chat WhatsApp Guru ({{ $nomorGuru }})
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
