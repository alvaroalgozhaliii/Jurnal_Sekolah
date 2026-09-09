<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'nik')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nik', 50)->nullable()->after('nip');
            });
        }

        if (Schema::hasTable('guru') && !Schema::hasColumn('guru', 'nik')) {
            Schema::table('guru', function (Blueprint $table) {
                $table->string('nik', 50)->nullable()->after('nip');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'nik')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nik');
            });
        }

        if (Schema::hasTable('guru') && Schema::hasColumn('guru', 'nik')) {
            Schema::table('guru', function (Blueprint $table) {
                $table->dropColumn('nik');
            });
        }
    }
};
