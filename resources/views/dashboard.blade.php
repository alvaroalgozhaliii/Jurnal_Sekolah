<!DOCTYPE html>
<html>
<head>
    <title>Sistem Jurnal Sekolah</title>
</head>
<body>

    <h1 align="center">Sistem Jurnal Sekolah</h1>
    <hr>

    <h2 align="center">Silakan Masuk Sebagai</h2>

    <table border="1" cellpadding="30" align="center">
        <tr>
            <td align="center" width="250">
                <h2>👨‍🏫 Guru</h2>

                <p>
                    Login untuk mengelola jurnal harian,
                    absensi siswa, dan melihat jadwal.
                </p>

                {{-- Ganti nanti ke route login guru --}}
                <a href="{{ route('guru.login') }}">
                    <button>Masuk Sebagai Guru</button>
                </a>
            </td>

            <td align="center" width="250">
                <h2>👨‍🎓 Murid</h2>

                <p>
                    Login untuk melihat jadwal,
                    absensi, dan informasi sekolah.
                </p>

                {{-- Ganti nanti ke route login siswa --}}
                <a href="{{ route('siswa.login') }}">
                    <button>Masuk Sebagai Murid</button>
                </a>
            </td>
        </tr>
    </table>

    <br>

    <div align="center">
        <small>
            *Halaman login akan dibuat pada tahap berikutnya.
        </small>
    </div>

</body>
</html>