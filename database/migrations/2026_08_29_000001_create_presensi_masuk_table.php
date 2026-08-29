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
        if (!Schema::hasTable('presensi_masuk')) {
            Schema::create('presensi_masuk', function (Blueprint $table) {
                $table->id('id_presensi');
                $table->unsignedBigInteger('id_user');
                $table->date('tanggal');
                $table->time('jam_masuk')->nullable();
                $table->time('jam_keluar')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_masuk');
    }
};
