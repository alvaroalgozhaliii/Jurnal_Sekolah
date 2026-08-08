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
        'username',
        'password',
        'role',
        'aktif',
        'created_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'aktif' => 'boolean',
        ];
    }

    public function guru()
    {
        return $this->hasOne(Guru::class, 'id_user', 'id_user');
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'id_user', 'id_user');
    }

    public function presensiMasuk()
    {
        return $this->hasMany(PresensiMasuk::class, 'id_user', 'id_user');
    }

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

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }
}
