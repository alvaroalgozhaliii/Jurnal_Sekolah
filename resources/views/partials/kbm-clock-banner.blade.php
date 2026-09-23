<style>
.kbm-clock-card {
    position: relative;
    overflow: hidden;
    background: var(--bg-card, #ffffff);
    color: var(--text-primary, #1e293b);
    border: 1px solid var(--border, #e2e8f0);
    border-left: 4px solid var(--navy-primary, #1e3a8a);
    border-radius: 16px;
    box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05), 0 2px 6px -2px rgba(15, 23, 42, 0.03);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), 
                box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), 
                border-color 0.25s ease;
    cursor: default;
}

/* Subtle ambient mesh background in corner */
.kbm-clock-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 260px;
    height: 100%;
    background: radial-gradient(circle at 100% 0%, rgba(37, 99, 235, 0.04) 0%, transparent 70%);
    pointer-events: none;
}

/* Smooth Hover Effect */
.kbm-clock-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.04);
    border-color: rgba(30, 58, 138, 0.25);
}

/* Left Icon Box */
.kbm-clock-icon-box {
    width: 48px;
    height: 48px;
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #dbeafe;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.25s ease;
}

.kbm-clock-card:hover .kbm-clock-icon-box {
    transform: scale(1.05);
    background: #dbeafe;
}

/* Live pulse dot */
.clock-live-tag {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--text-secondary, #64748b);
}

.clock-live-dot {
    width: 7px;
    height: 7px;
    background-color: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: livePulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
}

@keyframes livePulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Clock Digits */
.clock-time-display {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-top: 3px;
}

.clock-digits {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 30px;
    font-weight: 800;
    letter-spacing: -0.5px;
    font-variant-numeric: tabular-nums;
    line-height: 1.1;
    color: var(--text-primary, #0f172a);
}

.clock-tz-badge {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 2px 7px;
    border-radius: 6px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
}

.clock-date-display {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-secondary, #64748b);
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Right Status Box */
.kbm-status-box {
    background: var(--bg-app, #f8fafc);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 14px;
    padding: 12px 18px;
    min-width: 270px;
    transition: all 0.25s ease;
}

.kbm-status-box:hover {
    background: var(--bg-card, #ffffff);
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px -2px rgba(15, 23, 42, 0.06);
}

.kbm-status-header {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--text-secondary, #64748b);
    display: flex;
    align-items: center;
    gap: 6px;
}

.kbm-status-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary, #0f172a);
    margin-top: 3px;
    line-height: 1.3;
}

.kbm-detail-badge {
    margin-top: 6px;
    display: flex;
    align-items: center;
}

.kbm-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 9999px;
    font-size: 11.5px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.kbm-pill-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

/* Status Pill Variants */
.pill-kbm {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.pill-kbm .kbm-pill-dot {
    background: #16a34a;
    box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.25);
}

.pill-istirahat {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}
.pill-istirahat .kbm-pill-dot {
    background: #d97706;
    box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.25);
}

.pill-pulang {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}
.pill-pulang .kbm-pill-dot {
    background: #64748b;
}

.pill-libur {
    background: #f3e8ff;
    color: #7e22ce;
    border: 1px solid #e9d5ff;
}
.pill-libur .kbm-pill-dot {
    background: #9333ea;
}

.pill-menunggu {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.pill-menunggu .kbm-pill-dot {
    background: #0284c7;
}

/* DARK MODE SUPPORT */
[data-theme="dark"] .kbm-clock-card {
    background: var(--bg-card, #152246);
    color: var(--text-primary, #f8fafc);
    border-color: var(--border, #2a3d6e);
    box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.35);
}

[data-theme="dark"] .kbm-clock-card::before {
    background: radial-gradient(circle at 100% 0%, rgba(96, 165, 250, 0.08) 0%, transparent 70%);
}

[data-theme="dark"] .kbm-clock-card:hover {
    border-color: rgba(96, 165, 250, 0.3);
    box-shadow: 0 10px 28px -4px rgba(0, 0, 0, 0.45);
}

[data-theme="dark"] .kbm-clock-icon-box {
    background: rgba(59, 130, 246, 0.15);
    color: #93c5fd;
    border-color: rgba(59, 130, 246, 0.25);
}

[data-theme="dark"] .kbm-clock-card:hover .kbm-clock-icon-box {
    background: rgba(59, 130, 246, 0.25);
}

[data-theme="dark"] .clock-digits {
    color: #f8fafc;
}

[data-theme="dark"] .clock-tz-badge {
    background: rgba(59, 130, 246, 0.2);
    border-color: rgba(59, 130, 246, 0.35);
    color: #93c5fd;
}

[data-theme="dark"] .clock-date-display {
    color: #94a3b8;
}

[data-theme="dark"] .kbm-status-box {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.08);
}

[data-theme="dark"] .kbm-status-box:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.15);
}

[data-theme="dark"] .kbm-status-title {
    color: #f8fafc;
}

[data-theme="dark"] .pill-kbm {
    background: rgba(22, 163, 74, 0.2);
    color: #86efac;
    border-color: rgba(22, 163, 74, 0.35);
}

[data-theme="dark"] .pill-istirahat {
    background: rgba(217, 119, 6, 0.2);
    color: #fde047;
    border-color: rgba(217, 119, 6, 0.35);
}

[data-theme="dark"] .pill-pulang {
    background: rgba(148, 163, 184, 0.15);
    color: #cbd5e1;
    border-color: rgba(148, 163, 184, 0.25);
}

[data-theme="dark"] .pill-libur {
    background: rgba(147, 51, 234, 0.2);
    color: #d8b4fe;
    border-color: rgba(147, 51, 234, 0.35);
}

[data-theme="dark"] .pill-menunggu {
    background: rgba(2, 132, 199, 0.2);
    color: #7dd3fc;
    border-color: rgba(2, 132, 199, 0.35);
}
</style>

@php
    $nowServer = \Carbon\Carbon::now('Asia/Jakarta');
    $daysIndoServer = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $monthsIndoServer = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $initialDateStr = $daysIndoServer[$nowServer->dayOfWeek] . ', ' . $nowServer->format('d') . ' ' . $monthsIndoServer[$nowServer->month - 1] . ' ' . $nowServer->year;
    $initialTimeStr = $nowServer->format('H:i:s');
@endphp

<div class="card mb-24 kbm-clock-card" id="globalKbmClockCard">
    <div class="card-body" style="padding: 18px 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 18px;">
            {{-- Sisi Kiri: Jam & Tanggal --}}
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="kbm-clock-icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div>
                    <div class="clock-live-tag">
                        <span class="clock-live-dot"></span>
                        Waktu Real-Time (Perangkat)
                    </div>
                    <div class="clock-time-display">
                        <span class="clock-digits" id="globalLiveClock">{{ $initialTimeStr }}</span>
                        <span class="clock-tz-badge">WIB</span>
                    </div>
                    <div class="clock-date-display">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.8;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span id="globalLiveDate">{{ $initialDateStr }}</span>
                    </div>
                </div>
            </div>

            {{-- Sisi Kanan: Status KBM --}}
            <div class="kbm-status-box">
                <div class="kbm-status-header">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    Status Jam KBM Saat Ini
                </div>
                <div class="kbm-status-title" id="globalKbmSlotText">
                    Memuat status KBM...
                </div>
                <div class="kbm-detail-badge">
                    <span id="globalKbmBadge" class="kbm-status-pill pill-kbm">
                        <span class="kbm-pill-dot"></span>
                        <span id="globalKbmDetailText">Otomatis Terdeteksi Sistem</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const monthsIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    @php
        $kbmConfigJs = \App\Services\KbmService::getSlotsForJs();
        $userAuth = Auth::user();
        $userTingkat = null;
        if ($userAuth) {
            if ($userAuth->siswa && $userAuth->siswa->kelas) {
                $userTingkat = $userAuth->siswa->kelas->tingkat ?? $userAuth->siswa->kelas->nama_kelas;
            } elseif ($userAuth->guru && $userAuth->guru->kelasWali) {
                $userTingkat = $userAuth->guru->kelasWali->tingkat ?? $userAuth->guru->kelasWali->nama_kelas;
            }
        }
        $isUserKelasX = $userTingkat ? \App\Services\KbmService::isKelasX($userTingkat) : null;
    @endphp

    const kbmConfig = @json($kbmConfigJs);
    const userIsKelasX = @json($isUserKelasX);
    const seninKamisSlots = kbmConfig.senin_kamis_list || [];
    const jumatXSlots = kbmConfig.jumat_x_list || kbmConfig.jumat_list || [];
    const jumatXiSlots = kbmConfig.jumat_xi_list || [];
    const jamMasukGlobal = kbmConfig.jam_masuk || '07:00';
    const jamPulangSeninKamis = kbmConfig.jam_pulang_senin_kamis || '15:00';
    const jamPulangJumatX = kbmConfig.jam_pulang_jumat_x || '15:30';
    const jamPulangJumatXi = kbmConfig.jam_pulang_jumat_xi || '15:00';

    const cardEl = document.getElementById('globalKbmClockCard');
    const clockEl = document.getElementById('globalLiveClock');
    const dateEl = document.getElementById('globalLiveDate');
    const slotEl = document.getElementById('globalKbmSlotText');
    const detailEl = document.getElementById('globalKbmDetailText');
    const badgeEl = document.getElementById('globalKbmBadge');

    function setStatusState(type, slotTitle, detailText, accentColor) {
        if (slotEl) slotEl.innerHTML = slotTitle;
        if (detailEl) detailEl.textContent = detailText;
        if (badgeEl) badgeEl.className = 'kbm-status-pill pill-' + type;
        if (cardEl) cardEl.style.borderLeftColor = accentColor;
    }

    function updateGlobalKbmClock() {
        const now = new Date();
        const dayIdx = now.getDay();
        const dayName = daysIndo[dayIdx];
        const dateNum = String(now.getDate()).padStart(2, '0');
        const monthName = monthsIndo[now.getMonth()];
        const year = now.getFullYear();

        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        const timeStr = `${hh}:${mm}`;

        if (clockEl) clockEl.textContent = `${hh}:${mm}:${ss}`;
        if (dateEl) dateEl.textContent = `${dayName}, ${dateNum} ${monthName} ${year}`;

        if (!slotEl || !detailEl) return;

        if (dayIdx === 0 || dayIdx === 6) {
            setStatusState('libur', '🏖️ Hari Libur Sekolah', 'Tidak ada kegiatan belajar mengajar (KBM)', '#9333ea');
            return;
        }

        const isJumat = (dayIdx === 5);
        let activeSlots = seninKamisSlots;
        let jamPulang = jamPulangSeninKamis;

        if (isJumat) {
            if (userIsKelasX === false) {
                activeSlots = jumatXiSlots;
                jamPulang = jamPulangJumatXi;
            } else {
                activeSlots = jumatXSlots;
                jamPulang = jamPulangJumatX;
            }
        }

        if (timeStr < jamMasukGlobal) {
            setStatusState('menunggu', 'Belum Masuk Jam KBM', `KBM Dimulai Pukul ${jamMasukGlobal} WIB`, '#0284c7');
            return;
        }

        let found = false;
        for (let s of activeSlots) {
            if (timeStr >= s.mulai && timeStr <= s.selesai) {
                found = true;
                if (s.istirahat) {
                    const istKet = isJumat && s.istirahat === 8 ? 'Jeda Solat Jumat & Istirahat' : 'Jeda Kegiatan Belajar Mengajar';
                    setStatusState('istirahat', `☕ Sedang Waktu ${s.ket}`, istKet, '#d97706');
                } else {
                    if (isJumat && s.jam === 13) {
                        setStatusState('kbm', `Jam Ke-${s.jam} (${s.mulai} - ${s.selesai}) • Khusus Kelas X`, 'Kelas XI & XII telah pulang pukul 15:00 WIB', '#16a34a');
                    } else {
                        const kbmKet = s.ket ? `KBM: ${s.ket}` : 'Jam Kegiatan Belajar Mengajar Aktif';
                        setStatusState('kbm', `Jam Ke-${s.jam} (${s.mulai} - ${s.selesai})`, kbmKet, '#16a34a');
                    }
                }
                break;
            }
        }

        if (!found) {
            if (timeStr >= jamPulang) {
                let pulangKet = `KBM Hari ini telah selesai (Pukul ${jamPulang} WIB)`;
                if (isJumat && userIsKelasX === null) {
                    pulangKet = `KBM Selesai (Kelas 11/12: ${jamPulangJumatXi} WIB • Kelas 10: ${jamPulangJumatX} WIB)`;
                }
                setStatusState('pulang', '🏠 Jam Pulang Sekolah', pulangKet, '#64748b');
            } else {
                setStatusState('pulang', 'Di Luar Jam Sesi KBM', 'Tidak Ada Sesi KBM Berjalan', '#94a3b8');
            }
        }
    }

    updateGlobalKbmClock();
    setInterval(updateGlobalKbmClock, 1000);
})();
</script>
