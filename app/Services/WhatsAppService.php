<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PengajuanIzin;
use App\Models\User;

class WhatsAppService
{
    protected ?string $apiUrl;
    protected ?string $apiKey;
    protected ?string $sender;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', env('WHATSAPP_API_URL'));
        $this->apiKey = config('services.whatsapp.api_key', env('WHATSAPP_API_KEY'));
        $this->sender = config('services.whatsapp.sender', env('WHATSAPP_SENDER'));
    }

    /**
     * Format nomor HP ke standar internasional 628xxx
     */
    public static function formatNomor(?string $nomor): ?string
    {
        if (!$nomor) return null;
        $clean = preg_replace('/[^0-9]/', '', $nomor);
        if (str_starts_with($clean, '08')) {
            $clean = '628' . substr($clean, 2);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '628' . substr($clean, 1);
        }
        return $clean;
    }

    /**
     * Kirim pesan WhatsApp otomatis ke nomor tujuan via Gateway API
     * 
     * @param string $nomorHp
     * @param string $pesan
     * @return array ['success' => bool, 'message' => string]
     */
    public function kirim(string $nomorHp, string $pesan): array
    {
        $target = self::formatNomor($nomorHp);

        if (!$target) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tidak valid atau kosong.'
            ];
        }

        // Cek apakah API gateway sudah dikonfigurasi di .env
        if (empty($this->apiUrl)) {
            Log::info("WhatsApp (Simulasi - API_URL belum diatur): Kirim ke {$target}\nPesan:\n{$pesan}");
            return [
                'success' => false,
                'message' => 'WhatsApp Gateway API belum diisi di .env (WHATSAPP_API_URL).'
            ];
        }

        try {
            $payload = [
                'target' => $target,
                'message' => $pesan,
                'countryCode' => '62',
            ];

            $response = Http::asForm()
                ->withoutVerifying()
                ->withHeaders([
                    'Authorization' => $this->apiKey,
                ])
                ->timeout(15)
                ->post($this->apiUrl, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp Berhasil Dikirim ke {$target}");
                return [
                    'success' => true,
                    'message' => 'Pesan WhatsApp berhasil dikirim otomatis.'
                ];
            } else {
                $errorBody = $response->body();
                Log::warning("WhatsApp Gagal Dikirim ke {$target}. Status: {$response->status()}, Response: {$errorBody}");
                return [
                    'success' => false,
                    'message' => 'WhatsApp Gateway merespons error: ' . substr($errorBody, 0, 100)
                ];
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp Exception saat kirim ke {$target}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal menghubungi WhatsApp Gateway: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Dapatkan teks pesan untuk Waka
     */
    public static function getPesanWaka(PengajuanIzin $pengajuan): string
    {
        $nama = $pengajuan->siswa ? $pengajuan->siswa->nama : ($pengajuan->guru ? $pengajuan->guru->nama : ($pengajuan->pengaju->nama ?? '-'));
        $kelas = $pengajuan->siswa && $pengajuan->siswa->kelas ? $pengajuan->siswa->kelas->nama_kelas : ($pengajuan->guru ? 'Guru' : '-');
        $jenis = $pengajuan->jenis_izin ?? strtoupper(str_replace('_', ' ', $pengajuan->kategori));
        $tanggal = $pengajuan->tanggal;
        $alasan = $pengajuan->alasan;
        $linkApproval = url(route('waka.persetujuan.show', $pengajuan->id_pengajuan));

        return "Jurnal Sekolah\n\n"
             . "Ada pengajuan dispen/izin baru.\n\n"
             . "Nama: {$nama}\n"
             . "Kelas: {$kelas}\n"
             . "Jenis: {$jenis}\n"
             . "Tanggal: {$tanggal}\n"
             . "Alasan: {$alasan}\n\n"
             . "Silakan melakukan pemeriksaan dan keputusan melalui link berikut:\n"
             . "{$linkApproval}\n\n"
             . "Status: Menunggu persetujuan Waka.";
    }

    /**
     * Dapatkan teks pesan untuk Satpam
     */
    public static function getPesanSatpam(PengajuanIzin $pengajuan): string
    {
        $nama = $pengajuan->siswa ? $pengajuan->siswa->nama : ($pengajuan->guru ? $pengajuan->guru->nama : ($pengajuan->pengaju->nama ?? '-'));
        $kelas = $pengajuan->siswa && $pengajuan->siswa->kelas ? $pengajuan->siswa->kelas->nama_kelas : ($pengajuan->guru ? 'Guru' : '-');
        $jenis = $pengajuan->jenis_izin ?? strtoupper(str_replace('_', ' ', $pengajuan->kategori));
        $tanggal = $pengajuan->tanggal;
        $jam = $pengajuan->jam_mulai ? $pengajuan->jam_mulai . ($pengajuan->perkiraan_kembali ? ' (Kembali: '.$pengajuan->perkiraan_kembali.')' : '') : 'Hari ini';
        $linkSatpam = url(route('satpam.show', $pengajuan->id_pengajuan));

        return "Jurnal Sekolah\n\n"
             . "Pengajuan dispen telah DISETUJUI oleh Waka.\n\n"
             . "Nama: {$nama}\n"
             . "Kelas: {$kelas}\n"
             . "Tanggal: {$tanggal}\n"
             . "Jam keluar: {$jam}\n"
             . "Jenis: {$jenis}\n\n"
             . "Satpam dapat melakukan pengecekan melalui link:\n"
             . "{$linkSatpam}";
    }

    /**
     * Dapatkan link Direct WhatsApp wa.me ke Waka
     */
    public static function getDirectWaLinkWaka(PengajuanIzin $pengajuan, string $nomorHp = '085707300240'): string
    {
        $phone = self::formatNomor($nomorHp);
        $text = rawurlencode(self::getPesanWaka($pengajuan));
        return "https://api.whatsapp.com/send?phone={$phone}&text={$text}";
    }

    /**
     * Dapatkan link Direct WhatsApp wa.me ke Satpam
     */
    public static function getDirectWaLinkSatpam(PengajuanIzin $pengajuan, string $nomorHp = '081359472399'): string
    {
        $phone = self::formatNomor($nomorHp);
        $text = rawurlencode(self::getPesanSatpam($pengajuan));
        return "https://api.whatsapp.com/send?phone={$phone}&text={$text}";
    }

    /**
     * Kirim Notifikasi Pengajuan Dispen ke Waka (Background API)
     */
    public function kirimNotifDispenKeWaka(PengajuanIzin $pengajuan): array
    {
        $pesan = self::getPesanWaka($pengajuan);

        $targetRole = ($pengajuan->kategori === 'izin_guru') ? 'waka_sdm' : 'waka_kesiswaan';
        $wakas = User::whereIn('role', [$targetRole, 'waka_kesiswaan', 'waka_sdm'])
            ->where('aktif', 1)
            ->whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->get();

        if ($wakas->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada akun Waka dengan nomor HP terdaftar.'
            ];
        }

        $results = [];
        foreach ($wakas as $waka) {
            $results[] = $this->kirim($waka->no_hp, $pesan);
        }

        $anySuccess = collect($results)->contains('success', true);
        return [
            'success' => $anySuccess,
            'message' => $anySuccess ? 'Notifikasi WhatsApp berhasil dikirim ke Waka.' : ($results[0]['message'] ?? 'Gagal mengirim WhatsApp.')
        ];
    }

    /**
     * Kirim Notifikasi Dispen Disetujui ke Satpam (Background API)
     */
    public function kirimNotifDispenKeSatpam(PengajuanIzin $pengajuan): array
    {
        $pesan = self::getPesanSatpam($pengajuan);

        $satpams = User::where('role', 'satpam')
            ->where('aktif', 1)
            ->whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->get();

        if ($satpams->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada akun Satpam dengan nomor HP terdaftar.'
            ];
        }

        $results = [];
        foreach ($satpams as $satpam) {
            $results[] = $this->kirim($satpam->no_hp, $pesan);
        }

        $anySuccess = collect($results)->contains('success', true);
        return [
            'success' => $anySuccess,
            'message' => $anySuccess ? 'Notifikasi WhatsApp berhasil dikirim ke Satpam.' : ($results[0]['message'] ?? 'Gagal mengirim WhatsApp.')
        ];
    }
}
