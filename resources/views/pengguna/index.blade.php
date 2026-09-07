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
/* Role badge colors */
.rb { padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; letter-spacing: .4px; }
.rb-admin { background:#fee2e2; color:#991b1b; }
.rb-guru { background:#dbeafe; color:#1e40af; }
.rb-piket { background:#dcfce7; color:#166534; }
.rb-waka { background:#e0e7ff; color:#3730a3; }
.rb-kepala { background:#fef3c7; color:#92400e; }
.rb-satpam { background:#ffedd5; color:#9a3412; }
.rb-ortu, .rb-siswa { background:#f3f4f6; color:#374151; }
.rb-walikelas { background:#fce7f3; color:#9d174d; }
</style>

{{-- Modal Reset Password --}}
<div class="modal-overlay" id="modalResetPw">
    <div class="modal-box">
        <div class="modal-title">🔑 Reset Password</div>
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
                        👁
                    </button>
                </div>
            </div>
            <div class="form-group mb-24">
                <label class="form-label">Konfirmasi Password Baru <span style="color:red">*</span></label>
                <div class="pw-toggle-wrap">
                    <input type="password" id="inputConfPw" name="new_password_confirmation"
                           class="form-control" placeholder="Ulangi password baru" required minlength="6">
                    <button type="button" class="pw-eye" onclick="togglePw('inputConfPw', this)">
                        👁
                    </button>
                </div>
                <div id="pwMatchMsg" style="font-size:12px; margin-top:4px; color:#dc2626; display:none;">❌ Password tidak cocok!</div>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">💾 Simpan Password</button>
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
    ✅ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger mb-16" style="padding:12px 16px; border-radius:8px;">
    ❌ {{ session('error') }}
</div>
@endif

{{-- Ringkasan per Role --}}
@php
    $roleGroups = $users->groupBy('role');
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
<div style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:20px;">
    @foreach($roleMeta as $roleKey => $meta)
        @if($roleGroups->has($roleKey))
        <button onclick="filterRole('{{ $roleKey }}')" class="btn btn-secondary btn-sm" id="filterBtn_{{ $roleKey }}" style="gap:6px; display:flex; align-items:center;">
            <span class="rb {{ $meta['class'] }}">{{ $meta['label'] }}</span>
            <span style="font-weight:700;">{{ $roleGroups[$roleKey]->count() }}</span>
        </button>
        @endif
    @endforeach
    <button onclick="filterRole('')" class="btn btn-secondary btn-sm" id="filterBtnAll">
        Semua ({{ $users->count() }})
    </button>
</div>

{{-- Search --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('pengguna.index') }}" class="d-flex gap-8" style="align-items:center;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, username, role..." class="form-control" style="max-width:420px;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if($search ?? false)
                <a href="{{ route('pengguna.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($users->count() > 0)
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
                        <td class="no-col">{{ $loop->iteration }}</td>
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
                            <a href="{{ route('pengguna.edit', $item->id_user) }}" class="btn btn-secondary btn-sm" title="Edit">✏️ Edit</a>
                            <button type="button" class="btn btn-warning btn-sm"
                                style="background:#f59e0b; color:#fff; border-color:#f59e0b;"
                                onclick="openResetModal({{ $item->id_user }}, '{{ addslashes($item->username) }}', '{{ addslashes($item->nama) }}')"
                                title="Reset Password">
                                🔑 Password
                            </button>
                            <form action="{{ route('pengguna.destroy', $item->id_user) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Hapus akun {{ addslashes($item->nama) }}?')"
                                    title="Hapus">🗑</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
        btn.textContent = '🙈';
    } else {
        input.type = 'password';
        btn.textContent = '👁';
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

// Role filter
function filterRole(role) {
    const rows = document.querySelectorAll('#tableUsers tbody tr');
    rows.forEach(row => {
        if (!role || row.dataset.role === role) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    // Update counter display
    let count = 0;
    rows.forEach(r => { if(r.style.display !== 'none') count++; });
}
</script>
@endpush
@endsection
