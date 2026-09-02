@extends('layouts.app')

@section('title', Auth::user()->isOrtu() ? 'Daftar Pengajuan Izin Anak — Jurnal Sekolah' : 'Daftar Pengajuan Dispen & Izin — Jurnal Sekolah')
@section('page-title', Auth::user()->isOrtu() ? 'Pengajuan Izin Anak' : 'Izin & Dispensasi')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ Auth::user()->isOrtu() ? 'Daftar Pengajuan Izin Anak' : 'Daftar Pengajuan Dispensasi & Izin' }}</h1>
        <p class="page-subtitle">{{ Auth::user()->isOrtu() ? 'Monitoring status pengajuan izin anak (sakit, acara keluarga, dll) & persetujuan sekolah' : 'Monitoring status pengajuan dispen, persetujuan Waka & verifikasi Satpam' }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('pengajuan.create') }}" class="btn btn-primary">{{ Auth::user()->isOrtu() ? '+ Buat Pengajuan Izin' : '+ Buat Pengajuan Baru' }}</a>
    </div>
</div>

{{-- Rekap Excel Bulanan Card --}}
<div class="card mb-16" style="background:#f0fdf4; border:1px solid #bbf7d0;">
    <div class="card-body" style="padding:12px 16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <strong style="color:#166534; font-size:14px;">&#x1F4CA; Rekap Excel Persetujuan Izin per Bulan:</strong>
                <span style="color:#15803d; font-size:12px;">(Izin Siswa, Izin Guru, dan Ringkasan)</span>
            </div>
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <select id="bulanIzin" class="form-control" style="width:auto; padding:4px 10px; font-size:13px; height:auto;">
                    @php $curMonth = date('n'); @endphp
                    @foreach(['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $mVal => $mName)
                        <option value="{{ $mVal }}" {{ $curMonth == $mVal ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
                <input type="number" id="tahunIzin" value="{{ date('Y') }}" min="2020" max="2099" class="form-control" style="width:80px; padding:4px 8px; font-size:13px; height:auto;">
                <a id="btnDownloadIzin" href="{{ route('rekap.izin-excel', ['bulan' => date('n'), 'tahun' => date('Y')]) }}" class="btn btn-sm" style="background:#16a34a; color:#fff; font-weight:600;">
                    &#x2B07; Unduh Rekap Excel (.XLSX)
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const b = document.getElementById('bulanIzin');
    const t = document.getElementById('tahunIzin');
    const btn = document.getElementById('btnDownloadIzin');
    function updateUrl() {
        btn.href = "{{ route('rekap.izin-excel') }}?bulan=" + b.value + "&tahun=" + t.value;
    }
    b.addEventListener('change', updateUrl);
    t.addEventListener('input', updateUrl);
});
</script>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($pengajuanList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Kategori</th>
                        <th>Subjek / Pemohon</th>
                        <th>Tanggal</th>
                        <th>Waka Tujuan</th>
                        <th>Waktu Keluar</th>
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
                        <td>{{ $p->wakaTujuan->nama ?? 'Ditentukan berdasarkan alur' }}</td>
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
                                <a href="{{ asset('storage/' . $p->lampiran_foto) }}" target="_blank" class="btn btn-secondary btn-sm">Lihat Foto</a>
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
            <div class="empty-state-text">{{ Auth::user()->isOrtu() ? 'Belum ada pengajuan izin anak.' : 'Belum ada pengajuan dispensasi atau izin.' }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
