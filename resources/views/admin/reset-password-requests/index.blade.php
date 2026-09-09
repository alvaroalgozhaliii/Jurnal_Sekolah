@extends('layouts.app')

@section('title', 'Daftar Pengajuan Reset Password — Admin')
@section('page-title', 'Pengajuan Reset Password & Username')

@section('content')
<!-- TOP STAT METRIC CARDS -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <!-- Stat 1: Pending -->
    <div class="card" style="margin: 0; background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.02)); border: 1.5px solid rgba(245, 158, 11, 0.3); border-radius: 16px; padding: 18px; position: relative;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #d97706; letter-spacing: 0.5px;">MENUNGGU APPROVAL</span>
                <h2 style="margin: 6px 0 0 0; font-size: 26px; font-weight: 800; color: var(--text-primary, #1e293b);">{{ $counts['pending'] }}</h2>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #f59e0b; color: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 16px rgba(245, 158, 11, 0.35);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
        </div>
    </div>

    <!-- Stat 2: Approved -->
    <div class="card" style="margin: 0; background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.02)); border: 1.5px solid rgba(16, 185, 129, 0.3); border-radius: 16px; padding: 18px;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #059669; letter-spacing: 0.5px;">DISETUJUI (APPROVED)</span>
                <h2 style="margin: 6px 0 0 0; font-size: 26px; font-weight: 800; color: var(--text-primary, #1e293b);">{{ $counts['approved'] }}</h2>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #10b981; color: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
        </div>
    </div>

    <!-- Stat 3: Completed -->
    <div class="card" style="margin: 0; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.02)); border: 1.5px solid rgba(59, 130, 246, 0.3); border-radius: 16px; padding: 18px;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #2563eb; letter-spacing: 0.5px;">SELESAI DIRESET</span>
                <h2 style="margin: 6px 0 0 0; font-size: 26px; font-weight: 800; color: var(--text-primary, #1e293b);">{{ $counts['completed'] }}</h2>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #3b82f6; color: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 16px rgba(59, 130, 246, 0.35);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
        </div>
    </div>

    <!-- Stat 4: Total -->
    <div class="card" style="margin: 0; background: var(--bg-card, #fff); border: 1.5px solid var(--border, #cbd5e1); border-radius: 16px; padding: 18px;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text-secondary, #64748b); letter-spacing: 0.5px;">TOTAL PENGAJUAN</span>
                <h2 style="margin: 6px 0 0 0; font-size: 26px; font-weight: 800; color: var(--text-primary, #1e293b);">{{ $counts['all'] }}</h2>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 12px; background: var(--border, #94a3b8); color: #fff; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h3 style="margin: 0; font-size: 18px; font-weight: 700;">Manajemen Pengajuan Reset Password</h3>
            <p style="margin: 4px 0 0 0; font-size: 13px; color: var(--text-secondary);">Verifikasi dan beri persetujuan permohonan akun dari Orang Tua dan Guru/Staf</p>
        </div>
    </div>

    <div class="card-body">
        <!-- Status Filter Tabs -->
        <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; border-bottom: 1.5px solid var(--border, #e2e8f0); padding-bottom: 14px;">
            <a href="{{ route('admin.reset-password.index', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-light' }}" style="border-radius: 20px; font-weight: 600;">
                Semua ({{ $counts['all'] }})
            </a>
            <a href="{{ route('admin.reset-password.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-light' }}" style="border-radius: 20px; font-weight: 600; {{ $status !== 'pending' && $counts['pending'] > 0 ? 'border-color: #f59e0b; color: #d97706; background: rgba(245,158,11,0.1);' : '' }}">
                Menunggu Persetujuan ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('admin.reset-password.index', ['status' => 'approved']) }}" class="btn btn-sm {{ $status === 'approved' ? 'btn-primary' : 'btn-light' }}" style="border-radius: 20px; font-weight: 600;">
                Disetujui ({{ $counts['approved'] }})
            </a>
            <a href="{{ route('admin.reset-password.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'btn-primary' : 'btn-light' }}" style="border-radius: 20px; font-weight: 600;">
                Ditolak ({{ $counts['rejected'] }})
            </a>
            <a href="{{ route('admin.reset-password.index', ['status' => 'completed']) }}" class="btn btn-sm {{ $status === 'completed' ? 'btn-primary' : 'btn-light' }}" style="border-radius: 20px; font-weight: 600;">
                Selesai ({{ $counts['completed'] }})
            </a>
        </div>

        <div class="table-responsive">
            <table class="table" style="vertical-align: middle;">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Pengaju & Akun Terdaftar</th>
                        <th>Kategori</th>
                        <th>NISN / NIK</th>
                        <th>Waktu Pengajuan</th>
                        <th>Status</th>
                        <th style="text-align: right; width: 220px;">Aksi / Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $index => $req)
                        <tr>
                            <td>{{ $requests->firstItem() + $index }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: {{ $req->role_tipe === 'ortu' ? 'rgba(147, 51, 234, 0.15)' : 'rgba(37, 99, 235, 0.15)' }}; color: {{ $req->role_tipe === 'ortu' ? '#9333ea' : '#2563eb' }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0;">
                                        {{ strtoupper(substr($req->nama_pengaju, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong style="display: block; font-size: 14px; color: var(--text-primary, #1e293b);">{{ $req->nama_pengaju }}</strong>
                                        @if($req->user)
                                            <div style="font-size: 12px; color: var(--text-secondary, #64748b);">
                                                Username: <code style="color: #2563eb; font-weight: 700;">{{ $req->user->username }}</code>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($req->role_tipe === 'ortu')
                                    <span class="badge badge-purple" style="font-weight: 600; padding: 4px 10px; border-radius: 12px;">Ortu / Siswa</span>
                                @else
                                    <span class="badge badge-blue" style="font-weight: 600; padding: 4px 10px; border-radius: 12px;">Guru / Staf</span>
                                @endif
                            </td>
                            <td><code style="font-size: 13.5px; font-weight: 600; letter-spacing: 0.5px;">{{ $req->nisn_nik }}</code></td>
                            <td style="font-size: 13px; color: var(--text-secondary, #64748b);">
                                {{ $req->created_at ? $req->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td>
                                @if($req->status === 'pending')
                                    <span class="badge badge-amber" style="font-size: 12px; padding: 6px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; animation: pulse 1.5s infinite;"></span>
                                        Pending Approval
                                    </span>
                                @elseif($req->status === 'approved')
                                    <span class="badge badge-green" style="font-size: 12px; padding: 6px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981;"></span>
                                        Disetujui Admin
                                    </span>
                                @elseif($req->status === 'rejected')
                                    <span class="badge badge-red" style="font-size: 12px; padding: 6px 12px; border-radius: 20px;">Ditolak</span>
                                    @if($req->catatan)
                                        <div style="font-size: 11px; color: var(--text-secondary); margin-top: 3px;">{{ $req->catatan }}</div>
                                    @endif
                                @elseif($req->status === 'completed')
                                    <span class="badge badge-blue" style="font-size: 12px; padding: 6px 12px; border-radius: 20px;">Selesai Direset</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($req->status === 'pending')
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <!-- Form Approve -->
                                        <form action="{{ route('admin.reset-password.approve', $req->id_reset_request) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengajuan reset password ini? Pengaju akan otomatis bisa mereset sandi.')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" style="display: flex; align-items: center; gap: 5px; border-radius: 10px; font-weight: 600; padding: 6px 14px;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                Setujui
                                            </button>
                                        </form>

                                        <!-- Button Tolak (Open Modal) -->
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="openRejectModal({{ $req->id_reset_request }}, '{{ addslashes($req->nama_pengaju) }}')" style="border-radius: 10px; font-weight: 600; padding: 6px 14px;">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <span style="font-size: 12px; color: var(--text-secondary); italic;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 8px; color: var(--border);"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                <div style="font-size: 14px; font-weight: 600;">Belum ada pengajuan reset password untuk kategori ini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $requests->links() }}
        </div>
    </div>
</div>

<!-- Modal Reject Reason -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.55); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center;">
    <div style="background: var(--bg-card, #fff); border-radius: 18px; width: 100%; max-width: 460px; padding: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.25); border: 1px solid var(--border, #cbd5e1); animation: modalPop 0.25s ease-out;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; color: var(--text-primary, #1e293b);">Tolak Pengajuan Reset</h4>
            <button type="button" onclick="closeRejectModal()" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-secondary);">&times;</button>
        </div>

        <p id="rejectModalPengaju" style="font-size: 13.5px; color: var(--text-secondary, #64748b); margin-bottom: 16px; background: rgba(0,0,0,0.03); padding: 10px; border-radius: 10px;"></p>

        <!-- Quick Reason Chips -->
        <div style="margin-bottom: 12px;">
            <label style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); display: block; margin-bottom: 6px;">Pilih Alasan Cepat:</label>
            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                <button type="button" class="btn btn-xs btn-light" onclick="setRejectReason('NISN tidak ditemukan dalam database siswa terdaftar.')" style="font-size: 11px; border-radius: 12px;">NISN Tidak Ditemukan</button>
                <button type="button" class="btn btn-xs btn-light" onclick="setRejectReason('Data NIK / NIP tidak cocok dengan data guru & staf.')" style="font-size: 11px; border-radius: 12px;">NIK/NIP Tidak Cocok</button>
                <button type="button" class="btn btn-xs btn-light" onclick="setRejectReason('Silakan konfirmasi langsung ke bagian Kurikulum/TU.')" style="font-size: 11px; border-radius: 12px;">Konfirmasi ke TU</button>
            </div>
        </div>

        <form id="rejectForm" method="POST" action="">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Alasan Penolakan:</label>
                <textarea id="catatanTextarea" name="catatan" class="form-control" rows="3" placeholder="Tuliskan alasan penolakan..." required style="border-radius: 10px; font-size: 13.5px;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-light" onclick="closeRejectModal()" style="border-radius: 10px; font-weight: 600;">Batal</button>
                <button type="submit" class="btn btn-danger" style="border-radius: 10px; font-weight: 700; padding: 8px 18px;">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes pulse {
    0% { opacity: 0.4; }
    50% { opacity: 1; }
    100% { opacity: 0.4; }
}

@keyframes modalPop {
    0% { transform: scale(0.92); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<script>
function openRejectModal(id, nama) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const text = document.getElementById('rejectModalPengaju');
    const textarea = document.getElementById('catatanTextarea');

    form.action = `/admin/reset-password-requests/${id}/reject`;
    text.innerText = `Pengaju: ${nama}`;
    textarea.value = '';
    modal.style.display = 'flex';
}

function setRejectReason(reason) {
    document.getElementById('catatanTextarea').value = reason;
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>
@endsection
