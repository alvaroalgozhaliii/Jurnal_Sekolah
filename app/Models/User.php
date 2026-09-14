<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'nip',
        'nik',
        'username',
        'password',
        'role',
        'no_hp',
        'aktif',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // URL foto profil (fallback ke inisial nama via UI)
    public function getFotoProfilUrlAttribute(): ?string
    {
        return $this->foto_profil ? asset('storage/' . $this->foto_profil) : null;
    }

    // Role helper checks
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return in_array($this->role, ['guru', 'wali_kelas', 'walikelas']) || $this->guru !== null;
    }

    public function isPiket(): bool
    {
        return $this->role === 'piket';
    }

    public function isOrtu(): bool
    {
        return in_array($this->role, ['ortu', 'siswa']);
    }

    public function isSiswa(): bool
    {
        return in_array($this->role, ['siswa', 'ortu']);
    }

    public function getKelasWaliAttribute()
    {
        if ($this->guru) {
            $k = Kelas::where('id_guru_walikelas', $this->guru->id_guru)
                ->orWhere('wali_kelas', $this->guru->nama)
                ->first();
            if ($k) return $k;
        }
        return Kelas::where('wali_kelas', $this->nama)->first();
    }

    public function isWaliKelas(): bool
    {
        if (in_array($this->role, ['walikelas', 'wali_kelas'])) {
            return true;
        }
        return $this->kelas_wali !== null;
    }

    public function isWakaKesiswaan(): bool
    {
        return $this->role === 'waka_kesiswaan';
    }

    public function isWakaSdm(): bool
    {
        return $this->role === 'waka_sdm';
    }

    public function isWakaKurikulum(): bool
    {
        return $this->role === 'waka_kurikulum';
    }

    public function isWakaSarpras(): bool
    {
        return $this->role === 'waka_sarpras';
    }

    public function isWakaHumas(): bool
    {
        return $this->role === 'waka_humas';
    }

    public function isWaka(): bool
    {
        if (in_array($this->role, [
            'waka_kesiswaan',
            'waka_sdm',
            'waka_kurikulum',
            'waka_sarpras',
            'waka_humas',
        ])) {
            return true;
        }

        if ($this->id_user && class_exists(JadwalWaka::class)) {
            try {
                if (JadwalWaka::where('id_user_waka', $this->id_user)->exists()) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Ignore query error if table or column issue
            }
        }

        return false;
    }

    public function getAvailableAccesses(): array
    {
        $accesses = [];

        // 1. Guru (Hak Dasar)
        if ($this->isGuru()) {
            $accesses['guru'] = [
                'key'         => 'guru',
                'title'       => 'Guru',
                'badge'       => 'Pengajar',
                'subtitle'    => 'Dashboard Guru & Presensi KBM',
                'description' => 'Aktivitas KBM, jurnal mengajar, dan absensi siswa harian.',
                'icon'        => 'user',
                'route'       => 'guru.dashboard',
            ];
        }

        // 2. Wali Kelas (Tugas Tambahan)
        if ($this->isWaliKelas()) {
            $kelas = $this->kelas_wali;
            $namaKelas = $kelas ? $kelas->nama_kelas : '';
            $accesses['wali_kelas'] = [
                'key'         => 'wali_kelas',
                'title'       => 'Wali Kelas' . ($namaKelas ? ' ' . $namaKelas : ''),
                'badge'       => $namaKelas ? 'Kelas ' . $namaKelas : 'Wali Kelas',
                'subtitle'    => 'Kelola kelas binaan',
                'description' => 'Monitoring kehadiran, rekap presensi, dan rekap jurnal siswa binaan.',
                'icon'        => 'school',
                'route'       => 'walikelas.dashboard',
            ];
        }

        // 3. Waka (Tugas Tambahan)
        if ($this->isWaka()) {
            $wakaTitle = match ($this->role) {
                'waka_kurikulum' => 'Waka Kurikulum',
                'waka_kesiswaan' => 'Waka Kesiswaan',
                'waka_sdm'       => 'Waka SDM / Ketenagaan',
                'waka_sarpras'   => 'Waka Sarpras',
                'waka_humas'     => 'Waka Humas',
                default          => 'Waka (Wakil Kepala)',
            };
            $wakaRoute = match ($this->role) {
                'waka_kurikulum' => 'waka-kurikulum.dashboard',
                'waka_kesiswaan' => 'waka.monitoring-siswa',
                'waka_sarpras'   => 'waka.sarpras',
                'waka_humas'     => 'waka.humas',
                default          => 'waka.dashboard',
            };
            $accesses['waka'] = [
                'key'         => 'waka',
                'title'       => $wakaTitle,
                'badge'       => 'Manajemen Waka',
                'subtitle'    => 'Persetujuan & Monitoring Waka',
                'description' => 'Persetujuan dispensasi izin, jadwal piket, dan monitoring kesiswaan.',
                'icon'        => 'briefcase',
                'route'       => $wakaRoute,
            ];
        }

        // 4. Kepala Sekolah (Pimpinan Satuan Pendidikan)
        if ($this->isKepalaSekolah()) {
            $accesses['kepala_sekolah'] = [
                'key'         => 'kepala_sekolah',
                'title'       => 'Kepala Sekolah',
                'badge'       => 'Pimpinan Sekolah',
                'subtitle'    => 'Persetujuan & Monitoring Eksekutif',
                'description' => 'Monitoring seluruh kegiatan KBM, kehadiran sekolah, dan persetujuan pengajuan izin.',
                'icon'        => 'award',
                'route'       => 'kepala.dashboard',
            ];
        }

        return $accesses;
    }

    public function hasAccess(string $access): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $available = array_keys($this->getAvailableAccesses());
        if (in_array($access, $available)) {
            return true;
        }

        if ($access === 'ortu' || $access === 'siswa') {
            return $this->isOrtu() || $this->isSiswa();
        }

        if ($access === 'piket') {
            return $this->isPiket();
        }

        if ($access === 'kepala_sekolah') {
            return $this->isKepalaSekolah();
        }

        return $this->role === $access;
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah';
    }

    public function isSatpam(): bool
    {
        return $this->role === 'satpam';
    }

    // Relationships
    public function guru()
    {
        return $this->hasOne(Guru::class, 'id_user', 'id_user');
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'id_user', 'id_user');
    }

    public function anakList()
    {
        return $this->belongsToMany(Siswa::class, 'ortu_siswa', 'id_user', 'id_siswa');
    }
}