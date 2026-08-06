<h2>Detail Jadwal</h2>


<p>Hari : {{$jadwal->hari}}</p>

<p>Jam Ke : {{$jadwal->jam_ke}}</p>

<p>Kelas : {{$jadwal->id_kelas}}</p>

<p>Guru : {{$jadwal->id_guru}}</p>

<p>Mapel : {{$jadwal->mapel}}</p>

<p>Ruang : {{$jadwal->ruang}}</p>

<p>
{{$jadwal->waktu_mulai}}
-
{{$jadwal->waktu_selesai}}
</p>


<a href="{{route('jadwal.index')}}">
Kembali
</a>