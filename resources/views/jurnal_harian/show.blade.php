<!DOCTYPE html>
<html>
<head>
    <title>Detail Jurnal Harian</title>
</head>
<body>


<h2>Detail Jurnal Harian</h2>


<table border="1" cellpadding="10">


<tr>
<th>Tanggal</th>
<td>{{ $jurnal_harian->tanggal }}</td>
</tr>


<tr>
<th>Guru</th>
<td>{{ $jurnal_harian->guru->nama_guru }}</td>
</tr>


<tr>
<th>Kelas</th>
<td>{{ $jurnal_harian->kelas->nama_kelas }}</td>
</tr>


<tr>
<th>Mata Pelajaran</th>
<td>{{ $jurnal_harian->mata_pelajaran }}</td>
</tr>


<tr>
<th>Materi</th>
<td>{{ $jurnal_harian->materi }}</td>
</tr>


<tr>
<th>Keterangan</th>
<td>{{ $jurnal_harian->keterangan }}</td>
</tr>


</table>


<br>


<a href="{{ route('jurnal_harian.index') }}">
Kembali
</a>


</body>
</html>