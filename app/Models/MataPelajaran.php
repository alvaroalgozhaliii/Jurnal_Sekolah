<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataPelajaran extends Model
{
    use SoftDeletes;

    protected $table = 'mata_pelajaran';
    protected $primaryKey = 'id_mapel';

    protected $fillable = [
        'nama_mapel',
        'kode_mapel',
    ];

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mapel', 'id_mapel', 'id_kelas');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'mapel', 'nama_mapel');
    }
}
