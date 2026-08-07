<!DOCTYPE html>
<html>
<head>
    <title>Detail Absensi Siswa</title>
</head>
<body>

    <h2>Detail Absensi Siswa</h2>

    <a href="{{ route('absensi-siswa.index') }}">
        Kembali
    </a>

    <br><br>

    <table border="1" cellpadding="8">

        <tr>
            <td><b>ID Absensi</b></td>
            <td>{{ $absensi->id_absensi }}</td>
        </tr>

        <tr>
            <td><b>Jurnal Harian</b></td>
            <td>
                @if($absensi->jurnal)
                    {{ $absensi->jurnal->tanggal }}
                    <br>
                    {{ $absensi->jurnal->mata_pelajaran }}
                @else
                    -
                @endif
            </td>
        </tr>

        <tr>
            <td><b>Nama Siswa</b></td>
            <td>
                @if($absensi->siswa)
                    {{ $absensi->siswa->nama }}
                @else
                    -
                @endif
            </td>
        </tr>

        <tr>
            <td><b>Status</b></td>
            <td>{{ $absensi->status }}</td>
        </tr>

        <tr>
            <td><b>Keterangan</b></td>
            <td>{{ $absensi->keterangan }}</td>
        </tr>

        <tr>
            <td><b>Dicatat Oleh</b></td>
            <td>{{ $absensi->dicatat_oleh }}</td>
        </tr>

        <tr>
            <td><b>Tanggal Input</b></td>
            <td>{{ $absensi->created_at }}</td>
        </tr>

    </table>

    <br>

    <a href="{{ route('absensi-siswa.edit', $absensi->id_absensi) }}">
        Edit
    </a>

    |

    <form action="{{ route('absensi-siswa.destroy', $absensi->id_absensi) }}"
          method="POST"
          style="display:inline;">

        @csrf
        @method('DELETE')

        <button onclick="return confirm('Yakin ingin menghapus data ini?')">
            Hapus
        </button>

    </form>

</body>
</html>