<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guru;
use App\Models\Pengaturan;
use App\Services\WhatsAppService;

class WhatsAppAdminController extends Controller
{
    protected WhatsAppService $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');

        // Ambil user pejabat & petugas penting
        $wakaKesiswaan = User::where('role', 'waka_kesiswaan')->where('aktif', 1)->get();
        $wakaSdm       = User::where('role', 'waka_sdm')->where('aktif', 1)->get();
        $wakaKurikulum = User::where('role', 'waka_kurikulum')->where('aktif', 1)->get();
        $kepalaSekolah = User::where('role', 'kepala_sekolah')->where('aktif', 1)->get();
        $satpams       = User::where('role', 'satpam')->where('aktif', 1)->get();
        $pikets        = User::where('role', 'piket')->where('aktif', 1)->get();

        // Fallback nomor WA dari tabel pengaturan
        $fallbackWa = [
            'waka_kesiswaan' => Pengaturan::getVal('wa_waka_kesiswaan', '081359472399'),
            'waka_sdm'       => Pengaturan::getVal('wa_waka_sdm', '085707300240'),
            'kepala_sekolah' => Pengaturan::getVal('wa_kepala_sekolah', '081234567890'),
            'satpam'         => Pengaturan::getVal('wa_satpam', '081359472399'),
            'piket'          => Pengaturan::getVal('wa_piket', '081234567890'),
        ];

        // Gateway Settings
        $gateway = [
            'api_url' => Pengaturan::getVal('wa_api_url', config('services.whatsapp.api_url', env('WHATSAPP_API_URL'))),
            'api_key' => Pengaturan::getVal('wa_api_key', config('services.whatsapp.api_key', env('WHATSAPP_API_KEY'))),
            'sender'  => Pengaturan::getVal('wa_sender', config('services.whatsapp.sender', env('WHATSAPP_SENDER'))),
        ];

        // Query pengguna untuk pencarian cepat penggantian nomor WA
        $penggunaList = collect();
        if ($search) {
            $penggunaList = User::where('nama', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('no_hp', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%")
                ->orderBy('nama', 'asc')
                ->limit(30)
                ->get();
        } else {
            $penggunaList = User::whereIn('role', ['waka_kesiswaan', 'waka_sdm', 'waka_kurikulum', 'kepala_sekolah', 'satpam', 'piket'])
                ->orderBy('role', 'asc')
                ->orderBy('nama', 'asc')
                ->get();
        }

        return view('admin.whatsapp.index', compact(
            'wakaKesiswaan', 'wakaSdm', 'wakaKurikulum', 'kepalaSekolah',
            'satpams', 'pikets', 'fallbackWa', 'gateway', 'penggunaList', 'search'
        ));
    }

    public function updatePejabat(Request $request)
    {
        $request->validate([
            'user_wa' => 'nullable|array',
            'user_wa.*' => 'nullable|string|max:25',
            'fallback_wa' => 'nullable|array',
            'fallback_wa.*' => 'nullable|string|max:25',
        ]);

        // 1. Update nomor WA per User ID
        if ($request->has('user_wa')) {
            foreach ($request->user_wa as $idUser => $noHp) {
                $user = User::find($idUser);
                if ($user) {
                    $user->no_hp = $noHp;
                    $user->save();

                    // Sync ke tabel Guru jika user ini akun guru
                    if ($user->guru) {
                        $user->guru->no_telp = $noHp;
                        $user->guru->save();
                    }
                }
            }
        }

        // 2. Update fallback WhatsApp numbers
        if ($request->has('fallback_wa')) {
            foreach ($request->fallback_wa as $key => $val) {
                Pengaturan::setVal('wa_' . $key, $val, 'admin');
            }
        }

        return back()->with('success', 'Nomor WhatsApp penerima notifikasi pengajuan berhasil diperbarui.');
    }

    public function updateGateway(Request $request)
    {
        $request->validate([
            'api_url' => 'nullable|string|max:255',
            'api_key' => 'nullable|string|max:255',
            'sender'  => 'nullable|string|max:50',
        ]);

        Pengaturan::setVal('wa_api_url', $request->api_url, 'admin');
        Pengaturan::setVal('wa_api_key', $request->api_key, 'admin');
        Pengaturan::setVal('wa_sender', $request->sender, 'admin');

        return back()->with('success', 'Pengaturan WhatsApp Gateway API (Fonnte) berhasil disimpan.');
    }

    public function testKirim(Request $request)
    {
        $request->validate([
            'no_tujuan' => 'required|string',
            'pesan_tes'  => 'required|string',
        ]);

        $res = $this->waService->kirim($request->no_tujuan, $request->pesan_tes);

        if ($res['success']) {
            return back()->with('success', '✅ Tes WhatsApp Berhasil: ' . $res['message']);
        } else {
            return back()->with('error', '❌ Tes WhatsApp Gagal: ' . $res['message']);
        }
    }

    public function updateUserWa(Request $request, $id)
    {
        $request->validate([
            'no_hp' => 'nullable|string|max:25',
        ]);

        $user = User::findOrFail($id);
        $user->no_hp = $request->no_hp;
        $user->save();

        if ($user->guru) {
            $user->guru->no_telp = $request->no_hp;
            $user->guru->save();
        }

        return back()->with('success', "Nomor WhatsApp untuk akun {$user->nama} berhasil diubah.");
    }
}
