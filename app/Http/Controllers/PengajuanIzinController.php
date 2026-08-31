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
            $anakIds = $user->anakList->pluck('id_siswa');
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
            $siswas = $user->anakList;
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
            'kategori' => 'required|in:dispensasi,izin_masuk,izin_keluar,sakit,izin_guru,acara_keluarga,izin',
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
        // Jika dibuat oleh Orang Tua atau kategori izin siswa (sakit, izin): langsung sah/masuk ke Piket & Data Kelas
        $isOrtuFlow = $user->isOrtu() || in_array($request->kategori, ['sakit', 'izin']);

        $statusAwal = $isOrtuFlow ? 'completed' : 'pending_waka';
        $butuhSatpam = !$isGuruDispen && !$isOrtuFlow && in_array($request->kategori, ['dispensasi', 'izin_keluar', 'izin_masuk']);
        $idWakaTujuan = $isOrtuFlow ? null : $wakaTujuanUser?->id_user;

        $pengajuan = PengajuanIzin::create([
            'kategori' => $request->kategori,
            'id_siswa' => $idSiswa,
            'id_guru' => $idGuru,
            'id_user_pengaju' => $user->id_user,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'perkiraan_kembali' => $request->perkiraan_kembali,
            'jenis_izin' => $request->jenis_izin ?? ucfirst(str_replace('_', ' ', $request->kategori)),
            'alasan' => $request->alasan,
            'keterangan' => $request->keterangan,
            'lampiran_foto' => $fotoPath,
            'status' => $statusAwal,
            'butuh_satpam' => $butuhSatpam,
            'id_waka_tujuan' => $idWakaTujuan,
        ]);

        // Catat Log Riwayat
        DispenLog::catat(
            $pengajuan->id_pengajuan,
            $user->id_user,
            $user->role,
            null,
            $statusAwal,
            $isOrtuFlow ? 'Izin dari Orang Tua (' . $user->nama . ') langsung dicatat ke piket dan data kelas' : ('Pengajuan ' . strtoupper(str_replace('_', ' ', $request->kategori)) . ' dibuat oleh ' . $user->nama)
        );

        $namaKategoriText = match($request->kategori) {
            'sakit' => 'Izin Sakit',
            'izin' => 'Izin',
            'acara_keluarga' => 'Izin Acara Keluarga',
            'izin_keluar' => 'Izin Keluar Sekolah',
            'izin_masuk' => 'Izin Masuk / Terlambat',
            'izin_guru' => 'Dispensasi Guru',
            default => 'Dispensasi'
        };

        if ($isOrtuFlow) {
            $siswa = Siswa::with('kelas')->find($idSiswa);
            $namaSiswa = $siswa?->nama ?? 'Siswa';

            // ========================================================
            // LANGSUNG SINKRONISASI KE DATA KELAS / ABSENSI SISWA
            // ========================================================
            if ($siswa) {
                $tanggalIzin = $pengajuan->tanggal;
                $statusAbsensi = ($pengajuan->kategori === 'sakit') ? 'sakit' : 'izin';

                // Cari Jurnal Harian pada tanggal tersebut untuk kelas siswa
                $jurnal = \App\Models\JurnalHarian::where('tanggal', $tanggalIzin)
                    ->whereHas('jadwal', function ($q) use ($siswa) {
                        $q->where('id_kelas', $siswa->id_kelas);
                    })
                    ->first();

                if (!$jurnal) {
                    $jadwalFirst = \App\Models\Jadwal::where('id_kelas', $siswa->id_kelas)->where('aktif', 1)->first();
                    if ($jadwalFirst) {
                        $jurnal = \App\Models\JurnalHarian::firstOrCreate(
                            [
                                'id_jadwal' => $jadwalFirst->id_jadwal,
                                'tanggal' => $tanggalIzin,
                            ],
                            [
                                'id_guru' => $jadwalFirst->id_guru ?? Guru::first()?->id_guru,
                                'materi' => 'Presensi Kelas (Izin Orang Tua)',
                                'jam_ke' => $jadwalFirst->jam_ke ?? 1,
                            ]
                        );
                    }
                }

                if ($jurnal) {
                    \App\Models\AbsensiSiswa::updateOrCreate(
                        [
                            'id_jurnal' => $jurnal->id_jurnal,
                            'id_siswa' => $siswa->id_siswa,
                        ],
                        [
                            'status' => $statusAbsensi,
                            'keterangan' => ($pengajuan->alasan ? $pengajuan->alasan . ' ' : '') . '(Izin Orang Tua)',
                            'dicatat_oleh' => $user->id_user,
                            'created_at' => now(),
                        ]
                    );
                }
            }

            // Notifikasi ke Guru Piket
            Notifikasi::kirimKeRole(
                'piket',
                'Pemberitahuan ' . $namaKategoriText . ' Siswa',
                'Siswa ' . $namaSiswa . ' (' . ($siswa?->kelas->nama_kelas ?? '-') . ') telah dicatat ' . $namaKategoriText . ' oleh Orang Tua pada tanggal ' . $pengajuan->tanggal . '.',
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
                        'Siswa ' . $namaSiswa . ' tercatat ' . $namaKategoriText . ' oleh Orang Tua pada tanggal ' . $pengajuan->tanggal . ' dan langsung masuk ke data kelas.',
                        route('walikelas.data-kelas'),
                        'izin'
                    );
                }
            }

            $flashMessage = "Pengajuan {$namaKategoriText} anak berhasil dibuat dan langsung tercatat di piket serta data presensi kelas.";
        } else {
            // Kirim WhatsApp ke Waka Bertugas
            $waResult = $this->waService->kirimNotifDispenKeWaka($pengajuan->load(['siswa.kelas', 'guru', 'pengaju', 'wakaTujuan']));

            // Kirim Notifikasi Sistem Internal ke Waka
            if ($wakaTujuanUser) {
                Notifikasi::create([
                    'id_user' => $wakaTujuanUser->id_user,
                    'judul' => 'Pengajuan Dispen Baru (' . ($isGuruDispen ? 'Guru' : 'Siswa') . ')',
                    'pesan' => 'Ada pengajuan ' . ($isGuruDispen ? 'dispen guru' : 'dispen siswa') . ' baru menunggu persetujuan Anda.',
                    'link' => route('waka.persetujuan.show', $pengajuan->id_pengajuan),
                    'tipe' => 'dispen',
                    'dibaca' => false,
                ]);
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
            $statusSesudah = 'completed';
            $pengajuan->update([
                'status' => $statusSesudah,
                'id_piket_approver' => $user->id_user,
                'catatan_piket' => $request->catatan,
                'tgl_piket' => now(),
            ]);

            // Catat Log
            DispenLog::catat(
                $pengajuan->id_pengajuan,
                $user->id_user,
                $user->role,
                $statusSebelum,
                $statusSesudah,
                $request->catatan ?? 'Disetujui dan diverifikasi oleh Guru Piket'
            );

            // ==========================================
            // SINKRONISASI KE DATA KELAS / ABSENSI SISWA
            // ==========================================
            if ($siswa) {
                $tanggalIzin = $pengajuan->tanggal;
                $statusAbsensi = ($pengajuan->kategori === 'sakit') ? 'sakit' : 'izin';

                // Cari Jurnal Harian hari ini untuk kelas siswa tersebut
                $jurnal = \App\Models\JurnalHarian::where('tanggal', $tanggalIzin)
                    ->whereHas('jadwal', function ($q) use ($siswa) {
                        $q->where('id_kelas', $siswa->id_kelas);
                    })
                    ->first();

                // Jika belum ada jurnal harian dibuat oleh guru mapel, cari jadwal pertama kelas tersebut
                if (!$jurnal) {
                    $jadwalFirst = \App\Models\Jadwal::where('id_kelas', $siswa->id_kelas)->where('aktif', 1)->first();
                    if ($jadwalFirst) {
                        $jurnal = \App\Models\JurnalHarian::firstOrCreate(
                            [
                                'id_jadwal' => $jadwalFirst->id_jadwal,
                                'tanggal' => $tanggalIzin,
                            ],
                            [
                                'id_guru' => $jadwalFirst->id_guru ?? Guru::first()?->id_guru,
                                'materi' => 'Presensi Kelas (Disetujui Piket)',
                                'jam_ke' => $jadwalFirst->jam_ke ?? 1,
                            ]
                        );
                    }
                }

                if ($jurnal) {
                    \App\Models\AbsensiSiswa::updateOrCreate(
                        [
                            'id_jurnal' => $jurnal->id_jurnal,
                            'id_siswa' => $siswa->id_siswa,
                        ],
                        [
                            'status' => $statusAbsensi,
                            'keterangan' => ($pengajuan->alasan ? $pengajuan->alasan . ' ' : '') . ($request->catatan ? '(Piket: ' . $request->catatan . ')' : '(Diverifikasi Guru Piket)'),
                            'dicatat_oleh' => $user->id_user,
                            'created_at' => now(),
                        ]
                    );
                }
            }

            // Notifikasi ke Orang Tua
            if ($pengajuan->id_user_pengaju) {
                Notifikasi::kirim(
                    $pengajuan->id_user_pengaju,
                    'Pengajuan ' . $namaKategori . ' Disetujui',
                    'Pengajuan ' . $namaKategori . ' anak Anda (' . $namaSiswa . ') telah diverifikasi & disetujui oleh Guru Piket dan telah dicatat ke data presensi kelas.',
                    route('pengajuan.show', $pengajuan->id_pengajuan),
                    'izin'
                );
            }

            // Notifikasi ke Wali Kelas jika ada
            if ($siswa && $siswa->kelas && $siswa->kelas->id_guru_walikelas) {
                $guruWali = Guru::find($siswa->kelas->id_guru_walikelas);
                if ($guruWali && $guruWali->id_user) {
                    Notifikasi::kirim(
                        $guruWali->id_user,
                        'Pemberitahuan Izin Siswa Kelas',
                        'Siswa ' . $namaSiswa . ' tercatat ' . strtoupper($pengajuan->kategori) . ' pada tanggal ' . $pengajuan->tanggal . ' (Diverifikasi oleh Guru Piket).',
                        route('walikelas.dashboard'),
                        'izin'
                    );
                }
            }

            return redirect()->route('pengajuan.show', $pengajuan->id_pengajuan)->with('success', "Pengajuan {$namaKategori} untuk {$namaSiswa} berhasil DISETUJUI dan langsung dicatat ke data presensi kelas.");
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
        if ($pengajuan->id_waka_tujuan && (int) $pengajuan->id_waka_tujuan !== (int) $user->id_user && !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Pengajuan ini ditujukan kepada Waka yang bertugas pada tanggal pengajuan.');
        }
        $request->validate([
            'catatan' => 'nullable|string|required_if:keputusan,tolak',
            'keputusan' => 'required|in:setujui,tolak'
        ]);

        $statusSebelum = $pengajuan->status;
        $isPiketFlow = (bool) $pengajuan->id_waka_tujuan;

        if ($request->keputusan === 'setujui') {
            $statusSesudah = $isPiketFlow ? 'menunggu_satpam' : 'disetujui_waka';
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

            // Notifikasi in-app ke Piket
            Notifikasi::kirimKeRole(
                'piket',
                'Dispen Disetujui Waka',
                'Pengajuan dispen ' . ($pengajuan->siswa?->nama ?? $pengajuan->guru?->nama ?? 'Siswa/Guru') . ' telah disetujui Waka dan menunggu verifikasi Satpam.',
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'dispen'
            );

            // Notifikasi in-app ke Pengaju
            Notifikasi::kirim(
                $pengajuan->id_user_pengaju,
                'Pengajuan Dispen Disetujui Waka',
                'Pengajuan dispen Anda telah disetujui oleh Waka. Silakan verifikasi identitas ke Satpam saat keluar gerbang.',
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'dispen'
            );

            // Notifikasi in-app ke Satpam jika butuh satpam
            if ($pengajuan->butuh_satpam && !$isPiketFlow) {
                Notifikasi::kirimKeRole(
                    'satpam',
                    'Verifikasi Dispen Baru (Acc Waka)',
                    'Pengajuan dispen ' . ($pengajuan->siswa?->nama ?? $pengajuan->guru?->nama ?? '') . ' telah disetujui Waka. Menunggu verifikasi gerbang.',
                    route('satpam.show', $pengajuan->id_pengajuan),
                    'satpam'
                );

                // Kirim WhatsApp ke Satpam
                $waResult = $this->waService->kirimNotifDispenKeSatpam($pengajuan);
            }

            if ($pengajuan->butuh_satpam && $isPiketFlow) {
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
