<?php

namespace App\Services;

class KbmService
{
    /**
     * Map alokasi jam KBM Senin - Kamis
     */
    protected static array $seninKamisSlots = [
        1  => ['waktu_mulai' => '07:00', 'waktu_selesai' => '07:45'],
        2  => ['waktu_mulai' => '07:45', 'waktu_selesai' => '08:30'],
        3  => ['waktu_mulai' => '08:30', 'waktu_selesai' => '09:15'],
        4  => ['waktu_mulai' => '09:15', 'waktu_selesai' => '10:00'],
        5  => ['waktu_mulai' => '10:15', 'waktu_selesai' => '11:00'],
        6  => ['waktu_mulai' => '11:00', 'waktu_selesai' => '11:45'],
        7  => ['waktu_mulai' => '12:30', 'waktu_selesai' => '13:15'],
        8  => ['waktu_mulai' => '13:15', 'waktu_selesai' => '14:00'],
        9  => ['waktu_mulai' => '14:00', 'waktu_selesai' => '14:45'],
        10 => ['waktu_mulai' => '14:45', 'waktu_selesai' => '15:30'],
    ];

    /**
     * Map alokasi jam KBM Jumat
     */
    protected static array $jumatSlots = [
        1  => ['waktu_mulai' => '07:00', 'waktu_selesai' => '07:40'],
        2  => ['waktu_mulai' => '07:40', 'waktu_selesai' => '08:20'],
        3  => ['waktu_mulai' => '08:20', 'waktu_selesai' => '09:00'],
        4  => ['waktu_mulai' => '09:00', 'waktu_selesai' => '09:40'],
        5  => ['waktu_mulai' => '09:55', 'waktu_selesai' => '10:35'],
        6  => ['waktu_mulai' => '10:35', 'waktu_selesai' => '11:15'],
        7  => ['waktu_mulai' => '11:15', 'waktu_selesai' => '11:55'],
        8  => ['waktu_mulai' => '13:00', 'waktu_selesai' => '13:40'],
        9  => ['waktu_mulai' => '13:40', 'waktu_selesai' => '14:20'],
        10 => ['waktu_mulai' => '14:20', 'waktu_selesai' => '15:00'],
        11 => ['waktu_mulai' => '15:00', 'waktu_selesai' => '15:40'],
        12 => ['waktu_mulai' => '15:40', 'waktu_selesai' => '16:20'],
    ];

    /**
     * Ambil alokasi jam KBM berdasarkan hari dan jam_ke
     */
    public static function getAlokasiWaktu(string $hari, int $jamKe): ?array
    {
        $hariNorm = ucfirst(strtolower($hari));
        if ($hariNorm === 'Jumat') {
            return self::$jumatSlots[$jamKe] ?? null;
        }
        return self::$seninKamisSlots[$jamKe] ?? null;
    }

    /**
     * Cek apakah jam_ke valid untuk hari tertentu
     */
    public static function isJamKeValid(string $hari, int $jamKe): bool
    {
        return self::getAlokasiWaktu($hari, $jamKe) !== null;
    }

    /**
     * Mengambil daftar jam ke- beserta label waktunya
     */
    public static function getDaftarJamKe(string $hari): array
    {
        $hariNorm = ucfirst(strtolower($hari));
        $slots = ($hariNorm === 'Jumat') ? self::$jumatSlots : self::$seninKamisSlots;

        $daftar = [];
        foreach ($slots as $jam => $waktu) {
            $daftar[$jam] = "Jam ke-{$jam} ({$waktu['waktu_mulai']} - {$waktu['waktu_selesai']})";
        }
        return $daftar;
    }
}
