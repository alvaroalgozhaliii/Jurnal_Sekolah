<!DOCTYPE html>
<html>
<head>
    <title>Trash Jurnal Harian</title>
</head>

<body>

<h2>Data Terhapus Jurnal Harian</h2>

<a href="{{ route('jurnal-harian.index') }}">
    Kembali
</a>

<br><br>

<table border="1" cellpadding="8">

<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Aksi</th>
</tr>


@forelse($jurnal_harian as $item)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $item->tanggal }}</td>

<td>

<form action="{{ route('jurnal-harian.restore',$item->id_jurnal) }}"
method="POST">

@csrf
@method('PUT')

<button>
Restore
</button>

</form>


<form action="{{ route('jurnal-harian.forceDelete',$item->id_jurnal) }}"
method="POST">

@csrf
@method('DELETE')

<button>
Hapus Permanen
</button>

</form>


</td>

</tr>


@empty

<tr>
<td colspan="3">
Tidak ada data
</td>
</tr>

@endforelse


</table>

</body>
</html>