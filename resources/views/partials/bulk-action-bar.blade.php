{{--
    Partial: bulk-action-bar
    Variables expected:
      $bulkDeleteRoute  — URL POST untuk bulk delete
      $hasDetail        — bool, apakah ada tombol Detail (default: true)
      $hasEdit          — bool, apakah ada tombol Edit (default: true)
      $bulkEntityLabel  — label entitas, e.g. "guru", "siswa", "pengguna"
--}}
@php
    $hasDetail = $hasDetail ?? true;
    $hasEdit   = $hasEdit   ?? true;
    $bulkEntityLabel = $bulkEntityLabel ?? 'data';
@endphp

{{-- Floating Bulk Action Bar --}}
<div class="bulk-action-bar" id="bulkActionBar">
    <span class="bulk-selected-badge">
        <span id="bulkCount">0</span> dipilih
    </span>
    <div class="bulk-divider"></div>

    @if($hasDetail)
    <button type="button" class="btn btn-secondary btn-sm" id="btnBulkDetail" onclick="bulkStartQueue('detail')">
        🔍 Lihat Detail
    </button>
    @endif

    @if($hasEdit)
    <button type="button" class="btn btn-primary btn-sm" id="btnBulkEdit" onclick="bulkStartQueue('edit')">
        ✏️ Edit Antre
    </button>
    @endif

    <button type="button" class="btn btn-danger btn-sm" id="btnBulkDelete" onclick="openBulkDeleteModal()">
        🗑 Hapus Terpilih
    </button>

    <div class="bulk-divider"></div>

    <button type="button" class="btn btn-secondary btn-sm" onclick="clearBulkSelection()" style="font-size:12px; padding:5px 10px;">
        ✕ Batal
    </button>
</div>

{{-- Hidden form untuk bulk delete --}}
<form id="formBulkDelete" action="{{ $bulkDeleteRoute }}" method="POST" style="display:none;">
    @csrf
    <div id="bulkDeleteInputs"></div>
</form>

{{-- Konfirmasi Modal Bulk Delete --}}
<div class="bulk-modal-overlay" id="bulkDeleteModal">
    <div class="bulk-modal-box">
        <div class="bulk-modal-icon">⚠️</div>
        <div class="bulk-modal-title">Hapus Data Terpilih?</div>
        <div class="bulk-modal-desc">
            Anda akan menghapus <strong id="bulkDeleteCount">0</strong> {{ $bulkEntityLabel }}.
            Tindakan ini tidak dapat dibatalkan secara langsung.
        </div>
        <div class="bulk-modal-actions">
            <button type="button" class="btn btn-danger" onclick="confirmBulkDelete()">Ya, Hapus</button>
            <button type="button" class="btn btn-secondary" onclick="closeBulkDeleteModal()">Batal</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    // ── State ──
    let selectedIds   = new Set();
    let selectedData  = {};   // id -> { editUrl, showUrl }

    // ── Init checkboxes ──
    function initBulkCheckboxes() {
        document.querySelectorAll('.bulk-checkbox[data-id]').forEach(cb => {
            cb.addEventListener('change', function() {
                const id      = this.dataset.id;
                const editUrl = this.dataset.editUrl || null;
                const showUrl = this.dataset.showUrl || null;
                const row     = this.closest('tr');

                if (this.checked) {
                    selectedIds.add(id);
                    selectedData[id] = { editUrl, showUrl };
                    if (row) row.classList.add('bulk-row-selected');
                } else {
                    selectedIds.delete(id);
                    delete selectedData[id];
                    if (row) row.classList.remove('bulk-row-selected');
                }
                updateBulkBar();
            });
        });

        // Select-all
        const selectAll = document.getElementById('checkboxSelectAll');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.bulk-checkbox[data-id]').forEach(cb => {
                    // Only tick visible checkboxes (respect hidden rows from filter)
                    const row = cb.closest('tr');
                    if (!row || row.style.display !== 'none') {
                        cb.checked = this.checked;
                        cb.dispatchEvent(new Event('change'));
                    }
                });
            });
        }
    }

    function updateBulkBar() {
        const count = selectedIds.size;
        document.getElementById('bulkCount').textContent = count;
        const bar = document.getElementById('bulkActionBar');
        if (count > 0) {
            bar.classList.add('show');
        } else {
            bar.classList.remove('show');
            const sa = document.getElementById('checkboxSelectAll');
            if (sa) sa.checked = false;
        }
    }

    // ── Queue: simpan ke sessionStorage lalu navigasi ──
    window.bulkStartQueue = function(type) {
        if (selectedIds.size === 0) return;

        const urls = [];
        selectedIds.forEach(id => {
            const d = selectedData[id] || {};
            const url = type === 'edit' ? d.editUrl : d.showUrl;
            if (url) urls.push(url);
        });

        if (urls.length === 0) {
            alert('URL tidak tersedia untuk aksi ini.');
            return;
        }

        sessionStorage.setItem('bulkQueue', JSON.stringify({
            urls: urls,
            type: type,
            index: 0,
            total: urls.length,
        }));

        window.location.href = urls[0];
    };

    // ── clearBulkSelection ──
    window.clearBulkSelection = function() {
        document.querySelectorAll('.bulk-checkbox[data-id]').forEach(cb => {
            cb.checked = false;
            const row = cb.closest('tr');
            if (row) row.classList.remove('bulk-row-selected');
        });
        selectedIds.clear();
        selectedData = {};
        const sa = document.getElementById('checkboxSelectAll');
        if (sa) sa.checked = false;
        updateBulkBar();
    };

    // ── Bulk Delete Modal ──
    window.openBulkDeleteModal = function() {
        if (selectedIds.size === 0) return;
        document.getElementById('bulkDeleteCount').textContent = selectedIds.size;
        document.getElementById('bulkDeleteModal').classList.add('show');
    };
    window.closeBulkDeleteModal = function() {
        document.getElementById('bulkDeleteModal').classList.remove('show');
    };
    window.confirmBulkDelete = function() {
        const container = document.getElementById('bulkDeleteInputs');
        container.innerHTML = '';
        selectedIds.forEach(id => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'ids[]';
            inp.value = id;
            container.appendChild(inp);
        });
        document.getElementById('bulkDeleteModal').classList.remove('show');
        document.getElementById('formBulkDelete').submit();
    };

    document.getElementById('bulkDeleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeBulkDeleteModal();
    });

    // ── Init ──
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBulkCheckboxes);
    } else {
        initBulkCheckboxes();
    }
})();
</script>
@endpush
