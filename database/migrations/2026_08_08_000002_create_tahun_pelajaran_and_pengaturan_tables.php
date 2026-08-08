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
        if (!Schema::hasTable('tahun_pelajaran')) {
            Schema::create('tahun_pelajaran', function (Blueprint $table) {
                $table->increments('id_tahun_pelajaran');
                $table->string('tahun', 20); // e.g. 2025/2026
                $table->string('semester', 10)->default('Ganjil'); // Ganjil / Genap
                $table->boolean('aktif')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pengaturan')) {
            Schema::create('pengaturan', function (Blueprint $table) {
                $table->increments('id_pengaturan');
                $table->string('kunci', 100);
                $table->text('nilai')->nullable();
                $table->string('role', 20)->default('admin'); // admin, guru, piket, siswa
                $table->integer('id_user')->unsigned()->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
        Schema::dropIfExists('tahun_pelajaran');
    }
};
