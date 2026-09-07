<style>
.calendar-wrapper {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    padding: 20px;
    margin-bottom: 24px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    user-select: none;
}
.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.calendar-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.calendar-nav-btn {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    cursor: pointer;
    font-weight: 600;
    color: #475569;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}
.calendar-nav-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
}
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    text-align: center;
}
.calendar-day-header {
    font-weight: 600;
    color: #64748b;
    font-size: 0.85rem;
    padding-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.calendar-day {
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-radius: 10px;
    font-weight: 500;
    color: #334155;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    font-size: 1rem;
}
.calendar-day:hover:not(.empty) {
    background: #f1f5f9;
}
.calendar-day.empty {
    cursor: default;
}
.calendar-day.today {
    border: 2px solid #3b82f6;
    color: #3b82f6;
    font-weight: 700;
}
.calendar-day.selected {
    background: #3b82f6 !important;
    color: white !important;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);
    border: none;
}
.calendar-day .data-dot {
    width: 5px;
    height: 5px;
    background: #10b981;
    border-radius: 50%;
    position: absolute;
    bottom: 8px;
}
.calendar-day.selected .data-dot {
    background: #ffffff;
}
</style>

@php
    // Props default
    $mode = $mode ?? 'single'; // 'single' or 'month'
    $tanggal = $tanggal ?? \Carbon\Carbon::today()->toDateString();
    $bulan = $bulan ?? \Carbon\Carbon::parse($tanggal)->month;
    $tahun = $tahun ?? \Carbon\Carbon::parse($tanggal)->year;
    $inputName = $inputName ?? 'tanggal';
    
    // For single mode, the $tanggal specifies the selected day
    // For month mode, it just highlights $tanggal if it falls in the current month, but focuses on navigating month/year
    
    // Data dates to show dots
    $dataTanggal = isset($dataTanggal) ? json_encode($dataTanggal) : '[]';
    
    // Create random id for JS scoping if multiple calendars
    $calId = 'cal_' . uniqid();
@endphp

<div class="calendar-wrapper" id="{{ $calId }}">
    <div class="calendar-header">
        <button type="button" class="calendar-nav-btn prev-btn">
            ◀ <span>Bulan Sebelumnya</span>
        </button>
        <div class="calendar-title">
            📅 <span class="month-year-display"></span>
        </div>
        <button type="button" class="calendar-nav-btn next-btn">
            <span>Bulan Berikutnya</span> ▶
        </button>
    </div>
    
    <div class="calendar-grid">
        <div class="calendar-day-header">Min</div>
        <div class="calendar-day-header">Sen</div>
        <div class="calendar-day-header">Sel</div>
        <div class="calendar-day-header">Rab</div>
        <div class="calendar-day-header">Kam</div>
        <div class="calendar-day-header">Jum</div>
        <div class="calendar-day-header">Sab</div>
        
        <!-- Days injected via JS -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('{{ $calId }}');
    const grid = container.querySelector('.calendar-grid');
    const display = container.querySelector('.month-year-display');
    const prevBtn = container.querySelector('.prev-btn');
    const nextBtn = container.querySelector('.next-btn');
    
    const mode = '{{ $mode }}';
    const inputName = '{{ $inputName }}';
    const dataDates = {!! $dataTanggal !!};
    
    let currentSelectedDate = '{{ $tanggal }}';
    let currentMonth = parseInt('{{ $bulan }}') - 1; // 0-indexed for JS
    let currentYear = parseInt('{{ $tahun }}');
    
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    function renderCalendar(month, year) {
        // Clear existing days
        const headers = Array.from(grid.querySelectorAll('.calendar-day-header'));
        grid.innerHTML = '';
        headers.forEach(h => grid.appendChild(h));
        
        display.textContent = `${monthNames[month]} ${year}`;
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        const today = new Date();
        const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
        
        // Empty cells for days before start
        for (let i = 0; i < firstDay; i++) {
            const empty = document.createElement('div');
            empty.className = 'calendar-day empty';
            grid.appendChild(empty);
        }
        
        // Days
        for (let i = 1; i <= daysInMonth; i++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            const dayEl = document.createElement('div');
            dayEl.className = 'calendar-day';
            dayEl.textContent = i;
            
            if (dateStr === todayStr) {
                dayEl.classList.add('today');
            }
            if (dateStr === currentSelectedDate && mode === 'single') {
                dayEl.classList.add('selected');
            }
            
            if (dataDates.includes(dateStr)) {
                const dot = document.createElement('div');
                dot.className = 'data-dot';
                dayEl.appendChild(dot);
            }
            
            dayEl.addEventListener('click', () => handleDayClick(dateStr, dayEl));
            grid.appendChild(dayEl);
        }
    }
    
    function handleDayClick(dateStr, element) {
        if (mode === 'single') {
            currentSelectedDate = dateStr;
            
            // Highlight
            const prev = container.querySelector('.calendar-day.selected');
            if (prev) prev.classList.remove('selected');
            element.classList.add('selected');
            
            // Auto submit
            submitDateForm(dateStr);
        } else if (mode === 'month') {
            // Just highlight visual
            const prev = container.querySelector('.calendar-day.selected');
            if (prev) prev.classList.remove('selected');
            element.classList.add('selected');
        }
    }
    
    function submitDateForm(dateStr) {
        const form = container.closest('form');
        if (form) {
            let input = form.querySelector(`input[name="${inputName}"]`);
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = inputName;
                form.appendChild(input);
            }
            input.value = dateStr;
            form.submit();
        } else {
            // If no form, create temporary one and submit to current URL
            const tempForm = document.createElement('form');
            tempForm.method = 'GET';
            tempForm.action = window.location.pathname;
            
            // Keep existing query params except the one we're changing
            const params = new URLSearchParams(window.location.search);
            params.set(inputName, dateStr);
            
            for (const [key, value] of params.entries()) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                tempForm.appendChild(input);
            }
            
            document.body.appendChild(tempForm);
            tempForm.submit();
        }
    }
    
    function submitMonthForm(month, year) {
        const form = container.closest('form');
        if (form) {
            // Check if form has bulan/tahun inputs
            let bInput = form.querySelector('select[name="bulan"], input[name="bulan"]');
            let tInput = form.querySelector('select[name="tahun"], input[name="tahun"]');
            
            if (bInput && tInput) {
                bInput.value = month + 1;
                tInput.value = year;
                form.submit();
                return;
            }
        }
        
        // Fallback temp form
        const tempForm = document.createElement('form');
        tempForm.method = 'GET';
        tempForm.action = window.location.pathname;
        
        const params = new URLSearchParams(window.location.search);
        params.set('bulan', month + 1);
        params.set('tahun', year);
        if (mode === 'month' && params.has('tanggal')) params.delete('tanggal'); // Optional cleanup
        
        for (const [key, value] of params.entries()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            tempForm.appendChild(input);
        }
        
        document.body.appendChild(tempForm);
        tempForm.submit();
    }
    
    prevBtn.addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        if (mode === 'month') {
            submitMonthForm(currentMonth, currentYear);
        } else {
            renderCalendar(currentMonth, currentYear);
        }
    });
    
    nextBtn.addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        if (mode === 'month') {
            submitMonthForm(currentMonth, currentYear);
        } else {
            renderCalendar(currentMonth, currentYear);
        }
    });
    
    renderCalendar(currentMonth, currentYear);
});
</script>
