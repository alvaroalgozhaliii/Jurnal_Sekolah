<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Guru</title>
</head>
<body>

<h1>Dashboard Guru</h1>

<hr>

<h3>
Selamat datang,
{{ session('nama_guru') }}
</h3>

<table border="1" cellpadding="15">

<tr>

<td align="center">
<a href="{{ route('jadwal.index') }}">
<h3>Jadwal Mengajar</h3>
</a>
</td>

<td align="center">
<a href="{{ route('jurnal-harian.index') }}">
<h3>Jurnal Harian</h3>
</a>
</td>

</tr>

<tr>

<td align="center">
<a href="{{ route('absensi-siswa.index') }}">
<h3>Absensi</h3>
</td>

<td align="center">

<form action="{{ route('guru.logout') }}" method="POST">
@csrf

<button type="submit">
Logout
</button>

</form>

</td>

</tr>

</table>

</body>
</html>