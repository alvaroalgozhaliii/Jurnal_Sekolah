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
        'id_guru',
        'id_kelas',
        'tanggal',
        'mata_pelajaran',
        'materi',
        'keterangan'
    ];


    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }


    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}