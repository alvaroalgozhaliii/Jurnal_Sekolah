<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalHarian extends Model
{
    use SoftDeletes;

    protected $table = 'jurnal_harian';
    protected $primaryKey = 'id_jurnal';

    protected $fillable = [
        'id_jadwal',
        'tanggal',
        'id_guru',
        'mapel',
        'materi',
        'sub_materi',
        'catatan_pengajaran',
        'status_keterlaksanaan',
        'created_by',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal', 'id_jadwal');
    }

    public function kelas()
    {
        return $this->hasOneThrough(
            Kelas::class,
            Jadwal::class,
            'id_jadwal', // Foreign key on Jadwal table...
            'id_kelas',  // Foreign key on Kelas table...
            'id_jadwal', // Local key on JurnalHarian table...
            'id_kelas'   // Local key on Jadwal table...
        );
    }

    public function absensiSiswa()
    {
        return $this->hasMany(AbsensiSiswa::class, 'id_jurnal', 'id_jurnal');
    }
}