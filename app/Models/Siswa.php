<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nisn',
        'nis',
        'nama',
        'id_kelas',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'no_telp_ortu',
        'aktif',
        'deleted_at',
    ];

    public function getNisAttribute()
    {
        return $this->attributes['nisn'] ?? ($this->attributes['nis'] ?? null);
    }

    public function setNisAttribute($value)
    {
        $this->attributes['nisn'] = $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function ortu()
    {
        return $this->belongsToMany(User::class, 'ortu_siswa', 'id_siswa', 'id_user');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function absensi()
    {
        return $this->hasMany(AbsensiSiswa::class, 'id_siswa', 'id_siswa');
    }

    public function pengajuanIzin()
    {
        return $this->hasMany(PengajuanIzin::class, 'id_siswa', 'id_siswa');
    }
}