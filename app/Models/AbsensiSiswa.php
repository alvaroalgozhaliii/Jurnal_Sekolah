<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiSiswa extends Model
{
    protected $table = 'absensi_siswa';

    protected $primaryKey = 'id_absensi';

    public $timestamps = false;

    protected $fillable = [
        'id_jurnal',
        'id_siswa',
        'status',
        'keterangan',
        'dicatat_oleh',
        'created_at',
    ];

    public function jurnal()
    {
        return $this->belongsTo(JurnalHarian::class, 'id_jurnal', 'id_jurnal');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
}