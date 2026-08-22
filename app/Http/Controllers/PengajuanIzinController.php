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

        if ($user->isOrtu()) {
            $siswas = $user->anakList;
        } else {
            $siswas = Siswa::with('kelas')->where('aktif', 1)->orderBy('nama', 'asc')->get();
        }

        return view('pengajuan.create', compact('siswas'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'kategori' => 'required|in:dispensasi,izin_masuk,izin_keluar,sakit,izin_guru',
            'id_siswa' => 'nullable|exists:siswa,id_siswa',
            'tanggal' => 'required|date',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'perkiraan_kembali' => 'nullable',
            'jenis_izin' => 'nullable|string|max:100',
            'alasan' => 'required|string',
            'keterangan' => 'nullable|string',
            'lampiran_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('lampiran_foto')) {
            $file = $request->file('lampiran_foto');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $fotoPath = $file->storeAs('uploads/bukti_sakit', $filename, 'public');
        }

        $idGuru = $user->isGuru() ? $user->guru?->id_guru : null;
        $idSiswa = $request->id_siswa;

        // Tentukan status awal & alur approval
        // Jika Piket membuat dispen untuk siswa -> langsung PENDING_WAKA
        // Jika Guru membuat dispen guru -> langsung PENDING_WAKA
        // Jika Ortu membuat izin umum -> PENDING_WAKA
        $statusAwal = 'pending_waka';
        $butuhSatpam = in_array($request->kategori, ['dispensasi', 'izin_keluar', 'izin_masuk', 'izin_guru']);

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
        ]);

        // Catat Log Riwayat
        DispenLog::catat(
            $pengajuan->id_pengajuan,
            $user->id_user,
            $user->role,
            null,
            $statusAwal,
            'Pengajuan ' . strtoupper(str_replace('_', ' ', $request->kategori)) . ' dibuat oleh ' . $user->nama
        );

        // Notifikasi In-App ke Waka
        $targetRole = ($request->kategori === 'izin_guru') ? 'waka_sdm' : 'waka_kesiswaan';
        Notifikasi::kirimKeRole(
            $targetRole,
            'Pengajuan Dispen Baru',
            'Ada pengajuan dispen/izin baru dari ' . $user->nama . ' menunggu persetujuan Waka.',
            route('pengajuan.show', $pengajuan->id_pengajuan),
            'dispen'
        );

        // Kirim Notifikasi WhatsApp ke Waka
        $waResult = $this->waService->kirimNotifDispenKeWaka($pengajuan);

        $flashMessage = 'Pengajuan dispensasi/izin berhasil dibuat dan diteruskan ke Waka.';
        if (!$waResult['success']) {
            $flashMessage .= ' (' . $waResult['message'] . ')';
        }

        return redirect()->route('pengajuan.index')->with('success', $flashMessage);
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
        $request->validate([
            'catatan' => 'nullable|string',
            'keputusan' => 'required|in:setujui,tolak'
        ]);

        $statusSebelum = $pengajuan->status;

        if ($request->keputusan === 'setujui') {
            $statusSesudah = 'disetujui_waka';
            $pengajuan->update([
                'status' => $statusSesudah,
                'id_waka_approver' => $user->id_user,
                'catatan_waka' => $request->catatan,
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
            if ($pengajuan->butuh_satpam) {
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
                'tgl_waka' => now(),
            ]);

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

            return redirect()->route('pengajuan.index')->with('success', 'Pengajuan DITOLAK oleh Waka.');
        }
    }

    public function resendWa(Request $request, $id)
    {
        $pengajuan = PengajuanIzin::with(['siswa.kelas', 'guru', 'pengaju'])->findOrFail($id);
        $target = $request->input('target', 'waka');

        if ($target === 'waka') {
            $res = $this->waService->kirimNotifDispenKeWaka($pengajuan);
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
