<!DOCTYPE html>
<html>
<head>
    <title>Data Jadwal</title>
</head>

<body>

<h2>Data Jadwal</h2>

<a href="{{ route('jadwal.create') }}">
    Tambah Jadwal
</a>

<br><br>


@if(session('success'))
    <p>
        {{ session('success') }}
    </p>
@endif


<table border="1" cellpadding="10">

<tr>
    <th>No</th>
    <th>Hari</th>
    <th>Jam Ke</th>
    <th>ID Kelas</th>
    <th>ID Guru</th>
    <th>Mapel</th>
    <th>Ruang</th>
    <th>Mulai</th>
    <th>Selesai</th>
    <th>Aktif</th>
    <th>Aksi</th>
</tr>


@foreach($jadwal as $j)

<tr>

    <td>
        {{ $loop->iteration }}
    </td>

    <td>
        {{ $j->hari }}
    </td>

    <td>
        {{ $j->jam_ke }}
    </td>

    <td>
        {{ $j->id_kelas }}
    </td>

    <td>
        {{ $j->id_guru }}
    </td>

    <td>
        {{ $j->mapel }}
    </td>

    <td>
        {{ $j->ruang }}
    </td>

    <td>
        {{ $j->waktu_mulai }}
    </td>

    <td>
        {{ $j->waktu_selesai }}
    </td>

    <td>
        {{ $j->aktif }}
    </td>


    <td>

        <a href="{{ route('jadwal.edit', $j->id_jadwal) }}">
            Edit
        </a>


        <form action="{{ route('jadwal.destroy', $j->id_jadwal) }}" 
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