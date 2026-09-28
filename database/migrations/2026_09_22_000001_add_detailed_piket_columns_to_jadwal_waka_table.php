<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_waka', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_waka', 'petugas_pagi')) {
                $table->text('petugas_pagi')->nullable()->after('id_guru_piket');
            }
            if (!Schema::hasColumn('jadwal_waka', 'koordinator_pagi')) {
                $table->string('koordinator_pagi', 255)->nullable()->after('petugas_pagi');
            }
            if (!Schema::hasColumn('jadwal_waka', 'petugas_siang')) {
                $table->text('petugas_siang')->nullable()->after('koordinator_pagi');
            }
            if (!Schema::hasColumn('jadwal_waka', 'koordinator_siang')) {
                $table->string('koordinator_siang', 255)->nullable()->after('petugas_siang');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_waka', function (Blueprint $table) {
            $table->dropColumn([
                'petugas_pagi',
                'koordinator_pagi',
                'petugas_siang',
                'koordinator_siang'
            ]);
        });
    }
};
