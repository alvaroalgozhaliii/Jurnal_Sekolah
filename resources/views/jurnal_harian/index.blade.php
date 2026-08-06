<!DOCTYPE html>
<html>
<head>
    <title>Data Jurnal Harian</title>
</head>

<body>

<h2>Data Jurnal Harian</h2>


@if(session('success'))
    <p style="color:green">
        {{ session('success') }}
    </p>
@endif


<p>
    <a href="{{ route('jurnal-harian.create') }}">
        + Tambah Jurnal
    </a>

    |

    <a href="{{ route('jurnal-harian.trash') }}">
        Data Terhapus
    </a>
</p>



<table border="1" cellpadding="8" cellspacing="0">

<thead>

<tr>
    <th>No</th>
    <th>Guru</th>
    <th>Kelas</th>
    <th>Tanggal</th>
    <th>Mata Pelajaran</th>
    <th>Materi</th>
    <th>Kegiatan</th>
    <th>Catatan</th>
    <th>Status</th>
    <th width="220">Aksi</th>
</tr>

</thead>


<tbody>


@forelse($jurnal_harian as $item)


<tr>

<td>
    {{ $loop->iteration }}
</td>


<td>
    {{ $item->guru->nama_guru ?? $item->id_guru }}
</td>


<td>
    {{ $item->kelas->nama_kelas ?? $item->id_kelas }}
</td>


<td>
    {{ $item->tanggal }}
</td>


<td>
    {{ $item->mata_pelajaran }}
</td>


<td>
    {{ $item->materi }}
</td>


<td>
    {{ $item->kegiatan }}
</td>


<td>
    {{ $item->catatan }}
</td>


<td>

@if($item->aktif)

    Aktif

@else

    Tidak Aktif

@endif

</td>


<td>


<a href="{{ route('jurnal-harian.show',$item->id_jurnal) }}">
    Detail
</a>


|


<a href="{{ route('jurnal-harian.edit',$item->id_jurnal) }}">
    Edit
</a>


|


<form action="{{ route('jurnal-harian.destroy',$item->id_jurnal) }}"
      method="POST"
      style="display:inline;">


@csrf

@method('DELETE')


<button type="submit"
onclick="return confirm('Yakin ingin menghapus data ini?')">

Hapus

</button>


</form>


</td>


</tr>



@empty


<tr>

<td colspan="10" align="center">

Data jurnal harian belum ada.

</td>

</tr>


@endforelse



</tbody>


</table>


</body>
</html>