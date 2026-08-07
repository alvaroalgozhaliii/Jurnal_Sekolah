<!DOCTYPE html>
<html>
<head>
    <title>Tambah Absensi Siswa</title>
</head>
<body>

    <h2>Tambah Absensi Siswa</h2>

    <a href="{{ route('absensi-siswa.index') }}">
        Kembali
    </a>

    <br><br>

    @if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('absensi-siswa.store') }}" method="POST">
        @csrf

        <table>

            <tr>
                <td>Jurnal Harian</td>
                <td>
                    <select name="id_jurnal" required>
                        <option value="">-- Pilih Jurnal --</option>

                        @foreach($jurnal as $j)
                            <option value="{{ $j->id_jurnal }}">
                                {{ $j->tanggal }}
                                -
                                {{ $j->mata_pelajaran }}
                            </option>
                        @endforeach

                    </select>
                </td>
            </tr>

            <tr>
                <td>Siswa</td>
                <td>
                    <select name="id_siswa" required>
                        <option value="">-- Pilih Siswa --</option>

                        @foreach($siswa as $s)
                            <option value="{{ $s->id_siswa }}">
                                {{ $s->nama }}
                            </option>
                        @endforeach

                    </select>
                </td>
            </tr>

            <tr>
                <td>Status</td>
                <td>

                    <select name="status" required>

                        <option value="">-- Pilih Status --</option>

                        <option value="Hadir">
                            Hadir
                        </option>

                        <option value="Izin">
                            Izin
                        </option>

                        <option value="Sakit">
                            Sakit
                        </option>

                        <option value="Alpha">
                            Alpha
                        </option>

                    </select>

                </td>
            </tr>

            <tr>
                <td>Keterangan</td>
                <td>
                    <textarea
                        name="keterangan"
                        rows="4"
                        cols="40"
                    ></textarea>
                </td>
            </tr>

            <tr>

                <td></td>

                <td>

                    <button type="submit">
                        Simpan
                    </button>

                    <button type="reset">
                        Reset
                    </button>

                </td>

            </tr>

        </table>

    </form>

</body>
</html>