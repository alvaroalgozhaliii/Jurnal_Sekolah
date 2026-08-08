<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use SoftDeletes;

    protected $table = 'guru';
    protected $primaryKey = 'id_guru';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama',
        'nip',
        'bidang_studi',
        'no_telp',
        'created_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'id_guru', 'id_guru');
    }

    public function jurnalHarian()
    {
        return $this->hasMany(JurnalHarian::class, 'id_guru', 'id_guru');
    }
}