<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrtuSiswa extends Model
{
    protected $table = 'ortu_siswa';
    protected $primaryKey = 'id_ortu_siswa';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_siswa',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
}
