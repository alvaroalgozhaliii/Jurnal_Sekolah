<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispenLog extends Model
{
    protected $table = 'dispen_log';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'id_pengajuan',
        'id_user',
        'role',
        'status_sebelum',
        'status_sesudah',
        'catatan',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanIzin::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public static function catat($idPengajuan, $idUser, $role, $statusSebelum, $statusSesudah, $catatan = null)
    {
        return static::create([
            'id_pengajuan' => $idPengajuan,
            'id_user' => $idUser,
            'role' => $role,
            'status_sebelum' => $statusSebelum,
            'status_sesudah' => $statusSesudah,
            'catatan' => $catatan,
            'created_at' => now(),
        ]);
    }
}
