<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
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
        'aktif'
    ];
}