<label>Hari</label>
<br>

<select name="hari">

<option value="senin">Senin</option>
<option value="selasa">Selasa</option>
<option value="rabu">Rabu</option>
<option value="kamis">Kamis</option>
<option value="jumat">Jumat</option>
<option value="sabtu">Sabtu</option>

</select>

<br><br>


<label>Jam Ke</label>
<br>

<input type="number"
name="jam_ke"
value="{{ old('jam_ke',$jadwal->jam_ke ?? '') }}">

<br><br>


<label>ID Kelas</label>
<br>

<input type="number"
name="id_kelas"
value="{{ old('id_kelas',$jadwal->id_kelas ?? '') }}">

<br><br>


<label>ID Guru</label>
<br>

<input type="number"
name="id_guru"
value="{{ old('id_guru',$jadwal->id_guru ?? '') }}">

<br><br>


<label>Mata Pelajaran</label>
<br>

<input type="text"
name="mapel"
value="{{ old('mapel',$jadwal->mapel ?? '') }}">

<br><br>


<label>Ruang</label>
<br>

<input type="text"
name="ruang"
value="{{ old('ruang',$jadwal->ruang ?? '') }}">

<br><br>


<label>Waktu Mulai</label>
<br>

<input type="time"
name="waktu_mulai"
value="{{ old('waktu_mulai',$jadwal->waktu_mulai ?? '') }}">

<br><br>


<label>Waktu Selesai</label>
<br>

<input type="time"
name="waktu_selesai"
value="{{ old('waktu_selesai',$jadwal->waktu_selesai ?? '') }}">

<br><br>


<label>Status</label>
<br>

<select name="aktif">

<option value="1">Aktif</option>

<option value="0">Tidak Aktif</option>

</select>

<br><br>