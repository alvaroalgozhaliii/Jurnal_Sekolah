<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiGuruPiket extends Model
{
    protected $table = 'absensi_guru_piket';
    protected $primaryKey = 'id_piket';

    public $timestamps = false;

    protected $fillable = [
        'id_jadwal',
        'tanggal',
        'status_guru',
        'jam_keluar',
        'jam_masuk',
        'keperluan',
        'pengganti',
        'keterangan',
        'dicatat_oleh',
        'created_at',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal', 'id_jadwal');
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh', 'id_user');
    }
}
