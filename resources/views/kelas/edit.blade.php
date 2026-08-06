<!DOCTYPE html>
<html>
<head>
    <title>Edit Kelas</title>
</head>
<body>

<h1>Edit Data Kelas</h1>

<form action="{{ route('kelas.update',$kelas->id_kelas) }}" method="POST">

    @csrf
    @method('PUT')

    <p>
        Nama Kelas
        <br>
        <input type="text" name="nama_kelas" value="{{ $kelas->nama_kelas }}">
    </p>

    <p>
        Tingkat
        <br>

        <select name="tingkat">

            <option value="X" {{ $kelas->tingkat=='X'?'selected':'' }}>X</option>

            <option value="XI" {{ $kelas->tingkat=='XI'?'selected':'' }}>XI</option>

            <option value="XII" {{ $kelas->tingkat=='XII'?'selected':'' }}>XII</option>

        </select>

    </p>

    <p>
        Wali Kelas
        <br>
        <input type="text"
               name="wali_kelas"
               value="{{ $kelas->wali_kelas }}">
    </p>

    <button type="submit">
        Update
    </button>

    <a href="{{ route('kelas.index') }}">
        Kembali
    </a>

</form>

</body>
</html>