<!DOCTYPE html>
<html>
<head>
    <title>Data Kelas</title>
</head>
<body>

<h1>Data Kelas</h1>

<a href="{{ route('kelas.create') }}">
    Tambah Kelas
</a>

<br><br>

<table border="1" cellpadding="8">

<tr>
    <th>No</th>
    <th>Nama Kelas</th>
    <th>Tingkat</th>
    <th>ID Jurusan</th>
    <th>Wali Kelas</th>
    <th>Aksi</th>
</tr>

@foreach($kelas as $item)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $item->nama_kelas }}</td>

<td>{{ $item->tingkat }}</td>

<td>{{ $item->id_jurusan }}</td>

<td>{{ $item->wali_kelas }}</td>

<td>

<a href="{{ route('kelas.show',$item->id_kelas) }}">
Detail
</a>

|

<a href="{{ route('kelas.edit',$item->id_kelas) }}">
Edit
</a>

|

<form action="{{ route('kelas.destroy',$item->id_kelas) }}"
method="POST"
style="display:inline">

@csrf
@method('DELETE')

<button type="submit">
Hapus
</button>

</form>

</td>

</tr>

@endforeach

</table>

</body>
</html>