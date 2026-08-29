<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_waka', function (Blueprint $table) {
            $table->id('id_jadwal_waka');
            $table->date('tanggal')->unique();
            $table->unsignedBigInteger('id_user_waka');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_user_waka')
                ->references('id_user')
                ->on('users')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_waka');
    }
};