<!DOCTYPE html>
<html>
<head>
    <title>Tambah Guru</title>
</head>
<body>

<h1>Tambah Guru</h1>

<form action="{{ route('guru.store') }}" method="POST">

    @csrf

    <p>
        Nama
        <br>
        <input type="text" name="nama">
    </p>

    <p>
        NIP
        <br>
        <input type="text" name="nip">
    </p>

    <p>
        Bidang Studi
        <br>
        <input type="text" name="bidang_studi">
    </p>

    <p>
        No Telepon
        <br>
        <input type="text" name="no_telp">
    </p>

    <button type="submit">
        Simpan
    </button>

</form>

<br>

<a href="{{ route('guru.index') }}">
    Kembali
</a>

</body>
</html>