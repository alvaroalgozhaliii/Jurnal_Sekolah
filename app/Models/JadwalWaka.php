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
        'id_guru_piket',
        'petugas_pagi',
        'koordinator_pagi',
        'petugas_siang',
        'koordinator_siang',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function waka()
    {
        return $this->belongsTo(User::class, 'id_user_waka', 'id_user');
    }

    public function guruPiket()
    {
        return $this->belongsTo(Guru::class, 'id_guru_piket', 'id_guru');
    }

    public function scopeUntukTanggal(Builder $query, string $tanggal): Builder
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function isGuruBertugas($guru): bool
    {
        if (!$guru) return false;

        $guruId = is_object($guru) ? ($guru->id_guru ?? null) : (is_numeric($guru) ? (int)$guru : null);
        $guruNama = is_object($guru) ? ($guru->nama ?? '') : (is_string($guru) ? $guru : '');

        if ($guruId && $this->id_guru_piket && (int)$this->id_guru_piket === (int)$guruId) {
            return true;
        }

        if (!empty($guruNama)) {
            $cleanNama = trim(strtok($guruNama, ',')); // Ambil nama depan/tengah tanpa gelar jika perlu
            $searchTexts = [
                $this->koordinator_pagi ?? '',
                $this->petugas_pagi ?? '',
                $this->koordinator_siang ?? '',
                $this->petugas_siang ?? '',
            ];
            $combined = implode(' ; ', $searchTexts);

            if (stripos($combined, $guruNama) !== false || (!empty($cleanNama) && stripos($combined, $cleanNama) !== false)) {
                return true;
            }
        }

        return false;
    }

    public static function wakaBertugasPada(string $tanggal): ?self
    {
        return static::with(['waka', 'guruPiket'])->untukTanggal($tanggal)->first();
    }
}