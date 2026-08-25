<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class JadwalWaka extends Model
{
    protected $table = 'jadwal_waka';
    protected $primaryKey = 'id_jadwal_waka';

    protected $fillable = [
        'tanggal',
        'id_user_waka',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function waka()
    {
        return $this->belongsTo(User::class, 'id_user_waka', 'id_user');
    }

    public function scopeUntukTanggal(Builder $query, string $tanggal): Builder
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public static function wakaBertugasPada(string $tanggal): ?self
    {
        return static::with('waka')->untukTanggal($tanggal)->first();
    }
}