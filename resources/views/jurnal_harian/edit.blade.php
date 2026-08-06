<!DOCTYPE html>
<html>
<head>
    <title>Edit Jurnal Harian</title>
</head>
<body>


<h2>Edit Jurnal Harian</h2>


<form action="{{ route('jurnal_harian.update',$jurnal_harian->id_jurnal) }}"
method="POST">

@csrf
@method('PUT')


<label>Tanggal</label>
<br>

<input type="date"
name="tanggal"
value="{{ $jurnal_harian->tanggal }}">


<br><br>


<label>Guru</label>
<br>

<select name="id_guru">


@foreach($guru as $g)

<option value="{{ $g->id_guru }}"
{{ $jurnal_harian->id_guru==$g->id_guru?'selected':'' }}>

{{ $g->nama_guru }}

</option>

@endforeach


</select>


<br><br>


<label>Kelas</label>
<br>

<select name="id_kelas">


@foreach($kelas as $k)

<option value="{{ $k->id_kelas }}"
{{ $jurnal_harian->id_kelas==$k->id_kelas?'selected':'' }}>

{{ $k->nama_kelas }}

</option>

@endforeach


</select>


<br><br>


<label>Mata Pelajaran</label>
<br>

<input type="text"
name="mata_pelajaran"
value="{{ $jurnal_harian->mata_pelajaran }}">


<br><br>


<label>Materi</label>
<br>

<textarea name="materi">{{ $jurnal_harian->materi }}</textarea>


<br><br>


<label>Keterangan</label>
<br>

<textarea name="keterangan">{{ $jurnal_harian->keterangan }}</textarea>


<br><br>


<button type="submit">
Update
</button>


</form>


</body>
</html>