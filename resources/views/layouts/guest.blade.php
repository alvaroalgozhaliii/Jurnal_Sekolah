<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jurnal Sekolah')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Early Theme Detection -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('jurnal_theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <link rel="stylesheet" href="{{ asset('css/jurnal.css') }}?v={{ file_exists(public_path('css/jurnal.css')) ? filemtime(public_path('css/jurnal.css')) : time() }}">

    <!-- ===== ANIMASI LATAR ANGKASA ===== -->
    <style>
    /* Canvas bintang full-page di belakang semua elemen */
    #spaceBgCanvas {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        display: block;
        pointer-events: none;
        z-index: 0;
    }
    /* Nebula glow blob — hanya tambahan, tidak ubah apapun */
    .space-nebula {
        position: fixed;
        border-radius: 50%;
        pointer-events: none;
        z-index: 0;
        animation: nebulaPulse 8s ease-in-out infinite alternate;
    }
    .space-nebula-1 {
        width: 520px; height: 520px;
        top: -120px; left: -100px;
        background: radial-gradient(circle, rgba(99,51,198,0.18) 0%, transparent 70%);
        animation-delay: 0s;
        animation-duration: 9s;
    }
    .space-nebula-2 {
        width: 480px; height: 480px;
        bottom: -80px; right: -80px;
        background: radial-gradient(circle, rgba(14,116,180,0.16) 0%, transparent 70%);
        animation-delay: -4s;
        animation-duration: 11s;
    }
    .space-nebula-3 {
        width: 360px; height: 360px;
        top: 40%; left: 50%;
        transform: translate(-50%, -50%);
        background: radial-gradient(circle, rgba(30,58,138,0.12) 0%, transparent 70%);
        animation-delay: -2s;
        animation-duration: 13s;
    }
    @keyframes nebulaPulse {
        0%   { opacity: 0.6; transform: scale(1); }
        100% { opacity: 1;   transform: scale(1.12); }
    }
    .space-nebula-3 {
        transform-origin: center center;
    }

    /* Pastikan sudut SSO container dan panel selalu melengkung / rounded sama persis seperti halaman login */
    .sso-container {
        border-radius: 24px !important;
    }
    .sso-left {
        border-radius: 24px 0 0 24px !important;
        overflow: hidden !important;
    }
    .sso-right {
        border-radius: 0 24px 24px 0 !important;
    }
    </style>
</head>
<body>
<div class="login-page">
    <!-- ===== LATAR ANGKASA: Canvas Bintang + Nebula ===== -->
    <canvas id="spaceBgCanvas" aria-hidden="true"></canvas>
    <div class="space-nebula space-nebula-1" aria-hidden="true"></div>
    <div class="space-nebula space-nebula-2" aria-hidden="true"></div>
    <div class="space-nebula space-nebula-3" aria-hidden="true"></div>

    <!-- Aesthetic Ambient Floating Glowing Orbs -->
    <div class="login-orb orb-1"></div>
    <div class="login-orb orb-2"></div>
    <div class="login-orb orb-3"></div>

    <div class="login-bg-dot-1"></div>
    <div class="login-bg-dot-2"></div>
    
    <!-- Background Dot Grid Pattern (Left) -->
    <div class="login-bg-grid-left">
        <svg width="120" height="120" viewBox="0 0 100 100" fill="currentColor">
            <circle cx="10" cy="10" r="2"/><circle cx="30" cy="10" r="2"/><circle cx="50" cy="10" r="2"/><circle cx="70" cy="10" r="2"/><circle cx="90" cy="10" r="2"/>
            <circle cx="10" cy="30" r="2"/><circle cx="30" cy="30" r="2"/><circle cx="50" cy="30" r="2"/><circle cx="70" cy="30" r="2"/><circle cx="90" cy="30" r="2"/>
            <circle cx="10" cy="50" r="2"/><circle cx="30" cy="50" r="2"/><circle cx="50" cy="50" r="2"/><circle cx="70" cy="50" r="2"/><circle cx="90" cy="50" r="2"/>
            <circle cx="10" cy="70" r="2"/><circle cx="30" cy="70" r="2"/><circle cx="50" cy="70" r="2"/><circle cx="70" cy="70" r="2"/><circle cx="90" cy="70" r="2"/>
            <circle cx="10" cy="90" r="2"/><circle cx="30" cy="90" r="2"/><circle cx="50" cy="90" r="2"/><circle cx="70" cy="90" r="2"/><circle cx="90" cy="90" r="2"/>
        </svg>
    </div>

    <!-- Background Dot Grid Pattern (Right) -->
    <div class="login-bg-grid-right">
        <svg width="120" height="120" viewBox="0 0 100 100" fill="currentColor">
            <circle cx="10" cy="10" r="2"/><circle cx="30" cy="10" r="2"/><circle cx="50" cy="10" r="2"/><circle cx="70" cy="10" r="2"/><circle cx="90" cy="10" r="2"/>
            <circle cx="10" cy="30" r="2"/><circle cx="30" cy="30" r="2"/><circle cx="50" cy="30" r="2"/><circle cx="70" cy="30" r="2"/><circle cx="90" cy="30" r="2"/>
            <circle cx="10" cy="50" r="2"/><circle cx="30" cy="50" r="2"/><circle cx="50" cy="50" r="2"/><circle cx="70" cy="50" r="2"/><circle cx="90" cy="50" r="2"/>
            <circle cx="10" cy="70" r="2"/><circle cx="30" cy="70" r="2"/><circle cx="50" cy="70" r="2"/><circle cx="70" cy="70" r="2"/><circle cx="90" cy="70" r="2"/>
            <circle cx="10" cy="90" r="2"/><circle cx="30" cy="90" r="2"/><circle cx="50" cy="90" r="2"/><circle cx="70" cy="90" r="2"/><circle cx="90" cy="90" r="2"/>
        </svg>
    </div>

    <!-- Aesthetic Floating Rings, Wave Lines, Cubes & Sparkles -->
    <div class="login-aesthetic-shapes" aria-hidden="true">
        <!-- Rings -->
        <div class="shape-ring ring-1"></div>
        <div class="shape-ring ring-2"></div>
        <div class="shape-ring ring-3"></div>

        <!-- Ambient Wave Mesh Lines -->
        <svg class="ambient-wave wave-top" viewBox="0 0 1440 320" fill="none" preserveAspectRatio="none">
            <path d="M0,96L48,112C96,128,192,160,288,154.7C384,149,480,107,576,112C672,117,768,171,864,186.7C960,203,1056,181,1152,154.7C1248,128,1344,96,1392,80L1440,64" stroke="currentColor" stroke-width="1.5" stroke-dasharray="6 6"/>
        </svg>
        <svg class="ambient-wave wave-bottom" viewBox="0 0 1440 320" fill="none" preserveAspectRatio="none">
            <path d="M0,192L60,181.3C120,171,240,149,360,160C480,171,600,213,720,213.3C840,213,960,171,1080,154.7C1200,139,1320,149,1380,154.7L1440,160" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 4"/>
        </svg>

        <!-- Particles & Shooting Stars Container -->
        <div class="shooting-stars-container" aria-hidden="true">
            <div class="shooting-star star-1"></div>
            <div class="shooting-star star-2"></div>
            <div class="shooting-star star-3"></div>
            <div class="shooting-star star-4"></div>
            <div class="shooting-star star-5"></div>
            <div class="glow-particle p-1"></div>
            <div class="glow-particle p-2"></div>
            <div class="glow-particle p-3"></div>
            <div class="glow-particle p-4"></div>
        </div>

        <!-- Floating Isometric Badges (10 Badges Berjarak Rapi) -->
        <div class="shape-badge badge-live-update">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 6l-9.5 9.5-5-5L1 16"/><polyline points="17 6 23 6 23 12"/></svg>
            <span>Live Update</span>
        </div>
        <div class="shape-badge badge-accurate">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
            <span>Highly Accurate</span>
        </div>
        <div class="shape-badge badge-direct-alert">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/><circle cx="18" cy="4" r="3" fill="#ef4444"/></svg>
            <span>Direct Alert</span>
        </div>
        <div class="shape-badge badge-presensi">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
            <span>Presensi Digital</span>
        </div>
        <div class="shape-badge badge-realtime">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Real Time</span>
        </div>
        <div class="shape-badge badge-secure">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
            <span>Secure Data</span>
        </div>
        <div class="shape-badge badge-recap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            <span>Auto Recap</span>
        </div>
        <div class="shape-badge badge-discipline">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
            <span>Discipline</span>
        </div>
        <div class="shape-badge badge-transparent">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <span>Transparent</span>
        </div>
        <div class="shape-badge badge-terverifikasi">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>Terverifikasi</span>
        </div>

        <!-- Floating Geometric Squares -->
        <div class="shape-cube cube-1"></div>
        <div class="shape-cube cube-2"></div>

        <!-- Sparkles & Plus marks -->
        <div class="shape-sparkle sp-1">&#10022;</div>
        <div class="shape-sparkle sp-2">&#10022;</div>
        <div class="shape-sparkle sp-3">&#10022;</div>
        <div class="shape-sparkle sp-4">&#10022;</div>
        <div class="shape-plus pl-1">+</div>
        <div class="shape-plus pl-2">+</div>
    </div>

    @yield('content')
</div>

<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    
    // Toggle Eye SVG Icon
    btn.innerHTML = isPass ? 
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>' :
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
}

// Dark / Light Mode Toggle Controller
(function() {
    const toggleBtn = document.getElementById('themeToggleBtn');
    if (!toggleBtn) return;

    toggleBtn.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('jurnal_theme', newTheme);
    });
})();
</script>

<!-- ===== JS ANIMASI LATAR ANGKASA ===== -->
<script>
(function () {
    var canvas = document.getElementById('spaceBgCanvas');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    var W, H, stars = [];

    function resize() {
        canvas.style.display = 'none';
        W = canvas.width  = window.innerWidth;
        H = canvas.height = window.innerHeight;
        canvas.style.display = 'block';
        buildStars();
    }

    function buildStars() {
        stars = [];
        var total = Math.floor((W * H) / 2800);
        for (var i = 0; i < total; i++) {
            stars.push({
                x: Math.random() * W,
                y: Math.random() * H,
                r: Math.random() * 1.5 + 0.2,
                a: Math.random(),
                da: (Math.random() * 0.006 + 0.001) * (Math.random() < 0.5 ? 1 : -1),
                c: ['255,255,255','200,220,255','255,240,180','180,210,255'][Math.floor(Math.random()*4)]
            });
        }
    }

    // ===== METEOR VERTIKAL KE SAMPING (DIAGONAL FALLING SHOOTING STARS) =====
    var meteors = [];
    var lastMeteorTime = 0;

    function createMeteor() {
        // Sudut jatuh vertikal ke samping: ~45 derajat (jatuh ke bawah meluncur ke kanan)
        var angle = Math.PI / 4; 
        var speed = Math.random() * 8 + 14; // 14 - 22 px per frame
        var length = Math.random() * 110 + 90; // 90 - 200 px
        
        // Spawn dari sisi atas atau kiri atas melintasi layar
        var startX = Math.random() * (W * 1.3) - (W * 0.2);
        var startY = Math.random() * (H * 0.4) - 120;

        var isDark = document.documentElement.getAttribute('data-theme') !== 'light';
        var colors = isDark 
            ? ['56,189,248', '96,165,250', '192,132,252', '255,255,255', '52,211,153']
            : ['37,99,235', '59,130,246', '124,58,237', '2,132,199'];

        return {
            x: startX,
            y: startY,
            dx: Math.cos(angle) * speed,
            dy: Math.sin(angle) * speed,
            length: length,
            speed: speed,
            thickness: Math.random() * 1.5 + 1.2,
            opacity: 1,
            fadeSpeed: Math.random() * 0.012 + 0.008,
            colorRgb: colors[Math.floor(Math.random() * colors.length)]
        };
    }

    function updateAndDrawMeteors() {
        var now = Date.now();
        if (now - lastMeteorTime > (Math.random() * 1400 + 900) && meteors.length < 5) {
            meteors.push(createMeteor());
            lastMeteorTime = now;
        }

        for (var i = meteors.length - 1; i >= 0; i--) {
            var m = meteors[i];
            m.x += m.dx;
            m.y += m.dy;
            m.opacity -= m.fadeSpeed;

            if (m.opacity <= 0 || m.x > W + 250 || m.y > H + 250) {
                meteors.splice(i, 1);
                continue;
            }

            var tailX = m.x - Math.cos(Math.PI / 4) * m.length;
            var tailY = m.y - Math.sin(Math.PI / 4) * m.length;

            ctx.save();
            var grad = ctx.createLinearGradient(tailX, tailY, m.x, m.y);
            grad.addColorStop(0, 'rgba(' + m.colorRgb + ', 0)');
            grad.addColorStop(0.65, 'rgba(' + m.colorRgb + ', ' + (m.opacity * 0.5) + ')');
            grad.addColorStop(1, 'rgba(255, 255, 255, ' + m.opacity + ')');

            ctx.beginPath();
            ctx.moveTo(tailX, tailY);
            ctx.lineTo(m.x, m.y);
            ctx.strokeStyle = grad;
            ctx.lineWidth = m.thickness;
            ctx.lineCap = 'round';
            ctx.shadowBlur = 10;
            ctx.shadowColor = 'rgba(' + m.colorRgb + ', 0.9)';
            ctx.stroke();

            // Kepala meteor (bintang jatuh bercahaya terang di ujung lintasan)
            ctx.beginPath();
            ctx.arc(m.x, m.y, m.thickness * 1.2, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(255, 255, 255, ' + m.opacity + ')';
            ctx.shadowBlur = 14;
            ctx.shadowColor = '#ffffff';
            ctx.fill();
            ctx.restore();
        }
    }

    function draw() {
        ctx.clearRect(0, 0, W, H);
        for (var i = 0; i < stars.length; i++) {
            var s = stars[i];
            s.a += s.da;
            if (s.a >= 1) { s.a = 1; s.da = -Math.abs(s.da); }
            else if (s.a <= 0) { s.a = 0; s.da = Math.abs(s.da); }
            ctx.beginPath();
            ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(' + s.c + ',' + s.a + ')';
            ctx.fill();
        }
        updateAndDrawMeteors();
        requestAnimationFrame(draw);
    }

    window.addEventListener('resize', resize);
    resize();
    draw();
})();
</script>
@stack('scripts')
</body>
</html>
