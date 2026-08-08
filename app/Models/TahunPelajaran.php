<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunPelajaran extends Model
{
    protected $table = 'tahun_pelajaran';
    protected $primaryKey = 'id_tahun_pelajaran';

    protected $fillable = [
        'tahun',
        'semester',
        'aktif',
    ];
}
