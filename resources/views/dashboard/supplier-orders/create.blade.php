@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-plus"></i> Create Weekly Order Batch</h1>
            <p>Start a new weekly order batch for a supplier</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('supplier-orders.index') }}">Weekly Orders</a></li>
            <li class="breadcrumb-item">Create</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="tile">
                <h3 class="tile-title">Order Details</h3>
                <div class="tile-body">
                    <form action="{{ route('supplier-orders.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label class="control-label">Supplier</label>
                            <select name="supplier_id" class="form-control" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Start Date</label>
                                    <input name="start_date" class="form-control" type="date" required
                                        value="{{ date('Y-m-d', strtotime('monday this week')) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">End Date</label>
                                    <input name="end_date" class="form-control" type="date" required
                                        value="{{ date('Y-m-d', strtotime('sunday this week')) }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"
                                placeholder="Additional information..."></textarea>
                        </div>
                        <div class="tile-footer">
                            <button class="btn btn-primary" type="submit"><i
                                    class="fa fa-fw fa-lg fa-check-circle"></i>Continue to Add Items</button>
                            <a class="btn btn-secondary" href="{{ route('supplier-orders.index') }}"><i
                                    class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Future logic for LPO date sync if needed
        });
    </script>
@endsection
