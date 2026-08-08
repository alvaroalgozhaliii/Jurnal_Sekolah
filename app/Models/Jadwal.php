<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
    use SoftDeletes;

    protected $table = 'jadwal';
    protected $primaryKey = 'id_jadwal';

    public $timestamps = false;

    protected $fillable = [
        'hari',
        'jam_ke',
        'id_kelas',
        'id_guru',
        'mapel',
        'ruang',
        'waktu_mulai',
        'waktu_selesai',
        'aktif',
        'deleted_at',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function jurnalHarian()
    {
        return $this->hasMany(JurnalHarian::class, 'id_jadwal', 'id_jadwal');
    }
}