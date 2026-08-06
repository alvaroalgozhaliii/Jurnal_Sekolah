<!DOCTYPE html>
<html>
<head>
    <title>Detail Kelas</title>
</head>
<body>

<h1>Detail Kelas</h1>

<p><b>Nama Kelas :</b> {{ $kelas->nama_kelas }}</p>
<p><b>Tingkat :</b> {{ $kelas->tingkat }}</p>
<p><b>ID Jurusan :</b> {{ $kelas->id_jurusan }}</p>
<p><b>Wali Kelas :</b> {{ $kelas->wali_kelas }}</p>

<a href="{{ route('kelas.index') }}">Kembali</a>

</body>
</html>