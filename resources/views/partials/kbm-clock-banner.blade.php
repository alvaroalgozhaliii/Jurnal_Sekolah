<style>
.kbm-clock-card {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 16px;
    box-shadow: 0 10px 30px -8px rgba(30, 58, 138, 0.35), 0 4px 12px -2px rgba(0, 0, 0, 0.08);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), 
                box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), 
                border-color 0.35s ease;
    cursor: default;
}

/* Ambient subtle background glow */
.kbm-clock-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, rgba(96, 165, 250, 0.22) 0%, rgba(30, 58, 138, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
    transition: opacity 0.4s ease;
}

/* Smooth Hover Effect */
.kbm-clock-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 38px -6px rgba(30, 58, 138, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.28);
}

.kbm-clock-card:hover .kbm-clock-icon-box {
    transform: scale(1.06);
    background: rgba(255, 255, 255, 0.22);
    border-color: rgba(255, 255, 255, 0.35);
}

.kbm-clock-card:hover .kbm-status-box {
    background: rgba(255, 255, 255, 0.14);
    border-color: rgba(255, 255, 255, 0.28);
}

/* Dedicated Hover Effect pada Box Status KBM */
.kbm-status-box:hover {
    background: rgba(255, 255, 255, 0.22) !important;
    border-color: rgba(255, 255, 255, 0.50) !important;
    transform: translateY(-4px) scale(1.02) !important;
    box-shadow: 0 14px 30px -4px rgba(0, 0, 0, 0.30), 0 0 24px rgba(255, 255, 255, 0.18) !important;
}

.kbm-status-box .kbm-status-header svg {
    transition: transform 0.3s ease, color 0.3s ease;
}

.kbm-status-box:hover .kbm-status-header svg {
    transform: scale(1.2);
    color: #ffffff;
}

/* Left Icon Box */
.kbm-clock-icon-box {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* Live pulse dot */
.clock-live-tag {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.1px;
    color: #93c5fd;
}

.clock-live-dot {
    width: 7px;
    height: 7px;
    background-color: #34d399;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7);
    animation: livePulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
}

@keyframes livePulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(52, 211, 153, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
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
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.5px;
    font-variant-numeric: tabular-nums;
    line-height: 1.1;
    color: #ffffff;
}

.clock-tz-badge {
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.6px;
    padding: 3px 8px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.16);
    border: 1px solid rgba(255, 255, 255, 0.18);
    color: #ffffff;
}

.clock-date-display {
    font-size: 13px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.8);
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Right Status Box */
.kbm-status-box {
    background: rgba(255, 255, 255, 0.10);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.20);
    border-radius: 14px;
    padding: 14px 20px;
    min-width: 270px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
}

.kbm-status-header {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #bfdbfe;
    display: flex;
    align-items: center;
    gap: 6px;
}

.kbm-status-title {
    font-size: 16.5px;
    font-weight: 700;
    color: #ffffff;
    margin-top: 4px;
    line-height: 1.3;
}

.kbm-detail-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 5px;
}
</style>

<div class="card mb-24 kbm-clock-card">
    <div class="card-body" style="padding: 20px 26px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 18px;">
            {{-- Sisi Kiri: Jam & Tanggal --}}
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="kbm-clock-icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px; color: #ffffff;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div>
                    <div class="clock-live-tag">
                        <span class="clock-live-dot"></span>
                        Waktu Laptop / Perangkat Saat Ini
                    </div>
                    <div class="clock-time-display">
                        <span class="clock-digits" id="globalLiveClock">--:--:--</span>
                        <span class="clock-tz-badge">WIB</span>
                    </div>
                    <div class="clock-date-display">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.8;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span id="globalLiveDate">Memuat tanggal...</span>
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
                    <span id="globalKbmDetailText" style="color: #86efac;">Otomatis Terdeteksi System</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

        const clockEl = document.getElementById('globalLiveClock');
        const dateEl = document.getElementById('globalLiveDate');
        const slotEl = document.getElementById('globalKbmSlotText');
        const detailEl = document.getElementById('globalKbmDetailText');

        if (clockEl) clockEl.textContent = `${hh}:${mm}:${ss}`;
        if (dateEl) dateEl.textContent = `${dayName}, ${dateNum} ${monthName} ${year}`;

        if (!slotEl || !detailEl) return;

        // Weekend check
<<<<<<< HEAD
        if (dayIdx === 0 || dayIdx === 6) {
            slotEl.innerHTML = 'Hari Libur Sekolah';
=======
        if (dayIdx === 0 || dayIdx === 0 || dayIdx === 6) {
            slotEl.innerHTML = '🏖️ Hari Libur Sekolah';
>>>>>>> 17c7770e8d8f725f51c4468bb61b31a80b427243
            detailEl.textContent = 'Tidak ada kegiatan belajar mengajar (KBM)';
            detailEl.style.color = '#a7f3d0';
            return;
        }

        const isJumat = (dayIdx === 5);
        let activeSlots = seninKamisSlots;
        let jamPulang = jamPulangSeninKamis;

        if (isJumat) {
            if (userIsKelasX === false) {
                // Khusus Kelas 11 & 12
                activeSlots = jumatXiSlots;
                jamPulang = jamPulangJumatXi;
            } else {
                // Kelas 10 atau Tampilan Umum
                activeSlots = jumatXSlots;
                jamPulang = jamPulangJumatX;
            }
        }

        if (timeStr < jamMasukGlobal) {
            slotEl.innerHTML = 'Belum Masuk Jam KBM';
            detailEl.textContent = `Kegiatan KBM Dimulai Pukul ${jamMasukGlobal} WIB`;
            detailEl.style.color = '#cbd5e1';
            return;
        }

        let found = false;
        for (let s of activeSlots) {
            if (timeStr >= s.mulai && timeStr <= s.selesai) {
                found = true;
                if (s.istirahat) {
<<<<<<< HEAD
                    slotEl.innerHTML = `Sedang Waktu ${s.ket}`;
                    detailEl.textContent = 'Jeda Kegiatan Belajar Mengajar';
=======
                    slotEl.innerHTML = `☕ Sedang Waktu ${s.ket}`;
                    detailEl.textContent = isJumat && s.istirahat === 8 ? 'Jeda Solat Jumat & Istirahat' : 'Jeda Kegiatan Belajar Mengajar';
>>>>>>> 17c7770e8d8f725f51c4468bb61b31a80b427243
                    detailEl.style.color = '#fed7aa';
                } else {
                    if (isJumat && s.jam === 13) {
                        slotEl.innerHTML = `Jam Ke-${s.jam} (${s.mulai} - ${s.selesai}) • Khusus Kelas X`;
                        detailEl.textContent = 'Kelas XI & XII telah pulang pukul 15:00 WIB';
                        detailEl.style.color = '#c4b5fd';
                    } else {
                        slotEl.innerHTML = `Jam Ke-${s.jam} (${s.mulai} - ${s.selesai})`;
                        detailEl.textContent = s.ket ? `KBM: ${s.ket}` : 'Jam Kegiatan Belajar Mengajar Aktif';
                        detailEl.style.color = '#86efac';
                    }
                }
                break;
            }
        }

        if (!found) {
            if (timeStr >= jamPulang) {
<<<<<<< HEAD
                slotEl.innerHTML = 'Jam Pulang Sekolah';
                detailEl.textContent = `KBM Hari ini telah selesai (Pukul ${jamPulang} WIB)`;
=======
                slotEl.innerHTML = '🏠 Jam Pulang Sekolah';
                if (isJumat && userIsKelasX === null) {
                    detailEl.textContent = `KBM Selesai (Kelas 11/12: ${jamPulangJumatXi} WIB • Kelas 10: ${jamPulangJumatX} WIB)`;
                } else {
                    detailEl.textContent = `KBM Hari ini telah selesai (Pukul ${jamPulang} WIB)`;
                }
>>>>>>> 17c7770e8d8f725f51c4468bb61b31a80b427243
                detailEl.style.color = '#fca5a5';
            } else {
                slotEl.innerHTML = 'Di Luar Jam Sesi KBM';
                detailEl.textContent = 'Tidak Ada Sesi KBM Berjalan';
                detailEl.style.color = '#e2e8f0';
            }
        }
    }

    updateGlobalKbmClock();
    setInterval(updateGlobalKbmClock, 1000);
});
</script>
