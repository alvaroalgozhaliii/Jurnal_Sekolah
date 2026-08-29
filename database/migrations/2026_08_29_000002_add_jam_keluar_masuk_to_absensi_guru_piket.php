<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensi_guru_piket', function (Blueprint $table) {
            if (!Schema::hasColumn('absensi_guru_piket', 'jam_keluar')) {
                $table->time('jam_keluar')->nullable()->after('status_guru');
            }
            if (!Schema::hasColumn('absensi_guru_piket', 'jam_masuk')) {
                $table->time('jam_masuk')->nullable()->after('jam_keluar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_guru_piket', function (Blueprint $table) {
            if (Schema::hasColumn('absensi_guru_piket', 'jam_masuk')) {
                $table->dropColumn('jam_masuk');
            }
            if (Schema::hasColumn('absensi_guru_piket', 'jam_keluar')) {
                $table->dropColumn('jam_keluar');
            }
        });
    }
};
