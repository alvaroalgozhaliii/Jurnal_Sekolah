@extends('layouts.app')

@section('title', 'Dashboard Piket — Jurnal Sekolah')
@section('page-title', 'Dashboard Piket')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Petugas Piket</h1>
        <p class="page-subtitle">Monitoring Jam Pelajaran, Kehadiran Guru, Kelas Kosong & Pengajuan Dispensasi</p>
    </div>
    <div class="page-actions" style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('piket.jadwal-piket') }}" class="btn btn-secondary" style="font-weight:600;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Jadwal Piket
        </a>
        <a href="{{ route('piket.pengajuan.create') }}" class="btn btn-primary" style="background:#1e3a8a; color:#ffffff; font-weight:600;">+ Input Dispen Siswa</a>
        <a href="{{ route('pengajuan.create') }}?tipe=guru" class="btn" style="background:#d97706; color:#ffffff; font-weight:600; padding:7px 14px; border-radius:6px; border:none;">+ Input Dispen Guru</a>
        <a href="{{ route('piket.anak-sakit') }}" class="btn btn-secondary" style="font-weight:600;">Catat Anak Sakit</a>
    </div>
</div>

@include('partials.kbm-clock-banner')

<!-- BANNER PETUGAS PIKET & WAKA HARI INI -->
<div class="card mb-20" style="border-left: 4px solid #38bdf8; background:var(--bg-card); padding:16px 20px;">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:12px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="badge" style="background:#16a34a; color:#fff; font-size:11px; font-weight:700; padding:3px 8px;">HARI INI</span>
            <strong style="font-size:15px; color:#38bdf8;">Penugasan Piket &amp; Waka Bertugas</strong>
            <span class="text-muted" style="font-size:12px;">({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }})</span>
        </div>
        <a href="{{ route('piket.jadwal-piket') }}" class="btn btn-secondary btn-sm" style="font-size:12px;">
            Lihat Kalender Piket &rarr;
        </a>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:12px;">
        <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:10px 14px;">
            <span class="text-muted" style="font-size:11px; font-weight:700; text-transform:uppercase; display:block; margin-bottom:2px;">Waka Bertugas</span>
            <strong class="text-navy" style="font-size:14px;">{{ $wakaHariIni ? $wakaHariIni->waka->nama : 'Belum Terjadwal' }}</strong>
            <div class="text-muted" style="font-size:11.5px; margin-top:2px;">
                {{ $wakaHariIni && $wakaHariIni->waka ? strtoupper(str_replace('_', ' ', $wakaHariIni->waka->role)) : 'Otomatis diarahkan ke Waka SDM / Kesiswaan' }}
                @if($wakaHariIni && $wakaHariIni->waka && $wakaHariIni->waka->no_hp)
                    &bull; <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wakaHariIni->waka->no_hp) }}" target="_blank" style="color:#16a34a; text-decoration:none; font-weight:600;">WA: {{ $wakaHariIni->waka->no_hp }}</a>
                @endif
            </div>
        </div>

        <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:10px 14px;">
            <span class="text-muted" style="font-size:11px; font-weight:700; text-transform:uppercase; display:block; margin-bottom:2px;">Guru Piket</span>
            @if($wakaHariIni && $wakaHariIni->guruPiket)
                <strong style="font-size:14px; color:var(--text-primary);">{{ $wakaHariIni->guruPiket->nama }}</strong>
                <div class="text-muted" style="font-size:11.5px; margin-top:2px;">
                    {{ $wakaHariIni->guruPiket->bidang_studi ?? 'Guru Piket' }}
                    @if($wakaHariIni->guruPiket->no_telp)
                        &bull; <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wakaHariIni->guruPiket->no_telp) }}" target="_blank" style="color:#16a34a; text-decoration:none; font-weight:600;">WA: {{ $wakaHariIni->guruPiket->no_telp }}</a>
                    @endif
                </div>
            @else
                <span class="text-muted" style="font-size:13px; font-style:italic;">Belum ditentukan</span>
            @endif
        </div>

        @if($wakaHariIni && $wakaHariIni->keterangan)
        <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:10px 14px;">
            <span class="text-muted" style="font-size:11px; font-weight:700; text-transform:uppercase; display:block; margin-bottom:2px;">Catatan Penugasan</span>
            <div style="font-size:12.5px; color:var(--text-primary);">{{ $wakaHariIni->keterangan }}</div>
        </div>
        @endif
    </div>
</div>

<!-- WARNING KELAS KOSONG -->
@if(count($kelasKosong) > 0)
    <div class="alert alert-danger mb-24">
        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        <div>
            <strong class="d-block mb-8">Peringatan Kelas Kosong Saat Ini:</strong>
            <ul style="margin-left: 16px;">
                @foreach($kelasKosong as $kk)
                    <li>{{ $kk['pesan'] }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!-- STATISTIK KEHADIRAN GURU & SISWA -->
<div class="grid-4 mb-24">
    <div class="stat-card" style="border-left: 4px solid #0284c7;">
        <div class="stat-icon-box blue">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #0284c7;">{{ $totalIzinOrtuHariIni }}</div>
            <div class="stat-label">Izin Siswa dari Ortu Hari Ini</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #16a34a;">
        <div class="stat-icon-box green">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #16a34a;">{{ $jumlahGuruHadir }}</div>
            <div class="stat-label">Guru Hadir Hari Ini</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #d97706;">
        <div class="stat-icon-box amber">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #d97706;">{{ $totalPendingWaka }}</div>
            <div class="stat-label">Dispen Menunggu Waka</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #9333ea;">
        <div class="stat-icon-box purple">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #9333ea;">{{ $totalVerifiedSatpam }}</div>
            <div class="stat-label">Dispen Selesai</div>
        </div>
    </div>
</div>

@if(isset($izinOrtuHariIniList) && $izinOrtuHariIniList->count() > 0)
<!-- DAFTAR IZIN ORTU TERBARU -->
<div class="card mb-24">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 class="card-title" style="color: #38bdf8;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            Daftar Izin Siswa dari Orang Tua (Tercatat Otomatis di Kelas)
        </h3>
        <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary btn-sm">Lihat Semua &rarr;</a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tanggal Izin</th>
                        <th>Keterangan / Alasan</th>
                        <th>Status</th>
                        <th>Lampiran</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($izinOrtuHariIniList as $idx => $iz)
                    <tr>
                        <td class="no-col">{{ $idx + 1 }}</td>
                        <td>
                            <span class="badge {{ $iz->kategori === 'sakit' ? 'badge-purple' : 'badge-info' }}">
                                {{ $iz->kategori === 'sakit' ? 'IZIN SAKIT' : 'IZIN' }}
                            </span>
                        </td>
                        <td class="fw-bold text-navy">{{ $iz->siswa->nama ?? '-' }}</td>
                        <td>{{ $iz->siswa->kelas->nama_kelas ?? '-' }}</td>
                        <td>{{ $iz->tanggal }}</td>
                        <td>{{ Str::limit($iz->alasan, 40) }}</td>
                        <td><span class="badge badge-success">TERCATAT DI KELAS</span></td>
                        <td>
                            @if($iz->lampiran_foto)
                                <a href="{{ asset('storage/' . $iz->lampiran_foto) }}" target="_blank" class="btn btn-secondary btn-sm">Lihat Bukti</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="action-col">
                            <a href="{{ route('pengajuan.show', $iz->id_pengajuan) }}" class="btn btn-primary btn-sm">
                                Detail &rarr;
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- TABEL MONITORING JADWAL HARI INI -->
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
            Jadwal Mengajar KBM Hari Ini ({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }})
        </h3>
        @if($jadwalHariIni->count() > 10)
        <div style="display:flex; align-items:center; gap:8px;">
            <button type="button" id="jadwalPrevBtn" class="btn btn-secondary btn-sm" style="padding:4px 10px; display:inline-flex; align-items:center; gap:4px; font-weight:600;">
                &larr; Prev
            </button>
            <span id="jadwalSlideInfo" style="font-size:12.5px; font-weight:600; color:var(--text-secondary);">Slide 1</span>
            <button type="button" id="jadwalNextBtn" class="btn btn-secondary btn-sm" style="padding:4px 10px; display:inline-flex; align-items:center; gap:4px; font-weight:600;">
                Next &rarr;
            </button>
        </div>
        @endif
    </div>
    <div class="card-body" style="padding:0;">
        @if($jadwalHariIni->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table" id="tableJadwalPiket">
                <thead>
                    <tr>
                        <th class="no-col">Jam</th>
                        <th>Waktu</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalHariIni as $j)
                    <tr class="jadwal-row">
                        <td class="no-col fw-bold">{{ $j->jam_ke }}</td>
                        <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
                        <td><span class="badge badge-navy">{{ $j->kelas->nama_kelas ?? '-' }}</span></td>
                        <td class="fw-bold text-navy">{{ $j->mapel }}</td>
                        <td>{{ $j->guru->nama ?? 'Belum ditentukan' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($jadwalHariIni->count() > 10)
        <div class="card-footer" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:12px 18px; border-top:1px solid var(--border);">
            <div style="font-size:12.5px; color:var(--text-secondary);">
                Menampilkan <strong id="jadwalSlideDetail" style="color:var(--text-primary);">1 - 10</strong> dari <strong style="color:var(--text-primary);">{{ $jadwalHariIni->count() }}</strong> jadwal hari ini (10 data per slide)
            </div>
            <div style="display:flex; align-items:center; gap:6px;" id="jadwalSlideDots">
                {{-- Diisi dinamis oleh JavaScript --}}
            </div>
        </div>
        @endif

        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada jadwal KBM untuk hari ini.</div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.jadwal-row');
    const pageSize = 10;
    const totalRows = rows.length;
    if (totalRows <= pageSize) return;

    let currentSlide = 1;
    const totalSlides = Math.ceil(totalRows / pageSize);

    const prevBtn = document.getElementById('jadwalPrevBtn');
    const nextBtn = document.getElementById('jadwalNextBtn');
    const slideInfo = document.getElementById('jadwalSlideInfo');
    const slideDetail = document.getElementById('jadwalSlideDetail');
    const slideDots = document.getElementById('jadwalSlideDots');

    function showSlide(slide) {
        if (slide < 1 || slide > totalSlides) return;
        currentSlide = slide;
        const start = (slide - 1) * pageSize;
        const end = start + pageSize;

        rows.forEach((row, idx) => {
            if (idx >= start && idx < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        if (slideInfo) {
            slideInfo.textContent = `Slide ${slide} dari ${totalSlides}`;
        }

        if (slideDetail) {
            const startNum = start + 1;
            const endNum = Math.min(end, totalRows);
            slideDetail.textContent = `${startNum} - ${endNum}`;
        }

        if (prevBtn) {
            prevBtn.disabled = slide === 1;
            prevBtn.style.opacity = slide === 1 ? '0.4' : '1';
            prevBtn.style.cursor = slide === 1 ? 'not-allowed' : 'pointer';
        }
        if (nextBtn) {
            nextBtn.disabled = slide === totalSlides;
            nextBtn.style.opacity = slide === totalSlides ? '0.4' : '1';
            nextBtn.style.cursor = slide === totalSlides ? 'not-allowed' : 'pointer';
        }

        renderDots();
    }

    function renderDots() {
        if (!slideDots) return;
        slideDots.innerHTML = '';

        let pages = [];
        if (totalSlides <= 5) {
            for (let i = 1; i <= totalSlides; i++) pages.push(i);
        } else {
            pages.push(1);
            if (currentSlide > 3) {
                pages.push('...');
            }
            const startRange = Math.max(2, currentSlide - 1);
            const endRange = Math.min(totalSlides - 1, currentSlide + 1);
            for (let i = startRange; i <= endRange; i++) {
                pages.push(i);
            }
            if (currentSlide < totalSlides - 2) {
                pages.push('...');
            }
            pages.push(totalSlides);
        }

        // Tombol Prev ‹
        const prevPageBtn = document.createElement('button');
        prevPageBtn.type = 'button';
        prevPageBtn.innerHTML = '&lsaquo;';
        prevPageBtn.title = 'Slide Sebelumnya';
        prevPageBtn.disabled = currentSlide === 1;
        prevPageBtn.style.cssText = 'width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 16px; font-weight: 700; background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border); cursor: ' + (currentSlide === 1 ? 'not-allowed; opacity: 0.4;' : 'pointer;');
        prevPageBtn.addEventListener('click', () => {
            if (currentSlide > 1) showSlide(currentSlide - 1);
        });
        slideDots.appendChild(prevPageBtn);

        // Angka & Ellipsis …
        pages.forEach(p => {
            if (p === '...') {
                const span = document.createElement('span');
                span.textContent = '…';
                span.style.cssText = 'display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 32px; color: var(--text-secondary); font-size: 13px; font-weight: 600; user-select: none;';
                slideDots.appendChild(span);
            } else {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = p;
                if (p === currentSlide) {
                    btn.style.cssText = 'min-width: 32px; height: 32px; padding: 0 8px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 12.5px; font-weight: 700; background: #2563eb; color: #ffffff; border: 1px solid #2563eb; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3); cursor: default;';
                } else {
                    btn.style.cssText = 'min-width: 32px; height: 32px; padding: 0 8px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 12.5px; font-weight: 600; background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border); cursor: pointer; transition: all 0.15s ease;';
                    btn.onmouseover = () => { btn.style.background = 'rgba(59, 130, 246, 0.1)'; btn.style.borderColor = '#3b82f6'; };
                    btn.onmouseout = () => { btn.style.background = 'var(--bg-card)'; btn.style.borderColor = 'var(--border)'; };
                }
                btn.addEventListener('click', () => showSlide(p));
                slideDots.appendChild(btn);
            }
        });

        // Tombol Next ›
        const nextPageBtn = document.createElement('button');
        nextPageBtn.type = 'button';
        nextPageBtn.innerHTML = '&rsaquo;';
        nextPageBtn.title = 'Slide Selanjutnya';
        nextPageBtn.disabled = currentSlide === totalSlides;
        nextPageBtn.style.cssText = 'width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 16px; font-weight: 700; background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border); cursor: ' + (currentSlide === totalSlides ? 'not-allowed; opacity: 0.4;' : 'pointer;');
        nextPageBtn.addEventListener('click', () => {
            if (currentSlide < totalSlides) showSlide(currentSlide + 1);
        });
        slideDots.appendChild(nextPageBtn);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentSlide > 1) showSlide(currentSlide - 1);
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentSlide < totalSlides) showSlide(currentSlide + 1);
        });
    }

    showSlide(1);
});
</script>
@endpush
@endsection
