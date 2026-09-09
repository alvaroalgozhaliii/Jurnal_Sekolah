<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reset_password_requests')) {
            Schema::create('reset_password_requests', function (Blueprint $table) {
                $table->id('id_reset_request');
                $table->unsignedBigInteger('id_user')->nullable();
                $table->string('role_tipe', 30)->default('ortu');
                $table->string('nisn_nik', 50);
                $table->string('nama_pengaju', 150)->nullable();
                $table->string('reset_token', 64)->unique();
                $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
                $table->text('catatan')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->timestamps();

                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reset_password_requests');
    }
};
