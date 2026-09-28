<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'device_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('device_token', 64)->nullable()->index()->after('foto_profil');
            });
        }

        if (!Schema::hasTable('device_requests')) {
            Schema::create('device_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_user');
                $table->string('device_token', 64);
                $table->text('user_agent')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('keterangan')->nullable();
                $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->timestamps();

                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
                $table->foreign('approved_by')->references('id_user')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('device_requests');

        if (Schema::hasColumn('users', 'device_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('device_token');
            });
        }
    }
};