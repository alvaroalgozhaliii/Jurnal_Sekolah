<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sinkronisasi seluruh siswa yang memiliki id_user ke tabel pivot ortu_siswa
        if (Schema::hasTable('ortu_siswa') && Schema::hasTable('siswa')) {
            DB::statement("
                INSERT IGNORE INTO ortu_siswa (id_user, id_siswa, created_at)
                SELECT id_user, id_siswa, NOW()
                FROM siswa
                WHERE id_user IS NOT NULL
            ");
        }

        // 2. Sinkronisasi pengajuan izin yang sudah ada (completed / verified / disetujui) ke absensi_siswa
        if (Schema::hasTable('pengajuan_izin') && Schema::hasTable('absensi_siswa')) {
            $izins = DB::table('pengajuan_izin')
                ->whereNotNull('id_siswa')
                ->whereIn('kategori', ['sakit', 'izin', 'acara_keluarga'])
                ->whereIn('status', ['completed', 'verified', 'disetujui', 'disetujui_waka', 'disetujui_kepala'])
                ->get();

            foreach ($izins as $izin) {
                $statusAbsensi = ($izin->kategori === 'sakit') ? 'sakit' : 'izin';
                $tgl = $izin->tanggal;

                $exists = DB::table('absensi_siswa')
                    ->where('id_siswa', $izin->id_siswa)
                    ->where(function ($q) use ($tgl) {
                        $q->whereDate('created_at', $tgl)
                          ->orWhereExists(function ($sub) use ($tgl) {
                              $sub->select(DB::raw(1))
                                  ->from('jurnal_harian')
                                  ->whereColumn('jurnal_harian.id_jurnal', 'absensi_siswa.id_jurnal')
                                  ->where('jurnal_harian.tanggal', $tgl);
                          });
                    })
                    ->exists();

                if (!$exists) {
                    $siswaObj = DB::table('siswa')->where('id_siswa', $izin->id_siswa)->first();
                    $idJurnal = null;
                    if ($siswaObj && $siswaObj->id_kelas) {
                        $jurnal = DB::table('jurnal_harian')
                            ->where('tanggal', $tgl)
                            ->whereExists(function ($q) use ($siswaObj) {
                                $q->select(DB::raw(1))
                                  ->from('jadwal')
                                  ->whereColumn('jadwal.id_jadwal', 'jurnal_harian.id_jadwal')
                                  ->where('jadwal.id_kelas', $siswaObj->id_kelas);
                            })
                            ->first();
                        if ($jurnal) {
                            $idJurnal = $jurnal->id_jurnal;
                        }
                    }

                    $dicatatOleh = null;
                    if ($izin->id_user_pengaju && DB::table('users')->where('id_user', $izin->id_user_pengaju)->exists()) {
                        $dicatatOleh = $izin->id_user_pengaju;
                    }

                    DB::table('absensi_siswa')->insert([
                        'id_jurnal' => $idJurnal,
                        'id_siswa' => $izin->id_siswa,
                        'status' => $statusAbsensi,
                        'jam_masuk' => $izin->jam_mulai,
                        'keterangan' => ($izin->alasan ? $izin->alasan . ' ' : '') . '(Izin Orang Tua)',
                        'dicatat_oleh' => $dicatatOleh,
                        'created_at' => $tgl . ' 07:00:00',
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
    }
};