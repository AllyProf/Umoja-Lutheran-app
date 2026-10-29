@extends('dashboard.layouts.app')

@section('content')
@php
    $isStorekeeper = $isStorekeeper ?? (($role ?? '') === 'storekeeper');
    $createIndexRoute = $isStorekeeper
        ? (Route::has('storekeeper.stock-receipts.index') ? route('storekeeper.stock-receipts.index') : route('storekeeper.dashboard'))
        : (Route::has('admin.dashboard') ? route('admin.dashboard') : url('/manager'));
    $dashboardRoute = $isStorekeeper
        ? route('storekeeper.dashboard')
        : (Route::has('admin.dashboard') ? route('admin.dashboard') : url('/manager'));
@endphp
<div class="app-title">
    <div>
        <h1><i class="fa fa-inbox"></i> Receive Stock</h1>
        <p>Only lists / LPOs already approved by the accountant (funds disbursed) are shown</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ $dashboardRoute }}">Dashboard</a></li>
        @if($isStorekeeper)
            <li class="breadcrumb-item"><a href="{{ route('storekeeper.stock-receipts.index') }}">Stock Receipts</a></li>
        @endif
        <li class="breadcrumb-item active">Receive</li>
    </ul>
</div>

<form id="receiveForm">
    @csrf

    {{-- Step 1: source type --}}
    <div class="tile shadow-sm mb-4">
        <h3 class="tile-title"><span class="badge badge-primary mr-2">1</span> Where did these items come from?</h3>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="sr-source-card" id="cardPurchase">
                    <input type="radio" name="source" value="purchase_list" class="d-none" checked>
                    <div class="sr-source-inner">
                        <i class="fa fa-shopping-basket fa-2x text-primary mb-2"></i>
                        <h4 class="mb-1">Purchase list</h4>
                        <p class="text-muted mb-0 small">A shopping / purchase list you already created</p>
                    </div>
                </label>
            </div>
            <div class="col-md-6 mb-3">
                <label class="sr-source-card" id="cardLpo">
                    <input type="radio" name="source" value="lpo" class="d-none">
                    <div class="sr-source-inner">
                        <i class="fa fa-file-text-o fa-2x text-success mb-2"></i>
                        <h4 class="mb-1">LPO budget</h4>
                        <p class="text-muted mb-0 small">A Local Purchase Order already created</p>
                    </div>
                </label>
            </div>
        </div>
    </div>

    {{-- Step 2: pick document --}}
    <div class="tile shadow-sm mb-4">
        <h3 class="tile-title"><span class="badge badge-primary mr-2">2</span> Select document</h3>

        <div class="form-group" id="purchaseListGroup">
            <label class="font-weight-bold">Purchase list <span class="text-danger">*</span></label>
            <select id="purchase_list_id" class="form-control form-control-lg">
                <option value="">Select purchase list...</option>
                @forelse($shoppingLists as $list)
                    <option value="{{ $list->id }}">
                        {{ $list->name ?: ('List #'.$list->id) }}
                        · {{ optional($list->shopping_date)->format('d M Y') ?? 'no date' }}
                        · {{ ucfirst(str_replace('_', ' ', $list->status)) }}
                        · {{ $list->items_count }} item{{ $list->items_count === 1 ? '' : 's' }}
                        @if($list->lpo_id) · LPO #{{ $list->lpo_id }} @endif
                    </option>
                @empty
                    <option value="" disabled>No approved purchase lists — wait for accountant to approve &amp; disburse funds</option>
                @endforelse
            </select>
            <small class="text-muted">Shown only after accountant approval and funds disbursement (Ready for purchase / Purchased).</small>
        </div>

        <div class="form-group d-none" id="lpoGroup">
            <label class="font-weight-bold">LPO <span class="text-danger">*</span></label>
            <select id="lpo_id" class="form-control form-control-lg">
                <option value="">Select LPO...</option>
                @forelse($lpos as $lpo)
                    <option value="{{ $lpo->id }}">
                        LPO #{{ $lpo->id }}
                        · {{ $lpo->start_date?->format('d M') }} – {{ $lpo->end_date?->format('d M Y') }}
                        · {{ ucfirst(str_replace('_', ' ', $lpo->status)) }}
                        · {{ $lpo->items_count }} item{{ $lpo->items_count === 1 ? '' : 's' }}
                        @if($lpo->total_amount) · {{ number_format($lpo->total_amount) }} TZS @endif
                    </option>
                @empty
                    <option value="" disabled>No approved LPOs — wait for accountant to process the LPO</option>
                @endforelse
            </select>
            <small class="text-muted">Shown only after the accountant has processed the LPO.</small>
        </div>

        <div class="row">
            <div class="col-md-6 form-group mb-0">
                <label class="font-weight-bold">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" id="supplier_id" class="form-control" required>
                    <option value="">Select supplier...</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 form-group mb-0">
                <label class="font-weight-bold">Received date <span class="text-danger">*</span></label>
                <input type="date" name="received_date" id="received_date" class="form-control"
                    value="{{ date('Y-m-d') }}" required>
            </div>
        </div>
    </div>

    {{-- Step 3: items --}}
    <div class="tile shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <h3 class="tile-title mb-0"><span class="badge badge-primary mr-2">3</span> Confirm quantities</h3>
            <div id="sourceMeta" class="text-muted small"></div>
        </div>

        <div id="itemsEmpty" class="alert alert-light border mb-0">
            <i class="fa fa-info-circle text-primary mr-1"></i>
            Select a purchase list or LPO above to load its items.
        </div>

        <div id="itemsLoading" class="text-center py-4 d-none">
            <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2 mb-0 text-muted">Loading items...</p>
        </div>

        <div id="itemsTableWrap" class="d-none">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0" id="itemsTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:40px" class="text-center">
                                <input type="checkbox" id="checkAll" title="Select all" checked>
                            </th>
                            <th>Item</th>
                            <th class="text-center" style="width:110px">Ordered</th>
                            <th class="text-center" style="width:110px">Already</th>
                            <th class="text-center" style="width:140px">Receive now</th>
                            <th class="text-center" style="width:140px">Buying price</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody"></tbody>
                </table>
            </div>
            <p class="small text-muted mt-2 mb-0">
                Untick or set quantity to 0 to skip an item. Only linked products can go into store stock.
            </p>
        </div>
    </div>

    <div id="formAlert"></div>

    <div class="d-flex flex-wrap" style="gap:10px;">
        <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
            <i class="fa fa-check"></i> Save to store stock
        </button>
        <a href="{{ $createIndexRoute }}" class="btn btn-outline-secondary btn-lg">
            Cancel
        </a>
    </div>
</form>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<style>
    .sr-source-card {
        display: block;
        cursor: pointer;
        margin: 0;
    }
    .sr-source-inner {
        border: 2px solid #e3e6f0;
        border-radius: 10px;
        padding: 22px 18px;
        text-align: center;
        background: #fff;
        transition: border-color .15s, box-shadow .15s, background .15s;
        height: 100%;
    }
    .sr-source-card.is-active .sr-source-inner,
    .sr-source-card:has(input:checked) .sr-source-inner {
        border-color: #4e73df;
        background: #f8f9fc;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, .12);
    }
    .sr-source-inner h4 { font-size: 1.15rem; font-weight: 700; }
    #itemsTable input[type="number"] { min-width: 90px; }
    #itemsTable tr.skip-row { opacity: .45; }
    #itemsTable tr.unlink-row { background: #fff8f0; }
</style>
<script>
(function () {
    const sourceItemsUrl = @json($isStorekeeper
        ? route('storekeeper.stock-receipts.source-items')
        : route('admin.stock-receipts.source-items'));
    const receiveUrl = @json($isStorekeeper
        ? route('storekeeper.stock-receipts.receive-from-source')
        : route('admin.stock-receipts.receive-from-source'));
    const indexUrl = @json($createIndexRoute);
    const preselectedSource = @json($preselectedSource ?? null);
    const preselectedId = @json($preselectedId ?? null);
    const csrf = document.querySelector('#receiveForm input[name="_token"]').value;

    let currentItems = [];

    const purchaseCard = document.getElementById('cardPurchase');
    const lpoCard = document.getElementById('cardLpo');
    const purchaseGroup = document.getElementById('purchaseListGroup');
    const lpoGroup = document.getElementById('lpoGroup');
    const purchaseSelect = document.getElementById('purchase_list_id');
    const lpoSelect = document.getElementById('lpo_id');
    const supplierSelect = document.getElementById('supplier_id');
    const submitBtn = document.getElementById('submitBtn');

    function currentSource() {
        return document.querySelector('input[name="source"]:checked')?.value || 'purchase_list';
    }

    function syncSourceUi() {
        const source = currentSource();
        purchaseCard.classList.toggle('is-active', source === 'purchase_list');
        lpoCard.classList.toggle('is-active', source === 'lpo');
        purchaseGroup.classList.toggle('d-none', source !== 'purchase_list');
        lpoGroup.classList.toggle('d-none', source !== 'lpo');
        clearItems();
        if (source === 'purchase_list' && purchaseSelect.value) loadItems();
        if (source === 'lpo' && lpoSelect.value) loadItems();
    }

    function applyPreselection() {
        if (!preselectedSource) return;
        const radio = document.querySelector('input[name="source"][value="' + preselectedSource + '"]');
        if (radio) {
            radio.checked = true;
        }
        if (preselectedSource === 'lpo' && preselectedId) {
            lpoSelect.value = String(preselectedId);
            // If this LPO is not in the dropdown (status filter), add a temp option
            if (lpoSelect.value !== String(preselectedId)) {
                const opt = document.createElement('option');
                opt.value = String(preselectedId);
                opt.textContent = 'LPO #' + preselectedId;
                opt.selected = true;
                lpoSelect.appendChild(opt);
            }
        }
        if (preselectedSource === 'purchase_list' && preselectedId) {
            purchaseSelect.value = String(preselectedId);
        }
    }

    function clearItems() {
        currentItems = [];
        document.getElementById('itemsBody').innerHTML = '';
        document.getElementById('itemsTableWrap').classList.add('d-none');
        document.getElementById('itemsLoading').classList.add('d-none');
        document.getElementById('itemsEmpty').classList.remove('d-none');
        document.getElementById('sourceMeta').textContent = '';
        submitBtn.disabled = true;
    }

    function formatNum(n) {
        const v = parseFloat(n) || 0;
        return v.toLocaleString(undefined, { maximumFractionDigits: 2 });
    }

    function loadItems() {
        const source = currentSource();
        const id = source === 'purchase_list' ? purchaseSelect.value : lpoSelect.value;
        if (!id) {
            clearItems();
            return;
        }

        document.getElementById('itemsEmpty').classList.add('d-none');
        document.getElementById('itemsTableWrap').classList.add('d-none');
        document.getElementById('itemsLoading').classList.remove('d-none');
        submitBtn.disabled = true;

        fetch(sourceItemsUrl + '?source=' + encodeURIComponent(source) + '&id=' + encodeURIComponent(id), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('itemsLoading').classList.add('d-none');
            if (!data.success) {
                document.getElementById('itemsEmpty').classList.remove('d-none');
                document.getElementById('itemsEmpty').innerHTML =
                    '<span class="text-danger">' + (data.message || 'Could not load items.') + '</span>';
                return;
            }

            currentItems = data.items || [];
            document.getElementById('sourceMeta').textContent = (data.label || '') + (data.meta ? ' · ' + data.meta : '');

            // Prefill supplier from first linked item if empty
            if (!supplierSelect.value) {
                const withSupplier = currentItems.find(i => i.supplier_id);
                if (withSupplier) supplierSelect.value = withSupplier.supplier_id;
            }

            renderItems();
        })
        .catch(() => {
            document.getElementById('itemsLoading').classList.add('d-none');
            document.getElementById('itemsEmpty').classList.remove('d-none');
            document.getElementById('itemsEmpty').innerHTML =
                '<span class="text-danger">Failed to load items. Please try again.</span>';
        });
    }

    function renderItems() {
        const body = document.getElementById('itemsBody');
        body.innerHTML = '';

        if (!currentItems.length) {
            document.getElementById('itemsEmpty').classList.remove('d-none');
            document.getElementById('itemsEmpty').innerHTML =
                '<i class="fa fa-info-circle text-primary mr-1"></i> This document has no items.';
            document.getElementById('itemsTableWrap').classList.add('d-none');
            submitBtn.disabled = true;
            return;
        }

        currentItems.forEach((item, idx) => {
            const tr = document.createElement('tr');
            if (!item.can_receive) tr.classList.add('unlink-row');

            tr.innerHTML = `
                <td class="text-center align-middle">
                    <input type="checkbox" class="item-check" data-idx="${idx}"
                        ${item.can_receive ? 'checked' : 'disabled'}>
                </td>
                <td>
                    <strong>${escapeHtml(item.name)}</strong>
                    ${item.can_receive
                        ? `<div class="small text-muted">${escapeHtml(item.unit || '')}</div>`
                        : `<div class="small text-danger"><i class="fa fa-warning"></i> Not linked to a product — skip or link in LPO / list edit</div>`}
                </td>
                <td class="text-center align-middle">${formatNum(item.ordered_qty)} <small class="text-muted">${escapeHtml(item.unit || '')}</small></td>
                <td class="text-center align-middle">${formatNum(item.already_qty)}</td>
                <td class="align-middle">
                    <input type="number" class="form-control item-qty" data-idx="${idx}"
                        min="0" step="0.01" value="${item.can_receive ? (item.suggested_qty || 0) : 0}"
                        ${item.can_receive ? '' : 'disabled'}>
                </td>
                <td class="align-middle">
                    <input type="number" class="form-control item-buy" data-idx="${idx}"
                        min="0" step="0.01" value="${item.buying_price || 0}"
                        ${item.can_receive ? '' : 'disabled'}>
                </td>
            `;
            body.appendChild(tr);
        });

        document.getElementById('itemsEmpty').classList.add('d-none');
        document.getElementById('itemsTableWrap').classList.remove('d-none');
        document.getElementById('checkAll').checked = true;
        refreshRowStates();
        submitBtn.disabled = false;
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function refreshRowStates() {
        document.querySelectorAll('#itemsBody tr').forEach(tr => {
            const check = tr.querySelector('.item-check');
            if (!check || check.disabled) return;
            tr.classList.toggle('skip-row', !check.checked);
            const qty = tr.querySelector('.item-qty');
            const buy = tr.querySelector('.item-buy');
            if (qty) qty.disabled = !check.checked;
            if (buy) buy.disabled = !check.checked;
        });
    }

    document.querySelectorAll('input[name="source"]').forEach(el => {
        el.addEventListener('change', syncSourceUi);
    });
    purchaseSelect.addEventListener('change', loadItems);
    lpoSelect.addEventListener('change', loadItems);

    document.getElementById('checkAll').addEventListener('change', function () {
        document.querySelectorAll('.item-check:not(:disabled)').forEach(cb => {
            cb.checked = this.checked;
        });
        refreshRowStates();
    });

    document.getElementById('itemsBody').addEventListener('change', function (e) {
        if (e.target.classList.contains('item-check')) refreshRowStates();
    });

    document.getElementById('receiveForm').addEventListener('submit', function (e) {
        e.preventDefault();
        document.getElementById('formAlert').innerHTML = '';

        const source = currentSource();
        const sourceId = source === 'purchase_list' ? purchaseSelect.value : lpoSelect.value;
        if (!sourceId) {
            alert('Select a purchase list or LPO first.');
            return;
        }
        if (!supplierSelect.value) {
            alert('Select a supplier.');
            supplierSelect.focus();
            return;
        }

        const payloadItems = [];
        document.querySelectorAll('#itemsBody tr').forEach(tr => {
            const check = tr.querySelector('.item-check');
            const qtyInput = tr.querySelector('.item-qty');
            const buyInput = tr.querySelector('.item-buy');
            if (!check || check.disabled || !check.checked) return;
            const idx = parseInt(check.dataset.idx, 10);
            const item = currentItems[idx];
            if (!item || !item.can_receive) return;
            const qty = parseFloat(qtyInput.value) || 0;
            if (qty <= 0) return;
            payloadItems.push({
                id: item.id,
                qty: qty,
                buying_price: parseFloat(buyInput.value) || 0,
                selling_price: item.selling_price || 0,
            });
        });

        if (!payloadItems.length) {
            alert('Enter a receive quantity for at least one item.');
            return;
        }

        const original = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

        fetch(receiveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({
                source: source,
                source_id: parseInt(sourceId, 10),
                supplier_id: parseInt(supplierSelect.value, 10),
                received_date: document.getElementById('received_date').value,
                items: payloadItems,
            }),
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                swal({
                    title: 'Stock received!',
                    text: data.message || 'Saved to store stock.',
                    type: 'success',
                    confirmButtonColor: '#28a745'
                }, function () {
                    window.location.href = indexUrl;
                });
            } else {
                let msg = data.message || 'Could not save.';
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

    applyPreselection();
    syncSourceUi();
})();
</script>
@endsection
