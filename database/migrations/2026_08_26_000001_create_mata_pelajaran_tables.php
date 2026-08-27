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
        if (!Schema::hasTable('mata_pelajaran')) {
            Schema::create('mata_pelajaran', function (Blueprint $table) {
                $table->id('id_mapel');
                $table->string('nama_mapel', 100);
                $table->string('kode_mapel', 20)->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('kelas_mapel')) {
            Schema::create('kelas_mapel', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('id_kelas');
                $table->unsignedBigInteger('id_mapel');
                $table->timestamps();

                $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
                $table->foreign('id_mapel')->references('id_mapel')->on('mata_pelajaran')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_mapel');
        Schema::dropIfExists('mata_pelajaran');
    }
};
