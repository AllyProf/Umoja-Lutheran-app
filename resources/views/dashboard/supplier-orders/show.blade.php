@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-info-circle"></i> Weekly Order Details: #{{ $supplierOrder->id }}</h1>
            <p>Order batch for <strong>{{ $supplierOrder->supplier->name }}</strong></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('supplier-orders.index') }}">Weekly Orders</a></li>
            <li class="breadcrumb-item">Details</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="tile">
                <h3 class="tile-title">Order Info</h3>
                <div class="tile-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Type:</th>
                            <td>
                                @if($supplierOrder->order_type === 'lpo')
                                    <span class="badge badge-primary">LPO / BUDGET</span>
                                @else
                                    <span class="badge badge-info">Regular Order</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Supplier:</th>
                            <td>{{ $supplierOrder->supplier->name }}</td>
                        </tr>
                        <tr>
                            <th>Period:</th>
                            <td>{{ $supplierOrder->start_date }} to {{ $supplierOrder->end_date }}</td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td class="text-primary h5">{{ number_format($supplierOrder->total_amount, 2) }} TZS</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @php
                                    $badgeClass = match ($supplierOrder->status) {
                                        'pending' => 'badge-warning',
                                        'sent_to_accountant' => 'badge-info',
                                        'sent_to_manager' => 'badge-primary',
                                        'verified_by_manager' => 'badge-success',
                                        'closed' => 'badge-dark',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                @endphp
                                <span
                                    class="badge {{ $badgeClass }} h6">{{ ucfirst(str_replace('_', ' ', $supplierOrder->status)) }}</span>

                                @if($supplierOrder->received_at)
                                    <span class="badge badge-success h6 ml-1"><i class="fa fa-inbox"></i> RECEIVED</span>
                                @endif

                                @if($supplierOrder->received_at)
                                    <div class="mt-1 small text-success">
                                        <i class="fa fa-clock-o"></i> Fully Received on
                                        {{ $supplierOrder->received_at->format('M d, Y H:i') }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Storekeeper:</th>
                            <td>{{ $supplierOrder->storekeeper->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Accountant:</th>
                            <td>{{ $supplierOrder->accountant->name ?? 'N/A' }}</td>
                        </tr>
                        <tr class="bg-light">
                            <th>Payment Status:</th>
                            <td>
                                @php
                                    $pStatus = $supplierOrder->payment_status ?: 'unpaid';
                                    $pBadge = match ($pStatus) {
                                        'unpaid' => 'badge-danger',
                                        'partial' => 'badge-info',
                                        'settled' => 'badge-success',
                                        default => 'badge-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $pBadge }} h6">{{ ucfirst($pStatus) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Amount Paid:</th>
                            <td class="text-success font-weight-bold">{{ number_format($supplierOrder->amount_paid, 2) }}
                                TZS</td>
                        </tr>
                        <tr>
                            <th>Balance:</th>
                            <td class="text-danger font-weight-bold">
                                {{ number_format($supplierOrder->total_amount - $supplierOrder->amount_paid, 2) }} TZS
                            </td>
                        </tr>
                    </table>
                    @if($supplierOrder->notes)
                        <div class="bg-light p-2 mt-3 border rounded">
                            <strong>Order Notes & History:</strong><br>
                            <small>{{ $supplierOrder->notes }}</small>
                        </div>
                    @endif
                </div>
                <div class="tile-footer">
                    @if($supplierOrder->status === 'pending' && auth()->guard('staff')->user()->role === 'storekeeper')
                        <a href="{{ route('supplier-orders.edit', $supplierOrder->id) }}"
                            class="btn btn-primary btn-block mb-2"><i class="fa fa-edit"></i> Edit Items</a>
                        <form id="sendToAccountantForm" action="{{ route('supplier-orders.send', $supplierOrder->id) }}"
                            method="POST">
                            @csrf
                            <button type="button" id="sendToAccountantBtn" class="btn btn-success btn-block"><i
                                    class="fa fa-paper-plane"></i> Send to
                                Accountant</button>
                        </form>
                    @endif

                    @if($supplierOrder->status === 'sent_to_accountant' && auth()->guard('staff')->user()->role === 'accountant')
                        <button type="button" class="btn btn-primary btn-block mb-2" onclick="confirmSendToManager()"><i
                                class="fa fa-share"></i> Send to Manager for Verification</button>
                    @endif

                    @if($supplierOrder->status === 'sent_to_manager' && in_array(auth()->guard('staff')->user()->role, ['manager', 'super_admin']))
                        <button type="button" class="btn btn-success btn-block"
                            onclick="$('#verifyOrderModal').modal('show')"><i class="fa fa-check-circle"></i> Verify
                            Order</button>
                    @endif

                    @if($supplierOrder->status === 'verified_by_manager' && auth()->guard('staff')->user()->role === 'accountant')
                        <button type="button" class="btn btn-success btn-block" onclick="$('#closeOrderModal').modal('show')"><i
                                class="fa fa-check-circle"></i> Settle & Close Order</button>
                    @endif

                    @if(in_array($supplierOrder->status, ['verified_by_manager', 'closed']) && auth()->guard('staff')->user()->role === 'accountant' && $supplierOrder->payment_status !== 'settled')
                        <button type="button" class="btn btn-primary btn-block mt-2" onclick="$('#paymentModal').modal('show')">
                            <i class="fa fa-money"></i> Record Supplier Payment
                        </button>
                    @endif

                    @php
                        $fullyReceived = $supplierOrder->items->every(fn($i) => (float) $i->qty_received >= (float) $i->quantity);
                    @endphp

                    @if(!$fullyReceived && in_array($supplierOrder->status, ['verified_by_manager', 'closed']) && in_array(auth()->guard('staff')->user()->role, ['storekeeper', 'super_admin']))
                        <a href="{{ route('supplier-orders.receive', $supplierOrder->id) }}"
                            class="btn btn-warning btn-block mt-2">
                            <i class="fa fa-inbox"></i> Receive Items into Inventory
                        </a>
                    @endif

                    @if($fullyReceived)
                        <div class="alert alert-success mt-2 mb-0 py-2 text-center border-success">
                            <i class="fa fa-check-circle"></i> Fully received into inventory.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="tile shadow-sm">
                <h3 class="tile-title border-bottom pb-2">Items & Inventory Status</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th class="text-center">Planned Qty</th>
                                <th class="text-center">Received So Far</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supplierOrder->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->item_name }}</td>
                                    <td class="text-center">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                                    <td class="text-center font-weight-bold {{ (float) $item->qty_received >= (float) $item->quantity ? 'text-success' : 'text-info' }}">
                                        {{ number_format($item->qty_received, 2) }} {{ $item->unit }}
                                    </td>
                                    <td class="text-center">
                                        @if((float) $item->qty_received >= (float) $item->quantity)
                                            <span class="badge badge-success">COMPLETE</span>
                                        @elseif($item->qty_received > 0)
                                            <span class="badge badge-info">PARTIAL</span>
                                        @else
                                            <span class="badge badge-secondary">PENDING</span>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-light">
                                @if($supplierOrder->order_type === 'lpo')
                                    <td colspan="4" class="text-right">TOTAL BUDGET BALANCE:</td>
                                    <td colspan="3" class="text-success text-right h5">{{ number_format($supplierOrder->remaining_balance, 2) }} TZS</td>
                                @else
                                    <td colspan="6" class="text-right">GRAND TOTAL:</td>
                                    <td class="text-primary text-right">{{ number_format($supplierOrder->total_amount, 2) }} TZS</td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('supplier-orders.payment', $supplierOrder->id) }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Record Supplier Payment</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2">
                            <strong>Balance to Settle:</strong>
                            {{ number_format($supplierOrder->total_amount - $supplierOrder->amount_paid, 2) }} TZS
                        </div>
                        <div class="form-group">
                            <label>Payment Amount (TZS)</label>
                            <input type="number" name="amount"
                                class="form-control form-control-lg text-primary font-weight-bold" step="0.01"
                                value="{{ max(0, $supplierOrder->total_amount - $supplierOrder->amount_paid) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Payment Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3"
                                placeholder="e.g. Paid via NMB bank..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Record Payment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Close Modal -->
    <div class="modal fade" id="closeOrderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('supplier-orders.close', $supplierOrder->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Settle & Close Weekly Order</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Mark this order as settled. This means payment has been processed or recorded.</p>
                        <div class="alert alert-info">
                            <strong>Manager Notes:</strong><br>
                            {{ $supplierOrder->manager_notes ?? 'No specific verification notes.' }}
                        </div>
                        <div class="form-group">
                            <label>Settlement Notes (Optional)</label>
                            <textarea name="closing_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Finalize Settlement</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Verify Modal -->
    <div class="modal fade" id="verifyOrderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('supplier-orders.manager-verify', $supplierOrder->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Verify Weekly Order</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Verify that this weekly order is correct and proceed to accountant for settlement.</p>
                        <div class="form-group">
                            <label>Verification Notes (Optional)</label>
                            <textarea name="manager_notes" class="form-control" rows="3"
                                placeholder="Add instructions or comments for the accountant..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Mark as Verified</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form id="sendToManagerForm" action="{{ route('supplier-orders.send-to-manager', $supplierOrder->id) }}" method="POST"
        style="display: none;">
        @csrf
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#sendToAccountantBtn').click(function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Confirm Submission',
                    text: "Are you sure you want to send this order to the accountant for review?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Send it!',
                    cancelButtonText: 'No, Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#sendToAccountantForm').submit();
                    }
                });
            });
        });

        function confirmSendToManager() {
            Swal.fire({
                title: 'Send to Manager?',
                text: "This will send the order to the manager for final verification before settlement.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Send to Manager',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#sendToManagerForm').submit();
                }
            });
        }
    </script>
@endsection