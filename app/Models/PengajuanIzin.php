<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanIzin extends Model
{
    protected $table = 'pengajuan_izin';
    protected $primaryKey = 'id_pengajuan';

    protected $fillable = [
        'kategori',
        'id_siswa',
        'id_guru',
        'id_user_pengaju',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'perkiraan_kembali',
        'jenis_izin',
        'alasan',
        'keterangan',
        'lampiran_foto',
        'status',
        'id_piket_approver',
        'catatan_piket',
        'tgl_piket',
        'id_waka_approver',
        'catatan_waka',
        'tgl_waka',
        'id_kepala_approver',
        'catatan_kepala',
        'tgl_kepala',
        'butuh_satpam',
        'status_satpam',
        'catatan_satpam',
        'tgl_satpam',
        'id_satpam',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'id_user_pengaju', 'id_user');
    }

    public function piketApprover()
    {
        return $this->belongsTo(User::class, 'id_piket_approver', 'id_user');
    }

    public function wakaApprover()
    {
        return $this->belongsTo(User::class, 'id_waka_approver', 'id_user');
    }

    public function kepalaApprover()
    {
        return $this->belongsTo(User::class, 'id_kepala_approver', 'id_user');
    }

    public function satpam()
    {
        return $this->belongsTo(User::class, 'id_satpam', 'id_user');
    }

    public function logs()
    {
        return $this->hasMany(DispenLog::class, 'id_pengajuan', 'id_pengajuan')->orderBy('created_at', 'asc');
    }

    // Status Helpers
    public function isPendingWaka(): bool
    {
        return in_array($this->status, ['pending_waka', 'pending_piket']);
    }

    public function isDisetujuiWaka(): bool
    {
        return in_array($this->status, ['disetujui_waka', 'pending_kepala', 'completed']);
    }

    public function isDitolak(): bool
    {
        return str_starts_with($this->status, 'ditolak_') || str_starts_with($this->status, 'rejected_');
    }

    public function isSelesai(): bool
    {
        return in_array($this->status, ['verified', 'disetujui_satpam', 'completed']);
    }
}
