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
        'username',
        'password',
        'role',
        'no_hp',
        'aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Role helper checks
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
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

    public function isWaliKelas(): bool
    {
        return in_array($this->role, ['walikelas', 'wali_kelas']);
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

    public function isWaka(): bool
    {
        return in_array($this->role, ['waka_kesiswaan', 'waka_sdm']);
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