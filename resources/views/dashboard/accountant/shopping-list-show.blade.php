@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-file-text"></i> Shopping List Details</h1>
            <p>Review and verify purchase details</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.shopping-lists') }}">Shopping Lists</a></li>
            <li class="breadcrumb-item">Details</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="tile">
                <h3 class="tile-title">Items in List: {{ $shoppingList->name }}</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Est. Price/Unit</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shoppingList->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                                    <td>
                                        @php
                                            $unitPrice = $item->quantity > 0 ? $item->estimated_price / $item->quantity : 0;
                                        @endphp
                                        {{ number_format($unitPrice, 0) }}
                                    </td>
                                    <td>{{ number_format($item->estimated_price, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <th colspan="3" class="text-right">Total Estimated Cost:</th>
                                <th>TZS {{ number_format($shoppingList->total_estimated_cost) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Direct Purchase Option for Accountant --}}
            @if(in_array($shoppingList->status, ['pending', 'accountant_checked', 'approved', 'ready_for_purchase']))
                @if(is_null($shoppingList->purchaser_id))
                    <div class="tile">
                        <h3 class="tile-title text-primary"><i class="fa fa-shopping-bag"></i> Direct Purchase</h3>
                        <p class="small mb-2">Will you be purchasing these items directly?</p>
                        <form action="{{ route('accountant.shopping-list.claim', $shoppingList->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary btn-block">
                                <i class="fa fa-hand-paper-o mr-2"></i> I will purchase this
                            </button>
                        </form>
                    </div>
                @elseif($shoppingList->purchaser_id === Auth::guard('staff')->id())
                    <div class="tile border border-success">
                        <h3 class="tile-title text-success"><i class="fa fa-check-circle"></i> Direct Purchase Claimed</h3>
                        <p class="small mb-2">You have claimed this list. You can now record the purchases directly.</p>
                        <a href="{{ route('accountant.shopping-list.record-purchase', $shoppingList->id) }}"
                            class="btn btn-success btn-block">
                            <i class="fa fa-shopping-cart mr-2"></i> Record Purchase Details
                        </a>
                    </div>
                @endif
            @endif

            @if($shoppingList->status === 'pending')
                <div class="tile">
                    <h3 class="tile-title">Step 1: Financial Review</h3>
                    <form action="{{ route('accountant.shopping-list.approve', $shoppingList->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Budget Amount (TZS)</label>
                            <input type="number" name="budget_amount" class="form-control"
                                value="{{ $shoppingList->total_estimated_cost }}" required>
                            <small class="text-muted">Set the allowed budget for this shopping list.</small>
                        </div>
                        <div class="form-group">
                            <label>Approval Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ $shoppingList->notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block"><i class="fa fa-paper-plane mr-2"></i> Check &
                            Send to Manager</button>
                    </form>

                    <hr>

                    <button class="btn btn-outline-danger btn-block" data-toggle="collapse" data-target="#rejectBlock">
                        <i class="fa fa-times mr-2"></i> Reject List
                    </button>

                    <div id="rejectBlock" class="collapse mt-3">
                        <form action="{{ route('accountant.shopping-list.reject', $shoppingList->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Reason for rejection</label>
                                <textarea name="rejection_reason" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger btn-sm">Confirm Rejection</button>
                        </form>
                    </div>
                </div>
            @elseif($shoppingList->status === 'approved')
                @if($shoppingList->purchaser_id === Auth::guard('staff')->id())
                    <div class="tile">
                        <h3 class="tile-title text-success">Step 2: Proceed to Purchase</h3>
                        <p class="text-info">The manager has approved this list (Budget: TZS
                            {{ number_format($shoppingList->budget_amount) }}). Since you claimed this list, you do not need to
                            disburse funds. Click below to proceed.
                        </p>
                        <form action="{{ route('accountant.shopping-list.disburse', $shoppingList->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="direct_purchase" value="1">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-shopping-cart mr-2"></i> Proceed to Record Purchase
                            </button>
                        </form>
                    </div>
                @else
                    <div class="tile">
                        <h3 class="tile-title">Step 2: Fund Disbursement</h3>
                        <p class="text-info">The manager has approved this list. Please disburse the funds (TZS
                            {{ number_format($shoppingList->budget_amount) }}) to the Storekeeper so they can proceed with the
                            purchase.
                        </p>
                        <form action="{{ route('accountant.shopping-list.disburse', $shoppingList->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-money mr-2"></i> Disburse Funds to Storekeeper
                            </button>
                        </form>
                    </div>
                @endif
            @elseif($shoppingList->status === 'purchased')
                <div class="tile">
                    <h3 class="tile-title">Final Step: Reconciliation</h3>
                    <p class="text-info">The storekeeper has recorded the purchase. Please verify the actual cost for
                        reconciliation.</p>
                    <form action="{{ route('accountant.payment.verify', $shoppingList->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Actual Total Cost (TZS)</label>
                            <input type="number" name="actual_cost" class="form-control"
                                value="{{ $shoppingList->total_actual_cost }}" required>
                        </div>
                        <div class="form-group">
                            <label>Reconciliation Notes</label>
                            <textarea name="payment_notes" class="form-control" rows="3"
                                placeholder="Receipt details, transaction IDs, etc."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-check-circle mr-2"></i> Verify & Finalize
                        </button>
                    </form>
                </div>
            @elseif($shoppingList->status === 'ready_for_purchase')
                <div class="tile">
                    <h3 class="tile-title">Status: <span class="badge badge-warning">READY FOR PURCHASE</span></h3>
                    @if($shoppingList->purchaser_id === Auth::guard('staff')->id())
                        <p>You have claimed this list. Please record the purchase details using the button above.</p>
                    @else
                        <p>Funds have been disbursed. Waiting for the Storekeeper to record the purchase.</p>
                    @endif
                    <p><strong>Approved Budget:</strong> TZS {{ number_format($shoppingList->budget_amount) }}</p>
                    <p><strong>Notes:</strong> {{ $shoppingList->notes }}</p>
                </div>
            @else
                <div class="tile">
                    <h3 class="tile-title">Status: <span
                            class="badge badge-info">{{ strtoupper(str_replace('_', ' ', $shoppingList->status)) }}</span>
                    </h3>
                    <p><strong>Approved Budget:</strong> TZS {{ number_format($shoppingList->budget_amount) }}</p>
                    @if($shoppingList->status === 'completed')
                        <p><strong>Actual Cost:</strong> TZS {{ number_format($shoppingList->total_actual_cost) }}</p>
                    @endif
                    <p><strong>Notes:</strong> {{ $shoppingList->notes }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection