<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';

    protected $fillable = [
        'id_user',
        'judul',
        'pesan',
        'link',
        'dibaca',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public static function kirim($idUser, string $judul, string $pesan, ?string $link = null, ?string $type = null)
    {
        if (!$idUser) return null;
        return static::create([
            'id_user' => $idUser,
            'judul' => $judul,
            'pesan' => $pesan,
            'link' => $link,
            'dibaca' => false,
            'type' => $type,
        ]);
    }

    public static function kirimKeRole(string $role, string $judul, string $pesan, ?string $link = null, ?string $type = null)
    {
        $users = User::where('role', $role)->where('aktif', 1)->get();
        foreach ($users as $u) {
            static::kirim($u->id_user, $judul, $pesan, $link, $type);
        }
    }
}
