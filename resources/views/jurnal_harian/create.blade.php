@extends('layouts.app')

@section('content')
<div class="container">

    <h2>Tambah Jurnal Harian</h2>

    @if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('jurnal-harian.store') }}" method="POST">
        @csrf

        <div>
            <label>Guru</label><br>
            <select name="id_guru" required>
                <option value="">-- Pilih Guru --</option>
                @foreach ($guru as $g)
                    <option value="{{ $g->id_guru }}">
                        {{ $g->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <br>

        <div>
            <label>Kelas</label><br>
            <select name="id_kelas" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id_kelas }}">
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>

        <br>

        <div>
            <label>Tanggal</label><br>
            <input
                type="date"
                name="tanggal"
                value="{{ old('tanggal') }}"
                required>
        </div>

        <br>

        <div>
            <label>Mata Pelajaran</label><br>
            <input
                type="text"
                name="mata_pelajaran"
                value="{{ old('mata_pelajaran') }}"
                required>
        </div>

        <br>

        <div>
            <label>Materi</label><br>
            <textarea
                name="materi"
                rows="4"
                required>{{ old('materi') }}</textarea>
        </div>

        <br>

        <div>
            <label>Kegiatan</label><br>
            <textarea
                name="kegiatan"
                rows="5"
                required>{{ old('kegiatan') }}</textarea>
        </div>

        <br>

        <div>
            <label>Catatan</label><br>
            <textarea
                name="catatan"
                rows="4">{{ old('catatan') }}</textarea>
        </div>

        <br>

        <div>
            <label>Status</label><br>

            <select name="aktif">
                <option value="1" selected>Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
        </div>

        <br>

        <button type="submit">
            Simpan
        </button>

        <a href="{{ route('jurnal-harian.index') }}">
            Batal
        </a>

    </form>

</div>
@endsection