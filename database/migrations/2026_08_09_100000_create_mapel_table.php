<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mapel')) {
            Schema::create('mapel', function (Blueprint $table) {
                $table->id('id_mapel');
                $table->string('kode_mapel', 20)->nullable();
                $table->string('nama_mapel', 100);
                $table->text('deskripsi')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mapel');
    }
};
