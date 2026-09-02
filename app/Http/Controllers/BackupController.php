<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class BackupController extends Controller
{
    public function index()
    {
        $backupFiles = [];
        if (Storage::exists('backups')) {
            $files = Storage::files('backups');
            foreach ($files as $file) {
                $backupFiles[] = [
                    'name' => basename($file),
                    'size' => Storage::size($file),
                    'modified' => date('d/m/Y H:i:s', Storage::lastModified($file)),
                    'path' => $file,
                ];
            }
            usort($backupFiles, fn($a, $b) => strcmp($b['name'], $a['name']));
        }

        return view('admin.backup', compact('backupFiles'));
    }

    public function exportDatabase()
    {
        $dbName = config('database.connections.mysql.database') ?? env('DB_DATABASE', 'jurnal_sekolah');
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $dbName;

        $sql = "-- ======================================================\n";
        $sql .= "-- BACKUP DATABASE JURNAL SEKOLAH SMKN 1 BOYOLANGU\n";
        $sql .= "-- Tanggal: " . now()->format('d/m/Y H:i:s') . "\n";
        $sql .= "-- Database: $dbName\n";
        $sql .= "-- ======================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

        foreach ($tables as $tableObj) {
            $props = (array) $tableObj;
            $tableName = reset($props);

            if (empty($tableName)) continue;

            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (!empty($createTable)) {
                $createArr = (array) $createTable[0];
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= ($createArr['Create Table'] ?? '') . ";\n\n";
            }

            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                foreach ($rows->chunk(100) as $chunk) {
                    $insertValues = [];
                    foreach ($chunk as $row) {
                        $escaped = [];
                        foreach ((array)$row as $val) {
                            if ($val === null) {
                                $escaped[] = 'NULL';
                            } else {
                                $escaped[] = "'" . addslashes((string)$val) . "'";
                            }
                        }
                        $insertValues[] = '(' . implode(', ', $escaped) . ')';
                    }
                    $sql .= "INSERT INTO `{$tableName}` VALUES\n" . implode(",\n", $insertValues) . ";\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $filename = 'backup_jurnal_sekolah_' . date('Y_m_d_His') . '.sql';

        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }
        Storage::put('backups/' . $filename, $sql);

        return response()->streamDownload(function () use ($sql) {
            echo $sql;
        }, $filename, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function downloadBackup(string $filename)
    {
        $path = 'backups/' . $filename;
        if (!Storage::exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }
        return Storage::download($path, $filename);
    }

    public function deleteBackup(string $filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::exists($path)) {
            Storage::delete($path);
            return back()->with('success', "File backup {$filename} berhasil dihapus.");
        }
        return back()->with('error', 'File backup tidak ditemukan.');
    }

    public function restoreFromStorage(string $filename)
    {
        $path = 'backups/' . $filename;
        if (!Storage::exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        $content = Storage::get($path);
        return $this->executeSqlRestore($content, $filename);
    }

    public function restoreDatabase(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:51200',
        ]);

        $file = $request->file('backup_file');
        $ext = strtolower($file->getClientOriginalExtension());
        
        $content = '';
        try {
            $content = $file->getContent();
        } catch (\Throwable $e) {
            $content = '';
        }

        if (empty($content)) {
            $path = $file->getPathname() ?: ($file->getRealPath() ?: null);
            if ($path && file_exists($path)) {
                $content = file_get_contents($path);
            }
        }

        if (empty($content)) {
            return back()->with('error', 'File backup kosong atau tidak dapat dibaca.');
        }

        if ($ext === 'sql') {
            return $this->executeSqlRestore($content, $file->getClientOriginalName());
        } elseif ($ext === 'json') {
            return $this->executeJsonRestore($content);
        }

        return back()->with('error', 'Format file tidak didukung. Harap upload file .sql atau .json');
    }

    private function executeSqlRestore(string $sql, string $name = 'backup')
    {
        try {
            DB::unprepared("SET FOREIGN_KEY_CHECKS=0;\n" . $sql . "\nSET FOREIGN_KEY_CHECKS=1;");
            return back()->with('success', "Database berhasil dipulihkan dari {$name}!");
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }

    private function executeJsonRestore(string $json)
    {
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return back()->with('error', 'Format JSON backup tidak valid.');
        }

        try {
            DB::beginTransaction();

            foreach ($data as $table => $rows) {
                if (Schema::hasTable($table)) {
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
            return back()->with('success', 'Database JSON berhasil dipulihkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }
}
