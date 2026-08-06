<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_harian', function (Blueprint $table) {
            $table->id('id_jurnal');
            $table->unsignedBigInteger('id_guru');
            $table->unsignedBigInteger('id_kelas');
            $table->date('tanggal');
            $table->string('mata_pelajaran');
            $table->text('materi');
            $table->text('kegiatan');
            $table->text('catatan')->nullable();
            $table->boolean('aktif')->default(1);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_harian');
    }
};