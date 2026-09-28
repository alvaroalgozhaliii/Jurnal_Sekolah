<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceRequest extends Model
{
    protected $table = 'device_requests';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_user',
        'device_token',
        'user_agent',
        'ip_address',
        'keterangan',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
    }
}
