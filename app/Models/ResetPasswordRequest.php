<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPasswordRequest extends Model
{
    use HasFactory;

    protected $table = 'reset_password_requests';
    protected $primaryKey = 'id_reset_request';

    protected $fillable = [
        'id_user',
        'role_tipe',
        'nisn_nik',
        'nama_pengaju',
        'reset_token',
        'status',
        'catatan',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
