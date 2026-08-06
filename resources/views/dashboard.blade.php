<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Sistem Jurnal Sekolah</title>
</head>
<body>

    <h1>Sistem Jurnal Sekolah</h1>
    <hr>

    <h3>Menu Utama</h3>

    <table border="1" cellpadding="15">
        <tr>
            <td align="center">
                <a href="{{ route('guru.index') }}">
                    <h3>Data Guru</h3>
                </a>
            </td>

            <td align="center">
                <h3>Data Jurusan</h3>
                <small>Belum tersedia</small>
            </td>
        </tr>

        <tr>
            <td align="center">
                <a href="{{ route('kelas.index') }}">
                    <h3>Data Kelas</h3>
                </a>
            </td>

            <td align="center">
                <a href="{{ route('siswa.index') }}">
                    <h3>Data Siswa</h3>
                </a>
            </td>
        </tr>

        <tr>
            <td align="center">
                <a href="{{ route('jadwal.index') }}">
                    <h3>Data Jadwal</h3>
                </a>
            </td>

            <td align="center">
                <a href="{{ route('jurnal-harian.index') }}">
                    <h3>Data Jurnal Harian</h3>
                </a>
                <a href="{{ route('jurnal-harian.trash') }}">
                    Trash
                </a>
            </td>
        </tr>

        <tr>
            <td colspan="2" align="center">
                <h3>Absensi</h3>
                <small>Belum tersedia</small>
            </td>
        </tr>
    </table>

</body>
</html>