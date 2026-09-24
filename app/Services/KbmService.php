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

    /**
     * Cek apakah hari Senin ada Upacara Bendera
     */
    public static function isSeninAdaUpacara(?string $tanggal = null): bool
    {
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $targetDate = $tanggal ?: $now->toDateString();

        try {
            // Cek override situasional untuk tanggal tertentu
            $overrideTgl = Pengaturan::getVal('senin_override_tanpa_upacara_tanggal');
            if ($overrideTgl && $overrideTgl === $targetDate) {
                return false;
            }

            // Pengaturan default mingguan
            $val = Pengaturan::getVal('senin_ada_upacara', '1');
            return ($val !== '0' && $val !== 0 && $val !== false);
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Cek apakah hari Jumat ada Pembiasaan Pagi
     */
    public static function isJumatAdaPembiasaan(?string $tanggal = null): bool
    {
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $targetDate = $tanggal ?: $now->toDateString();

        try {
            // Cek override situasional untuk tanggal tertentu
            $overrideTgl = Pengaturan::getVal('jumat_override_tanpa_pembiasaan_tanggal');
            if ($overrideTgl && $overrideTgl === $targetDate) {
                return false;
            }

            // Pengaturan default mingguan
            $val = Pengaturan::getVal('jumat_ada_pembiasaan', '1');
            return ($val !== '0' && $val !== 0 && $val !== false);
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Ambil data Kepulangan Khusus / Acara Mendadak jika aktif untuk tanggal tersebut
     */
    public static function getAcaraMendadakInfo(?string $tanggal = null): ?array
    {
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $targetDate = $tanggal ?: $now->toDateString();

        try {
            $aktif = Pengaturan::getVal('acara_mendadak_aktif', '0');
            $tglAcara = Pengaturan::getVal('acara_mendadak_tanggal', '');

            if (($aktif === '1' || $aktif === 1 || $aktif === true) && $tglAcara === $targetDate) {
                return [
                    'aktif' => true,
                    'tanggal' => $targetDate,
                    'jam_pulang' => Pengaturan::getVal('acara_mendadak_jam_pulang', '11:30') ?: '11:30',
                    'alasan' => Pengaturan::getVal('acara_mendadak_alasan', 'Acara Khusus Sekolah (Pulang Cepat)') ?: 'Acara Khusus Sekolah (Pulang Cepat)',
                    'target' => Pengaturan::getVal('acara_mendadak_target', 'semua') ?: 'semua',
                ];
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return null;
    }

    /**
     * Helper mengurangi waktu format HH:mm sebanyak N menit
     */
    public static function kurangiWaktuMenit(string $waktuStr, int $menit): string
    {
        try {
            $parts = explode(':', trim($waktuStr));
            $jam = (int)($parts[0] ?? 0);
            $menitAwal = (int)($parts[1] ?? 0);
            $totalMenit = ($jam * 60 + $menitAwal) - $menit;
            if ($totalMenit < 0) {
                $totalMenit += 24 * 60;
            }
            $jamBaru = intdiv($totalMenit, 60) % 24;
            $menitBaru = $totalMenit % 60;
            return sprintf('%02d:%02d', $jamBaru, $menitBaru);
        } catch (\Throwable $e) {
            return $waktuStr;
        }
    }

    /**
     * Mengambil Jam Pulang Standar/Normal (tanpa mempertimbangkan penyesuaian situasional)
     */
    public static function getJamPulangNormal(string $hari = 'Senin', ?string $tingkat = null): string
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

    /**
     * Mengambil Jam Pulang Efektif dengan memperhitungkan:
     * 1. Acara Mendadak (Pulang Cepat) jika ada
     * 2. Senin tanpa upacara (maju 1 JP = 40 menit -> 14:20)
     * 3. Jumat tanpa pembiasaan (maju 1 JP = 30 menit -> X: 15:00, XI/XII: 14:30)
     */
    public static function getJamPulang(string $hari = 'Senin', ?string $tingkat = null, ?string $tanggal = null, bool $checkAcaraMendadak = true): string
    {
        $hariNorm = ucfirst(strtolower($hari));

        // 1. Cek Acara Mendadak jika aktif untuk tanggal ini
        if ($checkAcaraMendadak) {
            $acara = self::getAcaraMendadakInfo($tanggal);
            if ($acara) {
                $target = strtolower($acara['target'] ?? 'semua');
                if ($target === 'semua') {
                    return $acara['jam_pulang'];
                }
                if ($tingkat !== null) {
                    if ($target === 'x' && self::isKelasX($tingkat)) {
                        return $acara['jam_pulang'];
                    }
                    if (($target === 'xi' || $target === 'xii') && !self::isKelasX($tingkat)) {
                        return $acara['jam_pulang'];
                    }
                } else {
                    return $acara['jam_pulang'];
                }
            }
        }

        // 2. Cek Aturan Hari Senin
        if ($hariNorm === 'Senin') {
            $normalPulang = self::getJamPulangNormal('Senin');
            if (!self::isSeninAdaUpacara($tanggal)) {
                $durasiJp = self::getDurasiPelajaran('Senin'); // default 40 menit
                return self::kurangiWaktuMenit($normalPulang, $durasiJp);
            }
            return $normalPulang;
        }

        // 3. Cek Aturan Hari Jumat
        if ($hariNorm === 'Jumat') {
            $isX = ($tingkat === null || self::isKelasX($tingkat));
            $normalPulang = self::getJamPulangNormal('Jumat', $tingkat);
            if (!self::isJumatAdaPembiasaan($tanggal)) {
                $durasiJp = self::getDurasiPelajaran('Jumat'); // default 30 menit
                return self::kurangiWaktuMenit($normalPulang, $durasiJp);
            }
            return $normalPulang;
        }

        return self::getJamPulangNormal($hariNorm, $tingkat);
    }

    public static function getJamPulangJumatX(?string $tanggal = null, bool $checkAcaraMendadak = true): string
    {
        return self::getJamPulang('Jumat', 'X', $tanggal, $checkAcaraMendadak);
    }

    public static function getJamPulangJumatXi(?string $tanggal = null, bool $checkAcaraMendadak = true): string
    {
        return self::getJamPulang('Jumat', 'XI', $tanggal, $checkAcaraMendadak);
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
        $todayDate = $now->toDateString();
        $currentTime = $now->format('H:i');

        $jamMasuk = self::getJamMasuk();
        $jamPulang = self::getJamPulang($hariIndo, null, $todayDate, true);
        $slots = self::getSlots($hariIndo);
        $istirahatList = self::getIstirahat($hariIndo);
        $acaraMendadak = self::getAcaraMendadakInfo($todayDate);

        if ($hariIndo === 'Sabtu' || $hariIndo === 'Minggu') {
            return [
                'hari' => $hariIndo,
                'jam_ke' => null,
                'waktu_label' => $currentTime,
                'status' => 'libur',
                'keterangan' => 'Hari Libur Sekolah (Tidak Ada Jadwal Mengajar)',
            ];
        }

        // Sudah lewat jam terakhir KBM / Pulang (Evaluasi awal terutama jika ada acara mendadak pulang cepat)
        if ($currentTime >= $jamPulang) {
            $ketPulang = "Jam Pulang Sekolah (KBM Selesai Pukul {$jamPulang} WIB)";
            if ($acaraMendadak) {
                $ketPulang = "Jam Pulang Sekolah (Acara Mendadak: {$acaraMendadak['alasan']} - Dipulangkan Pukul {$jamPulang} WIB)";
            } elseif ($hariIndo === 'Senin' && !self::isSeninAdaUpacara($todayDate)) {
                $ketPulang = "Jam Pulang Sekolah (Senin Tanpa Upacara: Pulang Maju Pukul {$jamPulang} WIB)";
            } elseif ($hariIndo === 'Jumat' && !self::isJumatAdaPembiasaan($todayDate)) {
                $ketPulang = "Jam Pulang Sekolah (Jumat Tanpa Pembiasaan: Pulang Maju Pukul {$jamPulang} WIB)";
            }

            return [
                'hari' => $hariIndo,
                'jam_ke' => null,
                'waktu_label' => $currentTime,
                'status' => 'jam_pulang',
                'keterangan' => $ketPulang,
            ];
        }

        // Cek apakah dalam slot mengajar
        foreach ($slots as $jamKe => $slot) {
            if ($currentTime >= $slot['waktu_mulai'] && $currentTime <= $slot['waktu_selesai']) {
                $ketSlot = $slot['keterangan'] ?? "Jam Ke-{$jamKe}";
                if ($jamKe == 1 && $hariIndo === 'Senin' && !self::isSeninAdaUpacara($todayDate)) {
                    $ketSlot = 'KBM Jam Ke-1 (Tanpa Upacara Bendera)';
                } elseif ($jamKe == 1 && $hariIndo === 'Jumat' && !self::isJumatAdaPembiasaan($todayDate)) {
                    $ketSlot = 'KBM Jam Ke-1 (Tanpa Pembiasaan Pagi)';
                }

                return [
                    'hari' => $hariIndo,
                    'jam_ke' => (int)$jamKe,
                    'waktu_mulai' => $slot['waktu_mulai'],
                    'waktu_selesai' => $slot['waktu_selesai'],
                    'waktu_label' => $slot['waktu_mulai'] . ' - ' . $slot['waktu_selesai'],
                    'status' => 'kbm',
                    'keterangan' => $ketSlot,
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
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $todayDate = $now->toDateString();
        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hariIndo = $days[$now->format('l')] ?? 'Senin';

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

        $seninAdaUpacara = self::isSeninAdaUpacara($todayDate);
        $jumatAdaPembiasaan = self::isJumatAdaPembiasaan($todayDate);
        $acaraMendadak = self::getAcaraMendadakInfo($todayDate);

        // Keterangan khusus jika ada penyesuaian kepulangan hari ini
        $infoKhususHariIni = null;
        if ($acaraMendadak) {
            $infoKhususHariIni = [
                'tipe' => 'acara_mendadak',
                'judul' => 'Acara Mendadak / Pulang Cepat',
                'pesan' => "Hari ini siswa dipulangkan pukul {$acaraMendadak['jam_pulang']} WIB sehubungan dengan {$acaraMendadak['alasan']}.",
                'jam_pulang' => $acaraMendadak['jam_pulang'],
                'target' => $acaraMendadak['target'],
            ];
        } elseif ($hariIndo === 'Senin' && !$seninAdaUpacara) {
            $jamPulangSenin = self::getJamPulang('Senin', null, $todayDate);
            $infoKhususHariIni = [
                'tipe' => 'tanpa_upacara',
                'judul' => 'Senin Tanpa Upacara Bendera',
                'pesan' => "Upacara hari ini ditiadakan. Kepulangan siswa dimajukan 1 jam pelajaran (Pulang pukul {$jamPulangSenin} WIB).",
                'jam_pulang' => $jamPulangSenin,
            ];
        } elseif ($hariIndo === 'Jumat' && !$jumatAdaPembiasaan) {
            $jamPulangX = self::getJamPulangJumatX($todayDate);
            $jamPulangXi = self::getJamPulangJumatXi($todayDate);
            $infoKhususHariIni = [
                'tipe' => 'tanpa_pembiasaan',
                'judul' => 'Jumat Tanpa Pembiasaan',
                'pesan' => "Pembiasaan hari ini ditiadakan. Kepulangan siswa dimajukan 1 jam pelajaran (Kelas X: {$jamPulangX} WIB, Kelas XI/XII: {$jamPulangXi} WIB).",
                'jam_pulang_x' => $jamPulangX,
                'jam_pulang_xi' => $jamPulangXi,
            ];
        }

        return [
            'jam_masuk' => self::getJamMasuk(),
            'jam_pulang_senin_kamis' => self::getJamPulang('Senin', null, $todayDate, false),
            'jam_pulang_jumat' => self::getJamPulang('Jumat', null, $todayDate, false),
            'jam_pulang_jumat_x' => self::getJamPulangJumatX($todayDate, false),
            'jam_pulang_jumat_xi' => self::getJamPulangJumatXi($todayDate, false),
            'toleransi_terlambat' => self::getToleransiTerlambat(),
            'senin_ada_upacara' => $seninAdaUpacara,
            'jumat_ada_pembiasaan' => $jumatAdaPembiasaan,
            'acara_mendadak' => $acaraMendadak,
            'info_khusus_hari_ini' => $infoKhususHariIni,
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

