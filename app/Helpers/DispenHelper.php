<?php

namespace App\Helpers;

class DispenHelper
{
    public static function kategoriLabel(?string $kategori): string
    {
        return match($kategori) {
            'dispen_masuk'   => 'Dispensasi Masuk',
            'dispen_keluar'  => 'Dispensasi Keluar',
            'dispen_lomba'   => 'Dispensasi Lomba / Kegiatan',
            'terlambat'      => 'Terlambat',
            'sakit'          => 'Izin Sakit',
            'izin'           => 'Izin',
            'acara_keluarga' => 'Izin Acara Keluarga',
            'izin_guru'      => 'Dispensasi Guru',
            'dispensasi'     => 'Dispensasi Siswa',
            'izin_masuk'     => 'Dispensasi Masuk',
            'izin_keluar'    => 'Dispensasi Keluar',
            default          => $kategori ? ucfirst(str_replace('_', ' ', $kategori)) : '-'
        };
    }

    public static function badgeColor(?string $kategori): string
    {
        return match($kategori) {
            'dispen_masuk'   => 'badge-navy',
            'dispen_keluar'  => 'badge-amber',
            'dispen_lomba'   => 'badge-purple',
            'terlambat'      => 'badge-warning',
            'sakit'          => 'badge-purple',
            'izin'           => 'badge-info',
            'acara_keluarga' => 'badge-info',
            'izin_guru'      => 'badge-warning',
            default          => 'badge-primary'
        };
    }
}
