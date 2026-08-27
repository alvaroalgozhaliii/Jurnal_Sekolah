@extends('layouts.app')

@section('content')
<h2>Edit Jadwal Pelajaran</h2>
<a href="{{ route('jadwal.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('jadwal.update', $jadwal->id_jadwal) }}" method="POST">
    @csrf
    @method('PUT')
    <table>
        <tr>
            <td><label>Hari *</label></td>
            <td>
                <select name="hari" id="select_hari" required style="padding:5px; width:200px;">
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                    <option value="{{ $h }}" {{ old('hari', $jadwal->hari) == $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td><label>Jam Ke *</label></td>
            <td>
                <select name="jam_ke" id="select_jam_ke" required style="padding:5px; width:300px;">
                    <!-- JS populate -->
                </select>
            </td>
        </tr>
        <tr>
            <td><label>Waktu KBM (Otomatis)</label></td>
            <td>
                <span id="preview_waktu" style="font-weight:bold; color:#0066cc;">-</span>
            </td>
        </tr>
        <tr>
            <td><label>Kelas *</label></td>
            <td>
                <select name="id_kelas" required style="width:300px; padding:5px;">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                    <option value="{{ $k->id_kelas }}" {{ old('id_kelas', $jadwal->id_kelas) == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td><label>Guru Pengajar</label></td>
            <td>
                <select name="id_guru" style="width:300px; padding:5px;">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($guru as $g)
                    <option value="{{ $g->id_guru }}" {{ old('id_guru', $jadwal->id_guru) == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }} ({{ $g->bidang_studi ?? '-' }})</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td><label>Mata Pelajaran *</label></td>
            <td>
                <select name="mapel" required style="width:300px; padding:5px;">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($mapel as $mp)
                    <option value="{{ $mp->nama_mapel }}" {{ old('mapel', $jadwal->mapel) == $mp->nama_mapel ? 'selected' : '' }}>{{ $mp->nama_mapel }} ({{ $mp->kode_mapel ?? '-' }})</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td><label>Ruangan</label></td>
            <td><input type="text" name="ruang" value="{{ old('ruang', $jadwal->ruang) }}" style="width:150px; padding:5px;"></td>
        </tr>
        <tr>
            <td></td>
            <td><button type="submit" style="padding:8px 20px; margin-top:10px;">UPDATE JADWAL</button></td>
        </tr>
    </table>
</form>

<script>
    const slotsSeninKamis = {
        1: "07:00 - 07:45",
        2: "07:45 - 08:30",
        3: "08:30 - 09:15",
        4: "09:15 - 10:00",
        5: "10:15 - 11:00",
        6: "11:00 - 11:45",
        7: "12:30 - 13:15",
        8: "13:15 - 14:00",
        9: "14:00 - 14:45",
        10: "14:45 - 15:30"
    };

    const slotsJumat = {
        1: "07:00 - 07:40",
        2: "07:40 - 08:20",
        3: "08:20 - 09:00",
        4: "09:00 - 09:40",
        5: "09:55 - 10:35",
        6: "10:35 - 11:15",
        7: "11:15 - 11:55",
        8: "13:00 - 13:40",
        9: "13:40 - 14:20",
        10: "14:20 - 15:00",
        11: "15:00 - 15:40",
        12: "15:40 - 16:20"
    };

    const selectHari = document.getElementById('select_hari');
    const selectJamKe = document.getElementById('select_jam_ke');
    const previewWaktu = document.getElementById('preview_waktu');
    const currentJamKe = "{{ old('jam_ke', $jadwal->jam_ke) }}";

    function updateJamOptions() {
        const hari = selectHari.value;
        const slots = (hari === 'Jumat') ? slotsJumat : slotsSeninKamis;

        selectJamKe.innerHTML = '';
        for (let jam in slots) {
            const opt = document.createElement('option');
            opt.value = jam;
            opt.textContent = `Jam ke-${jam} (${slots[jam]})`;
            if (currentJamKe == jam) {
                opt.selected = true;
            }
            selectJamKe.appendChild(opt);
        }
        updateWaktuPreview();
    }

    function updateWaktuPreview() {
        const hari = selectHari.value;
        const jam = selectJamKe.value;
        const slots = (hari === 'Jumat') ? slotsJumat : slotsSeninKamis;
        if (slots[jam]) {
            previewWaktu.textContent = `${slots[jam]} (Otomatis Sesuai Alokasi KBM ${hari})`;
        } else {
            previewWaktu.textContent = '-';
        }
    }

    selectHari.addEventListener('change', updateJamOptions);
    selectJamKe.addEventListener('change', updateWaktuPreview);

    // Initial populate
    updateJamOptions();
</script>
@endsection