<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $table = 'siswa';

    protected $primaryKey = 'id_siswa';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'nis',
        'nama',
        'id_kelas',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'no_telp_ortu',
        'aktif',
    ];
}