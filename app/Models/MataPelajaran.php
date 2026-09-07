<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataPelajaran extends Model
{
    use SoftDeletes;

    protected $table = 'mata_pelajaran';
    protected $primaryKey = 'id_mapel';

    protected $fillable = [
        'nama_mapel',
        'tingkat',
        'kode_mapel',
    ];

    public static function generateKode(string $namaMapel, string $tingkat, ?int $ignoreId = null): string
    {
        $tingkat = strtoupper(trim($tingkat));
        $suffix = match($tingkat) {
            'X' => '01',
            'XI' => '02',
            'XII' => '03',
            '10' => '01',
            '11' => '02',
            '12' => '03',
            default => '01'
        };

        $clean = trim(strtolower($namaMapel));
        
        // Common mapel abbreviations
        $known = [
            'bahasa indonesia' => 'bin',
            'bahasa inggris' => 'bing',
            'bahasa jawa' => 'bjw',
            'bahasa jepang' => 'bjp',
            'matematika' => 'mat',
            'sejarah' => 'sej',
            'pendidikan pancasila' => 'pp',
            'pendidikan agama islam dan budi pekerti' => 'pai',
            'pendidikan agama islam' => 'pai',
            'pendidikan agama kristen' => 'pak',
            'pendidikan agama katolik' => 'pakat',
            'pendidikan agama katholik' => 'pakat',
            'pendidikan jasmani, olahraga dan kesehatan' => 'pjok',
            'seni budaya' => 'sb',
            'informatika' => 'inf',
            'projek ilmu pengetahuan alam dan sosial' => 'ipas',
            'kreativitas, inovasi, dan kewirausahaan' => 'pkwu',
            'kreativitas, inovasi dan kewirausahaan' => 'pkwu',
            'bimbingan konseling' => 'bk',
            'bk' => 'bk',
            'dasar program keahlian' => 'dpk',
            'konsentrasi keahlian' => 'kk',
            'mata pelajaran pilihan' => 'mpp',
        ];

        if (isset($known[$clean])) {
            $prefix = $known[$clean];
        } else {
            $words = preg_split('/\s+/', $clean);
            if (count($words) > 1) {
                $prefix = '';
                foreach ($words as $w) {
                    if (!in_array($w, ['dan', '&', 'dan/atau', 'atau', 'ke'])) {
                        $prefix .= substr($w, 0, 1);
                    }
                }
                if (strlen($prefix) < 2) {
                    $prefix = substr($words[0], 0, 3);
                }
            } else {
                $prefix = substr($words[0], 0, 3);
            }
        }

        $prefix = preg_replace('/[^a-z0-9]/', '', $prefix);
        if (empty($prefix)) $prefix = 'mp';

        $candidate = $prefix . '-' . $suffix;
        
        $query = static::where('kode_mapel', $candidate);
        if ($ignoreId) {
            $query->where('id_mapel', '!=', $ignoreId);
        }
        $existing = $query->first();
        if (!$existing || ($existing->nama_mapel === $namaMapel && $existing->tingkat === $tingkat)) {
            return $candidate;
        }

        $finalKode = $candidate;
        $counter = 1;

        while (static::where('kode_mapel', $finalKode)->when($ignoreId, fn($q) => $q->where('id_mapel', '!=', $ignoreId))->exists()) {
            $finalKode = $candidate . '-' . $counter;
            $counter++;
        }

        return $finalKode;
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mapel', 'id_mapel', 'id_kelas');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'mapel', 'nama_mapel');
    }
}
