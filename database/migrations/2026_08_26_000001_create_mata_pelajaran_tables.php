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
        if (!Schema::hasTable('jurusan')) {
            Schema::create('jurusan', function (Blueprint $table) {
                $table->id('id_jurusan');
                $table->string('nama_jurusan', 100);
                $table->string('rombel', 20)->nullable();
                $table->unsignedSmallInteger('maks_rombel')->nullable();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('kelas')) {
            Schema::create('kelas', function (Blueprint $table) {
                $table->id('id_kelas');
                $table->string('nama_kelas', 50);
                $table->string('tingkat', 10)->nullable();
                $table->unsignedBigInteger('id_jurusan')->nullable();
                $table->string('wali_kelas', 150)->nullable();
                $table->unsignedBigInteger('id_guru_walikelas')->nullable();
                $table->softDeletes();

                $table->foreign('id_jurusan')->references('id_jurusan')->on('jurusan')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('jadwal')) {
            Schema::create('jadwal', function (Blueprint $table) {
                $table->id('id_jadwal');
                $table->string('hari', 20);
                $table->unsignedTinyInteger('jam_ke');
                $table->unsignedBigInteger('id_kelas');
                $table->unsignedBigInteger('id_guru')->nullable();
                $table->string('mapel', 150);
                $table->string('ruang', 50)->nullable();
                $table->time('waktu_mulai')->nullable();
                $table->time('waktu_selesai')->nullable();
                $table->boolean('aktif')->default(true);
                $table->softDeletes();

                $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
                $table->foreign('id_guru')->references('id_guru')->on('guru')->nullOnDelete();
            });
        }

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
                $table->unsignedBigInteger('id_kelas');
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
