<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('siswa_terlambat')) {
            Schema::create('siswa_terlambat', function (Blueprint $table) {
                $table->id('id_terlambat');
                $table->unsignedBigInteger('id_siswa');
                $table->unsignedBigInteger('id_kelas')->nullable();
                $table->date('tanggal');
                $table->string('jam_kedatangan', 10);
                $table->integer('terlambat_sampai_jam')->default(1);
                $table->text('alasan');
                $table->text('tindakan_piket')->nullable();
                $table->unsignedBigInteger('id_petugas_piket')->nullable();
                $table->boolean('status_notifikasi')->default(true);
                $table->timestamps();

                $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
                $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('set null');
                $table->foreign('id_petugas_piket')->references('id_user')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_terlambat');
    }
};
