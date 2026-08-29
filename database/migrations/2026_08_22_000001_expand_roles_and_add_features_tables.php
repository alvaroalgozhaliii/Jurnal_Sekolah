<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // 1. Modify users role column to varchar(30) to support all 9 roles
            DB::statement("ALTER TABLE `users` MODIFY `role` VARCHAR(30) NOT NULL DEFAULT 'ortu'");
        }

        // Update existing 'siswa' role users to 'ortu'
        DB::table('users')->where('role', 'siswa')->update(['role' => 'ortu']);

        // 2. Create ortu_siswa pivot table
        if (!Schema::hasTable('ortu_siswa')) {
            Schema::create('ortu_siswa', function (Blueprint $table) {
                $table->id('id_ortu_siswa');
                $table->unsignedBigInteger('id_user');
                $table->unsignedBigInteger('id_siswa');
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
                $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            });

            // Populate ortu_siswa from current siswa table link
            $siswas = DB::table('siswa')->whereNotNull('id_user')->get();
            foreach ($siswas as $s) {
                DB::table('ortu_siswa')->insertOrIgnore([
                    'id_user' => $s->id_user,
                    'id_siswa' => $s->id_siswa,
                    'created_at' => now(),
                ]);
            }
        }

        if (!Schema::hasTable('absensi_siswa')) {
            Schema::create('absensi_siswa', function (Blueprint $table) {
                $table->id('id_absensi');
                $table->unsignedBigInteger('id_jurnal');
                $table->unsignedBigInteger('id_siswa');
                $table->string('status', 20)->default('hadir');
                $table->time('jam_masuk')->nullable();
                $table->unsignedSmallInteger('menit_terlambat')->nullable();
                $table->text('keterangan')->nullable();
                $table->unsignedBigInteger('dicatat_oleh')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('id_jurnal')->references('id_jurnal')->on('jurnal_harian')->onDelete('cascade');
                $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
                $table->foreign('dicatat_oleh')->references('id_user')->on('users')->nullOnDelete();
            });
        } elseif (!Schema::hasColumn('absensi_siswa', 'jam_masuk')) {
            Schema::table('absensi_siswa', function (Blueprint $table) {
                $table->time('jam_masuk')->nullable()->after('status');
                $table->unsignedSmallInteger('menit_terlambat')->nullable()->after('jam_masuk');
            });
        }

        // 4. Add id_guru_walikelas to kelas table
        if (Schema::hasTable('kelas') && !Schema::hasColumn('kelas', 'id_guru_walikelas')) {
            Schema::table('kelas', function (Blueprint $table) {
                $table->unsignedBigInteger('id_guru_walikelas')->nullable()->after('wali_kelas');
            });
        }

        // 5. Create pengajuan_izin table
        if (!Schema::hasTable('pengajuan_izin')) {
            Schema::create('pengajuan_izin', function (Blueprint $table) {
                $table->id('id_pengajuan');
                $table->enum('kategori', ['dispensasi', 'izin_masuk', 'izin_keluar', 'sakit', 'izin_guru'])->default('dispensasi');
                $table->unsignedBigInteger('id_siswa')->nullable();
                $table->unsignedBigInteger('id_guru')->nullable();
                $table->unsignedBigInteger('id_user_pengaju')->nullable();
                $table->date('tanggal');
                $table->time('jam_mulai')->nullable();
                $table->time('jam_selesai')->nullable();
                $table->string('jenis_izin', 100)->nullable();
                $table->text('alasan')->nullable();
                $table->string('lampiran_foto', 255)->nullable();
                $table->string('status', 50)->default('pending_piket');

                $table->unsignedBigInteger('id_piket_approver')->nullable();
                $table->text('catatan_piket')->nullable();
                $table->dateTime('tgl_piket')->nullable();

                $table->unsignedBigInteger('id_waka_approver')->nullable();
                $table->text('catatan_waka')->nullable();
                $table->dateTime('tgl_waka')->nullable();

                $table->unsignedBigInteger('id_kepala_approver')->nullable();
                $table->text('catatan_kepala')->nullable();
                $table->dateTime('tgl_kepala')->nullable();

                $table->boolean('butuh_satpam')->default(false);
                $table->enum('status_satpam', ['belum_diperiksa', 'valid', 'tidak_valid'])->default('belum_diperiksa');
                $table->text('catatan_satpam')->nullable();
                $table->dateTime('tgl_satpam')->nullable();
                $table->unsignedBigInteger('id_satpam')->nullable();

                $table->timestamps();
            });
        }

        // 6. Create notifikasi table
        if (!Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $table) {
                $table->id('id_notifikasi');
                $table->unsignedBigInteger('id_user');
                $table->string('judul', 150);
                $table->text('pesan');
                $table->string('link', 255)->nullable();
                $table->boolean('dibaca')->default(false);
                $table->string('type', 50)->nullable();
                $table->timestamps();

                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('pengajuan_izin');
        Schema::dropIfExists('ortu_siswa');

        if (Schema::hasColumn('absensi_siswa', 'jam_masuk')) {
            Schema::table('absensi_siswa', function (Blueprint $table) {
                $table->dropColumn(['jam_masuk', 'menit_terlambat']);
            });
        }

        if (Schema::hasColumn('kelas', 'id_guru_walikelas')) {
            Schema::table('kelas', function (Blueprint $table) {
                $table->dropColumn('id_guru_walikelas');
            });
        }
    }
};
