<!DOCTYPE html>
<html>
<head>
    <title>Detail Siswa</title>
</head>
<body>

<h1>Detail Siswa</h1>

<p>
    <strong>ID :</strong><br>
    {{ $siswa->id_siswa }}
</p>

<p>
    <strong>NIS :</strong><br>
    {{ $siswa->nis }}
</p>

<p>
    <strong>Nama :</strong><br>
    {{ $siswa->nama }}
</p>

<p>
    <strong>ID Kelas :</strong><br>
    {{ $siswa->id_kelas }}
</p>

<p>
    <strong>Jenis Kelamin :</strong><br>
    {{ $siswa->jenis_kelamin }}
</p>

<p>
    <strong>Tempat Lahir :</strong><br>
    {{ $siswa->tempat_lahir }}
</p>

<p>
    <strong>Tanggal Lahir :</strong><br>
    {{ $siswa->tanggal_lahir }}
</p>

<p>
    <strong>No Telp Ortu :</strong><br>
    {{ $siswa->no_telp_ortu }}
</p>

<p>
    <strong>Status :</strong><br>
    {{ $siswa->aktif ? 'Aktif' : 'Tidak Aktif' }}
</p>

<a href="{{ route('siswa.index') }}">Kembali</a>

</body>
</html>