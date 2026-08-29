<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jurnal_harian')) {
            Schema::create('jurnal_harian', function (Blueprint $table) {
                $table->id('id_jurnal');
                $table->unsignedBigInteger('id_jadwal')->nullable();
                $table->unsignedBigInteger('id_guru')->nullable();
                $table->date('tanggal');
                $table->string('mapel')->nullable();
                $table->text('materi')->nullable();
                $table->text('sub_materi')->nullable();
                $table->text('catatan_pengajaran')->nullable();
                $table->string('status_keterlaksanaan')->default('terlaksana');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->boolean('aktif')->default(1);
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_harian');
    }
};