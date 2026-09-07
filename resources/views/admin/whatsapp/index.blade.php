@extends('layouts.app')

@section('title', 'Pengaturan Nomor WhatsApp — Jurnal Sekolah')
@section('page-title', 'Pengaturan Nomor WhatsApp')

@section('content')
<style>
.wa-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
.wa-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border);
}
.wa-card-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
}
.wa-card-subtitle {
    font-size: 13px;
    color: var(--text-secondary);
    margin-top: 2px;
}
.role-badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
}
.role-badge.kesiswaan { background: #dbeafe; color: #1e40af; }
.role-badge.sdm { background: #e0e7ff; color: #3730a3; }
.role-badge.kepala { background: #fef3c7; color: #92400e; }
.role-badge.satpam { background: #fee2e2; color: #991b1b; }
.role-badge.piket { background: #dcfce7; color: #166534; }

.officer-item {
    display: grid;
    grid-template-columns: 220px 1fr 200px;
    align-items: center;
    gap: 16px;
    padding: 14px 16px;
    background: var(--bg-page);
    border: 1px solid var(--border);
    border-radius: 10px;
    margin-bottom: 12px;
}
@media (max-width: 768px) {
    .officer-item { grid-template-columns: 1fr; }
}
.officer-info .oname { font-weight: 700; font-size: 14.5px; color: var(--text-primary); }
.officer-info .ometa { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }

.quick-search-box {
    display: flex;
    gap: 12px;
    margin-bottom: 18px;
}
</style>

<div class="page-header mb-24">
    <div>
        <h1 class="page-title">Pengaturan & Penggantian Nomor WhatsApp</h1>
        <p class="page-subtitle">Kelola nomor WhatsApp penerima notifikasi pengajuan (Waka, Kepala Sekolah, Satpam, Piket) & integrasi Gateway API</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success mb-24" style="padding: 14px 18px; border-radius: 10px;">
    <strong>✅ Sukses:</strong> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger mb-24" style="padding: 14px 18px; border-radius: 10px;">
    <strong>❌ Terjadi Kesalahan:</strong> {{ session('error') }}
</div>
@endif

<div class="grid" style="display: grid; grid-template-columns: 1fr; gap: 24px;">

    {{-- SECTION 1: PETUGAS PENERIMA NOTIFIKASI PENGAJUAN --}}
    <div class="wa-card">
        <div class="wa-card-header">
            <div>
                <div class="wa-card-title">
                    <span>📱 Nomor WhatsApp Penerima Notifikasi Pengajuan</span>
                </div>
                <div class="wa-card-subtitle">Nomor ini digunakan secara otomatis saat sistem mendistribusikan notifikasi pengajuan dispen/izin</div>
            </div>
            <button type="submit" form="formPejabatWa" class="btn btn-primary">💾 Simpan Semua Nomor</button>
        </div>

        <form action="{{ route('admin.whatsapp.pejabat.update') }}" method="POST" id="formPejabatWa">
            @csrf

            {{-- 1. WAKA KESISWAAN --}}
            <div style="margin-bottom: 20px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                    <span class="role-badge kesiswaan">Waka Kesiswaan</span>
                    <span style="font-size:13px; font-weight:600; color:var(--text-primary);">Verifikasi & Persetujuan Dispen Siswa</span>
                </div>
                @forelse($wakaKesiswaan as $u)
                <div class="officer-item">
                    <div class="officer-info">
                        <div class="oname">{{ $u->nama }}</div>
                        <div class="ometa">@ {{ $u->username }}</div>
                    </div>
                    <div>
                        <input type="text" name="user_wa[{{ $u->id_user }}]" value="{{ old('user_wa.'.$u->id_user, $u->no_hp) }}" class="form-control" placeholder="Contoh: 081234567890">
                    </div>
                    <div>
                        @if($u->no_hp)
                            <a href="https://wa.me/{{ \App\Services\WhatsAppService::formatNomor($u->no_hp) }}" target="_blank" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                                💬 Test Chat WA
                            </a>
                        @else
                            <span class="text-muted" style="font-size:12px;">Belum diisi</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-muted" style="padding:10px 0; font-size:13px;">Tidak ada akun dengan role Waka Kesiswaan. Silakan isi fallback nomor di bawah.</div>
                @endforelse
                <div style="background:var(--bg-page); padding:10px 14px; border-radius:8px; border:1px dashed var(--border); display:flex; align-items:center; gap:12px;">
                    <span style="font-size:12px; font-weight:600; color:var(--text-secondary);">Fallback Nomor WA Waka Kesiswaan:</span>
                    <input type="text" name="fallback_wa[waka_kesiswaan]" value="{{ old('fallback_wa.waka_kesiswaan', $fallbackWa['waka_kesiswaan'] ?? '') }}" class="form-control" style="max-width:260px;" placeholder="081359472399">
                </div>
            </div>

            {{-- 2. WAKA SDM / HUMAS --}}
            <div style="margin-bottom: 20px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                    <span class="role-badge sdm">Waka SDM / Humas</span>
                    <span style="font-size:13px; font-weight:600; color:var(--text-primary);">Verifikasi & Persetujuan Dispen Guru</span>
                </div>
                @forelse($wakaSdm as $u)
                <div class="officer-item">
                    <div class="officer-info">
                        <div class="oname">{{ $u->nama }}</div>
                        <div class="ometa">@ {{ $u->username }}</div>
                    </div>
                    <div>
                        <input type="text" name="user_wa[{{ $u->id_user }}]" value="{{ old('user_wa.'.$u->id_user, $u->no_hp) }}" class="form-control" placeholder="Contoh: 085707300240">
                    </div>
                    <div>
                        @if($u->no_hp)
                            <a href="https://wa.me/{{ \App\Services\WhatsAppService::formatNomor($u->no_hp) }}" target="_blank" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                                💬 Test Chat WA
                            </a>
                        @else
                            <span class="text-muted" style="font-size:12px;">Belum diisi</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-muted" style="padding:10px 0; font-size:13px;">Tidak ada akun dengan role Waka SDM.</div>
                @endforelse
                <div style="background:var(--bg-page); padding:10px 14px; border-radius:8px; border:1px dashed var(--border); display:flex; align-items:center; gap:12px;">
                    <span style="font-size:12px; font-weight:600; color:var(--text-secondary);">Fallback Nomor WA Waka SDM:</span>
                    <input type="text" name="fallback_wa[waka_sdm]" value="{{ old('fallback_wa.waka_sdm', $fallbackWa['waka_sdm'] ?? '') }}" class="form-control" style="max-width:260px;" placeholder="085707300240">
                </div>
            </div>

            {{-- 3. KEPALA SEKOLAH --}}
            <div style="margin-bottom: 20px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                    <span class="role-badge kepala">Kepala Sekolah</span>
                    <span style="font-size:13px; font-weight:600; color:var(--text-primary);">Persetujuan Akhir Dispen Guru</span>
                </div>
                @forelse($kepalaSekolah as $u)
                <div class="officer-item">
                    <div class="officer-info">
                        <div class="oname">{{ $u->nama }}</div>
                        <div class="ometa">@ {{ $u->username }}</div>
                    </div>
                    <div>
                        <input type="text" name="user_wa[{{ $u->id_user }}]" value="{{ old('user_wa.'.$u->id_user, $u->no_hp) }}" class="form-control" placeholder="Contoh: 081234567890">
                    </div>
                    <div>
                        @if($u->no_hp)
                            <a href="https://wa.me/{{ \App\Services\WhatsAppService::formatNomor($u->no_hp) }}" target="_blank" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                                💬 Test Chat WA
                            </a>
                        @else
                            <span class="text-muted" style="font-size:12px;">Belum diisi</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-muted" style="padding:10px 0; font-size:13px;">Tidak ada akun dengan role Kepala Sekolah.</div>
                @endforelse
                <div style="background:var(--bg-page); padding:10px 14px; border-radius:8px; border:1px dashed var(--border); display:flex; align-items:center; gap:12px;">
                    <span style="font-size:12px; font-weight:600; color:var(--text-secondary);">Fallback Nomor WA Kepala Sekolah:</span>
                    <input type="text" name="fallback_wa[kepala_sekolah]" value="{{ old('fallback_wa.kepala_sekolah', $fallbackWa['kepala_sekolah'] ?? '') }}" class="form-control" style="max-width:260px;" placeholder="081234567890">
                </div>
            </div>

            {{-- 4. SATPAM / SECURITY --}}
            <div style="margin-bottom: 20px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                    <span class="role-badge satpam">Satpam / Pos Gerbang</span>
                    <span style="font-size:13px; font-weight:600; color:var(--text-primary);">Pemberitahuan Izin Keluar/Masuk Siswa di Gerbang</span>
                </div>
                @forelse($satpams as $u)
                <div class="officer-item">
                    <div class="officer-info">
                        <div class="oname">{{ $u->nama }}</div>
                        <div class="ometa">@ {{ $u->username }}</div>
                    </div>
                    <div>
                        <input type="text" name="user_wa[{{ $u->id_user }}]" value="{{ old('user_wa.'.$u->id_user, $u->no_hp) }}" class="form-control" placeholder="Contoh: 081359472399">
                    </div>
                    <div>
                        @if($u->no_hp)
                            <a href="https://wa.me/{{ \App\Services\WhatsAppService::formatNomor($u->no_hp) }}" target="_blank" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                                💬 Test Chat WA
                            </a>
                        @else
                            <span class="text-muted" style="font-size:12px;">Belum diisi</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-muted" style="padding:10px 0; font-size:13px;">Tidak ada akun dengan role Satpam.</div>
                @endforelse
                <div style="background:var(--bg-page); padding:10px 14px; border-radius:8px; border:1px dashed var(--border); display:flex; align-items:center; gap:12px;">
                    <span style="font-size:12px; font-weight:600; color:var(--text-secondary);">Fallback Nomor WA Satpam:</span>
                    <input type="text" name="fallback_wa[satpam]" value="{{ old('fallback_wa.satpam', $fallbackWa['satpam'] ?? '') }}" class="form-control" style="max-width:260px;" placeholder="081359472399">
                </div>
            </div>

            {{-- 5. GURU PIKET --}}
            <div>
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                    <span class="role-badge piket">Guru Piket</span>
                    <span style="font-size:13px; font-weight:600; color:var(--text-primary);">Piket Harian & Keterlambatan Siswa</span>
                </div>
                @forelse($pikets as $u)
                <div class="officer-item">
                    <div class="officer-info">
                        <div class="oname">{{ $u->nama }}</div>
                        <div class="ometa">@ {{ $u->username }}</div>
                    </div>
                    <div>
                        <input type="text" name="user_wa[{{ $u->id_user }}]" value="{{ old('user_wa.'.$u->id_user, $u->no_hp) }}" class="form-control" placeholder="Contoh: 081234567890">
                    </div>
                    <div>
                        @if($u->no_hp)
                            <a href="https://wa.me/{{ \App\Services\WhatsAppService::formatNomor($u->no_hp) }}" target="_blank" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                                💬 Test Chat WA
                            </a>
                        @else
                            <span class="text-muted" style="font-size:12px;">Belum diisi</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-muted" style="padding:10px 0; font-size:13px;">Tidak ada akun dengan role Piket.</div>
                @endforelse
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding:10px 24px;">💾 Simpan Perubahan Nomor</button>
            </div>
        </form>
    </div>

    {{-- SECTION 2: SEARCH & QUICK EDIT ALL USER NUMBERS --}}
    <div class="wa-card">
        <div class="wa-card-header">
            <div>
                <div class="wa-card-title">
                    <span>🔍 Pencarian Cepat & Edit Nomor WA Akun Lainnya</span>
                </div>
                <div class="wa-card-subtitle">Cari akun Guru, Wali Kelas, Orang Tua, atau Staf untuk mengganti nomor WhatsApp mereka dengan cepat</div>
            </div>
        </div>

        <form action="{{ route('admin.whatsapp.index') }}" method="GET" class="quick-search-box">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Ketik nama, username, role, atau nomor HP...">
            <button type="submit" class="btn btn-secondary">🔍 Cari Akun</button>
            @if($search)
                <a href="{{ route('admin.whatsapp.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Nama Pengguna</th>
                        <th>Role / Jabatan</th>
                        <th>Username</th>
                        <th>Nomor WhatsApp / HP</th>
                        <th style="width:180px; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penggunaList as $idx => $u)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <strong style="color:var(--text-primary);">{{ $u->nama }}</strong>
                        </td>
                        <td>
                            <span class="role-badge kesiswaan">{{ strtoupper(str_replace('_', ' ', $u->role)) }}</span>
                        </td>
                        <td class="text-muted">@ {{ $u->username }}</td>
                        <td>
                            <form action="{{ route('admin.whatsapp.user.update', $u->id_user) }}" method="POST" style="display:flex; gap:8px;">
                                @csrf
                                <input type="text" name="no_hp" value="{{ $u->no_hp }}" class="form-control form-control-sm" placeholder="08xxxxxxxxxx" style="max-width:180px;">
                                <button type="submit" class="btn btn-secondary btn-sm" title="Simpan Nomor">💾 Simpan</button>
                            </form>
                        </td>
                        <td style="text-align:right;">
                            @if($u->no_hp)
                                <a href="https://wa.me/{{ \App\Services\WhatsAppService::formatNomor($u->no_hp) }}" target="_blank" class="btn btn-secondary btn-sm">
                                    💬 Chat WA
                                </a>
                            @else
                                <span class="text-muted" style="font-size:12px;">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding:24px;">Tidak ada pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SECTION 3 & 4: GATEWAY CONFIG & TEST SENDER --}}
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
        
        {{-- GATEWAY CONFIG --}}
        <div class="wa-card">
            <div class="wa-card-header">
                <div>
                    <div class="wa-card-title">🌐 Pengaturan WhatsApp Gateway API</div>
                    <div class="wa-card-subtitle">Fonnte / Gateway Multi-Device Provider API</div>
                </div>
            </div>

            <form action="{{ route('admin.whatsapp.gateway.update') }}" method="POST">
                @csrf
                <div class="form-group mb-16">
                    <label class="form-label">API Gateway URL</label>
                    <input type="text" name="api_url" value="{{ old('api_url', $gateway['api_url']) }}" class="form-control" placeholder="https://api.fonnte.com/send">
                    <div class="form-hint">Default Fonnte: <code>https://api.fonnte.com/send</code></div>
                </div>

                <div class="form-group mb-16">
                    <label class="form-label">API Key / Token Fonnte</label>
                    <input type="password" name="api_key" value="{{ old('api_key', $gateway['api_key']) }}" class="form-control" placeholder="Masukkan Token API Fonnte Anda">
                </div>

                <div class="form-group mb-20">
                    <label class="form-label">Nomor Pengirim (Sender Device)</label>
                    <input type="text" name="sender" value="{{ old('sender', $gateway['sender']) }}" class="form-control" placeholder="081234567890">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">💾 Simpan Konfigurasi Gateway</button>
            </form>
        </div>

        {{-- TEST KIRIM WA --}}
        <div class="wa-card">
            <div class="wa-card-header">
                <div>
                    <div class="wa-card-title">🚀 Uji Coba Pengiriman WA</div>
                    <div class="wa-card-subtitle">Tes apakah Gateway WhatsApp berhasil mengirim pesan secara otomatis</div>
                </div>
            </div>

            <form action="{{ route('admin.whatsapp.test') }}" method="POST">
                @csrf
                <div class="form-group mb-16">
                    <label class="form-label">Nomor WhatsApp Tujuan <span style="color:red">*</span></label>
                    <input type="text" name="no_tujuan" value="{{ old('no_tujuan', auth()->user()->no_hp ?? '081234567890') }}" class="form-control" placeholder="Contoh: 081234567890" required>
                </div>

                <div class="form-group mb-20">
                    <label class="form-label">Pesan Uji Coba <span style="color:red">*</span></label>
                    <textarea name="pesan_tes" class="form-control" rows="3" required>Halo! Ini adalah tes pengiriman pesan otomatis dari Sistem Jurnal Sekolah.</textarea>
                </div>

                <button type="submit" class="btn btn-success" style="width:100%; justify-content:center;">🚀 Kirim Pesan Tes Sekarang</button>
            </form>
        </div>

    </div>

</div>
@endsection
