@extends('layouts.app')

@section('title', 'Manajemen Perangkat Pengguna (1 Device = 1 Akun) — Admin')
@section('page-title', 'Persetujuan Perangkat Pengguna')

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-12">
    <div>
        <h1 class="page-title">Persetujuan Perangkat Pengguna</h1>
        <p class="page-subtitle">Kebijakan 1 Akun = 1 Perangkat. Setujui permintaan login pengguna dari perangkat atau HP baru.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success mb-20" style="display:flex; align-items:center; gap:10px;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
    <div>{{ session('success') }}</div>
</div>
@endif

<!-- TOP STAT CARDS -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <!-- Stat 1: Pending -->
    <a href="{{ route('admin.device-requests', ['status' => 'pending']) }}" class="card stat-filter-card" style="margin:0; text-decoration:none; border-radius:12px; padding:16px 18px; display:block; background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.02)); border: 1px solid rgba(245, 158, 11, 0.3); border-left: 4px solid #f59e0b !important; {{ $statusFilter === 'pending' ? 'box-shadow: 0 0 0 2px #f59e0b, 0 8px 20px rgba(245, 158, 11, 0.2);' : '' }}">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #d97706; letter-spacing: 0.5px;">MENUNGGU PERSETUJUAN</span>
                <h2 style="margin: 6px 0 0 0; font-size: 26px; font-weight: 800; color: #1e293b;">{{ $pendingCount }}</h2>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #f59e0b; color: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 16px rgba(245, 158, 11, 0.35);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
        </div>
    </a>

    <!-- Stat 2: Disetujui -->
    <a href="{{ route('admin.device-requests', ['status' => 'disetujui']) }}" class="card stat-filter-card" style="margin:0; text-decoration:none; border-radius:12px; padding:16px 18px; display:block; background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.02)); border: 1px solid rgba(16, 185, 129, 0.3); border-left: 4px solid #10b981 !important; {{ $statusFilter === 'disetujui' ? 'box-shadow: 0 0 0 2px #10b981, 0 8px 20px rgba(16, 185, 129, 0.2);' : '' }}">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #059669; letter-spacing: 0.5px;">DISETUJUI</span>
                <h2 style="margin: 6px 0 0 0; font-size: 26px; font-weight: 800; color: #1e293b;">{{ $disetujuiCount }}</h2>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #10b981; color: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
        </div>
    </a>

    <!-- Stat 3: Ditolak -->
    <a href="{{ route('admin.device-requests', ['status' => 'ditolak']) }}" class="card stat-filter-card" style="margin:0; text-decoration:none; border-radius:12px; padding:16px 18px; display:block; background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.02)); border: 1px solid rgba(239, 68, 68, 0.3); border-left: 4px solid #ef4444 !important; {{ $statusFilter === 'ditolak' ? 'box-shadow: 0 0 0 2px #ef4444, 0 8px 20px rgba(239, 68, 68, 0.2);' : '' }}">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #dc2626; letter-spacing: 0.5px;">DITOLAK</span>
                <h2 style="margin: 6px 0 0 0; font-size: 26px; font-weight: 800; color: #1e293b;">{{ $ditolakCount }}</h2>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #ef4444; color: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </div>
        </div>
    </a>
</div>

<!-- FILTER & SEARCH -->
<div class="card mb-24">
    <div class="card-body" style="padding: 14px 20px;">
        <form method="GET" action="{{ route('admin.device-requests') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama pengguna, username, role, atau IP..." value="{{ $search }}">
            </div>
            <div style="width: 180px;">
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ $statusFilter === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ $statusFilter === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 18px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                Filter
            </button>
            @if($search || $statusFilter)
                <a href="{{ route('admin.device-requests') }}" class="btn btn-secondary" style="padding: 10px 14px;">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- TABEL DAFTAR REQUEST PERANGKAT -->
<div class="card mb-32">
    <div class="card-header d-flex justify-between align-center flex-wrap gap-12">
        <h3 class="card-title">Daftar Permintaan Perangkat Baru (Ganti HP / Login Baru)</h3>
        <span class="badge badge-navy">Total {{ $requests->total() }} Data</span>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($requests->count() > 0)
        <div class="table-wrapper" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Perangkat &amp; Browser</th>
                        <th>IP Address</th>
                        <th>Alasan / Keterangan</th>
                        <th>Waktu Request</th>
                        <th>Status</th>
                        <th style="width: 180px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $idx => $req)
                    <tr>
                        <td>{{ $requests->firstItem() + $idx }}</td>
                        <td>
                            <strong class="text-navy" style="font-size: 13.5px;">{{ $req->user->nama ?? '-' }}</strong>
                            <div class="text-muted" style="font-size: 12px;">{{ $req->user->username ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge badge-gray" style="text-transform: uppercase;">
                                {{ str_replace('_', ' ', $req->user->role ?? '-') }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size: 12px; color: #334155; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $req->user_agent }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px; margin-right: 4px;"><rect x="5" y="2" width="14" height="20" rx="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                                {{ Str::limit($req->user_agent ?? 'Unknown Browser', 45) }}
                            </div>
                        </td>
                        <td>
                            <code style="font-size: 12px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ $req->ip_address ?? '-' }}</code>
                        </td>
                        <td>
                            @if($req->keterangan)
                                <span style="font-size: 12.5px; color: #0f172a; font-weight: 500;">{{ $req->keterangan }}</span>
                            @else
                                <span class="text-muted" style="font-size: 12px; font-style: italic;">Tidak ada catatan</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size: 12.5px; font-weight: 600;">{{ $req->created_at->format('d/m/Y H:i') }}</div>
                            <div class="text-muted" style="font-size: 11px;">{{ $req->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            @if($req->status === 'pending')
                                <span class="badge badge-warning" style="font-weight: 700;">MENUNGGU</span>
                            @elseif($req->status === 'disetujui')
                                <span class="badge badge-success" style="font-weight: 700;">DISETUJUI</span>
                                @if($req->approvedBy)
                                    <div class="text-muted" style="font-size: 10.5px; margin-top: 2px;">Oleh: {{ $req->approvedBy->nama }}</div>
                                @endif
                            @elseif($req->status === 'ditolak')
                                <span class="badge badge-danger" style="font-weight: 700;">DITOLAK</span>
                            @endif
                        </td>
                        <td>
                            @if($req->status === 'pending')
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <form action="{{ route('admin.device-requests.approve', $req->id) }}" method="POST" onsubmit="return confirm('Setujui perangkat baru ini untuk {{ $req->user->nama ?? 'user ini' }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary" style="background: #10b981; border-color: #10b981; padding: 6px 12px; font-size: 12px; font-weight: 700;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.device-requests.reject', $req->id) }}" method="POST" onsubmit="return confirm('Tolak permintaan perangkat baru ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 6px 12px; font-size: 12px; font-weight: 700;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div style="text-align: center; font-size: 12px; color: var(--text-muted);">
                                    Selesai
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 14px 20px;">
            {{ $requests->links() }}
        </div>
        @else
        <div class="empty-state" style="padding: 40px 20px; text-align: center;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--text-muted); margin-bottom: 12px;">
                <rect x="5" y="2" width="14" height="20" rx="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line>
            </svg>
            <div style="font-weight: 700; color: #1e293b; margin-bottom: 4px;">Tidak Ada Permintaan Perangkat</div>
            <div class="text-muted" style="font-size: 13px;">Belum ada pengguna yang mencoba login dari perangkat atau HP baru.</div>
        </div>
        @endif
    </div>
</div>

<!-- SECTION 2: RESET PERANGKAT PENGGUNA TERIKAT -->
<div class="card">
    <div class="card-header d-flex justify-between align-center flex-wrap gap-12">
        <div>
            <h3 class="card-title">Reset Ikatan Perangkat Pengguna</h3>
            <p style="margin: 2px 0 0; font-size: 12.5px; color: var(--text-muted);">
                Gunakan fitur ini jika ada guru, siswa, atau wali kelas yang ganti HP dan ingin langsung diizinkan login tanpa menunggu approval.
            </p>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($usersWithDevice->count() > 0)
        <div class="table-wrapper" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Pengguna</th>
                        <th>Username / NIP / NISN</th>
                        <th>Role</th>
                        <th>Status Ikatan</th>
                        <th style="width: 140px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usersWithDevice as $uIdx => $u)
                    <tr>
                        <td>{{ $usersWithDevice->firstItem() + $uIdx }}</td>
                        <td><strong class="text-navy">{{ $u->nama }}</strong></td>
                        <td><code>{{ $u->username }}</code></td>
                        <td><span class="badge badge-gray" style="text-transform: uppercase;">{{ str_replace('_', ' ', $u->role) }}</span></td>
                        <td>
                            <span class="badge badge-success" style="font-size: 11px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                                Terikat ke Perangkat
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <form action="{{ route('admin.device-requests.reset', $u->id_user) }}" method="POST" onsubmit="return confirm('Reset perangkat untuk pengguna {{ $u->nama }}? Pengguna dapat login dari HP/perangkat baru mana saja.');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary" style="font-size: 11.5px; padding: 5px 10px; color: #dc2626; border-color: #fecaca; background: #fef2f2;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                                    Reset Perangkat
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 14px 20px;">
            {{ $usersWithDevice->links() }}
        </div>
        @else
        <div class="empty-state" style="padding: 30px 20px; text-align: center;">
            <div class="text-muted" style="font-size: 13px;">Belum ada akun yang terikat ke perangkat.</div>
        </div>
        @endif
    </div>
</div>
@endsection
