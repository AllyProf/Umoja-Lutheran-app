@extends('dashboard.layouts.app')

@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class="fa fa-exclamation-triangle"></i> Low Stock Alerts</h1>
                <p>View all items across the inventory that have reached their minimum stock level</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
                <li class="breadcrumb-item"><a href="#">Inventory Control</a></li>
                <li class="breadcrumb-item active"><a href="#">Low Stock</a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="title">Low Stock Items ({{ $totalLowStock }})</h3>
                    </div>
                    <div class="tile-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="lowStockTable">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Current Stock</th>
                                        <th>Minimum Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lowStockVariants as $variant)
                                        <tr>
                                            <td>
                                                @php
                                                    $vName = $variant->variant_name;
                                                @endphp
                                                @if(empty($vName) || strtolower($vName) === 'standard' || strtolower($vName) === strtolower($variant->product->name))
                                                    {{ $variant->product->name }}
                                                @else
                                                    {{ $vName }}
                                                @endif
                                            </td>
                                            <td class="text-danger font-weight-bold">
                                                {{ number_format($variant->current_stock, 2) }}
                                                {{ ucfirst($variant->receiving_unit ?: 'Pcs') }}
                                            </td>
                                            <td>{{ number_format($variant->minimum_stock_level, 2) }}
                                                {{ ucfirst($variant->receiving_unit ?: 'Pcs') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No low stock items found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#lowStockTable').DataTable();
        });
    </script>
@endsection