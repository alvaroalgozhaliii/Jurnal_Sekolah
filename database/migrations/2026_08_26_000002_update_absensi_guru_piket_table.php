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
        if (!Schema::hasTable('absensi_guru_piket')) {
            Schema::create('absensi_guru_piket', function (Blueprint $table) {
                $table->id('id_piket');
                $table->unsignedBigInteger('id_jadwal');
                $table->date('tanggal');
                $table->string('status_guru', 30)->default('hadir');
                $table->string('keperluan', 150)->nullable();
                $table->string('pengganti', 150)->nullable();
                $table->text('keterangan')->nullable();
                $table->unsignedBigInteger('dicatat_oleh')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        } elseif (!Schema::hasColumn('absensi_guru_piket', 'keperluan')) {
            Schema::table('absensi_guru_piket', function (Blueprint $table) {
                $table->string('keperluan', 150)->nullable()->after('status_guru');
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
