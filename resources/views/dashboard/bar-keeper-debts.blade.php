@extends('dashboard.layouts.app')

@section('content')
@php
    $debtors = $debtors ?? collect();
    $summary = $summary ?? ['debtors' => 0, 'orders' => 0, 'total_due' => 0];
    $search = $search ?? '';
@endphp

<style>
  .debt-hero {
    display: flex; flex-wrap: wrap; justify-content: space-between; gap: 14px; align-items: flex-end;
    margin-bottom: 16px; padding: 16px 18px;
    background: linear-gradient(135deg, #1f2937 0%, #7c2d12 100%);
    border-radius: 14px; color: #fff;
  }
  .debt-hero h1 { margin: 0 0 4px; font-size: 1.4rem; font-weight: 700; }
  .debt-hero p { margin: 0; opacity: .8; font-size: .92rem; }
  .debt-kpis { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; margin-bottom: 16px; }
  @media (max-width: 767px) { .debt-kpis { grid-template-columns: 1fr; } }
  .debt-kpi {
    background: #fff; border: 1px solid #e8ecf1; border-radius: 12px; padding: 14px 16px;
    border-left: 4px solid #0f766e;
  }
  .debt-kpi.warn { border-left-color: #b45309; }
  .debt-kpi.danger { border-left-color: #b91c1c; }
  .debt-kpi-label { display:block; font-size:11px; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; font-weight:600; }
  .debt-kpi-value { display:block; font-size:1.4rem; font-weight:750; color:#111827; margin-top:4px; }
  .debt-card {
    background:#fff; border:1px solid #e8ecf1; border-radius:12px; margin-bottom:12px; overflow:hidden;
  }
  .debt-card-head {
    display:flex; flex-wrap:wrap; justify-content:space-between; gap:10px; align-items:center;
    padding:14px 16px; background:#fff7ed; border-bottom:1px solid #fed7aa; cursor:pointer;
  }
  .debt-card-head h3 { margin:0; font-size:15px; font-weight:700; color:#9a3412; }
  .debt-card-meta { font-size:12px; color:#78716c; }
  .debt-card-body { display:none; padding:0; }
  .debt-card.is-open .debt-card-body { display:block; }
  .debt-card.is-open .debt-chevron { transform: rotate(180deg); }
  .debt-chevron { transition: transform .15s ease; color:#78716c; }
  .debt-table th { font-size:10px; text-transform:uppercase; letter-spacing:.04em; color:#6b7280; background:#f8fafc; border-top:0; }
  .debt-table td { vertical-align: middle; font-size:13px; }
  .debt-empty { text-align:center; padding:36px 12px; color:#6b7280; }
  .debt-filter { display:flex; flex-wrap:wrap; gap:8px; align-items:end; margin-bottom:14px; }
  .debt-filter .form-control { border-radius:8px; min-width:220px; }
</style>

@php
  $isCashierCtx = str_starts_with(optional(request()->route())->getName() ?? '', 'cashier.');
  $debtHome = $isCashierCtx ? 'cashier.dashboard' : 'bar-keeper.dashboard';
  $debtRoute = $isCashierCtx ? 'cashier.debts' : 'bar-keeper.debts';
@endphp

<div class="debt-hero">
  <div>
    <h1><i class="fa fa-handshake-o"></i> Debt Management</h1>
    <p>{{ $isManager ? 'All unpaid counter guests across staff' : 'Guests who still owe for served orders' }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb mb-0" style="background:transparent;">
    <li class="breadcrumb-item"><a href="{{ route($debtHome) }}" style="color:rgba(255,255,255,.75);">Dashboard</a></li>
    <li class="breadcrumb-item" style="color:rgba(255,255,255,.9);">Debts</li>
  </ul>
</div>

<div class="debt-kpis">
  <div class="debt-kpi warn">
    <span class="debt-kpi-label">Debtors</span>
    <span class="debt-kpi-value">{{ number_format($summary['debtors']) }}</span>
  </div>
  <div class="debt-kpi">
    <span class="debt-kpi-label">Unpaid Orders</span>
    <span class="debt-kpi-value">{{ number_format($summary['orders']) }}</span>
  </div>
  <div class="debt-kpi danger">
    <span class="debt-kpi-label">Total Due</span>
    <span class="debt-kpi-value">{{ number_format($summary['total_due']) }} <small style="font-size:.7em;">TZS</small></span>
  </div>
</div>

<form method="GET" action="{{ route($debtRoute) }}" class="debt-filter tile p-3 mb-3">
  <div>
    <label class="small text-muted font-weight-bold text-uppercase mb-1 d-block">Search guest / room / item</label>
    <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="e.g. Walk-in, Room 12, Counter name">
  </div>
  <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px;height:34px;"><i class="fa fa-search"></i> Search</button>
  @if($search !== '')
    <a href="{{ route($debtRoute) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;height:34px;">Clear</a>
  @endif
</form>

@forelse($debtors as $debtor)
  <div class="debt-card">
    <div class="debt-card-head js-debt-toggle">
      <div>
        <h3>{{ $debtor['guest'] }}</h3>
        <div class="debt-card-meta">
          {{ $debtor['orders'] }} order(s)
          @if(!empty($debtor['oldest_at']))
            · since {{ \Carbon\Carbon::parse($debtor['oldest_at'])->format('d M Y') }}
          @endif
        </div>
        @if(!empty($debtor['shift_labels']))
          <div class="debt-card-meta mt-1">
            @foreach($debtor['shift_labels'] as $shiftLabel)
              <span class="badge badge-light border mr-1 mb-1" style="font-weight:600;color:#9a3412;">
                <i class="fa fa-clock-o"></i> {{ $shiftLabel }}
              </span>
            @endforeach
          </div>
        @endif
      </div>
      <div class="d-flex align-items-center" style="gap:10px;">
        <span class="badge badge-warning" style="font-size:13px;padding:7px 10px;">
          {{ number_format($debtor['amount']) }} TZS
        </span>
        <button type="button"
                class="btn btn-sm btn-success js-settle-guest"
                style="border-radius:8px;"
                data-order-id="{{ $debtor['items'][0]['id'] ?? '' }}"
                data-guest="{{ $debtor['guest'] }}"
                data-amount="{{ number_format($debtor['amount']) }}"
                data-walk-in="{{ !empty($debtor['items'][0]['is_walk_in']) ? '1' : '0' }}">
          <i class="fa fa-money"></i> Collect All
        </button>
        <i class="fa fa-chevron-down debt-chevron"></i>
      </div>
    </div>
    <div class="debt-card-body">
      <div class="table-responsive">
        <table class="table table-sm mb-0 debt-table">
          <thead>
            <tr>
              <th>When</th>
              <th>Item</th>
              <th>Shift</th>
              <th class="text-center">Qty</th>
              <th class="text-right">Amount</th>
              @if($isManager)<th>Served by</th>@endif
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($debtor['items'] as $item)
              <tr>
                <td class="text-nowrap">{{ $item['time'] }}</td>
                <td>{{ $item['item'] }}</td>
                <td>
                  <strong>{{ $item['shift_staff'] ?? '—' }}</strong>
                  <small class="d-block text-muted">{{ $item['shift_label'] ?? '' }}</small>
                  @if(!empty($item['shift_status']))
                    <span class="badge badge-{{ $item['shift_status'] === 'OPEN' ? 'success' : 'secondary' }}">{{ $item['shift_status'] }}</span>
                  @endif
                </td>
                <td class="text-center">{{ $item['qty'] }}</td>
                <td class="text-right font-weight-bold">{{ number_format($item['amount']) }}</td>
                @if($isManager)<td>{{ $item['served_by'] }}</td>@endif
                <td class="text-right">
                  <button type="button"
                          class="btn btn-xs btn-outline-success js-settle-order"
                          style="border-radius:6px;"
                          data-order-id="{{ $item['id'] }}"
                          data-guest="{{ $debtor['guest'] }}"
                          data-amount="{{ number_format($item['amount']) }}"
                          data-walk-in="{{ !empty($item['is_walk_in']) ? '1' : '0' }}">
                    Collect
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@empty
  <div class="tile">
    <div class="debt-empty">
      <i class="fa fa-check-circle fa-2x text-success mb-2"></i>
      <p class="mb-0">{{ $search !== '' ? 'No debts match your search.' : 'No outstanding debts right now.' }}</p>
    </div>
  </div>
@endempty

<script>
  document.querySelectorAll('.js-debt-toggle').forEach(function (head) {
    head.addEventListener('click', function (e) {
      if (e.target.closest('button')) return;
      head.closest('.debt-card').classList.toggle('is-open');
    });
  });

  function settleDebt(orderId, guest, amountLabel, isWalkIn) {
    if (!orderId) return;

    var methodOptions = {
      cash: 'Cash',
      mpesa: 'M-Pesa',
      card: 'Card',
      bank: 'Bank Transfer'
    };
    if (!isWalkIn) {
      methodOptions.room_charge = 'Room Charge';
    }

    Swal.fire({
      title: 'Collect Payment',
      html: '<div style="text-align:left;margin-bottom:8px;"><strong>' + guest + '</strong><br>Amount: <strong>' + amountLabel + ' TZS</strong></div>',
      input: 'select',
      inputOptions: methodOptions,
      inputValue: 'cash',
      inputPlaceholder: 'Payment method',
      showCancelButton: true,
      confirmButtonText: 'Confirm Paid',
      confirmButtonColor: '#15803d',
      cancelButtonText: 'Cancel'
    }).then(function (result) {
      if (!result.isConfirmed) return;
      var method = result.value;
      var needsRef = method !== 'cash' && method !== 'room_charge';

      var proceed = function (reference) {
        Swal.fire({ title: 'Recording...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });
        fetch('{{ url('/customer/pos/settle-payment') }}/' + orderId, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            payment_method: method,
            payment_reference: reference || ''
          })
        })
        .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
        .then(function (out) {
          if (!out.ok || !out.data.success) throw new Error((out.data && out.data.message) || 'Failed');
          return Swal.fire({ icon: 'success', title: 'Paid', text: out.data.message || 'Payment recorded', timer: 1600, showConfirmButton: false });
        })
        .then(function () { window.location.reload(); })
        .catch(function (err) {
          Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Could not record payment.' });
        });
      };

      if (needsRef) {
        Swal.fire({
          title: 'Reference',
          input: 'text',
          inputLabel: 'Payment reference / receipt no.',
          inputPlaceholder: 'e.g. MPESA code',
          showCancelButton: true,
          confirmButtonText: 'Save',
          confirmButtonColor: '#15803d'
        }).then(function (refResult) {
          if (!refResult.isConfirmed) return;
          if (!refResult.value || !String(refResult.value).trim()) {
            Swal.fire({ icon: 'warning', title: 'Reference required' });
            return;
          }
          proceed(String(refResult.value).trim());
        });
      } else {
        proceed('');
      }
    });
  }

  document.querySelectorAll('.js-settle-guest, .js-settle-order').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      settleDebt(
        btn.getAttribute('data-order-id'),
        btn.getAttribute('data-guest') || 'Guest',
        btn.getAttribute('data-amount') || '0',
        btn.getAttribute('data-walk-in') === '1'
      );
    });
  });
</script>
@endsection
