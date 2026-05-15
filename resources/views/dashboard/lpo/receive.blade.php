@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-inbox"></i> Receive items from LPO Budget: #{{ $lpo->id }}</h1>
            <p>Add items from the local purchase budget into the stock inventory</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('lpo.index') }}">LPO Budgets</a></li>
            <li class="breadcrumb-item">Receive Items</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title border-bottom pb-2">Receive Items</h3>
                <form action="{{ route('lpo.process-receive', $lpo->id) }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Item Details</th>
                                    <th class="text-center">Budgeted</th>
                                    <th class="text-center">Received So Far</th>
                                    <th class="text-center" style="width: 200px">Receiving Now</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lpo->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->item_name }}</strong><br>
                                            <small class="text-muted">
                                                @if($item->productVariant)
                                                    <i class="fa fa-link text-success"></i> Linked:
                                                    {{ $item->productVariant->product->name }}
                                                    ({{ $item->productVariant->variant_name }})
                                                @else
                                                    <i class="fa fa-warning text-warning"></i> Not linked to any stock product.
                                                @endif
                                            </small>
                                        </td>
                                        <td class="text-center">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                                        <td class="text-center">{{ number_format($item->qty_received, 2) }} {{ $item->unit }}
                                        </td>
                                        <td>
                                            @if($item->productVariant)
                                                <input type="number" name="items[{{ $item->id }}][received_qty]"
                                                    class="form-control" step="0.01" min="0" value="0">
                                            @else
                                                <span class="text-danger small">Link this item to a product in "Edit Items" to
                                                    receive it into stock.</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="tile-footer mt-4">
                        <button class="btn btn-primary btn-lg" type="submit"><i class="fa fa-check-circle"></i> Confirm
                            Receipt</button>
                        <a href="{{ route('lpo.show', $lpo->id) }}" class="btn btn-secondary btn-lg ml-2">Back to
                            Details</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
