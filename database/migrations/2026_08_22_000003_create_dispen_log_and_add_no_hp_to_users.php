<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add no_hp to users if not exists
        if (!Schema::hasColumn('users', 'no_hp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('no_hp', 25)->nullable()->after('role');
            });
        }

        // Add perkiraan_kembali and keterangan if not exists in pengajuan_izin
        if (Schema::hasTable('pengajuan_izin')) {
            Schema::table('pengajuan_izin', function (Blueprint $table) {
                if (!Schema::hasColumn('pengajuan_izin', 'perkiraan_kembali')) {
                    $table->time('perkiraan_kembali')->nullable()->after('jam_selesai');
                }
                if (!Schema::hasColumn('pengajuan_izin', 'keterangan')) {
                    $table->text('keterangan')->nullable()->after('alasan');
                }
            });
        }

        // Create dispen_log table
        if (!Schema::hasTable('dispen_log')) {
            Schema::create('dispen_log', function (Blueprint $table) {
                $table->id('id_log');
                $table->unsignedBigInteger('id_pengajuan');
                $table->unsignedBigInteger('id_user')->nullable();
                $table->string('role', 50)->nullable();
                $table->string('status_sebelum', 50)->nullable();
                $table->string('status_sesudah', 50);
                $table->text('catatan')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('id_pengajuan')->references('id_pengajuan')->on('pengajuan_izin')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dispen_log');

        if (Schema::hasColumn('pengajuan_izin', 'perkiraan_kembali')) {
            Schema::table('pengajuan_izin', function (Blueprint $table) {
                $table->dropColumn(['perkiraan_kembali', 'keterangan']);
            });
        }

        if (Schema::hasColumn('users', 'no_hp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('no_hp');
            });
        }
    }
};
