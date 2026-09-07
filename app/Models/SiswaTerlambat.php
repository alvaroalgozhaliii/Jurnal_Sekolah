<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaTerlambat extends Model
{
    protected $table = 'siswa_terlambat';
    protected $primaryKey = 'id_terlambat';

    protected $fillable = [
        'id_siswa',
        'id_kelas',
        'tanggal',
        'jam_kedatangan',
        'terlambat_sampai_jam',
        'alasan',
        'tindakan_piket',
        'id_petugas_piket',
        'status_notifikasi',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function petugasPiket()
    {
        return $this->belongsTo(User::class, 'id_petugas_piket', 'id_user');
    }
}
