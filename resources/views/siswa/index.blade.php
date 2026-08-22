@extends('layouts.app')

@section('title', 'Data Siswa — Jurnal Sekolah')
@section('page-title', 'Data Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Siswa</h1>
        <p class="page-subtitle">Kelola Master Data Siswa & Akun Orang Tua</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('siswa.create') }}" class="btn btn-primary">+ Tambah Siswa</a>
        <a href="{{ route('siswa.trash') }}" class="btn btn-secondary">Lihat Trash</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th>Kelas</th>
                        <th>JK</th>
                        <th>Status</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswa as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="text-muted fw-bold">{{ $item->nis }}</td>
                        <td class="fw-bold text-navy">{{ $item->nama }}</td>
                        <td><span class="badge badge-navy">{{ $item->kelas->nama_kelas ?? '-' }}</span></td>
                        <td>{{ $item->jenis_kelamin ?? '-' }}</td>
                        <td>
                            @if($item->aktif)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="action-col">
                            <a href="{{ route('siswa.show', $item->id_siswa) }}" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="{{ route('siswa.edit', $item->id_siswa) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('siswa.destroy', $item->id_siswa) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data siswa ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
