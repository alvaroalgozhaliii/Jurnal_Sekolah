<!DOCTYPE html>
<html>
<head>
    <title>Tambah Kelas</title>
</head>
<body>

<h1>Tambah Data Kelas</h1>

<form action="{{ route('kelas.store') }}" method="POST">
    @csrf

    <p>
        Nama Kelas
        <br>
        <input type="text" name="nama_kelas">
    </p>

    <p>
        Tingkat
        <br>
        <select name="tingkat">
            <option value="X">X</option>
            <option value="XI">XI</option>
            <option value="XII">XII</option>
        </select>
    </p>

    <p>
        Wali Kelas
        <br>
        <input type="text" name="wali_kelas">
    </p>

    <button type="submit">
        Simpan
    </button>

    <a href="{{ route('kelas.index') }}">
        Kembali
    </a>

</form>

</body>
</html>