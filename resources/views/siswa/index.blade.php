<!DOCTYPE html>
<html>
<head>
    <title>Data Siswa</title>
</head>
<body>

<h1>Data Siswa</h1>

<a href="{{ route('siswa.create') }}">Tambah Siswa</a>

@if(session('success'))
    <p>{{ session('success') }}</p>
@endif

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>NIS</th>
        <th>Nama</th>
        <th>Kelas</th>
        <th>Aksi</th>
    </tr>

    @foreach($siswa as $s)
    <tr>
        <td>{{ $s->id_siswa }}</td>
        <td>{{ $s->nis }}</td>
        <td>{{ $s->nama }}</td>
        <td>{{ $s->id_kelas }}</td>
        <td>
            <a href="{{ route('siswa.show',$s->id_siswa) }}">Detail</a> |
            <a href="{{ route('siswa.edit',$s->id_siswa) }}">Edit</a> |

            <form action="{{ route('siswa.destroy',$s->id_siswa) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Hapus</button>
            </form>

        </td>
    </tr>
    @endforeach

</table>

</body>
</html>