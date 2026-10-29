@extends('dashboard.layouts.app')

@section('content')
@php
    $firstName = explode(' ', trim($user->name ?? 'Storekeeper'))[0];
@endphp

<div class="app-title sk-dash-title">
    <div>
        <h1><i class="fa fa-archive"></i> Store overview</h1>
        <p>Hello {{ $firstName }} — manage stock in, stock out, and department issues from here</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item active">Dashboard</li>
    </ul>
</div>

{{-- Snapshot --}}
<div class="row sk-stat-row">
    <div class="col-6 col-md-3 mb-3">
        <div class="sk-stat">
            <div class="sk-stat-label">Products</div>
            <div class="sk-stat-value">{{ number_format($stats['total_items']) }}</div>
            <a href="{{ route('storekeeper.products.index') }}" class="sk-stat-link">View products</a>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="sk-stat {{ $stats['low_stock_alerts'] > 0 ? 'is-warn' : '' }}">
            <div class="sk-stat-label">Low stock</div>
            <div class="sk-stat-value">{{ number_format($stats['low_stock_alerts']) }}</div>
            <a href="{{ route('storekeeper.inventory') }}" class="sk-stat-link">Check inventory</a>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="sk-stat {{ $stats['approved_requests'] > 0 ? 'is-action' : '' }}">
            <div class="sk-stat-label">Ready to issue</div>
            <div class="sk-stat-value">{{ number_format($stats['approved_requests']) }}</div>
            <a href="{{ route('storekeeper.transfers.create') }}" class="sk-stat-link">Distribute now</a>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="sk-stat {{ $stats['ready_to_receive'] > 0 ? 'is-action' : '' }}">
            <div class="sk-stat-label">Ready to receive</div>
            <div class="sk-stat-value">{{ number_format($stats['ready_to_receive']) }}</div>
            <a href="{{ route('storekeeper.stock-receipts.create') }}" class="sk-stat-link">Receive stock</a>
        </div>
    </div>
</div>

{{-- Main workflow --}}
<div class="tile shadow-sm mb-4">
    <h3 class="tile-title mb-3">What do you want to do?</h3>
    <div class="row">
        <div class="col-md-4 mb-3">
            <a href="{{ route('storekeeper.stock-receipts.create') }}" class="sk-action sk-action-receive">
                <span class="sk-action-step">1</span>
                <div>
                    <strong>Receive stock</strong>
                    <span>From approved purchase list or LPO</span>
                </div>
                <i class="fa fa-inbox"></i>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('storekeeper.transfers.create') }}" class="sk-action sk-action-distribute">
                <span class="sk-action-step">2</span>
                <div>
                    <strong>Distribute stock</strong>
                    <span>Issue to Restaurant or Housekeeping</span>
                </div>
                <i class="fa fa-exchange"></i>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('storekeeper.products.create') }}" class="sk-action sk-action-product">
                <span class="sk-action-step">+</span>
                <div>
                    <strong>Register product</strong>
                    <span>Restaurant · Housekeeping · Shared</span>
                </div>
                <i class="fa fa-plus-circle"></i>
            </a>
        </div>
    </div>

    <div class="sk-secondary-actions">
        <a href="{{ route('storekeeper.shopping-list.create') }}"><i class="fa fa-shopping-basket"></i> New purchase list</a>
        <a href="{{ route('storekeeper.inventory') }}"><i class="fa fa-cubes"></i> Main store stock</a>
        <a href="{{ route('stock-requests.index') }}"><i class="fa fa-clipboard"></i> Department requests</a>
        <a href="{{ route('lpo.index') }}"><i class="fa fa-file-text-o"></i> LPO budgets</a>
        <a href="{{ route('storekeeper.purchase-requests.emergency') }}" class="text-danger"><i class="fa fa-ambulance"></i> Emergency purchase</a>
    </div>
</div>

<div class="row">
    {{-- Waiting to issue --}}
    <div class="col-lg-6 mb-4">
        <div class="tile shadow-sm h-100">
            <div class="tile-title-w-btn">
                <h3 class="title"><i class="fa fa-clock-o mr-1"></i> Waiting to issue</h3>
                <a href="{{ route('storekeeper.transfers.create') }}" class="btn btn-sm btn-success">Distribute</a>
            </div>
            <p class="text-muted small mb-3">Manager-approved department requests ready for the storekeeper.</p>

            @forelse($waitingRequests as $req)
                <div class="sk-list-row">
                    <div>
                        <strong>{{ $req->productVariant->product->name ?? 'Item' }}</strong>
                        <div class="small text-muted">
                            {{ $req->requester->name ?? 'Department' }}
                            · {{ number_format($req->quantity, 2) }} {{ $req->unit }}
                            · {{ optional($req->updated_at)->diffForHumans() }}
                        </div>
                    </div>
                    <span class="badge badge-success">Approved</span>
                </div>
            @empty
                <div class="sk-empty">
                    <i class="fa fa-check-circle text-success"></i>
                    <p>No approved requests waiting.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Low stock --}}
    <div class="col-lg-6 mb-4">
        <div class="tile shadow-sm h-100">
            <div class="tile-title-w-btn">
                <h3 class="title"><i class="fa fa-exclamation-triangle mr-1"></i> Low stock alerts</h3>
                <a href="{{ route('storekeeper.inventory') }}" class="btn btn-sm btn-outline-secondary">Inventory</a>
            </div>
            <p class="text-muted small mb-3">Items at or below their minimum stock level.</p>

            @forelse($lowStockItems as $item)
                <div class="sk-list-row">
                    <div>
                        <strong>{{ $item['name'] }}</strong>
                        <div class="small text-muted">
                            Min {{ number_format($item['minimum'], 2) }} {{ $item['unit'] }}
                        </div>
                    </div>
                    <span class="badge badge-warning">
                        {{ number_format($item['stock'], 2) }} {{ $item['unit'] }}
                    </span>
                </div>
            @empty
                <div class="sk-empty">
                    <i class="fa fa-thumbs-up text-primary"></i>
                    <p>No low-stock alerts right now.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="row">
    {{-- Recent receipts --}}
    <div class="col-lg-4 mb-4">
        <div class="tile shadow-sm h-100">
            <div class="tile-title-w-btn">
                <h3 class="title">Recent receipts</h3>
                <a href="{{ route('storekeeper.stock-receipts.index') }}" class="btn btn-sm btn-link">All</a>
            </div>
            @forelse($recentReceipts as $receipt)
                <div class="sk-list-row compact">
                    <div>
                        <strong>{{ $receipt->product->name ?? 'Item' }}</strong>
                        <div class="small text-muted">
                            {{ number_format($receipt->quantity_received_packages, 2) }}
                            · {{ optional($receipt->received_date)->format('d M') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="sk-empty small-empty"><p>No receipts yet.</p></div>
            @endforelse
            <div class="small text-muted mt-2">Today: {{ $stats['today_receipts'] }}</div>
        </div>
    </div>

    {{-- Recent transfers --}}
    <div class="col-lg-4 mb-4">
        <div class="tile shadow-sm h-100">
            <div class="tile-title-w-btn">
                <h3 class="title">Recent transfers</h3>
                <a href="{{ route('storekeeper.transfers.index') }}" class="btn btn-sm btn-link">All</a>
            </div>
            @forelse($recentTransfers as $transfer)
                <div class="sk-list-row compact">
                    <div>
                        <strong>{{ $transfer->product->name ?? 'Item' }}</strong>
                        <div class="small text-muted">
                            {{ $transfer->formatted_quantity }} {{ $transfer->quantity_unit_name }}
                            → {{ $transfer->receivedBy->name ?? '—' }}
                        </div>
                    </div>
                    @if($transfer->status === 'completed')
                        <span class="badge badge-success">Done</span>
                    @else
                        <span class="badge badge-warning">Pending</span>
                    @endif
                </div>
            @empty
                <div class="sk-empty small-empty"><p>No transfers yet.</p></div>
            @endforelse
            <div class="small text-muted mt-2">Today: {{ $stats['today_transfers'] }}</div>
        </div>
    </div>

    {{-- Purchase lists --}}
    <div class="col-lg-4 mb-4">
        <div class="tile shadow-sm h-100">
            <div class="tile-title-w-btn">
                <h3 class="title">Purchase lists</h3>
                <a href="{{ route('storekeeper.shopping-list.index') }}" class="btn btn-sm btn-link">All</a>
            </div>
            @forelse($recentShoppingLists as $list)
                <div class="sk-list-row compact">
                    <div>
                        <strong>{{ $list->name ?: ('List #'.$list->id) }}</strong>
                        <div class="small text-muted">{{ optional($list->created_at)->diffForHumans() }}</div>
                    </div>
                    @php
                        $statusClass = match ($list->status) {
                            'pending', 'accountant_checked' => 'badge-warning',
                            'approved', 'ready_for_purchase' => 'badge-info',
                            'purchased', 'completed' => 'badge-success',
                            'rejected' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ str_replace('_', ' ', $list->status) }}</span>
                </div>
            @empty
                <div class="sk-empty small-empty"><p>No purchase lists yet.</p></div>
            @endforelse
            <div class="small text-muted mt-2">
                Pending review: {{ $stats['pending_lists'] }} · Ready to receive: {{ $stats['ready_to_receive'] }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .sk-stat-row { margin-top: 4px; }
    .sk-stat {
        background: #fff;
        border: 1px solid #e8ecf3;
        border-radius: 12px;
        padding: 18px 16px 14px;
        height: 100%;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
    }
    .sk-stat.is-warn { border-color: #f0c36d; background: #fffbeb; }
    .sk-stat.is-action { border-color: #8eb4ff; background: #f5f8ff; }
    .sk-stat-label {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .sk-stat-value {
        font-size: 1.85rem;
        font-weight: 700;
        line-height: 1.1;
        color: #1f2937;
        margin-bottom: 8px;
    }
    .sk-stat-link {
        font-size: .85rem;
        font-weight: 600;
        color: #4e73df;
    }

    .sk-action {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 16px;
        border-radius: 12px;
        border: 1px solid #e3e6f0;
        background: #f8f9fc;
        color: inherit;
        text-decoration: none !important;
        height: 100%;
        transition: border-color .15s, box-shadow .15s, transform .15s;
    }
    .sk-action:hover {
        border-color: #4e73df;
        box-shadow: 0 0 0 3px rgba(78,115,223,.12);
        transform: translateY(-1px);
        color: inherit;
    }
    .sk-action-step {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #4e73df;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }
    .sk-action-distribute .sk-action-step { background: #1cc88a; }
    .sk-action-product .sk-action-step { background: #f6c23e; color: #212529; }
    .sk-action strong { display: block; font-size: 1.05rem; }
    .sk-action span { display: block; font-size: .85rem; color: #6c757d; margin-top: 2px; }
    .sk-action > .fa { margin-left: auto; font-size: 1.4rem; color: #adb5bd; }

    .sk-secondary-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 18px;
        padding-top: 8px;
        border-top: 1px solid #eef1f6;
        margin-top: 4px;
    }
    .sk-secondary-actions a {
        font-size: .9rem;
        font-weight: 600;
        color: #4e73df;
        text-decoration: none;
    }
    .sk-secondary-actions a:hover { text-decoration: underline; }

    .sk-list-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #eef1f6;
    }
    .sk-list-row.compact { padding: 10px 0; }
    .sk-list-row:last-of-type { border-bottom: 0; }
    .sk-empty {
        text-align: center;
        padding: 28px 10px;
        color: #6c757d;
    }
    .sk-empty .fa { font-size: 1.8rem; margin-bottom: 8px; display: block; }
    .sk-empty p { margin: 0; }
    .sk-empty.small-empty { padding: 18px 8px; }
</style>
@endsection
