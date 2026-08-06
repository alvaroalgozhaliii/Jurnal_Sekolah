<!DOCTYPE html>
<html>
<head>
    <title>Edit Siswa</title>
</head>
<body>

<h1>Edit Data Siswa</h1>

<form action="{{ route('siswa.update',$siswa->id_siswa) }}" method="POST">
    @csrf
    @method('PUT')

    <p>
        NIS <br>
        <input type="text" name="nis" value="{{ $siswa->nis }}">
    </p>

    <p>
        Nama Siswa <br>
        <input type="text" name="nama" value="{{ $siswa->nama }}">
    </p>

    <p>
        ID Kelas <br>
        <input type="number" name="id_kelas" value="{{ $siswa->id_kelas }}">
    </p>

    <p>
        Jenis Kelamin <br>
        <select name="jenis_kelamin">
            <option value="L" {{ $siswa->jenis_kelamin=='L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ $siswa->jenis_kelamin=='P' ? 'selected' : '' }}>Perempuan</option>
        </select>
    </p>

    <p>
        Tempat Lahir <br>
        <input type="text" name="tempat_lahir" value="{{ $siswa->tempat_lahir }}">
    </p>

    <p>
        Tanggal Lahir <br>
        <input type="date" name="tanggal_lahir" value="{{ $siswa->tanggal_lahir }}">
    </p>

    <p>
        No Telp Ortu <br>
        <input type="text" name="no_telp_ortu" value="{{ $siswa->no_telp_ortu }}">
    </p>

    <p>
        Aktif <br>
        <select name="aktif">
            <option value="1" {{ $siswa->aktif==1 ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ $siswa->aktif==0 ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
    </p>

    <button type="submit">Update</button>

    <a href="{{ route('siswa.index') }}">Kembali</a>

</form>

</body>
</html>