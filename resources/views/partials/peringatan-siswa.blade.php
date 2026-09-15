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
    $colors = match($p['level']) {
        'danger'  => ['bg'=>'#fef2f2', 'border'=>'#fca5a5', 'text'=>'#991b1b', 'icon_bg'=>'#fee2e2'],
        'warning' => ['bg'=>'#fff7ed', 'border'=>'#fdba74', 'text'=>'#9a3412', 'icon_bg'=>'#ffedd5'],
        default   => ['bg'=>'#fefce8', 'border'=>'#fde047', 'text'=>'#854d0e', 'icon_bg'=>'#fef9c3'],
    };
@endphp
<div style="
    display:flex; align-items:flex-start; gap:14px;
    padding:14px 18px;
    background:{{ $colors['bg'] }};
    border:1.5px solid {{ $colors['border'] }};
    border-radius:12px;
    margin-bottom:10px;
    animation: fadeIn .35s ease;
">
    <div style="
        width:38px; height:38px; border-radius:10px;
        background:{{ $colors['icon_bg'] }};
        display:flex; align-items:center; justify-content:center;
        font-size:18px; flex-shrink:0;
    ">{{ $p['icon'] }}</div>
    <div style="flex:1; min-width:0;">
        <div style="font-size:13.5px; font-weight:700; color:{{ $colors['text'] }}; margin-bottom:3px;">
            ⚠ Siswa Butuh Perhatian{{ isset($namaSiswa) && $namaSiswa ? ' — '.$namaSiswa : '' }}
        </div>
        <div style="font-size:13px; color:{{ $colors['text'] }}; line-height:1.55;">
            {!! $p['pesan'] !!}
        </div>
        @if(!empty($p['saran']))
        <div style="font-size:12px; color:{{ $colors['text'] }}; opacity:.75; margin-top:5px;">
            💡 {{ $p['saran'] }}
        </div>
        @endif
    </div>
</div>
@endforeach
</div>
@endif
