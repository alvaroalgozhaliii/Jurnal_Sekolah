<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PengajuanIzin;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\DispenLog;
use App\Models\JadwalWaka;
use App\Services\WhatsAppService;

class PengajuanIzinController extends Controller
{
    protected WhatsAppService $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PengajuanIzin::with(['siswa.kelas', 'guru', 'pengaju', 'wakaApprover', 'satpam']);

        if ($user->isOrtu()) {
            $anakIds = $user->getAnakList()->pluck('id_siswa');
            $query->where(function ($q) use ($anakIds, $user) {
                $q->whereIn('id_siswa', $anakIds)
                  ->orWhere('id_user_pengaju', $user->id_user);
            });
        } elseif ($user->isGuru() && !$user->isAdmin() && !$user->isPiket() && !$user->isWaka()) {
            $idGuru = $user->guru?->id_guru;
            $query->where(function ($q) use ($idGuru, $user) {
                if ($idGuru) $q->where('id_guru', $idGuru);
                $q->orWhere('id_user_pengaju', $user->id_user);
            });
        } elseif ($user->isPiket()) {
            // Piket can monitor all
        } elseif ($user->isWaka()) {
            // Waka can monitor all
        } elseif ($user->isSatpam()) {
            $query->where('butuh_satpam', true)
                  ->whereIn('status', ['disetujui_waka', 'pending_satpam', 'verified', 'ditolak_satpam', 'completed']);
        }

        $pengajuanList = $query->orderBy('created_at', 'desc')->get();
        return view('pengajuan.index', compact('pengajuanList'));
    }

    public function create()
    {
        $user = Auth::user();
        $siswas = collect();
        $gurus = \App\Models\Guru::orderBy('nama', 'asc')->get();

        if ($user->isOrtu()) {
            $siswas = $user->getAnakList();
        } else {
            $siswas = Siswa::with('kelas')->where('aktif', 1)->orderBy('nama', 'asc')->get();
        }

        $wakaHariIni = JadwalWaka::wakaBertugasPada(date('Y-m-d'));

        return view('pengajuan.create', compact('siswas', 'gurus', 'wakaHariIni'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Fallback jika kategori tidak terkirim dari form
        if (!$request->filled('kategori')) {
            if ($request->filled('id_guru') || $user->isGuru()) {
                $request->merge(['kategori' => 'izin_guru']);
            } elseif ($user->isOrtu()) {
                $request->merge(['kategori' => 'sakit']);
            } else {
                $request->merge(['kategori' => 'dispensasi']);
            }
        }

        $request->validate([
            'kategori' => 'required|in:dispen_masuk,dispen_keluar,dispen_lomba,terlambat,dispensasi,izin_masuk,izin_keluar,sakit,izin_guru,acara_keluarga,izin',
            'id_siswa' => 'nullable|exists:siswa,id_siswa',
            'id_guru' => 'nullable|exists:guru,id_guru',
            'tanggal' => 'required|date',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'perkiraan_kembali' => 'nullable',
            'jenis_izin' => 'nullable|string|max:100',
            'alasan' => 'required|string',
            'keterangan' => 'nullable|string',
            'lampiran_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $isGuruDispen = ($request->kategori === 'izin_guru');
        $idGuru = $isGuruDispen ? ($request->id_guru ?? ($user->isGuru() ? $user->guru?->id_guru : null)) : null;
        $idSiswa = $isGuruDispen ? null : $request->id_siswa;

        // Cari Waka Bertugas dari Jadwal yang diatur oleh Waka Kurikulum
        $jadwalHariIni = JadwalWaka::wakaBertugasPada($request->tanggal);
        $wakaTujuanUser = $jadwalHariIni?->waka;

        // Fallback jika belum ada jadwal khusus pada tanggal tersebut
        if (!$wakaTujuanUser) {
            $fallbackRole = $isGuruDispen ? 'waka_sdm' : 'waka_kesiswaan';
            $wakaTujuanUser = User::where('role', $fallbackRole)->where('aktif', 1)->first()
                           ?? User::where('role', 'waka_sdm')->where('aktif', 1)->first()
                           ?? User::where('role', 'waka_kurikulum')->where('aktif', 1)->first()
                           ?? User::where('role', 'waka_kesiswaan')->where('aktif', 1)->first();
        }

        $fotoPath = null;
        if ($request->hasFile('lampiran_foto')) {
            $file = $request->file('lampiran_foto');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $fotoPath = $file->storeAs('uploads/bukti_sakit', $filename, 'public');
        }

        // Tentukan alur approval
        // Jika dibuat oleh Orang Tua atau kategori izin (sakit, izin, acara_keluarga, terlambat, izin_guru): langsung sah/DISETUJUI tanpa proses approval
        $isOrtuFlow = $user->isOrtu() || in_array($request->kategori, ['sakit', 'izin', 'acara_keluarga', 'terlambat', 'izin_guru']);

        // Untuk Dispen Siswa (dispensasi, dispen_masuk, dispen_keluar, dispen_lomba, izin_masuk, izin_keluar):
        // Wajib ada persetujuan dari Guru Piket DAN Waka Piket Hari Ini.
        // Jika dibuat oleh Guru Piket/Admin: Guru Piket otomatis menyetujui sebagai pembuat (status: pending_waka)
        // Jika dibuat oleh Siswa/Ortu: Menunggu Guru Piket lebih dulu (status: pending_piket)
        $isStudentDispen = !$isOrtuFlow && in_array($request->kategori, ['dispensasi', 'izin_keluar', 'izin_masuk', 'dispen_masuk', 'dispen_keluar', 'dispen_lomba']);

        $statusAwal = $isOrtuFlow ? 'completed' : (($user->isPiket() || $user->isAdmin()) ? 'pending_waka' : 'pending_piket');
        $butuhSatpam = $isStudentDispen;
        $idWakaTujuan = $isOrtuFlow ? null : $wakaTujuanUser?->id_user;

        $idPiketApprover = ($isStudentDispen && ($user->isPiket() || $user->isAdmin())) ? $user->id_user : null;
        $tglPiket = ($isOrtuFlow || ($isStudentDispen && ($user->isPiket() || $user->isAdmin()))) ? now() : null;
        $catatanPiket = $isOrtuFlow
            ? 'Disetujui otomatis oleh sistem (' . \App\Helpers\DispenHelper::kategoriLabel($request->kategori) . ').'
            : (($isStudentDispen && ($user->isPiket() || $user->isAdmin())) ? 'Diajukan & disetujui oleh Guru Piket (Diteruskan ke Waka Piket Hari Ini)' : null);

        $pengajuan = PengajuanIzin::create([
            'kategori' => $request->kategori,
            'id_siswa' => $idSiswa,
            'id_guru' => $idGuru,
            'id_user_pengaju' => $user->id_user,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'perkiraan_kembali' => $request->perkiraan_kembali,
            'jenis_izin' => $request->jenis_izin ?? \App\Helpers\DispenHelper::kategoriLabel($request->kategori),
            'alasan' => $request->alasan,
            'keterangan' => $request->keterangan,
            'lampiran_foto' => $fotoPath,
            'status' => $statusAwal,
            'butuh_satpam' => $butuhSatpam,
            'id_waka_tujuan' => $idWakaTujuan,
            'id_piket_approver' => $idPiketApprover,
            'catatan_piket' => $catatanPiket,
            'tgl_piket' => $tglPiket,
        ]);

        // Catat Log Riwayat
        DispenLog::catat(
            $pengajuan->id_pengajuan,
            $user->id_user,
            $user->role,
            null,
            $statusAwal,
            $isOrtuFlow ? ('Dicatat langsung oleh ' . $user->nama . ' (' . \App\Helpers\DispenHelper::kategoriLabel($request->kategori) . ')') : ('Pengajuan ' . \App\Helpers\DispenHelper::kategoriLabel($request->kategori) . ' dibuat oleh ' . $user->nama)
        );

        $namaKategoriText = \App\Helpers\DispenHelper::kategoriLabel($request->kategori);

        if ($isOrtuFlow) {
            if ($isGuruDispen) {
                $guruObj = Guru::find($idGuru);
                $namaGuru = $guruObj?->nama ?? $user->nama;
                Notifikasi::kirimKeRole(
                    'piket',
                    'Pemberitahuan Izin Guru',
                    'Guru ' . $namaGuru . ' telah dicatat Izin Guru pada tanggal ' . $pengajuan->tanggal . ' dan disetujui otomatis.',
                    route('pengajuan.show', $pengajuan->id_pengajuan),
                    'izin'
                );
                $flashMessage = "Pengajuan Izin Guru berhasil dibuat dan langsung disetujui otomatis.";
            } else {
                $siswa = Siswa::with('kelas')->find($idSiswa);
                $namaSiswa = $siswa?->nama ?? 'Siswa';
                $tanggalIzin = $pengajuan->tanggal;
                $statusAbsensi = match($pengajuan->kategori) {
                    'sakit' => 'sakit',
                    'terlambat' => 'terlambat',
                    default => 'izin'
                };

                // Sinkronisasi langsung ke data Absensi Siswa
                if ($siswa) {
                    // Jika terlambat, juga catat ke tabel siswa_terlambat (log piket)
                    if ($pengajuan->kategori === 'terlambat') {
                        \App\Models\SiswaTerlambat::create([
                            'id_siswa' => $siswa->id_siswa,
                            'id_kelas' => $siswa->id_kelas,
                            'tanggal' => $tanggalIzin,
                            'jam_kedatangan' => $pengajuan->jam_mulai ?: date('H:i:s'),
                            'alasan' => $pengajuan->alasan,
                            'id_petugas_piket' => $user->id_user,
                        ]);
                    }

                    // Cari Jurnal Harian hari ini untuk kelas siswa tersebut jika ada
                    $jurnal = \App\Models\JurnalHarian::where('tanggal', $tanggalIzin)
                        ->whereHas('jadwal', function ($q) use ($siswa) {
                            $q->where('id_kelas', $siswa->id_kelas);
                        })
                        ->first();

                    // Jika belum ada jurnal harian, cari jadwal kelas pertama jika ada
                    if (!$jurnal && $siswa->id_kelas) {
                        $jadwalFirst = \App\Models\Jadwal::where('id_kelas', $siswa->id_kelas)->where('aktif', 1)->first();
                        if ($jadwalFirst) {
                            $jurnal = \App\Models\JurnalHarian::firstOrCreate(
                                [
                                    'id_jadwal' => $jadwalFirst->id_jadwal,
                                    'tanggal' => $tanggalIzin,
                                ],
                                [
                                    'id_guru' => $jadwalFirst->id_guru ?? \App\Models\Guru::first()?->id_guru,
                                    'materi' => 'Presensi Kelas (' . $namaKategoriText . ')',
                                    'jam_ke' => $jadwalFirst->jam_ke ?? 1,
                                ]
                            );
                        }
                    }

                    $idJurnal = $jurnal ? $jurnal->id_jurnal : null;

                    \App\Models\AbsensiSiswa::updateOrCreate(
                        [
                            'id_siswa' => $siswa->id_siswa,
                            'created_at' => $tanggalIzin . ' 07:00:00',
                        ],
                        [
                            'id_jurnal' => $idJurnal,
                            'status' => $statusAbsensi,
                            'jam_masuk' => $pengajuan->jam_mulai,
                            'keterangan' => ($pengajuan->alasan ? $pengajuan->alasan . ' ' : '') . "(Tercatat: {$namaKategoriText})",
                            'dicatat_oleh' => $user->id_user,
                            'created_at' => $tanggalIzin . ' 07:00:00',
                        ]
                    );
                }

                // Notifikasi ke Guru Piket
                Notifikasi::kirimKeRole(
                    'piket',
                    'Pemberitahuan ' . $namaKategoriText . ' Siswa (Langsung Disetujui)',
                    'Siswa ' . $namaSiswa . ' (' . ($siswa?->kelas->nama_kelas ?? '-') . ') telah dicatat ' . $namaKategoriText . ' pada tanggal ' . $pengajuan->tanggal . ' dan telah disetujui otomatis.',
                    route('pengajuan.show', $pengajuan->id_pengajuan),
                    'izin'
                );

                // Notifikasi ke Wali Kelas jika ada
                if ($siswa && $siswa->kelas && $siswa->kelas->id_guru_walikelas) {
                    $guruWali = Guru::find($siswa->kelas->id_guru_walikelas);
                    if ($guruWali && $guruWali->id_user) {
                        Notifikasi::kirim(
                            $guruWali->id_user,
                            'Pemberitahuan ' . $namaKategoriText . ' Siswa Kelas',
                            'Siswa ' . $namaSiswa . ' tercatat ' . $namaKategoriText . ' pada tanggal ' . $pengajuan->tanggal . ' dan langsung masuk ke data kelas.',
                            route('walikelas.data-kelas'),
                            'izin'
                        );
                    }
                }

                $flashMessage = "Pengajuan {$namaKategoriText} berhasil dibuat dan otomatis tercatat di data kehadiran siswa.";
            }
        } else {
            // Kirim WhatsApp ke Waka Bertugas
            $waResult = $this->waService->kirimNotifDispenKeWaka($pengajuan->load(['siswa.kelas', 'guru', 'pengaju', 'wakaTujuan']));

            // Kirim Notifikasi Sistem Internal ke Waka
            if ($wakaTujuanUser) {
                Notifikasi::kirim(
                    $wakaTujuanUser->id_user,
                    'Pengajuan Dispen Baru (' . ($isGuruDispen ? 'Guru' : 'Siswa') . ')',
                    'Ada pengajuan ' . ($isGuruDispen ? 'dispen guru' : 'dispen siswa') . ' baru (' . $namaKategoriText . ') menunggu persetujuan Anda.',
                    route('waka.persetujuan.show', $pengajuan->id_pengajuan),
                    'dispen'
                );
            }

            $wakaNama = $wakaTujuanUser ? $wakaTujuanUser->nama : 'Waka';
            $flashMessage = "Pengajuan dispensasi berhasil dibuat dan diteruskan ke {$wakaNama}.";
            if (isset($waResult) && !$waResult['success']) {
                $flashMessage .= ' (WhatsApp: ' . $waResult['message'] . ')';
            }
        }

        return redirect()->route('pengajuan.show', $pengajuan->id_pengajuan)->with('success', $flashMessage);
    }

    public function approvePiket(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isPiket() && !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk memverifikasi pengajuan ini.');
        }

        $pengajuan = PengajuanIzin::with(['siswa.kelas'])->findOrFail($id);
        $request->validate([
            'catatan' => 'nullable|string|required_if:keputusan,tolak',
            'keputusan' => 'required|in:setujui,tolak'
        ]);

        $statusSebelum = $pengajuan->status;
        $siswa = $pengajuan->siswa;
        $namaSiswa = $siswa?->nama ?? 'Siswa';
        $namaKategori = match($pengajuan->kategori) {
            'sakit' => 'Izin Sakit',
            'izin' => 'Izin',
            default => ucfirst(str_replace('_', ' ', $pengajuan->kategori))
        };

        if ($request->keputusan === 'setujui') {
            // Piket menyetujui → teruskan ke Waka yang bertugas hari ini untuk persetujuan akhir
            $statusSesudah = 'pending_waka';

            // Cari Waka yang bertugas pada tanggal pengajuan
            $jadwalWakaPiket = JadwalWaka::wakaBertugasPada($pengajuan->tanggal ?? now()->toDateString());
            $wakaTujuanPiket = $jadwalWakaPiket?->waka;
            if (!$wakaTujuanPiket) {
                $wakaTujuanPiket = User::where('role', 'waka_kesiswaan')->where('aktif', 1)->first()
                               ?? User::where('role', 'waka_sdm')->where('aktif', 1)->first();
            }
            $idWakaTujuan = $wakaTujuanPiket?->id_user ?? $pengajuan->id_waka_tujuan;

            $pengajuan->update([
                'status'            => $statusSesudah,
                'id_piket_approver' => $user->id_user,
                'catatan_piket'     => $request->catatan ?? 'Disetujui oleh Guru Piket, diteruskan ke Waka Piket Hari Ini.',
                'tgl_piket'         => now(),
                'id_waka_tujuan'    => $idWakaTujuan,
            ]);

            // Catat Log
            DispenLog::catat(
                $pengajuan->id_pengajuan,
                $user->id_user,
                $user->role,
                $statusSebelum,
                $statusSesudah,
                $request->catatan ?? 'Disetujui oleh Guru Piket — menunggu persetujuan Waka'
            );

            // Notifikasi ke Waka yang bertugas
            if ($wakaTujuanPiket) {
                Notifikasi::kirim(
                    $wakaTujuanPiket->id_user,
                    'Pengajuan Izin Siswa Menunggu Persetujuan Anda',
                    'Pengajuan izin siswa ' . $namaSiswa . ' telah disetujui Guru Piket dan menunggu persetujuan Waka.',
                    route('pengajuan.show', $pengajuan->id_pengajuan),
                    'dispen'
                );
            }

            // Notifikasi ke Pengaju
            if ($pengajuan->id_user_pengaju) {
                Notifikasi::kirim(
                    $pengajuan->id_user_pengaju,
                    'Pengajuan ' . $namaKategori . ' Disetujui Piket',
                    'Pengajuan ' . $namaKategori . ' anak Anda (' . $namaSiswa . ') telah disetujui Guru Piket dan kini menunggu persetujuan Waka.',
                    route('pengajuan.show', $pengajuan->id_pengajuan),
                    'izin'
                );
            }

            // Kirim WhatsApp ke Waka
            $this->waService->kirimNotifDispenKeWaka($pengajuan->load(['siswa.kelas', 'guru', 'pengaju', 'wakaTujuan']));

            return redirect()->route('pengajuan.show', $pengajuan->id_pengajuan)->with('success', "Pengajuan {$namaKategori} untuk {$namaSiswa} telah DISETUJUI oleh Piket dan diteruskan ke Waka untuk persetujuan akhir.");
        } else {
            $statusSesudah = 'ditolak_piket';
            $pengajuan->update([
                'status' => $statusSesudah,
                'id_piket_approver' => $user->id_user,
                'catatan_piket' => $request->catatan,
                'alasan_penolakan' => $request->catatan,
                'tgl_piket' => now(),
            ]);

            DispenLog::catat(
                $pengajuan->id_pengajuan,
                $user->id_user,
                $user->role,
                $statusSebelum,
                $statusSesudah,
                $request->catatan ?? 'Ditolak oleh Guru Piket'
            );

            // Notifikasi ke Orang Tua
            if ($pengajuan->id_user_pengaju) {
                Notifikasi::kirim(
                    $pengajuan->id_user_pengaju,
                    'Pengajuan ' . $namaKategori . ' Ditolak',
                    'Pengajuan ' . $namaKategori . ' anak Anda (' . $namaSiswa . ') ditolak oleh Guru Piket. Alasan: ' . ($request->catatan ?? '-'),
                    route('pengajuan.show', $pengajuan->id_pengajuan),
                    'izin'
                );
            }

            return redirect()->route('pengajuan.show', $pengajuan->id_pengajuan)->with('success', "Pengajuan {$namaKategori} untuk {$namaSiswa} telah DITOLAK.");
        }
    }

    public function show($id)
    {
        $pengajuan = PengajuanIzin::with([
            'siswa.kelas.jurusan',
            'guru',
            'pengaju',
            'piketApprover',
            'wakaApprover',
            'kepalaApprover',
            'satpam',
            'logs.user'
        ])->findOrFail($id);

        return view('pengajuan.show', compact('pengajuan'));
    }

    public function approveWaka(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isWaka() && !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyetujui pengajuan ini.');
        }

        $pengajuan = PengajuanIzin::findOrFail($id);

        // Tentukan Waka yang berwenang menyetujui berdasarkan JadwalWaka pada tanggal pengajuan
        if (!$user->isAdmin()) {
            $tanggalPengajuan = $pengajuan->tanggal ?? $pengajuan->created_at?->toDateString() ?? now()->toDateString();
            $jadwalHariItu    = JadwalWaka::wakaBertugasPada($tanggalPengajuan);
            $wakaYangBerhak   = $jadwalHariItu?->waka;

            // Fallback: jika tidak ada jadwal, gunakan id_waka_tujuan yang tersimpan di pengajuan
            if (!$wakaYangBerhak && $pengajuan->id_waka_tujuan) {
                $wakaYangBerhak = User::find($pengajuan->id_waka_tujuan);
            }

            // Validasi: user yang login harus cocok dengan waka yang berwenang
            if ($wakaYangBerhak && (int) $wakaYangBerhak->id_user !== (int) $user->id_user) {
                $namaWakaBerhak = $wakaYangBerhak->nama ?? 'Waka Piket';
                return redirect()->back()->with('error',
                    "Pengajuan ini hanya bisa disetujui oleh Waka Piket pada tanggal tersebut: {$namaWakaBerhak}. " .
                    "Silakan hubungi Waka yang sedang bertugas."
                );
            }
        }

        $request->validate([
            'catatan'   => 'nullable|string|required_if:keputusan,tolak',
            'keputusan' => 'required|in:setujui,tolak'
        ]);

        $statusSebelum = $pengajuan->status;
        $isPiketFlow   = (bool) $pengajuan->id_waka_tujuan;

        if ($request->keputusan === 'setujui') {
            // Jika perlu verifikasi satpam → menunggu_satpam, jika tidak → completed langsung
            $statusSesudah = $pengajuan->butuh_satpam ? 'menunggu_satpam' : 'completed';
            $pengajuan->update([
                'status' => $statusSesudah,
                'id_waka_approver' => $user->id_user,
                'catatan_waka' => $request->catatan,
                'alasan_penolakan' => null,
                'tgl_waka' => now(),
            ]);

            // Catat log
            DispenLog::catat(
                $pengajuan->id_pengajuan,
                $user->id_user,
                $user->role,
                $statusSebelum,
                $statusSesudah,
                $request->catatan ?? 'Disetujui oleh Waka'
            );

            // ==========================================
            // SINKRONISASI KE DATA KELAS / ABSENSI SISWA
            // ==========================================
            $siswaWaka = $pengajuan->load('siswa.kelas')->siswa;
            if ($siswaWaka) {
                $tanggalIzin = $pengajuan->tanggal;
                $statusAbsensi = ($pengajuan->kategori === 'sakit') ? 'sakit' : 'izin';

                $jurnal = \App\Models\JurnalHarian::where('tanggal', $tanggalIzin)
                    ->whereHas('jadwal', function ($q) use ($siswaWaka) {
                        $q->where('id_kelas', $siswaWaka->id_kelas);
                    })->first();

                if (!$jurnal) {
                    $jadwalFirst = \App\Models\Jadwal::where('id_kelas', $siswaWaka->id_kelas)->where('aktif', 1)->first();
                    if ($jadwalFirst) {
                        $jurnal = \App\Models\JurnalHarian::firstOrCreate(
                            ['id_jadwal' => $jadwalFirst->id_jadwal, 'tanggal' => $tanggalIzin],
                            [
                                'id_guru' => $jadwalFirst->id_guru ?? Guru::first()?->id_guru,
                                'materi'  => 'Presensi Kelas (Disetujui Waka)',
                                'jam_ke'  => $jadwalFirst->jam_ke ?? 1,
                            ]
                        );
                    }
                }

                if ($jurnal) {
                    \App\Models\AbsensiSiswa::updateOrCreate(
                        ['id_jurnal' => $jurnal->id_jurnal, 'id_siswa' => $siswaWaka->id_siswa],
                        [
                            'status'      => $statusAbsensi,
                            'keterangan'  => ($pengajuan->alasan ? $pengajuan->alasan . ' ' : '') . ($request->catatan ? '(Waka: ' . $request->catatan . ')' : '(Disetujui Waka)'),
                            'dicatat_oleh' => $user->id_user,
                            'created_at'  => now(),
                        ]
                    );
                }
            }

            // Notifikasi in-app ke Piket
            Notifikasi::kirimKeRole(
                'piket',
                'Izin Siswa Disetujui Waka',
                'Pengajuan izin ' . ($pengajuan->siswa?->nama ?? $pengajuan->guru?->nama ?? 'Siswa/Guru') . ' telah disetujui Waka' . ($pengajuan->butuh_satpam ? ' dan menunggu verifikasi Satpam.' : ' dan selesai.'),
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'dispen'
            );

            // Notifikasi in-app ke Pengaju
            if ($pengajuan->id_user_pengaju) {
                Notifikasi::kirim(
                    $pengajuan->id_user_pengaju,
                    'Pengajuan Izin Disetujui Waka',
                    $pengajuan->butuh_satpam
                        ? 'Pengajuan izin Anda telah disetujui oleh Waka. Silakan verifikasi identitas ke Satpam saat keluar gerbang.'
                        : 'Pengajuan izin Anda telah disetujui oleh Waka dan telah dicatat ke data presensi.',
                    route('pengajuan.show', $pengajuan->id_pengajuan),
                    'izin'
                );
            }

            // Notifikasi in-app ke Satpam jika butuh satpam
            if ($pengajuan->butuh_satpam) {
                Notifikasi::kirimKeRole(
                    'satpam',
                    'Verifikasi Izin Baru (Acc Waka)',
                    'Pengajuan izin ' . ($pengajuan->siswa?->nama ?? $pengajuan->guru?->nama ?? '') . ' telah disetujui Waka. Menunggu verifikasi gerbang.',
                    route('satpam.show', $pengajuan->id_pengajuan),
                    'satpam'
                );
                // Kirim WhatsApp ke Satpam
                $waResult = $this->waService->kirimNotifDispenKeSatpam($pengajuan->load(['siswa.kelas', 'guru', 'pengaju']));
            }

            $msg = 'Pengajuan berhasil DISETUJUI oleh Waka.';
            if (isset($waResult) && !$waResult['success']) {
                $msg .= ' (Notifikasi WA Satpam: ' . $waResult['message'] . ')';
            }

            return redirect()->route('pengajuan.index')->with('success', $msg);
        } else {
            $statusSesudah = 'ditolak_waka';
            $pengajuan->update([
                'status' => $statusSesudah,
                'id_waka_approver' => $user->id_user,
                'catatan_waka' => $request->catatan,
                'alasan_penolakan' => $request->catatan,
                'tgl_waka' => now(),
            ]);

            $waResult = $this->waService->kirimNotifPenolakanWaka($pengajuan->load(['siswa', 'guru', 'pengaju']));

            // Catat log
            DispenLog::catat(
                $pengajuan->id_pengajuan,
                $user->id_user,
                $user->role,
                $statusSebelum,
                $statusSesudah,
                $request->catatan ?? 'Ditolak oleh Waka'
            );

            // Notifikasi in-app ke Piket
            Notifikasi::kirimKeRole(
                'piket',
                'Dispen Ditolak Waka',
                'Pengajuan dispen ' . ($pengajuan->siswa?->nama ?? $pengajuan->guru?->nama ?? '') . ' ditolak oleh Waka.',
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'dispen'
            );

            // Notifikasi in-app ke Pengaju
            Notifikasi::kirim(
                $pengajuan->id_user_pengaju,
                'Pengajuan Dispen Ditolak Waka',
                'Pengajuan dispen Anda ditolak oleh Waka. Alasan: ' . ($request->catatan ?? '-'),
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'dispen'
            );

            $message = 'Pengajuan DITOLAK oleh Waka.';
            if (!$waResult['success']) $message .= ' WhatsApp: ' . $waResult['message'];
            return redirect()->route('pengajuan.index')->with('success', $message);
        }
    }

    public function resendWa(Request $request, $id)
    {
        $pengajuan = PengajuanIzin::with(['siswa.kelas', 'guru', 'pengaju'])->findOrFail($id);
        $target = $request->input('target', 'waka');

        if ($target === 'waka') {
            $res = $this->waService->kirimNotifDispenKeWaka($pengajuan);
        } elseif ($target === 'kepala') {
            $res = $this->waService->kirimNotifDispenKeKepala($pengajuan);
        } else {
            $res = $this->waService->kirimNotifDispenKeSatpam($pengajuan);
        }

        if ($res['success']) {
            return redirect()->back()->with('success', $res['message']);
        } else {
            return redirect()->back()->with('error', $res['message']);
        }
    }

    public function anakSakitPiket(Request $request)
    {
        $siswas = Siswa::with('kelas')->where('aktif', 1)->orderBy('nama', 'asc')->get();
        $riwayatSakit = PengajuanIzin::with(['siswa.kelas', 'pengaju'])
            ->where('kategori', 'sakit')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('piket.anak-sakit', compact('siswas', 'riwayatSakit'));
    }

    public function storeAnakSakitPiket(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id_siswa',
            'tanggal' => 'required|date',
            'alasan' => 'required|string',
            'lampiran_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('lampiran_foto')) {
            $file = $request->file('lampiran_foto');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $fotoPath = $file->storeAs('uploads/bukti_sakit', $filename, 'public');
        }

        $siswa = Siswa::findOrFail($request->id_siswa);

        $pengajuan = PengajuanIzin::create([
            'kategori' => 'sakit',
            'id_siswa' => $siswa->id_siswa,
            'id_user_pengaju' => Auth::id(),
            'tanggal' => $request->tanggal,
            'jenis_izin' => 'Sakit',
            'alasan' => $request->alasan,
            'lampiran_foto' => $fotoPath,
            'status' => 'verified',
            'id_piket_approver' => Auth::id(),
            'catatan_piket' => 'Dicatat langsung oleh Guru Piket',
            'tgl_piket' => now(),
        ]);

        DispenLog::catat(
            $pengajuan->id_pengajuan,
            Auth::id(),
            Auth::user()->role,
            null,
            'verified',
            'Pencatatan siswa sakit oleh Piket'
        );

        return redirect()->route('piket.anak-sakit')->with('success', 'Data siswa sakit berhasil dicatat oleh Piket.');
    }
}
