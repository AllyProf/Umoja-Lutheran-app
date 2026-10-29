@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-exchange"></i> Distribute Stock</h1>
        <p>Issue from an approved department request, or send stock directly to a department</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('storekeeper.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('storekeeper.transfers.index') }}">Transfers</a></li>
        <li class="breadcrumb-item active">Distribute</li>
    </ul>
</div>

<form id="distributeForm">
    @csrf

    <div class="tile shadow-sm mb-4">
        <h3 class="tile-title"><span class="badge badge-primary mr-2">1</span> How do you want to distribute?</h3>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="sr-source-card is-active" id="cardRequest">
                    <input type="radio" name="mode" value="from_request" class="d-none" checked>
                    <div class="sr-source-inner">
                        <i class="fa fa-clipboard fa-2x text-primary mb-2"></i>
                        <h4 class="mb-1">Department request</h4>
                        <p class="text-muted mb-0 small">Issue items from a request already approved by the manager</p>
                    </div>
                </label>
            </div>
            <div class="col-md-6 mb-3">
                <label class="sr-source-card" id="cardDirect">
                    <input type="radio" name="mode" value="direct" class="d-none">
                    <div class="sr-source-inner">
                        <i class="fa fa-truck fa-2x text-success mb-2"></i>
                        <h4 class="mb-1">Distribute directly</h4>
                        <p class="text-muted mb-0 small">Storekeeper pushes stock to Restaurant or Housekeeping</p>
                    </div>
                </label>
            </div>
        </div>
    </div>

    {{-- FROM REQUEST --}}
    <div id="requestPanel">
        <div class="tile shadow-sm mb-4">
            <h3 class="tile-title"><span class="badge badge-primary mr-2">2</span> Select approved request</h3>
            <div class="form-group mb-0">
                <select id="batch_key" class="form-control form-control-lg">
                    <option value="">Select request batch...</option>
                    @forelse($batches as $batch)
                        <option value="{{ $batch['batch_key'] }}">
                            {{ $batch['batch_reference'] }}
                            · {{ $batch['requester'] }}
                            · {{ $batch['department'] }}
                            · {{ $batch['created_at'] }}
                            · {{ count($batch['items']) }} item{{ count($batch['items']) === 1 ? '' : 's' }}
                        </option>
                    @empty
                        <option value="" disabled>No approved requests waiting — departments must request &amp; manager must approve first</option>
                    @endforelse
                </select>
                <small class="text-muted">Only manager-approved requests are listed here.</small>
            </div>
        </div>
    </div>

    {{-- DIRECT --}}
    <div id="directPanel" class="d-none">
        <div class="tile shadow-sm mb-4">
            <h3 class="tile-title"><span class="badge badge-primary mr-2">2</span> Choose department</h3>
            <div class="row">
                @foreach([
                    'restaurant' => ['Restaurant', $receivers['restaurant']->name ?? null, $receivers['restaurant']->id ?? null],
                    'housekeeping' => ['Housekeeping', $receivers['housekeeping']->name ?? null, $receivers['housekeeping']->id ?? null],
                ] as $key => [$label, $staffName, $staffId])
                    <div class="col-md-6 mb-3">
                        <label class="sr-source-card dept-card {{ $key === 'restaurant' ? 'is-active' : '' }}" data-dept="{{ $key }}"
                            data-receiver-id="{{ $staffId }}">
                            <input type="radio" name="department" value="{{ $key }}" class="d-none" {{ $key === 'restaurant' ? 'checked' : '' }}>
                            <div class="sr-source-inner py-3">
                                <h4 class="mb-1" style="font-size:1.05rem;">{{ $label }}</h4>
                                <p class="text-muted mb-0 small">
                                    @if($staffName)
                                        Default receiver: {{ $staffName }}
                                    @else
                                        <span class="text-warning">No default staff — pick a receiver below</span>
                                    @endif
                                </p>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Receiver <span class="text-danger">*</span></label>
                    <div class="receiver-search-wrap position-relative">
                        <input type="hidden" id="received_by"
                            value="{{ $receivers['restaurant']->id ?? '' }}">
                        <input type="text" id="receiver_search" class="form-control"
                            placeholder="Type at least 2 letters to search staff..."
                            autocomplete="off"
                            value="{{ isset($receivers['restaurant']) ? ($receivers['restaurant']->name . ' (' . $receivers['restaurant']->role . ')') : '' }}">
                        <div id="receiver_results" class="receiver-results d-none"></div>
                    </div>
                    <small class="text-muted">Type a name (2+ letters). Required when there is no default staff.</small>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Transfer date</label>
                    <input type="date" id="transfer_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Notes</label>
                    <input type="text" id="direct_notes" class="form-control" placeholder="Optional notes">
                </div>
            </div>
        </div>
    </div>

    {{-- ITEMS --}}
    <div class="tile shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <h3 class="tile-title mb-0"><span class="badge badge-primary mr-2">3</span> Items to issue</h3>
            <div id="itemsMeta" class="text-muted small"></div>
        </div>

        <div id="itemsEmpty" class="alert alert-light border mb-0">
            <i class="fa fa-info-circle text-primary mr-1"></i>
            Select a request or department above to load items.
        </div>

        <div id="itemsTableWrap" class="d-none">
            <div class="mb-2" id="productSearchWrap" style="display:none;">
                <input type="text" id="productSearch" class="form-control" placeholder="Search product in store stock...">
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:40px" class="text-center">
                                <input type="checkbox" id="checkAll" checked>
                            </th>
                            <th>Item</th>
                            <th class="text-center" style="width:120px">In store</th>
                            <th class="text-center" id="colRequested" style="width:110px">Requested</th>
                            <th class="text-center" style="width:140px">Item issued</th>
                            <th class="text-center" style="width:120px" id="colUnit">Unit</th>
                            <th class="text-center" style="width:110px" title="Mark tools like broom/mop to be returned after use">Returnable</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="formAlert"></div>

    <div class="d-flex flex-wrap" style="gap:10px;">
        <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
            <i class="fa fa-check"></i> Issue now
        </button>
        <a href="{{ route('storekeeper.transfers.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
        <a href="{{ route('stock-requests.index') }}" class="btn btn-outline-primary btn-lg">
            View all requests
        </a>
    </div>
</form>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<style>
    .sr-source-card { display:block; cursor:pointer; margin:0; }
    .sr-source-inner {
        border: 2px solid #e3e6f0; border-radius: 10px; padding: 22px 18px;
        text-align: center; background: #fff; height: 100%;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .sr-source-card.is-active .sr-source-inner,
    .sr-source-card:has(input:checked) .sr-source-inner {
        border-color: #4e73df; background: #f8f9fc;
        box-shadow: 0 0 0 3px rgba(78,115,223,.12);
    }
    .sr-source-inner h4 { font-size: 1.15rem; font-weight: 700; }
    #itemsTableWrap tr.skip-row { opacity: .45; }
    .receiver-search-wrap { z-index: 20; }
    .receiver-results {
        position: absolute; left: 0; right: 0; top: 100%;
        max-height: 220px; overflow-y: auto; z-index: 30;
        background: #fff; border: 1px solid #d1d3e2; border-top: 0;
        border-radius: 0 0 6px 6px; box-shadow: 0 6px 16px rgba(0,0,0,.08);
    }
    .receiver-results .receiver-option {
        display: block; width: 100%; text-align: left;
        padding: 8px 12px; border: 0; background: #fff; cursor: pointer;
    }
    .receiver-results .receiver-option:hover,
    .receiver-results .receiver-option.is-active {
        background: #f0f4ff; color: #2e59d9;
    }
    .receiver-results .receiver-empty {
        padding: 10px 12px; color: #858796; font-size: .9rem;
    }
</style>
<script>
(function () {
    const batches = @json($batches);
    const catalog = @json($catalog);
    const staffList = @json($staffOptions ?? []);
    const storeUrl = @json(route('storekeeper.transfers.store'));
    const csrf = document.querySelector('#distributeForm input[name="_token"]').value;

    let currentRows = [];
    let currentMode = 'from_request';

    const requestPanel = document.getElementById('requestPanel');
    const directPanel = document.getElementById('directPanel');
    const batchSelect = document.getElementById('batch_key');
    const submitBtn = document.getElementById('submitBtn');
    const searchWrap = document.getElementById('productSearchWrap');
    const colRequested = document.getElementById('colRequested');
    const receivedByInput = document.getElementById('received_by');
    const receiverSearch = document.getElementById('receiver_search');
    const receiverResults = document.getElementById('receiver_results');

    function mode() {
        return document.querySelector('input[name="mode"]:checked')?.value || 'from_request';
    }
    function department() {
        return document.querySelector('input[name="department"]:checked')?.value || 'restaurant';
    }

    const defaultReceivers = {
        restaurant: {
            id: @json($receivers['restaurant']->id ?? null),
            label: @json(isset($receivers['restaurant']) ? ($receivers['restaurant']->name . ' (' . $receivers['restaurant']->role . ')') : null),
        },
        housekeeping: {
            id: @json($receivers['housekeeping']->id ?? null),
            label: @json(isset($receivers['housekeeping']) ? ($receivers['housekeeping']->name . ' (' . $receivers['housekeeping']->role . ')') : null),
        },
    };

    function setReceiver(id, label) {
        receivedByInput.value = id ? String(id) : '';
        receiverSearch.value = label || '';
        hideReceiverResults();
    }

    function hideReceiverResults() {
        receiverResults.classList.add('d-none');
        receiverResults.innerHTML = '';
    }

    function showReceiverMatches(query) {
        const q = (query || '').trim().toLowerCase();
        if (q.length < 2) {
            hideReceiverResults();
            return;
        }

        const matches = staffList.filter(function (s) {
            return (s.name || '').toLowerCase().includes(q)
                || (s.role || '').toLowerCase().includes(q)
                || (s.label || '').toLowerCase().includes(q);
        }).slice(0, 12);

        if (!matches.length) {
            receiverResults.innerHTML = '<div class="receiver-empty">No staff found for “' + query.replace(/[<>&]/g, '') + '”</div>';
            receiverResults.classList.remove('d-none');
            return;
        }

        receiverResults.innerHTML = matches.map(function (s) {
            return '<button type="button" class="receiver-option" data-id="' + s.id + '" data-label="' +
                String(s.label).replace(/"/g, '&quot;') + '">' +
                '<strong>' + s.name + '</strong>' +
                '<span class="text-muted small d-block">' + s.role + '</span>' +
                '</button>';
        }).join('');
        receiverResults.classList.remove('d-none');
    }

    function syncReceiverSelect() {
        const dept = department();
        const preferred = defaultReceivers[dept];
        if (preferred && preferred.id) {
            setReceiver(preferred.id, preferred.label);
        } else {
            setReceiver('', '');
        }
    }

    receiverSearch.addEventListener('input', function () {
        // Typing clears previous selection until a result is chosen
        receivedByInput.value = '';
        showReceiverMatches(this.value);
    });

    receiverSearch.addEventListener('focus', function () {
        if ((this.value || '').trim().length >= 2) {
            showReceiverMatches(this.value);
        }
    });

    receiverResults.addEventListener('click', function (e) {
        const btn = e.target.closest('.receiver-option');
        if (!btn) return;
        setReceiver(btn.dataset.id, btn.dataset.label);
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.receiver-search-wrap')) {
            hideReceiverResults();
        }
    });

    function syncMode() {
        currentMode = mode();
        document.getElementById('cardRequest').classList.toggle('is-active', currentMode === 'from_request');
        document.getElementById('cardDirect').classList.toggle('is-active', currentMode === 'direct');
        requestPanel.classList.toggle('d-none', currentMode !== 'from_request');
        directPanel.classList.toggle('d-none', currentMode !== 'direct');
        searchWrap.style.display = currentMode === 'direct' ? 'block' : 'none';
        colRequested.style.display = currentMode === 'from_request' ? '' : 'none';
        clearItems();
        if (currentMode === 'from_request' && batchSelect.value) loadRequestItems();
        if (currentMode === 'direct') loadDirectItems();
    }

    function clearItems() {
        currentRows = [];
        document.getElementById('itemsBody').innerHTML = '';
        document.getElementById('itemsTableWrap').classList.add('d-none');
        document.getElementById('itemsEmpty').classList.remove('d-none');
        document.getElementById('itemsMeta').textContent = '';
        submitBtn.disabled = true;
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function loadRequestItems() {
        const key = batchSelect.value;
        const batch = batches.find(b => String(b.batch_key) === String(key));
        if (!batch) { clearItems(); return; }

        document.getElementById('itemsMeta').textContent =
            batch.batch_reference + ' · ' + batch.requester + ' · ' + batch.department;

        currentRows = (batch.items || []).map(item => ({
            kind: 'request',
            id: item.id,
            product_id: item.product_id,
            product_variant_id: item.product_variant_id,
            name: item.name,
            available: item.available,
            requested_qty: item.requested_qty,
            qty: item.suggested_qty,
            unit: item.unit,
            unit_cost: item.unit_cost,
            package_mode: false,
            is_returnable: !!item.is_returnable,
        }));
        renderRows();
    }

    function loadDirectItems() {
        const dept = department();
        const items = catalog[dept] || [];
        document.getElementById('itemsMeta').textContent =
            'Direct to ' + dept + ' · ' + items.length + ' product' + (items.length === 1 ? '' : 's') + ' with stock';

        currentRows = items.map(item => ({
            kind: 'direct',
            product_id: item.product_id,
            product_variant_id: item.product_variant_id,
            name: item.name,
            available: item.available,
            available_packages: item.available_packages,
            requested_qty: null,
            qty: 0,
            unit: item.package_mode ? 'packages' : (item.receiving_unit || 'Pcs'),
            unit_label: item.package_mode ? item.purchasing_unit : item.receiving_unit,
            unit_cost: item.unit_cost,
            package_mode: item.package_mode,
            purchasing_unit: item.purchasing_unit,
            receiving_unit: item.receiving_unit,
            items_per_package: item.items_per_package,
            is_returnable: !!item.is_returnable,
        }));
        renderRows();
    }

    function renderRows() {
        const body = document.getElementById('itemsBody');
        body.innerHTML = '';
        const term = (document.getElementById('productSearch').value || '').toLowerCase().trim();

        if (!currentRows.length) {
            document.getElementById('itemsEmpty').classList.remove('d-none');
            document.getElementById('itemsEmpty').innerHTML =
                '<i class="fa fa-info-circle text-primary mr-1"></i> No items available.';
            document.getElementById('itemsTableWrap').classList.add('d-none');
            submitBtn.disabled = true;
            return;
        }

        let visible = 0;
        currentRows.forEach((row, idx) => {
            if (term && !(row.name || '').toLowerCase().includes(term)) return;
            visible++;
            const tr = document.createElement('tr');
            const unitOptions = row.package_mode
                ? `<select class="form-control form-control-sm item-unit" data-idx="${idx}">
                        <option value="packages">${escapeHtml(row.purchasing_unit || 'Package')}</option>
                        <option value="${escapeHtml(row.receiving_unit || 'Pcs')}">${escapeHtml(row.receiving_unit || 'Pcs')}</option>
                   </select>`
                : `<span class="small text-muted">${escapeHtml(row.unit_label || row.unit || 'Pcs')}</span>
                   <input type="hidden" class="item-unit" data-idx="${idx}" value="${escapeHtml(row.unit || row.receiving_unit || 'Pcs')}">`;

            const availText = row.package_mode
                ? `${Number(row.available_packages||0).toLocaleString()} ${escapeHtml(row.purchasing_unit||'pkg')}
                   <div class="small text-muted">(${Number(row.available||0).toLocaleString()} ${escapeHtml(row.receiving_unit||'pcs')})</div>`
                : `${Number(row.available||0).toLocaleString()} ${escapeHtml(row.receiving_unit || row.unit || '')}`;

            tr.innerHTML = `
                <td class="text-center align-middle">
                    <input type="checkbox" class="item-check" data-idx="${idx}" ${row.kind==='request' ? 'checked' : ''}>
                </td>
                <td>
                    <strong>${escapeHtml(row.name)}</strong>
                    ${row.is_returnable ? '<span class="badge badge-info ml-1"><i class="fa fa-undo"></i> Returnable</span>' : ''}
                </td>
                <td class="text-center align-middle">${availText}</td>
                <td class="text-center align-middle col-req" style="${currentMode==='from_request'?'':'display:none'}">
                    ${row.requested_qty != null ? Number(row.requested_qty).toLocaleString() : '—'}
                </td>
                <td class="align-middle">
                    <input type="number" class="form-control item-qty" data-idx="${idx}"
                        min="0" step="${(row.package_mode || /kg|litre|liter|gram|ml/i.test(row.unit_label || row.unit || '')) ? '0.01' : '1'}"
                        value="${(row.package_mode || /kg|litre|liter|gram|ml/i.test(row.unit_label || row.unit || '')) ? (row.qty || 0) : Math.round(row.qty || 0)}"
                        ${row.kind==='direct' ? 'disabled' : ''}>
                </td>
                <td class="text-center align-middle">${unitOptions}</td>
                <td class="text-center align-middle">
                    <label class="mb-0 small d-inline-flex align-items-center" style="gap:4px;cursor:pointer;">
                        <input type="checkbox" class="item-returnable" data-idx="${idx}" ${row.is_returnable ? 'checked' : ''}>
                        Yes
                    </label>
                </td>
            `;
            body.appendChild(tr);
        });

        document.getElementById('itemsEmpty').classList.toggle('d-none', visible > 0);
        document.getElementById('itemsTableWrap').classList.toggle('d-none', visible === 0);
        document.getElementById('checkAll').checked = currentMode === 'from_request';
        refreshRowStates();
        submitBtn.disabled = visible === 0;
    }

    function refreshRowStates() {
        document.querySelectorAll('#itemsBody tr').forEach(tr => {
            const check = tr.querySelector('.item-check');
            if (!check) return;
            tr.classList.toggle('skip-row', !check.checked);
            const qty = tr.querySelector('.item-qty');
            if (qty) qty.disabled = !check.checked;
            const ret = tr.querySelector('.item-returnable');
            if (ret) ret.disabled = !check.checked;
        });
    }

    document.querySelectorAll('input[name="mode"]').forEach(el => el.addEventListener('change', syncMode));
    document.querySelectorAll('input[name="department"]').forEach(el => {
        el.addEventListener('change', function () {
            document.querySelectorAll('.dept-card').forEach(c => c.classList.remove('is-active'));
            this.closest('.dept-card')?.classList.add('is-active');
            syncReceiverSelect();
            if (mode() === 'direct') loadDirectItems();
        });
    });
    batchSelect.addEventListener('change', loadRequestItems);
    document.getElementById('productSearch').addEventListener('input', renderRows);
    document.getElementById('checkAll').addEventListener('change', function () {
        document.querySelectorAll('.item-check').forEach(cb => { cb.checked = this.checked; });
        refreshRowStates();
    });
    document.getElementById('itemsBody').addEventListener('change', function (e) {
        if (e.target.classList.contains('item-check')) refreshRowStates();
        if (e.target.classList.contains('item-returnable')) {
            const idx = parseInt(e.target.dataset.idx, 10);
            if (currentRows[idx]) {
                currentRows[idx].is_returnable = e.target.checked;
                const nameCell = e.target.closest('tr')?.querySelector('td:nth-child(2)');
                if (nameCell) {
                    const strong = nameCell.querySelector('strong');
                    const existing = nameCell.querySelector('.badge-info');
                    if (existing) existing.remove();
                    if (e.target.checked && strong) {
                        strong.insertAdjacentHTML('afterend', ' <span class="badge badge-info ml-1"><i class="fa fa-undo"></i> Returnable</span>');
                    }
                }
            }
        }
    });

    syncReceiverSelect();

    document.getElementById('distributeForm').addEventListener('submit', function (e) {
        e.preventDefault();
        document.getElementById('formAlert').innerHTML = '';

        const payload = { mode: mode(), items: [] };

        if (payload.mode === 'from_request') {
            if (!batchSelect.value) {
                alert('Select an approved request first.');
                return;
            }
            payload.batch_key = batchSelect.value;
            document.querySelectorAll('#itemsBody tr').forEach(tr => {
                const check = tr.querySelector('.item-check');
                const qtyInput = tr.querySelector('.item-qty');
                if (!check || !check.checked) return;
                const idx = parseInt(check.dataset.idx, 10);
                const row = currentRows[idx];
                const qty = parseFloat(qtyInput.value) || 0;
                if (!row || qty <= 0) return;
                const ret = tr.querySelector('.item-returnable');
                payload.items.push({
                    id: row.id,
                    quantity_issued: qty,
                    unit_cost: row.unit_cost || 0,
                    is_returnable: !!(ret && ret.checked),
                });
            });
        } else {
            payload.department = department();
            payload.received_by = document.getElementById('received_by').value || null;
            payload.transfer_date = document.getElementById('transfer_date').value;
            payload.notes = document.getElementById('direct_notes').value;
            if (!payload.received_by) {
                alert('Select a receiver for this department. Type at least 2 letters to search.');
                document.getElementById('receiver_search').focus();
                return;
            }
            document.querySelectorAll('#itemsBody tr').forEach(tr => {
                const check = tr.querySelector('.item-check');
                const qtyInput = tr.querySelector('.item-qty');
                const unitInput = tr.querySelector('.item-unit');
                const ret = tr.querySelector('.item-returnable');
                if (!check || !check.checked) return;
                const idx = parseInt(check.dataset.idx, 10);
                const row = currentRows[idx];
                const qty = parseFloat(qtyInput.value) || 0;
                if (!row || qty <= 0) return;
                payload.items.push({
                    product_id: row.product_id,
                    product_variant_id: row.product_variant_id,
                    quantity: qty,
                    quantity_unit: unitInput?.value || row.unit || row.receiving_unit || 'Pcs',
                    unit_cost: row.unit_cost || 0,
                    is_returnable: !!(ret && ret.checked),
                });
            });
        }

        if (!payload.items.length) {
            alert('Enter an issue quantity for at least one selected item.');
            return;
        }

        const original = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Issuing...';

        fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify(payload),
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                swal({
                    title: 'Stock distributed!',
                    text: data.message || 'Issued successfully.',
                    type: 'success',
                    confirmButtonColor: '#28a745'
                }, function () {
                    window.location.href = data.redirect || @json(route('storekeeper.transfers.index'));
                });
            } else {
                let msg = data.message || 'Could not distribute.';
                if (data.errors) msg = Object.values(data.errors).flat().join('<br>');
                document.getElementById('formAlert').innerHTML = '<div class="alert alert-danger">' + msg + '</div>';
                submitBtn.disabled = false;
                submitBtn.innerHTML = original;
            }
        })
        .catch(() => {
            document.getElementById('formAlert').innerHTML =
                '<div class="alert alert-danger">Something went wrong. Please try again.</div>';
            submitBtn.disabled = false;
            submitBtn.innerHTML = original;
        });
    });

    syncMode();
})();
</script>
@endsection
