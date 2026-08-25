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
     * Kirim pesan WhatsApp otomatis ke nomor tujuan via Gateway API (Fonnte / Gateway)
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
        if (empty($this->apiUrl) || empty($this->apiKey)) {
            Log::warning('WhatsApp belum dikonfigurasi; pesan tidak dikirim.', ['target' => $target]);
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

            $response = Http::withoutVerifying()
                ->asForm()
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
     * Dapatkan teks pesan notifikasi untuk Waka (Kesiswaan / SDM)
     */
    public static function getPesanWaka(PengajuanIzin $pengajuan): string
    {
        $isGuru = ($pengajuan->kategori === 'izin_guru');
        $nama = $isGuru ? ($pengajuan->guru?->nama ?? $pengajuan->pengaju?->nama ?? 'Guru') : ($pengajuan->siswa?->nama ?? 'Siswa');
        $identitas = $isGuru ? 'Guru' : ('Kelas ' . ($pengajuan->siswa?->kelas?->nama_kelas ?? '-'));
        $jenis = $pengajuan->jenis_izin ?? strtoupper(str_replace('_', ' ', $pengajuan->kategori));
        $tanggal = $pengajuan->tanggal;
        $jam = $pengajuan->jam_mulai ? ($pengajuan->jam_mulai . ($pengajuan->perkiraan_kembali ? ' s/d ' . $pengajuan->perkiraan_kembali : '')) : 'Hari ini';
        $alasan = $pengajuan->alasan;
        $baseUrl = rtrim(config('app.url', url('/')), '/');
        $linkApproval = $baseUrl . '/waka-area/persetujuan/' . $pengajuan->id_pengajuan;

        $judul = $isGuru ? "*[JURNAL SEKOLAH - DISPEN GURU]*" : "*[JURNAL SEKOLAH - DISPEN SISWA]*";

        return "{$judul}\n\n"
             . "Ada pengajuan dispensasi/izin baru menunggu persetujuan Waka.\n\n"
             . "• *Nama:* {$nama}\n"
             . "• *Identitas:* {$identitas}\n"
             . "• *Keperluan:* {$jenis}\n"
             . "• *Tanggal:* {$tanggal}\n"
             . "• *Waktu:* {$jam}\n"
             . "• *Alasan:* {$alasan}\n\n"
             . "Klik link di bawah ini untuk melihat detail dan memberikan persetujuan:\n"
             . "{$linkApproval}\n\n"
             . "_Status Pengajuan: {$pengajuan->status}_\n"
             . "_(Jika link belum berwarna biru, balas chat ini dengan 'OK' atau simpan kontak ini)_";
    }

    /**
     * Dapatkan teks pesan notifikasi untuk Kepala Sekolah (Dispen Guru yang sudah di-Acc Waka SDM)
     */
    public static function getPesanKepala(PengajuanIzin $pengajuan): string
    {
        $namaGuru = $pengajuan->guru?->nama ?? $pengajuan->pengaju?->nama ?? 'Guru';
        $nip = $pengajuan->guru?->nip ?? '-';
        $jenis = $pengajuan->jenis_izin ?? 'Dispensasi / Izin Guru';
        $tanggal = $pengajuan->tanggal;
        $jam = $pengajuan->jam_mulai ? ($pengajuan->jam_mulai . ($pengajuan->perkiraan_kembali ? ' s/d ' . $pengajuan->perkiraan_kembali : '')) : 'Hari ini';
        $alasan = $pengajuan->alasan;
        $catatanWaka = $pengajuan->catatan_waka ? $pengajuan->catatan_waka : 'Telah disetujui Waka SDM';
        $baseUrl = rtrim(config('app.url', url('/')), '/');
        $linkApproval = $baseUrl . '/kepala-area/persetujuan/' . $pengajuan->id_pengajuan;

        return "*[JURNAL SEKOLAH - PERSETUJUAN FINAL KEPALA SEKOLAH]*\n\n"
             . "Yth. Bapak/Ibu Kepala Sekolah,\n"
             . "Terdapat pengajuan dispensasi/izin meninggalkan tugas guru yang telah disetujui oleh Waka SDM dan memerlukan persetujuan final dari Anda.\n\n"
             . "• *Nama Guru:* {$namaGuru}\n"
             . "• *NIP:* {$nip}\n"
             . "• *Keperluan:* {$jenis}\n"
             . "• *Tanggal:* {$tanggal}\n"
             . "• *Waktu:* {$jam}\n"
             . "• *Alasan:* {$alasan}\n"
             . "• *Catatan Waka SDM:* {$catatanWaka}\n\n"
             . "Silakan klik link berikut untuk memproses persetujuan:\n"
             . "{$linkApproval}\n\n"
             . "_Status: Menunggu Persetujuan Kepala Sekolah_\n"
             . "_(Jika link belum berwarna biru, balas chat ini dengan 'OK' atau simpan kontak ini)_";
    }

    /**
     * Dapatkan teks pesan untuk Satpam (Hanya untuk Siswa keluar gerbang)
     */
    public static function getPesanSatpam(PengajuanIzin $pengajuan): string
    {
        $nama = $pengajuan->siswa?->nama ?? 'Siswa';
        $kelas = $pengajuan->siswa?->kelas?->nama_kelas ?? '-';
        $nis = $pengajuan->siswa?->nis ?? '-';
        $jenis = $pengajuan->jenis_izin ?? strtoupper(str_replace('_', ' ', $pengajuan->kategori));
        $tanggal = $pengajuan->tanggal;
        $jam = $pengajuan->jam_mulai ? $pengajuan->jam_mulai . ($pengajuan->perkiraan_kembali ? ' (Kembali: '.$pengajuan->perkiraan_kembali.')' : '') : 'Hari ini';
        $baseUrl = rtrim(config('app.url', url('/')), '/');
        $linkSatpam = $baseUrl . '/satpam-area/periksa/' . $pengajuan->id_pengajuan;

        return "*[JURNAL SEKOLAH - VERIFIKASI GERBANG SATPAM]*\n\n"
             . "Dispen siswa telah *DISETUJUI OLEH WAKA*.\n\n"
             . "• *Nama Siswa:* {$nama}\n"
             . "• *NIS / Kelas:* {$nis} / {$kelas}\n"
             . "• *Tanggal:* {$tanggal}\n"
             . "• *Jam Keluar:* {$jam}\n"
             . "• *Keperluan:* {$jenis}\n\n"
             . "_Status Pengajuan: {$pengajuan->status}_\n\n"
             . "Petugas Satpam harap mencocokkan Kartu Pelajar siswa saat melewati gerbang melalui link:\n"
             . "{$linkSatpam}";
    }

    /**
     * Dapatkan teks pesan konfirmasi selesai untuk Guru
     */
    public static function getPesanSelesaiGuru(PengajuanIzin $pengajuan): string
    {
        $namaGuru = $pengajuan->guru?->nama ?? $pengajuan->pengaju?->nama ?? 'Bapak/Ibu Guru';
        $jenis = $pengajuan->jenis_izin ?? 'Dispensasi Guru';
        $tanggal = $pengajuan->tanggal;
        $catatanKepala = $pengajuan->catatan_kepala ? $pengajuan->catatan_kepala : '-';
        $baseUrl = rtrim(config('app.url', url('/')), '/');
        $linkDetail = $baseUrl . '/pengajuan-izin/' . $pengajuan->id_pengajuan;

        return "*[JURNAL SEKOLAH - STATUS DISPEN RESMI DISETUJUI]*\n\n"
             . "Halo {$namaGuru},\n"
             . "Pengajuan dispensasi/izin Anda untuk tanggal *{$tanggal}* telah *DISETUJUI LENGKAP* oleh Waka SDM dan Kepala Sekolah.\n\n"
             . "• *Keperluan:* {$jenis}\n"
             . "• *Catatan Kepala Sekolah:* {$catatanKepala}\n\n"
             . "Lihat dokumen izin resmi Anda di:\n"
             . "{$linkDetail}\n\n"
             . "Terima kasih.";
    }

    public static function getPesanDitolakWaka(PengajuanIzin $pengajuan): string
    {
        $nama = $pengajuan->siswa?->nama ?? $pengajuan->guru?->nama ?? $pengajuan->pengaju?->nama ?? 'Pemohon';
        $alasan = $pengajuan->alasan_penolakan ?: $pengajuan->catatan_waka ?: '-';
        $baseUrl = rtrim(config('app.url', url('/')), '/');
        $linkDetail = $baseUrl . '/pengajuan-izin/' . $pengajuan->id_pengajuan;

        return "*[JURNAL SEKOLAH - PENGAJUAN DITOLAK]*\n\n"
            . "Pengajuan dispen {$nama} telah ditolak oleh Waka.\n"
            . "• *Tanggal:* {$pengajuan->tanggal}\n"
            . "• *Alasan penolakan:* {$alasan}\n"
            . "• *Status Pengajuan:* {$pengajuan->status}\n\n"
            . "Lihat detail: {$linkDetail}";
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
     * Dapatkan link Direct WhatsApp wa.me ke Kepala Sekolah
     */
    public static function getDirectWaLinkKepala(PengajuanIzin $pengajuan, string $nomorHp = '085707300240'): string
    {
        $phone = self::formatNomor($nomorHp);
        $text = rawurlencode(self::getPesanKepala($pengajuan));
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

        if ($pengajuan->id_waka_tujuan) {
            $waka = User::where('id_user', $pengajuan->id_waka_tujuan)
                ->where('aktif', 1)
                ->whereNotNull('no_hp')
                ->where('no_hp', '!=', '')
                ->first();

            return $waka
                ? $this->hasilKirim([$this->kirim($waka->no_hp, $pesan)], 'Waka')
                : ['success' => false, 'message' => 'Nomor WhatsApp Waka tujuan belum tersedia.'];
        }

        $targetRole = ($pengajuan->kategori === 'izin_guru') ? 'waka_sdm' : 'waka_kesiswaan';
        $wakas = User::where('role', $targetRole)
            ->where('aktif', 1)
            ->whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->get();

        if ($wakas->isEmpty()) return ['success' => false, 'message' => 'Nomor WhatsApp Waka belum tersedia.'];

        $results = [];
        foreach ($wakas as $waka) {
            $results[] = $this->kirim($waka->no_hp, $pesan);
        }

        return $this->hasilKirim($results, 'Waka');
    }

    public function kirimNotifPenolakanWaka(PengajuanIzin $pengajuan): array
    {
        $nomor = $pengajuan->pengaju?->no_hp;
        return $nomor
            ? $this->kirim($nomor, self::getPesanDitolakWaka($pengajuan))
            : ['success' => false, 'message' => 'Nomor WhatsApp pengaju belum tersedia.'];
    }

    private function hasilKirim(array $results, string $penerima): array
    {
        $anySuccess = collect($results)->contains('success', true);
        return [
            'success' => $anySuccess,
            'message' => $anySuccess ? "Notifikasi WhatsApp berhasil dikirim ke {$penerima}." : ($results[0]['message'] ?? 'Gagal mengirim WhatsApp.')
        ];
    }

    /**
     * Kirim Notifikasi Dispen Guru ke Kepala Sekolah (Background API)
     */
    public function kirimNotifDispenKeKepala(PengajuanIzin $pengajuan): array
    {
        $pesan = self::getPesanKepala($pengajuan);

        $kepalas = User::where('role', 'kepala_sekolah')
            ->where('aktif', 1)
            ->whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->get();

        if ($kepalas->isEmpty()) return ['success' => false, 'message' => 'Nomor WhatsApp Kepala Sekolah belum tersedia.'];

        $results = [];
        foreach ($kepalas as $kepala) {
            $results[] = $this->kirim($kepala->no_hp, $pesan);
        }

        return $this->hasilKirim($results, 'Kepala Sekolah');
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

        if ($satpams->isEmpty()) return ['success' => false, 'message' => 'Nomor WhatsApp Satpam belum tersedia.'];

        $results = [];
        foreach ($satpams as $satpam) {
            $results[] = $this->kirim($satpam->no_hp, $pesan);
        }

        return $this->hasilKirim($results, 'Satpam');
    }

    /**
     * Kirim Notifikasi Selesai ke Guru (Background API)
     */
    public function kirimNotifSelesaiKeGuru(PengajuanIzin $pengajuan): array
    {
        $pesan = self::getPesanSelesaiGuru($pengajuan);

        // Ambil nomor HP guru yang bersangkutan atau pemohon
        $nomor = $pengajuan->guru?->no_telp ?? $pengajuan->pengaju?->no_hp;

        if (!$nomor) return ['success' => false, 'message' => 'Nomor WhatsApp Guru belum tersedia.'];

        return $this->kirim($nomor, $pesan);
    }
}
