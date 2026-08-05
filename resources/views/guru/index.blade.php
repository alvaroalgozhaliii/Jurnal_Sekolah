<!DOCTYPE html>
<html>
<head>
    <title>Data Guru</title>
</head>
<body>

<h1>Data Guru</h1>

<a href="{{ route('guru.create') }}">Tambah Guru</a>

<br><br>

@if(session('success'))
    <p>{{ session('success') }}</p>
@endif

<table border="1" cellpadding="8">

    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>NIP</th>
        <th>Bidang Studi</th>
        <th>No Telepon</th>
        <th>Aksi</th>
    </tr>

    @foreach($guru as $item)

    <tr>

        <td>{{ $loop->iteration }}</td>

        <td>{{ $item->nama }}</td>

        <td>{{ $item->nip }}</td>

        <td>{{ $item->bidang_studi }}</td>

        <td>{{ $item->no_telp }}</td>

        <td>

            <a href="{{ route('guru.show',$item->id_guru) }}">
                Detail
            </a>

            |

            <a href="{{ route('guru.edit',$item->id_guru) }}">
                Edit
            </a>

            |

            <form action="{{ route('guru.destroy',$item->id_guru) }}"
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