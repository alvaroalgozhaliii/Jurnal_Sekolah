<?php

namespace App\Services;

class KbmService
{
    /**
     * Alokasi Jam KBM SENIN - KAMIS
     * Sumber: ALOKASI JAM KBM BARU REVISI
     *
     * Jam 1-4  : 40 menit per JP
     * Jam 5-10 : 35 menit per JP
     */
    protected static array $seninKamisSlots = [
        1  => ['waktu_mulai' => '07:00', 'waktu_selesai' => '07:40'],
        2  => ['waktu_mulai' => '07:40', 'waktu_selesai' => '08:20'],
        3  => ['waktu_mulai' => '08:20', 'waktu_selesai' => '09:00'],
        4  => ['waktu_mulai' => '09:00', 'waktu_selesai' => '09:40'],
        // Istirahat 1: 09:40 - 10:00
        5  => ['waktu_mulai' => '10:00', 'waktu_selesai' => '10:35'],
        6  => ['waktu_mulai' => '10:35', 'waktu_selesai' => '11:10'],
        7  => ['waktu_mulai' => '11:10', 'waktu_selesai' => '11:45'],
        // Istirahat 2: 11:45 - 13:15
        8  => ['waktu_mulai' => '13:15', 'waktu_selesai' => '13:50'],
        9  => ['waktu_mulai' => '13:50', 'waktu_selesai' => '14:25'],
        10 => ['waktu_mulai' => '14:25', 'waktu_selesai' => '15:00'],
        // Jam 11, 12, 13 tidak ada alokasi Senin-Kamis
    ];

    /**
     * Istirahat Senin - Kamis (untuk tampilan jadwal)
     */
    protected static array $seninKamisIstirahat = [
        // Setelah jam 4
        4 => ['label' => 'Istirahat 1', 'waktu' => '09:40 - 10:00'],
        // Setelah jam 7
        7 => ['label' => 'Istirahat 2', 'waktu' => '11:45 - 13:15'],
    ];

    /**
     * Alokasi Jam KBM JUMAT
     * Sumber: ALOKASI JAM KBM BARU REVISI
     *
     * Jam 1-8  : 30 menit per JP
     * Jam 9-13 : 25 menit per JP (setelah istirahat kedua)
     */
    protected static array $jumatSlots = [
        1  => ['waktu_mulai' => '07:00', 'waktu_selesai' => '07:30'],
        2  => ['waktu_mulai' => '07:30', 'waktu_selesai' => '08:00'],
        3  => ['waktu_mulai' => '08:00', 'waktu_selesai' => '08:30'],
        4  => ['waktu_mulai' => '08:30', 'waktu_selesai' => '09:00'],
        // Istirahat 1: 09:00 - 09:30
        5  => ['waktu_mulai' => '09:30', 'waktu_selesai' => '09:50'],
        6  => ['waktu_mulai' => '09:50', 'waktu_selesai' => '10:20'],
        7  => ['waktu_mulai' => '10:20', 'waktu_selesai' => '10:50'],
        8  => ['waktu_mulai' => '10:50', 'waktu_selesai' => '11:20'],
        // Istirahat 2: 11:20 - 13:00
        9  => ['waktu_mulai' => '13:00', 'waktu_selesai' => '13:30'],
        10 => ['waktu_mulai' => '13:30', 'waktu_selesai' => '14:00'],
        11 => ['waktu_mulai' => '14:00', 'waktu_selesai' => '14:30'],
        12 => ['waktu_mulai' => '14:30', 'waktu_selesai' => '15:00'],
        13 => ['waktu_mulai' => '15:00', 'waktu_selesai' => '15:30'],
    ];

    /**
     * Istirahat Jumat (untuk tampilan jadwal)
     */
    protected static array $jumatIstirahat = [
        // Setelah jam 4
        4 => ['label' => 'Istirahat 1', 'waktu' => '09:00 - 09:30'],
        // Setelah jam 8
        8 => ['label' => 'Istirahat 2', 'waktu' => '11:20 - 13:00'],
    ];

    /**
     * Cek apakah tingkat kelas adalah Kelas X (10)
     */
    public static function isKelasX(?string $tingkat = null): bool
    {
        if (!$tingkat) return true; // default true jika tidak ada info tingkat
        $t = strtoupper(trim($tingkat));
        return $t === 'X' || $t === '10' || str_starts_with($t, 'X ') || str_starts_with($t, '10 ');
    }

    /**
     * Ambil alokasi jam KBM berdasarkan hari, jam_ke, dan tingkat kelas.
     * Khusus hari Jumat, Jam 13 hanya tersedia untuk Kelas X (10).
     */
    public static function getAlokasiWaktu(string $hari, int $jamKe, ?string $tingkat = null): ?array
    {
        $hariNorm = ucfirst(strtolower($hari));
        if ($hariNorm === 'Jumat') {
            // Jam 13 Jumat hanya ada untuk kelas X / 10
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
     * Mengembalikan array [jam_ke => 'Jam X (HH:MM - HH:MM)']
     */
    public static function getDaftarJamKe(string $hari, ?string $tingkat = null): array
    {
        $hariNorm = ucfirst(strtolower($hari));
        $slots = ($hariNorm === 'Jumat') ? self::$jumatSlots : self::$seninKamisSlots;

        $daftar = [];
        foreach ($slots as $jam => $waktu) {
            if ($hariNorm === 'Jumat' && $jam === 13 && $tingkat !== null && !self::isKelasX($tingkat)) {
                continue; // Skip jam 13 untuk non-kelas X di hari Jumat
            }
            $daftar[$jam] = "Jam {$jam} ({$waktu['waktu_mulai']} - {$waktu['waktu_selesai']})";
        }
        return $daftar;
    }

    /**
     * Mengambil daftar semua pilihan jam_ke (1-13) untuk dropdown statis.
     */
    public static function getAllJamKe(): array
    {
        $all = [];
        for ($i = 1; $i <= 13; $i++) {
            $all[$i] = "Jam {$i}";
        }
        return $all;
    }

    /**
     * Ambil data istirahat setelah jam tertentu berdasarkan hari.
     */
    public static function getIstirahatSetelahJam(string $hari, int $jamKe): ?array
    {
        $hariNorm = ucfirst(strtolower($hari));
        if ($hariNorm === 'Jumat') {
            return self::$jumatIstirahat[$jamKe] ?? null;
        }
        return self::$seninKamisIstirahat[$jamKe] ?? null;
    }

    /**
     * Ambil seluruh alokasi waktu (untuk JSON di frontend).
     */
    public static function getAllAlokasiAsJson(): string
    {
        $data = [];
        foreach (['Senin','Selasa','Rabu','Kamis','Jumat'] as $hari) {
            $hariKey = strtolower($hari);
            if ($hari === 'Jumat') {
                foreach (self::$jumatSlots as $jam => $waktu) {
                    $data[$hariKey][$jam] = $waktu['waktu_mulai'] . ' - ' . $waktu['waktu_selesai'];
                }
            } else {
                foreach (self::$seninKamisSlots as $jam => $waktu) {
                    $data[$hariKey][$jam] = $waktu['waktu_mulai'] . ' - ' . $waktu['waktu_selesai'];
                }
            }
        }
        return json_encode($data);
    }
}
