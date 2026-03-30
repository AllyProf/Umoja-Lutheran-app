@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-shopping-cart"></i> Purchase Requests</h1>
            <p>Review and manage purchase requests from all departments</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('storekeeper.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Purchase Requests</li>
        </ul>
    </div>

    <!-- Tab Navigation -->
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}"
                            href="{{ route('storekeeper.purchase-requests', ['tab' => 'pending']) }}">
                            <i class="fa fa-clock-o"></i> Pending
                            @if($tab === 'pending') <span class="badge badge-danger">{{ $requests->total() }}</span> @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'approved' ? 'active' : '' }}"
                            href="{{ route('storekeeper.purchase-requests', ['tab' => 'approved']) }}">
                            <i class="fa fa-check-circle"></i> In Progress
                        </a>
                    </li>
                </ul>

                <div class="tile-title-w-btn mb-3">
                    <h3 class="title">
                        @if ($tab === 'pending')
                            Pending Requests
                        @else
                            In-Progress Requests
                        @endif
                    </h3>
                    <div class="d-flex align-items-center">
                        @if ($tab === 'approved')
                            <button id="addToShoppingListBtn" class="btn btn-success btn-sm mr-2" disabled>
                                <i class="fa fa-shopping-basket"></i> Add Selected to Shopping List
                            </button>
                        @endif
                        <a href="{{ route('storekeeper.shopping-list.create') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-plus-circle"></i> Create New List
                        </a>
                    </div>
                </div>

                @if ($requests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="requestsTable">
                            <thead class="thead-light">
                                <tr>
                                    @if($tab === 'approved')
                                        <th style="width: 30px;">
                                            <div class="animated-checkbox">
                                                <label class="mb-0">
                                                    <input type="checkbox" id="selectAll"><span class="label-text"></span>
                                                </label>
                                            </div>
                                        </th>
                                    @endif
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Department</th>
                                    <th>Requested By</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $i => $req)
                                    <tr>
                                        @if($tab === 'approved')
                                            <td>
                                                <div class="animated-checkbox">
                                                    <label class="mb-0">
                                                        @if($req->status === 'approved')
                                                            <input type="checkbox" class="request-checkbox" value="{{ $req->id }}"><span
                                                                class="label-text"></span>
                                                        @else
                                                            <i class="fa fa-lock text-muted" title="Already on a list or purchased"></i>
                                                        @endif
                                                    </label>
                                                </div>
                                            </td>
                                        @endif
                                        <td>{{ $requests->firstItem() + $i }}</td>
                                        <td>
                                            <strong>{{ $req->item_name }}</strong>
                                            @if($req->is_emergency)
                                                <span class="badge badge-danger ml-1" title="Emergency Request">⚠️</span>
                                            @endif
                                            <br><small class="text-muted">{{ $req->description }}</small>
                                        </td>
                                        <td>{{ $req->quantity }} {{ $req->unit }}</td>
                                        <td><span
                                                class="badge badge-info">{{ $req->requestedBy?->getDepartmentName() ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $req->requestedBy?->name ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'approved' => 'info',
                                                    'on_list' => 'primary',
                                                    'purchased' => 'success',
                                                    'rejected' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusColors[$req->status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                            </span>
                                        </td>
                                        <td><small>{{ $req->created_at->format('M d, Y') }}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $requests->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
                        <h3>No Requests Found</h3>
                        <p class="text-muted">There are no {{ $tab === 'pending' ? 'pending' : 'in-progress' }} purchase
                            requests.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('dashboard_assets/js/plugins/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Select All functionality
            $('#selectAll').on('change', function () {
                $('.request-checkbox').prop('checked', $(this).prop('checked'));
                toggleActionButton();
            });

            $('.request-checkbox').on('change', function () {
                toggleActionButton();

                // Update Select All state
                var allChecked = $('.request-checkbox:checked').length === $('.request-checkbox').length;
                $('#selectAll').prop('checked', allChecked);
            });

            function toggleActionButton() {
                var checkedCount = $('.request-checkbox:checked').length;
                $('#addToShoppingListBtn').prop('disabled', checkedCount === 0);

                if (checkedCount > 0) {
                    $('#addToShoppingListBtn').html('<i class="fa fa-shopping-basket"></i> Add ' + checkedCount + ' to Shopping List');
                } else {
                    $('#addToShoppingListBtn').html('<i class="fa fa-shopping-basket"></i> Add Selected to Shopping List');
                }
            }

            // Add to Shopping List AJAX
            $('#addToShoppingListBtn').on('click', function () {
                var requestIds = [];
                $('.request-checkbox:checked').each(function () {
                    requestIds.push($(this).val());
                });

                if (requestIds.length === 0) return;

                swal({
                    title: "Prepare Shopping List?",
                    text: "This will pre-fill a new shopping list with the " + requestIds.length + " selected items.",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonText: "Yes, proceed",
                    showLoaderOnConfirm: true
                }, function () {
                    $.ajax({
                        url: "{{ route('admin.purchase-requests.add-to-shopping-list') }}",
                        method: 'POST',
                        data: {
                            request_ids: requestIds,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if (response.success \u0026\u0026 response.redirect_url) {
                        swal("Redirecting...", response.message, "success");
                        window.location.href = response.redirect_url;
                    } else {
                        swal("Error!", response.message, "error");
                    }
                },
                    error: function (xhr) {
                        var errorMsg = xhr.responseJSON?.message || 'Failed to process request.';
                        swal("Error!", errorMsg, "error");
                    }
                });
        });
        });
    });
    </script>
@endsection