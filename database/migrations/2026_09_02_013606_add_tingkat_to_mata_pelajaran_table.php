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
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->enum('tingkat', ['X', 'XI', 'XII'])->nullable()->after('nama_mapel')
                  ->comment('Tingkat kelas: X=01, XI=02, XII=03 untuk kode mapel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->dropColumn('tingkat');
        });
    }
};
