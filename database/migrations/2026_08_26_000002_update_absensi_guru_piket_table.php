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
        if (Schema::hasTable('absensi_guru_piket')) {
            Schema::table('absensi_guru_piket', function (Blueprint $table) {
                if (!Schema::hasColumn('absensi_guru_piket', 'keperluan')) {
                    $table->string('keperluan', 150)->nullable()->after('status_guru');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('absensi_guru_piket')) {
            Schema::table('absensi_guru_piket', function (Blueprint $table) {
                if (Schema::hasColumn('absensi_guru_piket', 'keperluan')) {
                    $table->dropColumn('keperluan');
                }
            });
        }
    }
};
