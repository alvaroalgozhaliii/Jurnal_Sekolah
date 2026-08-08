<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiMasuk extends Model
{
    protected $table = 'presensi_masuk';
    protected $primaryKey = 'id_presensi';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'keterangan',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
