<h2>Edit Jadwal</h2>


<form action="{{ route('jadwal.update',$jadwal->id) }}" method="POST">

@csrf

@method('PUT')


@include('jadwal.form')


<button>
Update
</button>


</form>