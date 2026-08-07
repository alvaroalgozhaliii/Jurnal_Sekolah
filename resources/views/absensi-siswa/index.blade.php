<!DOCTYPE html>
<html>
<head>
    <title>Data Absensi Siswa</title>
</head>
<body>

    <h2>Data Absensi Siswa</h2>

    @if(session('success'))
        <p style="color:green">
            {{ session('success') }}
        </p>
    @endif

    <a href="{{ route('dashboard.guru') }}">
        Dashboard
    </a>

    |

    <a href="{{ route('absensi-siswa.create') }}">
        Tambah Absensi
    </a>

    |


    <br><br>

    <table border="1" cellpadding="8">

        <tr>

            <th>No</th>

            <th>Jurnal</th>

            <th>Siswa</th>

            <th>Status</th>

            <th>Keterangan</th>

            <th>Dicatat Oleh</th>

            <th>Tanggal Input</th>

            <th>Aksi</th>

        </tr>

        @forelse($absensi as $item)

        <tr>

            <td>{{ $loop->iteration }}</td>

            <td>

                @if($item->jurnal)

                    {{ $item->jurnal->tanggal }}

                    <br>

                    {{ $item->jurnal->mata_pelajaran }}

                @endif

            </td>

            <td>

                @if($item->siswa)

                    {{ $item->siswa->nama }}

                @endif

            </td>

            <td>

                {{ $item->status }}

            </td>

            <td>

                {{ $item->keterangan }}

            </td>

            <td>

                {{ $item->dicatat_oleh }}

            </td>

            <td>

                {{ $item->created_at }}

            </td>

            <td>

                <a href="{{ route('absensi-siswa.show',$item->id_absensi) }}">
                    Detail
                </a>

                |

                <a href="{{ route('absensi-siswa.edit',$item->id_absensi) }}">
                    Edit
                </a>

                |

                <form action="{{ route('absensi-siswa.destroy',$item->id_absensi) }}"
                      method="POST"
                      style="display:inline;">

                    @csrf

                    @method('DELETE')

                    <button
                        onclick="return confirm('Hapus data ini?')">
                        Hapus
                    </button>

                </form>

            </td>

        </tr>

        @empty

        <tr>

            <td colspan="8" align="center">

                Belum ada data absensi.

            </td>

        </tr>

        @endforelse

    </table>

</body>
</html>