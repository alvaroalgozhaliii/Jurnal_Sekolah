<!DOCTYPE html>
<html>
<head>
    <title>Detail Guru</title>
</head>
<body>

<h1>Detail Guru</h1>

<p>
    <b>Nama</b>
    <br>
    {{ $guru->nama }}
</p>

<p>
    <b>NIP</b>
    <br>
    {{ $guru->nip }}
</p>

<p>
    <b>Bidang Studi</b>
    <br>
    {{ $guru->bidang_studi }}
</p>

<p>
    <b>No Telepon</b>
    <br>
    {{ $guru->no_telp }}
</p>

<p>
    <b>Dibuat</b>
    <br>
    {{ $guru->created_at }}
</p>

<a href="{{ route('guru.index') }}">
    Kembali
</a>

</body>
</html>