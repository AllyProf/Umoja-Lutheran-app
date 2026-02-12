@extends('dashboard.layouts.app')

@section('content')
<div class="app-title d-print-none">
    <div>
        <h1><i class="fa fa-cubes"></i> Kitchen Stock / Ingredients</h1>
        <p>Real-time balance of your kitchen inventory</p>
    </div>
    <ul class="app-breadcrumb breadcrumb text-capitalize">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('chef-master.dashboard') }}">Kitchen</a></li>
        <li class="breadcrumb-item">Stock</li>
    </ul>
</div>

<div class="row d-print-none mb-3">
    <div class="col-md-12 text-right">
        <button class="btn btn-primary" onclick="window.print()"><i class="fa fa-print"></i> Print Stock Sheet</button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile shadow-sm border-0" style="border-radius: 15px;">
            <!-- Premium Branding Header (Only shown when printing) -->
            <div class="d-none d-print-block text-center mb-5">
                <div class="mb-3">
                    <img src="{{ asset('dashboard_assets/images/Logo.png') }}" alt="PrimeLand Logo" style="height: 80px;">
                </div>
                <h1 class="mb-1" style="color: #e07632; font-weight: 800; letter-spacing: 2px;">PRIMELAND HOTEL</h1>
                <p class="mb-0 text-dark">Location: Sokoine Road - Moshi, Kilimanjaro - Tanzania</p>
                <p class="mb-2 text-dark font-weight-bold">Mobile/WhatsApp: 0677-155-156 / +255 677-155-157</p>
                <div style="height: 4px; background: #e07632; width: 100px; margin: 15px auto;"></div>
                <h3 class="text-uppercase font-weight-bold mt-3">KITCHEN INVENTORY STOCK SHEET</h3>
                <p class="text-muted">Generated on: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="stockTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Ingredient Name</th>
                                <th>Category</th>
                                <th class="text-center">Total Received</th>
                                <th class="text-center">Total Consumed</th>
                                <th class="text-center">Current Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockData as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->image)
                                            <img src="{{ asset('storage/' . $item->image) }}" width="30" class="mr-2 rounded">
                                        @endif
                                        <strong>{{ $item->name }}</strong>
                                    </div>
                                </td>
                                <td><span class="badge badge-light border">{{ $item->category }}</span></td>
                                <td class="text-center font-weight-bold text-muted">{{ number_format($item->received, 2) + 0 }} <small>{{ $item->unit ?? '' }}</small></td>
                                <td class="text-center font-weight-bold text-danger">{{ number_format($item->consumed, 2) + 0 }} <small>{{ $item->unit ?? '' }}</small></td>
                                <td class="text-center">
                                    <span class="badge {{ $item->balance > 0 ? 'badge-success' : 'badge-danger' }} px-3 py-2" style="font-size: 0.9rem;">
                                        {{ number_format($item->balance, 2) + 0 }} {{ $item->unit ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->balance <= 0)
                                        <span class="text-danger small"><i class="fa fa-exclamation-triangle"></i> Out of Stock</span>
                                    @elseif($item->balance < 5)
                                        <span class="text-warning small"><i class="fa fa-warning"></i> Low Stock</span>
                                    @else
                                        <span class="text-success small"><i class="fa fa-check-circle"></i> Healthy</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                            {{-- Show Unlinked/Ad-hoc Items --}}
                            @foreach($unlinkedItems as $item)
                            <tr class="table-warning">
                                <td>{{ $item['name'] }} <small class="text-muted">(Ad-hoc Purchase)</small></td>
                                <td>{{ $item['category'] }}</td>
                                <td class="text-center">{{ number_format($item['total_qty'], 2) + 0 }} <small>{{ $item['unit'] }}</small></td>
                                <td class="text-center">-</td>
                                <td class="text-center">
                                    <span class="badge badge-warning px-3 py-2" style="font-size: 0.9rem;">
                                        {{ number_format($item['total_qty'], 2) + 0 }} {{ $item['unit'] }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">Storage: {{ $item['storage'] ?: 'Main Kitchen' }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
    @media print {
        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .app-title, .app-sidebar, .app-header, .d-print-none {
            display: none !important;
        }
        .app-content {
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
        }
        .tile {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .table td, .table th {
            padding: 6px 8px !important;
            font-size: 10pt !important;
            color: #000 !important;
            border: 1px solid #000 !important;
        }
        .dataTables_info, .dataTables_paginate, .dataTables_filter, .dataTables_length {
            display: none !important;
        }
    }
</style>
@section('scripts')
<script type="text/javascript" src="{{ asset('dashboard/js/plugins/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('dashboard/js/plugins/dataTables.bootstrap.min.js') }}"></script>
<script>
    $('#stockTable').DataTable({
        "order": [[ 4, "asc" ]] // Sort by balance ascending (out of stock/low stock first)
    });
</script>
@endsection

@endsection
