<?php

namespace App\Services;

use Carbon\Carbon;

class KbmService
{
    /**
     * Alokasi Jam KBM SENIN - KAMIS
     * Sumber: Catatan KBM Halaman 49 PDF SMKN 1 Boyolangu
     * 1 JP = 40 menit
     */
    protected static array $seninKamisSlots = [
        1  => ['waktu_mulai' => '07:00', 'waktu_selesai' => '07:40', 'keterangan' => 'Upacara / Apel (Senin)'],
        2  => ['waktu_mulai' => '07:40', 'waktu_selesai' => '08:20'],
        3  => ['waktu_mulai' => '08:20', 'waktu_selesai' => '09:00'],
        4  => ['waktu_mulai' => '09:00', 'waktu_selesai' => '09:40'],
        // Istirahat 1: 09:40 - 10:00
        5  => ['waktu_mulai' => '10:00', 'waktu_selesai' => '10:40'],
        6  => ['waktu_mulai' => '10:40', 'waktu_selesai' => '11:20'],
        7  => ['waktu_mulai' => '11:20', 'waktu_selesai' => '12:00'],
        // Istirahat 2: 12:00 - 13:00
        8  => ['waktu_mulai' => '13:00', 'waktu_selesai' => '13:40'],
        9  => ['waktu_mulai' => '13:40', 'waktu_selesai' => '14:20'],
        10 => ['waktu_mulai' => '14:20', 'waktu_selesai' => '15:00'],
    ];

    /**
     * Istirahat Senin - Kamis
     */
    protected static array $seninKamisIstirahat = [
        4 => ['label' => 'Istirahat 1', 'waktu' => '09:40 - 10:00', 'waktu_mulai' => '09:40', 'waktu_selesai' => '10:00'],
        7 => ['label' => 'Istirahat 2', 'waktu' => '12:00 - 13:00', 'waktu_mulai' => '12:00', 'waktu_selesai' => '13:00'],
    ];

    /**
     * Alokasi Jam KBM JUMAT
     * Sumber: Catatan KBM Halaman 49 PDF SMKN 1 Boyolangu
     * 1 JP = 30 menit
     */
    protected static array $jumatSlots = [
        1  => ['waktu_mulai' => '07:00', 'waktu_selesai' => '07:30', 'keterangan' => 'Pembiasaan Hari Jumat'],
        2  => ['waktu_mulai' => '07:30', 'waktu_selesai' => '08:00'],
        3  => ['waktu_mulai' => '08:00', 'waktu_selesai' => '08:30'],
        4  => ['waktu_mulai' => '08:30', 'waktu_selesai' => '09:00'],
        5  => ['waktu_mulai' => '09:00', 'waktu_selesai' => '09:30'],
        // Istirahat 1: 09:30 - 09:50
        6  => ['waktu_mulai' => '09:50', 'waktu_selesai' => '10:20'],
        7  => ['waktu_mulai' => '10:20', 'waktu_selesai' => '10:50'],
        8  => ['waktu_mulai' => '10:50', 'waktu_selesai' => '11:20'],
        // Istirahat 2: 11:20 - 13:00
        9  => ['waktu_mulai' => '13:00', 'waktu_selesai' => '13:30'],
        10 => ['waktu_mulai' => '13:30', 'waktu_selesai' => '14:00'],
        11 => ['waktu_mulai' => '14:00', 'waktu_selesai' => '14:30'],
        12 => ['waktu_mulai' => '14:30', 'waktu_selesai' => '15:00'],
        13 => ['waktu_mulai' => '15:00', 'waktu_selesai' => '15:30'], // Khusus Kelas X
    ];

    /**
     * Istirahat Jumat
     */
    protected static array $jumatIstirahat = [
        5 => ['label' => 'Istirahat 1', 'waktu' => '09:30 - 09:50', 'waktu_mulai' => '09:30', 'waktu_selesai' => '09:50'],
        8 => ['label' => 'Istirahat 2', 'waktu' => '11:20 - 13:00', 'waktu_mulai' => '11:20', 'waktu_selesai' => '13:00'],
    ];

    /**
     * Cek apakah tingkat kelas adalah Kelas X (10)
     */
    public static function isKelasX(?string $tingkat = null): bool
    {
        if (!$tingkat) return true;
        $t = strtoupper(trim($tingkat));
        return $t === 'X' || $t === '10' || str_starts_with($t, 'X ') || str_starts_with($t, '10 ');
    }

    /**
     * Ambil alokasi jam KBM berdasarkan hari, jam_ke, dan tingkat kelas.
     */
    public static function getAlokasiWaktu(string $hari, int $jamKe, ?string $tingkat = null): ?array
    {
        $hariNorm = ucfirst(strtolower($hari));
        if ($hariNorm === 'Jumat') {
            if ($jamKe === 13 && $tingkat !== null && !self::isKelasX($tingkat)) {
                return null;
            }
            return self::$jumatSlots[$jamKe] ?? null;
        }
        return self::$seninKamisSlots[$jamKe] ?? null;
    }

    /**
     * Format label waktu "07:00 - 07:40" berdasarkan hari + jam_ke + tingkat.
     */
    public static function getLabelWaktu(string $hari, int $jamKe, ?string $tingkat = null): string
    {
        $alokasi = self::getAlokasiWaktu($hari, $jamKe, $tingkat);
        if (!$alokasi) return '-';
        return $alokasi['waktu_mulai'] . ' - ' . $alokasi['waktu_selesai'];
    }

    /**
     * Cek apakah jam_ke valid untuk hari dan tingkat tertentu.
     */
    public static function isJamKeValid(string $hari, int $jamKe, ?string $tingkat = null): bool
    {
        return self::getAlokasiWaktu($hari, $jamKe, $tingkat) !== null;
    }

    /**
     * Mengambil daftar jam ke- yang valid untuk hari dan tingkat tertentu.
     */
    public static function getDaftarJamKe(string $hari, ?string $tingkat = null): array
    {
        $hariNorm = ucfirst(strtolower($hari));
        $slots = ($hariNorm === 'Jumat') ? self::$jumatSlots : self::$seninKamisSlots;

        $daftar = [];
        foreach ($slots as $jam => $waktu) {
            if ($hariNorm === 'Jumat' && $jam === 13 && $tingkat !== null && !self::isKelasX($tingkat)) {
                continue;
            }
            $daftar[$jam] = "Jam {$jam} ({$waktu['waktu_mulai']} - {$waktu['waktu_selesai']})";
        }
        return $daftar;
    }

    /**
     * Deteksi slot jam KBM saat ini berdasarkan waktu laptop/server (Carbon $now).
     */
    public static function getCurrentSlotInfo(?Carbon $now = null): array
    {
        $now = $now ?? Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hariIndo = $days[$now->format('l')] ?? 'Senin';
        $currentTime = $now->format('H:i');

        $slots = ($hariIndo === 'Jumat') ? self::$jumatSlots : self::$seninKamisSlots;
        $istirahatList = ($hariIndo === 'Jumat') ? self::$jumatIstirahat : self::$seninKamisIstirahat;

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
                    'jam_ke' => $jamKe,
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

        if ($currentTime < '07:00') {
            return [
                'hari' => $hariIndo,
                'jam_ke' => null,
                'waktu_label' => $currentTime,
                'status' => 'sebelum_kbm',
                'keterangan' => 'Belum Masuk Jam KBM (Dimulai Pukul 07:00 WIB)',
            ];
        }

        // Sudah lewat jam terakhir KBM
        return [
            'hari' => $hariIndo,
            'jam_ke' => null,
            'waktu_label' => $currentTime,
            'status' => 'jam_pulang',
            'keterangan' => 'Jam Pulang Sekolah (Tidak Ada Jadwal Mengajar)',
        ];
    }
}

