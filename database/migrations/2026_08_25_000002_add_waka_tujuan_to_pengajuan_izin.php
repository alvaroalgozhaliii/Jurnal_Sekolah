<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuan_izin', 'id_waka_tujuan')) {
                $table->unsignedBigInteger('id_waka_tujuan')->nullable()->after('id_waka_approver');
                $table->foreign('id_waka_tujuan')->references('id_user')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('pengajuan_izin', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable()->after('catatan_waka');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            $table->dropForeign(['id_waka_tujuan']);
            $table->dropColumn(['id_waka_tujuan', 'alasan_penolakan']);
        });
    }
};