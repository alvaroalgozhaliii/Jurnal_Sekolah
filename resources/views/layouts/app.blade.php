<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Sekolah</title>
    @php
        $userTheme = 'light';
        if (Auth::check()) {
            $userTheme = \App\Models\Pengaturan::getVal('tema_tampilan', 'light', Auth::user()->role, Auth::id());
        }
    @endphp
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: {{ $userTheme === 'dark' ? '#121212' : '#ffffff' }};
            color: {{ $userTheme === 'dark' ? '#e0e0e0' : '#000000' }};
        }
        a {
            color: {{ $userTheme === 'dark' ? '#80d8ff' : '#0066cc' }};
        }
        nav a {
            text-decoration: none;
            margin-right: 8px;
            font-weight: bold;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid {{ $userTheme === 'dark' ? '#444' : '#ccc' }};
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: {{ $userTheme === 'dark' ? '#1e1e1e' : '#f2f2f2' }};
        }
        .alert-success {
            background-color: {{ $userTheme === 'dark' ? '#1b5e20' : '#d4edda' }};
            color: {{ $userTheme === 'dark' ? '#e8f5e9' : '#155724' }};
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid green;
        }
        .alert-error {
            background-color: {{ $userTheme === 'dark' ? '#b71c1c' : '#f8d7da' }};
            color: {{ $userTheme === 'dark' ? '#ffebee' : '#721c24' }};
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid red;
        }
        button, input[type="submit"] {
            cursor: pointer;
            padding: 5px 10px;
        }
    </style>
</head>
<body>

    @auth
        <div>
            <strong>Jurnal_Sekolah</strong> | 
            Halo, <strong>{{ Auth::user()->nama }}</strong> (Role: {{ strtoupper(Auth::user()->role) }})
        </div>
        <nav style="margin-top: 10px;">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}">Dashboard</a> |
                <a href="{{ route('guru.index') }}">Guru</a> |
                <a href="{{ route('siswa.index') }}">Siswa</a> |
                <a href="{{ route('kelas.index') }}">Kelas</a> |
                <a href="{{ route('jurusan.index') }}">Jurusan</a> |
                <a href="{{ route('jadwal.index') }}">Jadwal</a> |
                <a href="{{ route('jurnal-harian.index') }}">Jurnal Harian</a> |
                <a href="{{ route('piket.presensi') }}">Presensi Piket</a> |
                <a href="{{ route('pengguna.index') }}">Pengguna</a> |
                <a href="{{ route('admin.rekap-kehadiran') }}">Rekap Kehadiran</a> |
                <a href="{{ route('tahun-pelajaran.index') }}">Tahun Pelajaran</a> |
                <a href="{{ route('admin.backup') }}">Backup & Restore</a> |
            @elseif(Auth::user()->isGuru())
                <a href="{{ route('guru.dashboard') }}">Dashboard</a> |
                <a href="{{ route('jadwal.index') }}">Jadwal Mengajar</a> |
                <a href="{{ route('jurnal-harian.index') }}">Jurnal Harian</a> |
                <a href="{{ route('absensi-siswa.index') }}">Absensi Siswa</a> |
                <a href="{{ route('guru.presensi-saya') }}">Presensi Saya</a> |
            @elseif(Auth::user()->isPiket())
                <a href="{{ route('piket.dashboard') }}">Dashboard</a> |
                <a href="{{ route('jadwal.index') }}">Jadwal</a> |
                <a href="{{ route('kelas.index') }}">Kelas</a> |
                <a href="{{ route('siswa.index') }}">Siswa</a> |
                <a href="{{ route('guru.index') }}">Guru</a> |
                <a href="{{ route('jurnal-harian.index') }}">Jurnal Harian</a> |
                <a href="{{ route('piket.presensi') }}">Presensi Piket</a> |
            @elseif(Auth::user()->isSiswa())
                <a href="{{ route('siswa.dashboard') }}">Dashboard</a> |
                <a href="{{ route('siswa.jadwal-pelajaran') }}">Jadwal Pelajaran</a> |
                <a href="{{ route('siswa.presensi-saya') }}">Presensi Saya</a> |
                <a href="{{ route('siswa.kelas-info') }}">Kelas / Ruangan</a> |
            @endif

            <a href="{{ route('profil.show') }}">Profil</a> |
            <a href="{{ route('pengaturan.index') }}">Pengaturan</a> |

            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </nav>
        <hr>
    @endauth

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')

</body>
</html>