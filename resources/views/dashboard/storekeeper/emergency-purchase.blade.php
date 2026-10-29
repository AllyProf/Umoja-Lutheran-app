@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-ambulance text-danger"></i> Emergency purchase</h1>
        <p>Urgent buy when stock is finished — sent straight to the manager</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('storekeeper.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Emergency</li>
    </ul>
</div>

<div class="alert alert-warning border-0 shadow-sm">
    <strong><i class="fa fa-warning"></i> Use only when needed.</strong>
    Emergency orders skip the normal purchase-list timing and SMS the manager for fast approval.
</div>

<form id="emergencyForm">
    @csrf

    <div class="tile shadow-sm mb-4">
        <h3 class="tile-title"><span class="badge badge-danger mr-2">1</span> Why is this an emergency?</h3>
        <div class="form-group mb-0">
            <textarea name="reason" id="reason" class="form-control form-control-lg" rows="2" required
                placeholder="e.g. Cooking oil finished in kitchen, guests arriving tonight"></textarea>
            <small class="text-muted">One reason covers all items below.</small>
        </div>
    </div>

    <div class="tile shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <h3 class="tile-title mb-0"><span class="badge badge-danger mr-2">2</span> What do you need to buy?</h3>
            <button type="button" class="btn btn-outline-danger btn-sm" id="addItemBtn">
                <i class="fa fa-plus"></i> Add item
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered mb-0" id="itemsTable">
                <thead class="thead-light">
                    <tr>
                        <th>Item name</th>
                        <th style="width:160px">Department</th>
                        <th style="width:110px">Qty</th>
                        <th style="width:120px">Unit</th>
                        <th style="width:140px">Est. cost (TZS)</th>
                        <th style="width:50px"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody"></tbody>
            </table>
        </div>
        <p class="small text-muted mt-2 mb-0">
            Department helps the manager know if it is for Restaurant, Housekeeping, or Shared use.
        </p>
    </div>

    <div id="formAlert"></div>

    <div class="d-flex flex-wrap" style="gap:10px;">
        <button type="submit" class="btn btn-danger btn-lg" id="submitBtn">
            <i class="fa fa-paper-plane"></i> Send emergency request
        </button>
        <a href="{{ route('storekeeper.purchase-requests.my') }}" class="btn btn-outline-secondary btn-lg">
            My requests
        </a>
        <a href="{{ route('storekeeper.dashboard') }}" class="btn btn-outline-secondary btn-lg">
            Cancel
        </a>
    </div>
</form>

@if($recentEmergencies->count())
    <div class="tile shadow-sm mt-4">
        <h3 class="tile-title">Your recent emergency requests</h3>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentEmergencies as $req)
                        <tr>
                            <td>
                                <strong>{{ $req->item_name }}</strong>
                                <div class="small text-muted">{{ $req->reason }}</div>
                            </td>
                            <td>{{ number_format($req->quantity, 2) }} {{ $req->unit }}</td>
                            <td>
                                @php
                                    $badge = match($req->status) {
                                        'pending' => 'warning',
                                        'approved', 'on_list', 'purchased' => 'success',
                                        'rejected' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge badge-{{ $badge }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                            </td>
                            <td class="small text-muted">{{ optional($req->created_at)->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<style>
    #itemsTable input, #itemsTable select { min-width: 0; }
    #itemsBody tr td { vertical-align: middle; }
</style>
<script>
(function () {
    const body = document.getElementById('itemsBody');
    const storeUrl = @json(route('storekeeper.purchase-requests.emergency.store'));
    const csrf = document.querySelector('#emergencyForm input[name="_token"]').value;
    let index = 0;

    function unitOptions() {
        return `
            <option value="pcs">Pcs</option>
            <option value="kg">Kg</option>
            <option value="liters">Litres</option>
            <option value="bottles">Bottles</option>
            <option value="cartons">Cartons</option>
            <option value="packs">Packs</option>
            <option value="boxes">Boxes</option>
            <option value="bags">Bags</option>
        `;
    }

    function addRow(prefill = {}) {
        const tr = document.createElement('tr');
        tr.dataset.idx = index;
        tr.innerHTML = `
            <td>
                <input type="text" class="form-control item-name" required
                    placeholder="e.g. Cooking oil, Soap, Sugar"
                    value="${prefill.name || ''}">
            </td>
            <td>
                <select class="form-control item-dept">
                    <option value="restaurant" ${!prefill.dept || prefill.dept === 'restaurant' ? 'selected' : ''}>Restaurant</option>
                    <option value="housekeeping" ${prefill.dept === 'housekeeping' ? 'selected' : ''}>Housekeeping</option>
                    <option value="shared" ${prefill.dept === 'shared' ? 'selected' : ''}>Shared</option>
                </select>
            </td>
            <td>
                <input type="number" class="form-control item-qty" min="0.01" step="0.01" required
                    value="${prefill.qty || ''}" placeholder="0">
            </td>
            <td>
                <select class="form-control item-unit">${unitOptions()}</select>
            </td>
            <td>
                <input type="number" class="form-control item-cost" min="0" step="1"
                    placeholder="Optional" value="${prefill.cost || ''}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        `;
        if (prefill.unit) {
            tr.querySelector('.item-unit').value = prefill.unit;
        }
        body.appendChild(tr);
        index++;
        refreshRemoveButtons();
    }

    function refreshRemoveButtons() {
        const rows = body.querySelectorAll('tr');
        rows.forEach(tr => {
            const btn = tr.querySelector('.remove-row');
            btn.style.visibility = rows.length > 1 ? 'visible' : 'hidden';
        });
    }

    document.getElementById('addItemBtn').addEventListener('click', () => addRow());
    body.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-row');
        if (!btn) return;
        btn.closest('tr').remove();
        refreshRemoveButtons();
    });

    document.getElementById('emergencyForm').addEventListener('submit', function (e) {
        e.preventDefault();
        document.getElementById('formAlert').innerHTML = '';

        const reason = (document.getElementById('reason').value || '').trim();
        if (!reason) {
            alert('Please explain why this is an emergency.');
            document.getElementById('reason').focus();
            return;
        }

        const items = [];
        body.querySelectorAll('tr').forEach(tr => {
            const name = (tr.querySelector('.item-name').value || '').trim();
            const qty = parseFloat(tr.querySelector('.item-qty').value) || 0;
            if (!name || qty <= 0) return;
            items.push({
                item_name: name,
                department: tr.querySelector('.item-dept').value,
                quantity: qty,
                unit: tr.querySelector('.item-unit').value,
                estimated_cost: tr.querySelector('.item-cost').value || null,
            });
        });

        if (!items.length) {
            alert('Add at least one item with name and quantity.');
            return;
        }

        const submitBtn = document.getElementById('submitBtn');
        const original = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';

        fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ reason, items }),
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                swal({
                    title: 'Emergency request sent!',
                    text: data.message || 'Managers have been notified.',
                    type: 'success',
                    confirmButtonColor: '#dc3545'
                }, function () {
                    window.location.href = data.redirect || @json(route('storekeeper.purchase-requests.my'));
                });
            } else {
                let msg = data.message || 'Could not submit request.';
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

    // Start with one empty row
    addRow();
})();
</script>
@endsection
