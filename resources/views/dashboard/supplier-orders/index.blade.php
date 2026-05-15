@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-truck"></i> Weekly Supplier Orders</h1>
            <p>Manage weekly orders and settlements for suppliers (Meat, Chicken, etc.)</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="#">Weekly Orders</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title">Supplier Orders History</h3>
                    @if($role === 'storekeeper')
                        <p><a class="btn btn-primary icon-btn" href="{{ route('supplier-orders.create') }}"><i
                                    class="fa fa-plus"></i>New Order Batch</a></p>
                    @endif
                </div>
                <div class="tile-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Supplier</th>
                                    <th>Period</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Storekeeper</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($order->start_date)->format('M d') }} -
                                            {{ \Carbon\Carbon::parse($order->end_date)->format('M d, Y') }}
                                        </td>
                                        <td>{{ number_format($order->total_amount, 0) }} TZS</td>
                                        <td>
                                            @php
                                                $badgeClass = match ($order->status) {
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
                                                class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>

                                            @if($order->received_at)
                                                <span class="badge badge-success ml-1"><i class="fa fa-inbox"></i> RECEIVED</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->storekeeper->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('supplier-orders.show', $order->id) }}"
                                                    class="btn btn-sm btn-info" title="View Details"><i
                                                        class="fa fa-eye"></i></a>

                                                @if($order->status === 'pending' && $role === 'storekeeper')
                                                    <a href="{{ route('supplier-orders.edit', $order->id) }}"
                                                        class="btn btn-sm btn-primary" title="Edit/Add Items"><i
                                                            class="fa fa-edit"></i></a>
                                                @endif

                                                @if($order->status === 'sent_to_accountant' && $role === 'accountant')
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        onclick="window.location.href='{{ route('supplier-orders.show', $order->id) }}'"
                                                        title="Send to Manager"><i class="fa fa-share"></i></button>
                                                @endif

                                                @if($order->status === 'sent_to_manager' && in_array($role, ['manager', 'super_admin']))
                                                    <a href="{{ route('supplier-orders.show', $order->id) }}"
                                                        class="btn btn-sm btn-success" title="Verify Order"><i
                                                            class="fa fa-check-circle"></i></a>
                                                @endif

                                                @if($order->status === 'verified_by_manager' && $role === 'accountant')
                                                    <button type="button" class="btn btn-sm btn-success"
                                                        onclick="closeOrder({{ $order->id }})" title="Settle & Close"><i
                                                            class="fa fa-check"></i></button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Close Modal (Optional but good for notes) -->
    <div class="modal fade" id="closeOrderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="closeOrderForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Close Weekly Order Batch</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to mark this order batch as settled/closed?</p>
                        <div class="form-group">
                            <label>Closing Notes (optional)</label>
                            <textarea name="closing_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Confirm Settlement</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function closeOrder(id) {
            $('#closeOrderForm').attr('action', '/supplier-orders/' + id + '/close');
            $('#closeOrderModal').modal('show');
        }
    </script>
@endsection
