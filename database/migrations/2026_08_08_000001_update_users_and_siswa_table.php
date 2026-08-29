<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // 1. Modify users role column to include 'siswa'
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','guru','piket','siswa') NOT NULL");
        }

        // 2. Add id_user to siswa table if not exists
        if (!Schema::hasColumn('siswa', 'id_user')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->unsignedBigInteger('id_user')->nullable()->after('id_siswa');
                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('siswa', 'id_user')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->dropForeign(['id_user']);
                $table->dropColumn('id_user');
            });
        }

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','guru','piket') NOT NULL");
        }
    }
};
