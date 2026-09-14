<div class="card mb-24" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: #ffffff; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); border: none;">
    <div class="card-body" style="padding: 18px 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="background: rgba(255,255,255,0.15); padding: 12px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 28px; height: 28px; color: #ffffff;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 16 14"></polyline></svg>
                </div>
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #93c5fd; font-weight: 700;">
                        🕒 Waktu Laptop / Perangkat Saat Ini
                    </div>
                    <div style="font-size: 26px; font-weight: 800; letter-spacing: 0.5px; font-family: monospace; line-height: 1.2; margin-top: 2px;" id="globalLiveClock">
                        --:--:-- WIB
                    </div>
                    <div style="font-size: 13px; color: #e2e8f0; margin-top: 2px;" id="globalLiveDate">
                        Memuat tanggal...
                    </div>
                </div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.25); padding: 12px 20px; border-radius: 12px; min-width: 260px;">
                <div style="font-size: 11px; text-transform: uppercase; color: #bfdbfe; font-weight: 700; letter-spacing: 0.5px;">
                    📌 Status Jam KBM Saat Ini
                </div>
                <div style="font-size: 16px; font-weight: 700; margin-top: 4px; color: #ffffff;" id="globalKbmSlotText">
                    Memuat status KBM...
                </div>
                <div style="font-size: 12px; margin-top: 4px; font-weight: 600; color: #86efac;" id="globalKbmDetailText">
                    Otomatis Terdeteksi System
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
    @endphp

    const kbmConfig = @json($kbmConfigJs);
    const seninKamisSlots = kbmConfig.senin_kamis_list || [];
    const jumatSlots = kbmConfig.jumat_list || [];
    const jamMasukGlobal = kbmConfig.jam_masuk || '07:00';
    const jamPulangSeninKamis = kbmConfig.jam_pulang_senin_kamis || '15:00';
    const jamPulangJumat = kbmConfig.jam_pulang_jumat || '15:30';

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

        if (clockEl) clockEl.textContent = `${hh}:${mm}:${ss} WIB`;
        if (dateEl) dateEl.textContent = `${dayName}, ${dateNum} ${monthName} ${year}`;

        if (!slotEl || !detailEl) return;

        // Weekend check
        if (dayIdx === 0 || dayIdx === 6) {
            slotEl.innerHTML = '🏖️ Hari Libur Sekolah';
            detailEl.textContent = 'Tidak ada kegiatan belajar mengajar (KBM)';
            detailEl.style.color = '#a7f3d0';
            return;
        }

        const isJumat = (dayIdx === 5);
        const activeSlots = isJumat ? jumatSlots : seninKamisSlots;
        const jamPulang = isJumat ? jamPulangJumat : jamPulangSeninKamis;

        if (timeStr < jamMasukGlobal) {
            slotEl.innerHTML = '🌅 Belum Masuk Jam KBM';
            detailEl.textContent = `Kegiatan KBM Dimulai Pukul ${jamMasukGlobal} WIB`;
            detailEl.style.color = '#cbd5e1';
            return;
        }

        let found = false;
        for (let s of activeSlots) {
            if (timeStr >= s.mulai && timeStr <= s.selesai) {
                found = true;
                if (s.istirahat) {
                    slotEl.innerHTML = `☕ Sedang Waktu ${s.ket}`;
                    detailEl.textContent = 'Jeda Kegiatan Belajar Mengajar';
                    detailEl.style.color = '#fed7aa';
                } else {
                    slotEl.innerHTML = `Jam Ke-${s.jam} (${s.mulai} - ${s.selesai})`;
                    detailEl.textContent = s.ket ? `KBM Aktif: ${s.ket}` : 'Jam Kegiatan Belajar Mengajar Aktif';
                    detailEl.style.color = '#86efac';
                }
                break;
            }
        }

        if (!found) {
            if (timeStr >= jamPulang) {
                slotEl.innerHTML = '🏠 Jam Pulang Sekolah';
                detailEl.textContent = `KBM Hari ini telah selesai (Pukul ${jamPulang} WIB)`;
                detailEl.style.color = '#fca5a5';
            } else {
                slotEl.innerHTML = '📖 Di Luar Jam Sesi KBM';
                detailEl.textContent = 'Tidak Ada Sesi KBM Berjalan';
                detailEl.style.color = '#e2e8f0';
            }
        }
    }

    updateGlobalKbmClock();
    setInterval(updateGlobalKbmClock, 1000);
});
</script>
