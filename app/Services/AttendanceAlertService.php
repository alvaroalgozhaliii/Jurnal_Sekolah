<?php

namespace App\Services;

use App\Models\AbsensiSiswa;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceAlertService
{
    /**
     * Hitung peringatan untuk 1 siswa
     *
     * @param int $idSiswa
     * @return array
     */
    public static function checkSiswa(int $idSiswa): array
    {
        $alerts = [];
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        // 1. Ambil absensi 45 hari terakhir beserta tanggal jurnal
        $absensiTerbaru = AbsensiSiswa::with('jurnal')
            ->where('id_siswa', $idSiswa)
            ->whereHas('jurnal', function ($q) use ($now) {
                $q->where('tanggal', '<=', $now->toDateString())
                  ->where('tanggal', '>=', $now->copy()->subDays(45)->toDateString());
            })
            ->get();

        // Kelompokkan status per tanggal (unik per hari)
        $statusPerTanggal = [];
        foreach ($absensiTerbaru as $ab) {
            $tgl = $ab->jurnal->tanggal ?? null;
            if (!$tgl) continue;
            $tglKey = substr($tgl, 0, 10);
            $st = strtolower($ab->status);

            if (!isset($statusPerTanggal[$tglKey])) {
                $statusPerTanggal[$tglKey] = [];
            }
            $statusPerTanggal[$tglKey][] = $st;
        }

        // Tentukan status dominan per hari (Alpa > Sakit > Izin > Terlambat > Hadir)
        $harian = [];
        foreach ($statusPerTanggal as $tglKey => $statuses) {
            if (in_array('alpa', $statuses)) {
                $harian[$tglKey] = 'alpa';
            } elseif (in_array('sakit', $statuses)) {
                $harian[$tglKey] = 'sakit';
            } elseif (in_array('izin', $statuses)) {
                $harian[$tglKey] = 'izin';
            } elseif (in_array('terlambat', $statuses)) {
                $harian[$tglKey] = 'terlambat';
            } else {
                $harian[$tglKey] = 'hadir';
            }
        }

        // Urutkan tanggal dari yang paling baru ke terlama
        krsort($harian);

        // 2. Hitung streak berturut-turut dari tanggal terbaru
        $streakAlpa = 0;
        $streakIzinSakit = 0;
        $countingAlpa = true;
        $countingIzinSakit = true;

        foreach ($harian as $tglKey => $status) {
            // Hitung streak Alpa berturut-turut
            if ($countingAlpa) {
                if ($status === 'alpa') {
                    $streakAlpa++;
                } else {
                    $countingAlpa = false;
                }
            }

            // Hitung streak Izin/Sakit berturut-turut
            if ($countingIzinSakit) {
                if (in_array($status, ['izin', 'sakit'])) {
                    $streakIzinSakit++;
                } else {
                    $countingIzinSakit = false;
                }
            }

            if (!$countingAlpa && !$countingIzinSakit) {
                break;
            }
        }

        // 3. Hitung akumulasi status di bulan berjalan
        $absensiBulanIni = AbsensiSiswa::where('id_siswa', $idSiswa)
            ->whereHas('jurnal', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
            })
            ->get();

        $bulanStatusPerTanggal = [];
        foreach ($absensiBulanIni as $ab) {
            $tgl = $ab->jurnal->tanggal ?? null;
            if (!$tgl) continue;
            $tglKey = substr($tgl, 0, 10);
            $st = strtolower($ab->status);

            if (!isset($bulanStatusPerTanggal[$tglKey])) {
                $bulanStatusPerTanggal[$tglKey] = [];
            }
            $bulanStatusPerTanggal[$tglKey][] = $st;
        }

        $totalAlpaBulanIni = 0;
        $totalIzinSakitBulanIni = 0;
        foreach ($bulanStatusPerTanggal as $statuses) {
            if (in_array('alpa', $statuses)) {
                $totalAlpaBulanIni++;
            } elseif (in_array('sakit', $statuses) || in_array('izin', $statuses)) {
                $totalIzinSakitBulanIni++;
            }
        }

        // 4. Evaluasi Kondisi Peringatan
        // A. Alpa berturut-turut >= 3 hari
        if ($streakAlpa >= 3) {
            $alerts[] = [
                'level' => 'danger',
                'icon'  => '🚨',
                'tipe'  => 'streak_alpa',
                'pesan' => "Siswa tercatat <b>Alpa (Tanpa Keterangan) selama {$streakAlpa} hari berturut-turut</b> dalam catatan KBM.",
                'saran' => "Harap segera hubungi pihak sekolah / wali kelas atau orang tua untuk memastikan kondisi siswa."
            ];
        }

        // B. Izin / Sakit berturut-turut >= 5 hari
        if ($streakIzinSakit >= 5) {
            $alerts[] = [
                'level' => 'warning',
                'icon'  => '⚠️',
                'tipe'  => 'streak_izin_sakit',
                'pesan' => "Siswa tercatat <b>Izin / Sakit selama {$streakIzinSakit} hari berturut-turut</b>.",
                'saran' => "Pastikan surat dokter atau surat izin resmi telah diserahkan dan pantau kondisi kesehatan siswa."
            ];
        }

        // C. Akumulasi Alpa bulan ini >= 5 hari
        if ($totalAlpaBulanIni >= 5 && $streakAlpa < 3) {
            $alerts[] = [
                'level' => 'danger',
                'icon'  => '❗',
                'tipe'  => 'akumulasi_alpa',
                'pesan' => "Siswa memiliki total <b>{$totalAlpaBulanIni} hari Alpa</b> pada bulan ini.",
                'saran' => "Tingkat ketidakhadiran tanpa keterangan sudah tinggi dan berisiko mempengaruhi evaluasi akademik."
            ];
        }

        // D. Akumulasi Izin/Sakit bulan ini >= 8 hari
        if ($totalIzinSakitBulanIni >= 8 && $streakIzinSakit < 5) {
            $alerts[] = [
                'level' => 'caution',
                'icon'  => '📋',
                'tipe'  => 'akumulasi_izin',
                'pesan' => "Siswa memiliki total <b>{$totalIzinSakitBulanIni} hari Izin/Sakit</b> pada bulan ini.",
                'saran' => "Pertimbangkan koordinasi antara wali kelas dan orang tua mengenai materi pelajaran yang tertinggal."
            ];
        }

        $highestLevel = null;
        if (!empty($alerts)) {
            $levels = array_column($alerts, 'level');
            if (in_array('danger', $levels)) {
                $highestLevel = 'danger';
            } elseif (in_array('warning', $levels)) {
                $highestLevel = 'warning';
            } else {
                $highestLevel = 'caution';
            }
        }

        return [
            'has_alert'      => !empty($alerts),
            'highest_level'  => $highestLevel,
            'streak_alpa'    => $streakAlpa,
            'streak_izin'    => $streakIzinSakit,
            'total_alpa'     => $totalAlpaBulanIni,
            'total_izin'     => $totalIzinSakitBulanIni,
            'alerts'         => $alerts,
        ];
    }

    /**
     * Hitung peringatan untuk seluruh siswa dalam suatu kelas (bulk)
     *
     * @param Collection|array $siswaIds
     * @return array [id_siswa => alert_data]
     */
    public static function checkBulk(iterable $siswaIds): array
    {
        $result = [];
        foreach ($siswaIds as $id) {
            $idSiswa = is_object($id) ? ($id->id_siswa ?? $id->id) : (int)$id;
            $result[$idSiswa] = self::checkSiswa($idSiswa);
        }
        return $result;
    }
}
