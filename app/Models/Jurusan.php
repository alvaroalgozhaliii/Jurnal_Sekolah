<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jurusan extends Model
{
    use SoftDeletes;

    protected $table = 'jurusan';
    protected $primaryKey = 'id_jurusan';

    public $timestamps = false;

    protected $fillable = [
        'nama_jurusan',
        'rombel',
        'maks_rombel',
        'deleted_at',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_jurusan', 'id_jurusan');
    }
}
