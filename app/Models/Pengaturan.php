<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    protected $primaryKey = 'id_pengaturan';

    protected $fillable = [
        'kunci',
        'nilai',
        'role',
        'id_user',
    ];

    public static function getVal($kunci, $default = null, $role = 'admin', $id_user = null)
    {
        $query = static::where('kunci', $kunci)->where('role', $role);
        if ($id_user) {
            $query->where('id_user', $id_user);
        }
        $p = $query->first();
        return $p ? $p->nilai : $default;
    }

    public static function setVal($kunci, $nilai, $role = 'admin', $id_user = null)
    {
        return static::updateOrCreate(
            ['kunci' => $kunci, 'role' => $role, 'id_user' => $id_user],
            ['nilai' => $nilai]
        );
    }
}
