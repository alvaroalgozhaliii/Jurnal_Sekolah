{{--
    Partial: peringatan-siswa.blade.php
    Props:
      $peringatan  — array of ['level'=>'danger|warning|caution', 'icon'=>'...', 'pesan'=>'...', 'saran'=>'...']
      $namaSiswa   — string (optional, for title)
--}}
@if(!empty($peringatan))
<div style="margin-bottom:20px;">
@foreach($peringatan as $p)
@php
    $levelClass = match($p['level'] ?? 'caution') {
        'danger'  => 'level-danger',
        'warning' => 'level-warning',
        default   => 'level-caution',
    };
@endphp
<div class="alert-peringatan-siswa {{ $levelClass }}">
    <div class="icon-box">
        {{ $p['icon'] ?? '⚠️' }}
    </div>
    <div class="content-box">
        <div class="title-text">
            ⚠ Siswa Butuh Perhatian{{ isset($namaSiswa) && $namaSiswa ? ' — '.$namaSiswa : '' }}
        </div>
        <div class="desc-text">
            {!! $p['pesan'] !!}
        </div>
        @if(!empty($p['saran']))
        <div class="saran-text">
            💡 {{ $p['saran'] }}
        </div>
        @endif
    </div>
</div>
@endforeach
</div>
@endif
