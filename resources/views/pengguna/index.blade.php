@extends('layouts.app')

@section('title', 'Manajemen Pengguna — Jurnal Sekolah')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<style>
/* ── Modal Reset Password ─────────── */
.modal-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(4px);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; visibility: hidden;
    transition: opacity .2s, visibility .2s;
}
.modal-overlay.show { opacity: 1; visibility: visible; }
.modal-box {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 32px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.3);
    transform: translateY(20px);
    transition: transform .25s;
}
.modal-overlay.show .modal-box { transform: translateY(0); }
.modal-title {
    font-size: 18px; font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 6px;
}
.modal-sub { font-size: 13px; color: var(--text-secondary); margin-bottom: 20px; }
.pw-toggle-wrap {
    position: relative;
}
.pw-toggle-wrap input { padding-right: 44px; }
.pw-eye {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: var(--text-secondary); padding: 0; line-height: 1;
}
/* ── Role Badge & Role Filter (Modern Rounded Rectangle, Non-Oval) ── */
.rb {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .3px;
    line-height: 1.2;
    border: 1px solid transparent;
}
.rb-admin { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
.rb-guru { background:#dbeafe; color:#1e40af; border-color:#bfdbfe; }
.rb-piket { background:#dcfce7; color:#166534; border-color:#bbf7d0; }
.rb-waka { background:#e0e7ff; color:#3730a3; border-color:#c7d2fe; }
.rb-kepala { background:#fef3c7; color:#92400e; border-color:#fde68a; }
.rb-satpam { background:#ffedd5; color:#9a3412; border-color:#fed7aa; }
.rb-ortu, .rb-siswa { background:#f1f5f9; color:#334155; border-color:#e2e8f0; }
.rb-walikelas { background:#fce7f3; color:#9d174d; border-color:#fbcfe8; }

/* Dark mode adjustments for role badges */
[data-theme="dark"] .rb-admin { background: rgba(239, 68, 68, 0.16); color: #fca5a5; border-color: rgba(239, 68, 68, 0.3); }
[data-theme="dark"] .rb-guru { background: rgba(59, 130, 246, 0.16); color: #93c5fd; border-color: rgba(59, 130, 246, 0.3); }
[data-theme="dark"] .rb-piket { background: rgba(34, 197, 94, 0.16); color: #86efac; border-color: rgba(34, 197, 94, 0.3); }
[data-theme="dark"] .rb-waka { background: rgba(99, 102, 241, 0.16); color: #a5b4fc; border-color: rgba(99, 102, 241, 0.3); }
[data-theme="dark"] .rb-kepala { background: rgba(245, 158, 11, 0.16); color: #fde047; border-color: rgba(245, 158, 11, 0.3); }
[data-theme="dark"] .rb-satpam { background: rgba(249, 115, 22, 0.16); color: #fdba74; border-color: rgba(249, 115, 22, 0.3); }
[data-theme="dark"] .rb-ortu, [data-theme="dark"] .rb-siswa { background: rgba(148, 163, 184, 0.16); color: #cbd5e1; border-color: rgba(148, 163, 184, 0.25); }
[data-theme="dark"] .rb-walikelas { background: rgba(236, 72, 153, 0.16); color: #f472b6; border-color: rgba(236, 72, 153, 0.3); }

/* Role Filter Bar Styling */
.role-filter-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 10px;
    margin-bottom: 20px;
    align-items: center;
}
.role-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text-primary);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
}
.role-filter-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
}
.role-filter-btn.active {
    background: var(--navy-primary);
    border-color: var(--navy-primary);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.25);
}
.role-filter-btn.active .rb {
    border-color: rgba(255, 255, 255, 0.3);
}
.role-filter-btn.active .role-count-badge {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}
.role-count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 4px;
    background: #e2e8f0;
    color: #334155;
    font-size: 11px;
    font-weight: 700;
}

/* Dark Mode Role Filter */
[data-theme="dark"] .role-filter-btn {
    background: #1e293b;
    border-color: #334155;
    color: #f8fafc;
}
[data-theme="dark"] .role-filter-btn:hover {
    background: #28354d;
    border-color: #475569;
}
[data-theme="dark"] .role-filter-btn.active {
    background: #2563eb;
    border-color: #3b82f6;
    color: #ffffff;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.4);
}
[data-theme="dark"] .role-count-badge {
    background: #0f172a;
    color: #94a3b8;
}

/* Fix: cegah search input overlap (naik) saat hover/focus */
.search-bar-form .form-control,
.search-bar-form .form-control:hover,
.search-bar-form .form-control:focus {
    transform: none !important;
}

/* Fix: cegah baris tabel bergerak saat hover (supaya tidak menutupi search) */
#tableUsers tbody tr,
#tableUsers tbody tr:hover {
    transform: none !important;
}

/* Fix: cegah card tabel naik saat hover sehingga menutupi search */
#tableCard,
#tableCard:hover {
    transform: none !important;
    box-shadow: none !important;
}
</style>

{{-- Modal Reset Password --}}
<div class="modal-overlay" id="modalResetPw">
    <div class="modal-box">
        <div class="modal-title">Reset Password</div>
        <div class="modal-sub" id="modalSubText">Mengubah password untuk akun <strong id="modalUsername"></strong></div>
        <form id="formResetPw" method="POST">
            @csrf
            @method('PATCH')
            <div class="form-group mb-16">
                <label class="form-label">Password Baru <span style="color:red">*</span></label>
                <div class="pw-toggle-wrap">
                    <input type="password" id="inputNewPw" name="new_password"
                           class="form-control" placeholder="Min. 6 karakter" required minlength="6">
                    <button type="button" class="pw-eye" onclick="togglePw('inputNewPw', this)">
                        Show
                    </button>
                </div>
            </div>
            <div class="form-group mb-24">
                <label class="form-label">Konfirmasi Password Baru <span style="color:red">*</span></label>
                <div class="pw-toggle-wrap">
                    <input type="password" id="inputConfPw" name="new_password_confirmation"
                           class="form-control" placeholder="Ulangi password baru" required minlength="6">
                    <button type="button" class="pw-eye" onclick="togglePw('inputConfPw', this)">
                        Show
                    </button>
                </div>
                <div id="pwMatchMsg" style="font-size:12px; margin-top:4px; color:#dc2626; display:none;">Password tidak cocok!</div>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Simpan Password</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen Akun Pengguna</h1>
        <p class="page-subtitle">Kelola Seluruh Akun Login Pengguna System & Peran (Role)</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('pengguna.create') }}" class="btn btn-primary">+ Tambah Pengguna Baru</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success mb-16" style="padding:12px 16px; border-radius:8px;">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger mb-16" style="padding:12px 16px; border-radius:8px;">
    {{ session('error') }}
</div>
@endif

{{-- Ringkasan per Role --}}
@php
    $roleGroups = $allUsers->groupBy('role');
    $roleMeta = [
        'admin'          => ['label'=>'Admin',          'class'=>'rb-admin'],
        'guru'           => ['label'=>'Guru',           'class'=>'rb-guru'],
        'piket'          => ['label'=>'Piket',          'class'=>'rb-piket'],
        'waka_kesiswaan' => ['label'=>'Waka Kesiswaan', 'class'=>'rb-waka'],
        'waka_sdm'       => ['label'=>'Waka SDM',       'class'=>'rb-waka'],
        'waka_kurikulum' => ['label'=>'Waka Kurikulum', 'class'=>'rb-waka'],
        'kepala_sekolah' => ['label'=>'Kepala Sekolah', 'class'=>'rb-kepala'],
        'satpam'         => ['label'=>'Satpam',         'class'=>'rb-satpam'],
        'walikelas'      => ['label'=>'Wali Kelas',     'class'=>'rb-walikelas'],
        'ortu'           => ['label'=>'Orang Tua',      'class'=>'rb-ortu'],
        'siswa'          => ['label'=>'Siswa',          'class'=>'rb-siswa'],
    ];
@endphp
<div class="role-filter-container">
    @foreach($roleMeta as $roleKey => $meta)
        @if($roleGroups->has($roleKey))
        <a href="{{ route('pengguna.index', array_filter(['role' => $roleKey, 'search' => $search ?? ''])) }}" 
           class="role-filter-btn {{ ($role ?? '') === $roleKey ? 'active' : '' }}" 
           id="filterBtn_{{ $roleKey }}"
           style="text-decoration: none;">
            <span class="rb {{ $meta['class'] }}">{{ $meta['label'] }}</span>
            <span class="role-count-badge">{{ $roleGroups[$roleKey]->count() }}</span>
        </a>
        @endif
    @endforeach
    <a href="{{ route('pengguna.index', array_filter(['search' => $search ?? ''])) }}" 
       class="role-filter-btn {{ empty($role) ? 'active' : '' }}" 
       id="filterBtnAll"
       style="text-decoration: none;">
        <span>Semua</span>
        <span class="role-count-badge">{{ $allUsers->count() }}</span>
    </a>
</div>

{{-- Search --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('pengguna.index') }}" class="d-flex gap-8 search-bar-form" style="align-items:center;">
            @if($role ?? false)
                <input type="hidden" name="role" value="{{ $role }}">
            @endif
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, username, role..." class="form-control" style="max-width:420px;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if(($search ?? false) || ($role ?? false))
                <a href="{{ route('pengguna.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card" id="tableCard">
    <div class="card-body" style="padding:0;">
        @if($users->total() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table" id="tableUsers">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role / Peran</th>
                        <th>Profil Terhubung</th>
                        <th>No WA / HP</th>
                        <th class="action-col" style="min-width:240px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $item)
                    @php
                        $rm = $roleMeta[$item->role] ?? ['label' => strtoupper($item->role), 'class' => 'rb-ortu'];
                    @endphp
                    <tr data-role="{{ $item->role }}">
                        <td class="no-col">{{ ($users->currentPage() - 1) * 25 + $loop->iteration }}</td>
                        <td class="fw-bold" style="color:var(--text-primary);">{{ $item->nama }}</td>
                        <td>
                            <span class="badge badge-navy" style="font-family:monospace; font-size:13px;">{{ $item->username }}</span>
                        </td>
                        <td>
                            <span class="rb {{ $rm['class'] }}">{{ $rm['label'] }}</span>
                        </td>
                        <td>
                            @if($item->guru)
                                <span class="badge badge-info" style="font-size:11px;">{{ $item->guru->nama }}</span>
                            @elseif($item->siswa)
                                <span class="badge badge-success" style="font-size:11px;">{{ $item->siswa->nama }}</span>
                            @else
                                <span style="color:var(--text-secondary); font-size:12px;">—</span>
                            @endif
                        </td>
                        <td style="font-size:13px; color:var(--text-secondary);">
                            {{ $item->no_hp ?: '—' }}
                        </td>
                        <td class="action-col">
                            <a href="{{ route('pengguna.edit', $item->id_user) }}" class="btn btn-secondary btn-sm" title="Edit">Edit</a>
                            <button type="button" class="btn btn-warning btn-sm"
                                style="background:#f59e0b; color:#fff; border-color:#f59e0b;"
                                onclick="openResetModal({{ $item->id_user }}, '{{ addslashes($item->username) }}', '{{ addslashes($item->nama) }}')"
                                title="Reset Password">
                                Password
                            </button>
                            <form action="{{ route('pengguna.destroy', $item->id_user) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Hapus akun {{ addslashes($item->nama) }}?')"
                                    title="Hapus">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- Info & Navigasi Pagination --}}
        @if($users->lastPage() > 1)
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding:14px 16px; border-top:1px solid var(--border);">
            <span style="font-size:13px; color:var(--text-secondary);">
                Menampilkan
                <strong>{{ $users->firstItem() }}</strong>–<strong>{{ $users->lastItem() }}</strong>
                dari <strong>{{ $users->total() }}</strong> pengguna
            </span>
            <div style="display:flex; align-items:center; gap:4px; flex-wrap:wrap;">
                {{-- Prev --}}
                @if($users->onFirstPage())
                    <span style="padding:5px 10px; border-radius:6px; font-size:13px; color:var(--text-secondary); border:1px solid var(--border); cursor:not-allowed; opacity:.5;">‹</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" style="padding:5px 10px; border-radius:6px; font-size:13px; color:var(--text-primary); border:1px solid var(--border); text-decoration:none;">‹</a>
                @endif

                {{-- Halaman --}}
                @php
                    $cur  = $users->currentPage();
                    $last = $users->lastPage();
                    $pages = [];
                    if ($last <= 7) {
                        $pages = range(1, $last);
                    } else {
                        $pages = array_unique(array_filter(array_merge(
                            [1, 2],
                            ($cur > 4)              ? ['...l'] : [],
                            range(max(3, $cur-1), min($last-2, $cur+1)),
                            ($cur < $last - 3)      ? ['...r'] : [],
                            [$last-1, $last]
                        )));
                    }
                @endphp

                @foreach($pages as $p)
                    @if($p === '...l' || $p === '...r')
                        <span style="padding:5px 8px; font-size:13px; color:var(--text-secondary);">…</span>
                    @elseif($p == $cur)
                        <span style="padding:5px 10px; border-radius:6px; font-size:13px; font-weight:700; background:var(--primary,#3b82f6); color:#fff; border:1px solid var(--primary,#3b82f6);">{{ $p }}</span>
                    @else
                        <a href="{{ $users->url($p) }}" style="padding:5px 10px; border-radius:6px; font-size:13px; color:var(--text-primary); border:1px solid var(--border); text-decoration:none;">{{ $p }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" style="padding:5px 10px; border-radius:6px; font-size:13px; color:var(--text-primary); border:1px solid var(--border); text-decoration:none;">›</a>
                @else
                    <span style="padding:5px 10px; border-radius:6px; font-size:13px; color:var(--text-secondary); border:1px solid var(--border); cursor:not-allowed; opacity:.5;">›</span>
                @endif
            </div>
        </div>
        @endif
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data pengguna.</div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function openResetModal(id, username, nama) {
    document.getElementById('modalUsername').textContent = username + ' (' + nama + ')';
    document.getElementById('formResetPw').action = '/admin/pengguna/' + id + '/reset-password';
    document.getElementById('inputNewPw').value = '';
    document.getElementById('inputConfPw').value = '';
    document.getElementById('pwMatchMsg').style.display = 'none';
    document.getElementById('modalResetPw').classList.add('show');
    document.getElementById('inputNewPw').focus();
}

function closeModal() {
    document.getElementById('modalResetPw').classList.remove('show');
}

// Close on overlay click
document.getElementById('modalResetPw').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function togglePw(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = 'Hide';
    } else {
        input.type = 'password';
        btn.textContent = 'Show';
    }
}

// Validate password match before submit
document.getElementById('formResetPw').addEventListener('submit', function(e) {
    const pw = document.getElementById('inputNewPw').value;
    const conf = document.getElementById('inputConfPw').value;
    if (pw !== conf) {
        e.preventDefault();
        document.getElementById('pwMatchMsg').style.display = 'block';
        return;
    }
    document.getElementById('pwMatchMsg').style.display = 'none';
});

document.getElementById('inputConfPw').addEventListener('input', function() {
    const pw = document.getElementById('inputNewPw').value;
    const msg = document.getElementById('pwMatchMsg');
    msg.style.display = (this.value && this.value !== pw) ? 'block' : 'none';
});

});
</script>
@endpush
@endsection
