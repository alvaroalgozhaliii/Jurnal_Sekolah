<!DOCTYPE html>
<html>
<head>
    <title>Tambah Siswa</title>
</head>
<body>

<h1>Tambah Data Siswa</h1>

<form action="{{ route('siswa.store') }}" method="POST">
    @csrf

    <p>
        NIS <br>
        <input type="text" name="nis">
    </p>

    <p>
    Nama
    <br>
    <input type="text" name="nama">
    </p>

    <p>
        ID Kelas <br>
        <input type="number" name="id_kelas">
    </p>

    <p>
        Jenis Kelamin <br>
        <select name="jenis_kelamin">
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
        </select>
    </p>

    <p>
        Tempat Lahir <br>
        <input type="text" name="tempat_lahir">
    </p>

    <p>
        Tanggal Lahir <br>
        <input type="date" name="tanggal_lahir">
    </p>

    <p>
        No Telp Ortu <br>
        <input type="text" name="no_telp_ortu">
    </p>

    <p>
        Aktif <br>
        <select name="aktif">
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
        </select>
    </p>

    <button type="submit">Simpan</button>

    <a href="{{ route('siswa.index') }}">Kembali</a>

</form>

</body>
</html>