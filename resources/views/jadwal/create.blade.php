<h2>Tambah Jadwal</h2>


<form action="{{ route('jadwal.store') }}" method="POST">

@csrf


@include('jadwal.form')


<button type="submit">
Simpan
</button>


</form>