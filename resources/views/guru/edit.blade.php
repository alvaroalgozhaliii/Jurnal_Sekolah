<!DOCTYPE html>
<html>
<head>
    <title>Edit Guru</title>
</head>
<body>

<h1>Edit Guru</h1>

<form action="{{ route('guru.update',$guru->id_guru) }}"
      method="POST">

    @csrf
    @method('PUT')

    <p>
        Nama
        <br>
        <input type="text"
               name="nama"
               value="{{ $guru->nama }}">
    </p>

    <p>
        NIP
        <br>
        <input type="text"
               name="nip"
               value="{{ $guru->nip }}">
    </p>

    <p>
        Bidang Studi
        <br>
        <input type="text"
               name="bidang_studi"
               value="{{ $guru->bidang_studi }}">
    </p>

    <p>
        No Telepon
        <br>
        <input type="text"
               name="no_telp"
               value="{{ $guru->no_telp }}">
    </p>

    <button type="submit">
        Update
    </button>

</form>

<br>

<a href="{{ route('guru.index') }}">
    Kembali
</a>

</body>
</html>