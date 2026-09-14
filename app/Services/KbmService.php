<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Pengaturan;

class KbmService
{
    /**
     * Default Alokasi Jam KBM SENIN - KAMIS
     * 1 JP = 40 menit
     */
    protected static array $defaultSeninKamisSlots = [
        1  => ['waktu_mulai' => '07:00', 'waktu_selesai' => '07:40', 'keterangan' => 'Upacara / Apel (Senin)'],
        2  => ['waktu_mulai' => '07:40', 'waktu_selesai' => '08:20'],
        3  => ['waktu_mulai' => '08:20', 'waktu_selesai' => '09:00'],
        4  => ['waktu_mulai' => '09:00', 'waktu_selesai' => '09:40'],
        5  => ['waktu_mulai' => '10:00', 'waktu_selesai' => '10:40'],
        6  => ['waktu_mulai' => '10:40', 'waktu_selesai' => '11:20'],
        7  => ['waktu_mulai' => '11:20', 'waktu_selesai' => '12:00'],
        8  => ['waktu_mulai' => '13:00', 'waktu_selesai' => '13:40'],
        9  => ['waktu_mulai' => '13:40', 'waktu_selesai' => '14:20'],
        10 => ['waktu_mulai' => '14:20', 'waktu_selesai' => '15:00'],
    ];

    /**
     * Default Istirahat Senin - Kamis
     */
    protected static array $defaultSeninKamisIstirahat = [
        4 => ['label' => 'Istirahat 1', 'waktu' => '09:40 - 10:00', 'waktu_mulai' => '09:40', 'waktu_selesai' => '10:00'],
        7 => ['label' => 'Istirahat 2 (ISHOMA)', 'waktu' => '12:00 - 13:00', 'waktu_mulai' => '12:00', 'waktu_selesai' => '13:00'],
    ];

    /**
     * Default Alokasi Jam KBM JUMAT
     * 1 JP = 30 menit
     */
    protected static array $defaultJumatSlots = [
        1  => ['waktu_mulai' => '07:00', 'waktu_selesai' => '07:30', 'keterangan' => 'Pembiasaan Hari Jumat'],
        2  => ['waktu_mulai' => '07:30', 'waktu_selesai' => '08:00'],
        3  => ['waktu_mulai' => '08:00', 'waktu_selesai' => '08:30'],
        4  => ['waktu_mulai' => '08:30', 'waktu_selesai' => '09:00'],
        5  => ['waktu_mulai' => '09:00', 'waktu_selesai' => '09:30'],
        6  => ['waktu_mulai' => '09:50', 'waktu_selesai' => '10:20'],
        7  => ['waktu_mulai' => '10:20', 'waktu_selesai' => '10:50'],
        8  => ['waktu_mulai' => '10:50', 'waktu_selesai' => '11:20'],
        9  => ['waktu_mulai' => '13:00', 'waktu_selesai' => '13:30'],
        10 => ['waktu_mulai' => '13:30', 'waktu_selesai' => '14:00'],
        11 => ['waktu_mulai' => '14:00', 'waktu_selesai' => '14:30'],
        12 => ['waktu_mulai' => '14:30', 'waktu_selesai' => '15:00'],
        13 => ['waktu_mulai' => '15:00', 'waktu_selesai' => '15:30'], // Khusus Kelas X
    ];

    /**
     * Default Istirahat Jumat
     */
    protected static array $defaultJumatIstirahat = [
        5 => ['label' => 'Istirahat 1', 'waktu' => '09:30 - 09:50', 'waktu_mulai' => '09:30', 'waktu_selesai' => '09:50'],
        8 => ['label' => 'Istirahat 2 (Solat Jumat)', 'waktu' => '11:20 - 13:00', 'waktu_mulai' => '11:20', 'waktu_selesai' => '13:00'],
    ];

    public static function getJamMasuk(): string
    {
        try {
            return Pengaturan::getVal('jam_masuk', '07:00') ?: '07:00';
        } catch (\Throwable $e) {
            return '07:00';
        }
    }

    public static function getJamPulang(string $hari = 'Senin', ?string $tingkat = null): string
    {
        $hariNorm = ucfirst(strtolower($hari));
        try {
            if ($hariNorm === 'Jumat') {
                if ($tingkat !== null) {
                    if (self::isKelasX($tingkat)) {
                        return Pengaturan::getVal('jam_pulang_jumat_x', '15:30') ?: '15:30';
                    } else {
                        return Pengaturan::getVal('jam_pulang_jumat_xi', '15:00') ?: '15:00';
                    }
                }
                return Pengaturan::getVal('jam_pulang_jumat_x', Pengaturan::getVal('jam_pulang_jumat', '15:30')) ?: '15:30';
            }
            return Pengaturan::getVal('jam_pulang', '15:00') ?: '15:00';
        } catch (\Throwable $e) {
            if ($hariNorm === 'Jumat') {
                return ($tingkat !== null && !self::isKelasX($tingkat)) ? '15:00' : '15:30';
            }
            return '15:00';
        }
    }

    public static function getJamPulangJumatX(): string
    {
        try {
            return Pengaturan::getVal('jam_pulang_jumat_x', Pengaturan::getVal('jam_pulang_jumat', '15:30')) ?: '15:30';
        } catch (\Throwable $e) {
            return '15:30';
        }
    }

    public static function getJamPulangJumatXi(): string
    {
        try {
            return Pengaturan::getVal('jam_pulang_jumat_xi', '15:00') ?: '15:00';
        } catch (\Throwable $e) {
            return '15:00';
        }
    }

    public static function getDurasiPelajaran(string $hari = 'Senin'): int
    {
        $hariNorm = ucfirst(strtolower($hari));
        try {
            if ($hariNorm === 'Jumat') {
                return (int) (Pengaturan::getVal('durasi_pelajaran_jumat_menit', 30) ?: 30);
            }
            return (int) (Pengaturan::getVal('durasi_pelajaran_menit', 40) ?: 40);
        } catch (\Throwable $e) {
            return ($hariNorm === 'Jumat') ? 30 : 40;
        }
    }

    public static function getToleransiTerlambat(): int
    {
        try {
            return (int) (Pengaturan::getVal('toleransi_keterlambatan_menit', 15) ?: 15);
        } catch (\Throwable $e) {
            return 15;
        }
    }

    public static function getBatasWaktuJurnal(): int
    {
        try {
            return (int) (Pengaturan::getVal('batas_waktu_jurnal_menit', 60) ?: 60);
        } catch (\Throwable $e) {
            return 60;
        }
    }

    public static function getToleransiKelasKosong(): int
    {
        try {
            return (int) (Pengaturan::getVal('toleransi_kelas_kosong_menit', 15, 'piket') ?: 15);
        } catch (\Throwable $e) {
            return 15;
        }
    }

    public static function getSlots(string $hari = 'Senin'): array
    {
        $hariNorm = ucfirst(strtolower($hari));
        try {
            $customKey = ($hariNorm === 'Jumat') ? 'kbm_slots_jumat' : 'kbm_slots_senin_kamis';
            $saved = Pengaturan::getVal($customKey);
            if ($saved) {
                $decoded = json_decode($saved, true);
                if (is_array($decoded) && !empty($decoded)) {
                    // Pastikan keys adalah integer
                    $result = [];
                    foreach ($decoded as $k => $v) {
                        $result[(int)$k] = $v;
                    }
                    return $result;
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return ($hariNorm === 'Jumat') ? self::$defaultJumatSlots : self::$defaultSeninKamisSlots;
    }

    public static function getIstirahat(string $hari = 'Senin'): array
    {
        $hariNorm = ucfirst(strtolower($hari));
        try {
            $customKey = ($hariNorm === 'Jumat') ? 'kbm_istirahat_jumat' : 'kbm_istirahat_senin_kamis';
            $saved = Pengaturan::getVal($customKey);
            if ($saved) {
                $decoded = json_decode($saved, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $result = [];
                    foreach ($decoded as $k => $v) {
                        $result[(int)$k] = $v;
                    }
                    return $result;
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return ($hariNorm === 'Jumat') ? self::$defaultJumatIstirahat : self::$defaultSeninKamisIstirahat;
    }

    public static function isKelasX(?string $tingkat = null): bool
    {
        if (!$tingkat) return true;
        $t = strtoupper(trim($tingkat));
        return $t === 'X' || $t === '10' || str_starts_with($t, 'X ') || str_starts_with($t, '10 ');
    }

    public static function getAlokasiWaktu(string $hari, int $jamKe, ?string $tingkat = null): ?array
    {
        $hariNorm = ucfirst(strtolower($hari));
        $slots = self::getSlots($hariNorm);

        if ($hariNorm === 'Jumat') {
            if ($jamKe === 13 && $tingkat !== null && !self::isKelasX($tingkat)) {
                return null;
            }
        }

        return $slots[$jamKe] ?? null;
    }

    public static function getLabelWaktu(string $hari, int $jamKe, ?string $tingkat = null): string
    {
        $alokasi = self::getAlokasiWaktu($hari, $jamKe, $tingkat);
        if (!$alokasi) return '-';
        return $alokasi['waktu_mulai'] . ' - ' . $alokasi['waktu_selesai'];
    }

    public static function isJamKeValid(string $hari, int $jamKe, ?string $tingkat = null): bool
    {
        return self::getAlokasiWaktu($hari, $jamKe, $tingkat) !== null;
    }

    public static function getDaftarJamKe(string $hari, ?string $tingkat = null): array
    {
        $hariNorm = ucfirst(strtolower($hari));
        $slots = self::getSlots($hariNorm);

        $daftar = [];
        foreach ($slots as $jam => $waktu) {
            if ($hariNorm === 'Jumat' && $jam === 13 && $tingkat !== null && !self::isKelasX($tingkat)) {
                continue;
            }
            $daftar[$jam] = "Jam {$jam} ({$waktu['waktu_mulai']} - {$waktu['waktu_selesai']})";
        }
        return $daftar;
    }

    public static function getCurrentSlotInfo(?Carbon $now = null): array
    {
        $now = $now ?? Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hariIndo = $days[$now->format('l')] ?? 'Senin';
        $currentTime = $now->format('H:i');

        $jamMasuk = self::getJamMasuk();
        $jamPulang = self::getJamPulang($hariIndo);
        $slots = self::getSlots($hariIndo);
        $istirahatList = self::getIstirahat($hariIndo);

        if ($hariIndo === 'Sabtu' || $hariIndo === 'Minggu') {
            return [
                'hari' => $hariIndo,
                'jam_ke' => null,
                'waktu_label' => $currentTime,
                'status' => 'libur',
                'keterangan' => 'Hari Libur Sekolah (Tidak Ada Jadwal Mengajar)',
            ];
        }

        // Cek apakah dalam slot mengajar
        foreach ($slots as $jamKe => $slot) {
            if ($currentTime >= $slot['waktu_mulai'] && $currentTime <= $slot['waktu_selesai']) {
                return [
                    'hari' => $hariIndo,
                    'jam_ke' => (int)$jamKe,
                    'waktu_mulai' => $slot['waktu_mulai'],
                    'waktu_selesai' => $slot['waktu_selesai'],
                    'waktu_label' => $slot['waktu_mulai'] . ' - ' . $slot['waktu_selesai'],
                    'status' => 'kbm',
                    'keterangan' => $slot['keterangan'] ?? "Jam Ke-{$jamKe}",
                ];
            }
        }

        // Cek apakah jam istirahat
        foreach ($istirahatList as $jamAfter => $ist) {
            if ($currentTime >= $ist['waktu_mulai'] && $currentTime <= $ist['waktu_selesai']) {
                return [
                    'hari' => $hariIndo,
                    'jam_ke' => null,
                    'waktu_label' => $ist['waktu'],
                    'status' => 'istirahat',
                    'keterangan' => 'Waktu ' . $ist['label'] . " ({$ist['waktu']})",
                ];
            }
        }

        if ($currentTime < $jamMasuk) {
            return [
                'hari' => $hariIndo,
                'jam_ke' => null,
                'waktu_label' => $currentTime,
                'status' => 'sebelum_kbm',
                'keterangan' => "Belum Masuk Jam KBM (Dimulai Pukul {$jamMasuk} WIB)",
            ];
        }

        // Sudah lewat jam terakhir KBM / Pulang
        if ($currentTime >= $jamPulang) {
            return [
                'hari' => $hariIndo,
                'jam_ke' => null,
                'waktu_label' => $currentTime,
                'status' => 'jam_pulang',
                'keterangan' => "Jam Pulang Sekolah (KBM Selesai Pukul {$jamPulang} WIB)",
            ];
        }

        return [
            'hari' => $hariIndo,
            'jam_ke' => null,
            'waktu_label' => $currentTime,
            'status' => 'jeda',
            'keterangan' => 'Di Luar Jam Sesi KBM Aktif',
        ];
    }

    /**
     * Payload terstruktur untuk live clock banner di JavaScript.
     */
    public static function getSlotsForJs(): array
    {
        $seninKamisSlots = self::getSlots('Senin');
        $seninKamisIst = self::getIstirahat('Senin');
        $jumatSlots = self::getSlots('Jumat');
        $jumatIst = self::getIstirahat('Jumat');

        $formatList = function($slots, $istirahat) {
            $list = [];
            foreach ($slots as $jam => $s) {
                $list[] = [
                    'jam' => (int)$jam,
                    'mulai' => $s['waktu_mulai'],
                    'selesai' => $s['waktu_selesai'],
                    'ket' => $s['keterangan'] ?? null,
                ];
                if (isset($istirahat[$jam])) {
                    $ist = $istirahat[$jam];
                    $list[] = [
                        'istirahat' => (int)$jam,
                        'mulai' => $ist['waktu_mulai'],
                        'selesai' => $ist['waktu_selesai'],
                        'ket' => $ist['label'] . " ({$ist['waktu']})",
                    ];
                }
            }
            return $list;
        };

        $jumatSlotsX = $jumatSlots;
        $jumatSlotsXi = array_filter($jumatSlots, function($k) {
            return (int)$k <= 12;
        }, ARRAY_FILTER_USE_KEY);

        return [
            'jam_masuk' => self::getJamMasuk(),
            'jam_pulang_senin_kamis' => self::getJamPulang('Senin'),
            'jam_pulang_jumat' => self::getJamPulang('Jumat'),
            'jam_pulang_jumat_x' => self::getJamPulangJumatX(),
            'jam_pulang_jumat_xi' => self::getJamPulangJumatXi(),
            'toleransi_terlambat' => self::getToleransiTerlambat(),
            'senin_kamis_list' => $formatList($seninKamisSlots, $seninKamisIst),
            'jumat_list' => $formatList($jumatSlotsX, $jumatIst),
            'jumat_x_list' => $formatList($jumatSlotsX, $jumatIst),
            'jumat_xi_list' => $formatList($jumatSlotsXi, $jumatIst),
        ];
    }

    public static function getDefaultSeninKamisSlots(): array
    {
        return self::$defaultSeninKamisSlots;
    }

    public static function getDefaultSeninKamisIstirahat(): array
    {
        return self::$defaultSeninKamisIstirahat;
    }

    public static function getDefaultJumatSlots(): array
    {
        return self::$defaultJumatSlots;
    }

    public static function getDefaultJumatIstirahat(): array
    {
        return self::$defaultJumatIstirahat;
    }
}

