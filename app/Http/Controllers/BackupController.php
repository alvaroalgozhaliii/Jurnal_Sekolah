<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        return view('admin.backup');
    }

    public function exportDatabase()
    {
        $tables = ['users', 'guru', 'siswa', 'kelas', 'jurusan', 'jadwal', 'jurnal_harian', 'absensi_siswa', 'presensi_masuk', 'absensi_guru_piket', 'tahun_pelajaran', 'pengaturan'];
        $backupData = [];

        foreach ($tables as $table) {
            if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                $backupData[$table] = DB::table($table)->get()->toArray();
            }
        }

        $filename = 'backup_jurnal_sekolah_' . date('Y_m_d_His') . '.json';
        $json = json_encode($backupData, JSON_PRETTY_PRINT);

        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function restoreDatabase(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:json|max:10240',
        ]);

        $file = $request->file('backup_file');
        $content = file_get_contents($file->getRealPath());
        $data = json_decode($content, true);

        if (!is_array($data)) {
            return back()->with('error', 'Format file backup tidak valid.');
        }

        try {
            DB::beginTransaction();

            foreach ($data as $table => $rows) {
                if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                    // Safe restore: insert or update rows without dropping tables
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $primaryKey = match ($table) {
                            'users' => 'id_user',
                            'guru' => 'id_guru',
                            'siswa' => 'id_siswa',
                            'kelas' => 'id_kelas',
                            'jurusan' => 'id_jurusan',
                            'jadwal' => 'id_jadwal',
                            'jurnal_harian' => 'id_jurnal',
                            'absensi_siswa' => 'id_absensi',
                            'presensi_masuk' => 'id_presensi',
                            'absensi_guru_piket' => 'id_piket',
                            'tahun_pelajaran' => 'id_tahun_pelajaran',
                            'pengaturan' => 'id_pengaturan',
                            default => null,
                        };

                        if ($primaryKey && isset($rowArray[$primaryKey])) {
                            DB::table($table)->updateOrInsert(
                                [$primaryKey => $rowArray[$primaryKey]],
                                $rowArray
                            );
                        }
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Database berhasil direstore.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }
}
