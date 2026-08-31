<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pengajuan_izin')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE pengajuan_izin MODIFY COLUMN kategori VARCHAR(50) NOT NULL DEFAULT 'sakit'");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pengajuan_izin')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE pengajuan_izin MODIFY COLUMN kategori ENUM('dispensasi', 'izin_masuk', 'izin_keluar', 'sakit', 'izin_guru') NOT NULL DEFAULT 'dispensasi'");
            }
        }
    }
};
