@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-file-text"></i> Purchase Report</h1>
    <p>Detailed purchase report for: <strong>{{ $shoppingList->name }}</strong></p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.purchase-reports.index') }}">Purchase Reports</a></li>
    <li class="breadcrumb-item"><a href="#">Report Detail</a></li>
  </ul>
</div>

<!-- Budget Summary -->
<div class="row mb-3">
  <div class="col-md-4">
    <div class="tile">
      <h3 class="tile-title">Budget Information</h3>
      <div class="tile-body">
        <table class="table table-sm">
          <tr>
            <th>Budget Amount:</th>
            <td><strong>{{ number_format($shoppingList->budget_amount ?? 0, 2) }} TZS</strong></td>
          </tr>
          <tr>
            <th>Amount Used:</th>
            <td><strong>{{ number_format($shoppingList->amount_used ?? 0, 2) }} TZS</strong></td>
          </tr>
          <tr>
            <th>Amount Remaining:</th>
            <td>
              <strong class="{{ ($shoppingList->amount_remaining ?? 0) < 0 ? 'text-danger' : 'text-success' }}">
                {{ number_format($shoppingList->amount_remaining ?? 0, 2) }} TZS
              </strong>
            </td>
          </tr>
          @if($shoppingList->budget_amount && $shoppingList->budget_amount > 0)
          <tr>
            <th>Budget Usage:</th>
            <td>
              @php
                $usagePercentage = ($shoppingList->amount_used ?? 0) / $shoppingList->budget_amount * 100;
              @endphp
              <div class="progress" style="height: 20px;">
                <div class="progress-bar {{ $usagePercentage > 100 ? 'bg-danger' : ($usagePercentage > 80 ? 'bg-warning' : 'bg-success') }}" 
                     role="progressbar" 
                     style="width: {{ min($usagePercentage, 100) }}%">
                  {{ number_format($usagePercentage, 1) }}%
                </div>
              </div>
            </td>
          </tr>
          @endif
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="tile">
      <h3 class="tile-title">Purchase Summary</h3>
      <div class="tile-body">
        <table class="table table-sm">
          <tr>
            <th>Total Items:</th>
            <td><strong>{{ $itemStats['total'] }}</strong></td>
          </tr>
          <tr>
            <th>Found Items:</th>
            <td><strong class="text-success">{{ $itemStats['found'] }}</strong></td>
          </tr>
          <tr>
            <th>Missing Items:</th>
            <td><strong class="text-danger">{{ $itemStats['missing'] }}</strong></td>
          </tr>
          <tr>
            <th>Total Cost:</th>
            <td><strong>{{ number_format($shoppingList->total_actual_cost ?? 0, 2) }} TZS</strong></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="tile">
      <h3 class="tile-title">Purchase Details</h3>
      <div class="tile-body">
        <table class="table table-sm">
          <tr>
            <th>Market:</th>
            <td>{{ $shoppingList->market_name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Shopping Date:</th>
            <td>{{ $shoppingList->shopping_date ? \Carbon\Carbon::parse($shoppingList->shopping_date)->format('M d, Y') : 'N/A' }}</td>
          </tr>
          <tr>
            <th>Status:</th>
            <td>
              @if($shoppingList->status === 'completed')
                <span class="badge badge-success">Completed</span>
              @elseif($shoppingList->status === 'pending')
                <span class="badge badge-warning">Pending</span>
              @else
                <span class="badge badge-danger">Cancelled</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Created:</th>
            <td>{{ $shoppingList->created_at->format('M d, Y H:i') }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Items Detail Table -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-list"></i> Purchased Items</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Status</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Planned Qty</th>
                <th>Purchased Qty</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Total Cost</th>
                <th>Storage Location</th>
              </tr>
            </thead>
            <tbody>
              @foreach($shoppingList->items as $item)
              <tr class="{{ !$item->is_found ? 'table-danger' : '' }}">
                <td>
                  @if($item->is_found)
                    <span class="badge badge-success"><i class="fa fa-check"></i> Found</span>
                  @else
                    <span class="badge badge-danger"><i class="fa fa-times"></i> Missing</span>
                  @endif
                </td>
                <td><strong>{{ $item->product_name }}</strong></td>
                <td>{{ $item->category ? ucfirst(str_replace('_', ' ', $item->category)) : 'N/A' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>
                  @if($item->is_found)
                    {{ $item->purchased_quantity ?? 0 }}
                  @else
                    <span class="text-muted">0</span>
                  @endif
                </td>
                <td>{{ $item->unit }}</td>
                <td>
                  @if($item->is_found && $item->unit_price)
                    {{ number_format($item->unit_price, 2) }} TZS
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($item->is_found && $item->purchased_cost)
                    <strong>{{ number_format($item->purchased_cost, 2) }} TZS</strong>
                  @else
                    <span class="text-muted">0.00 TZS</span>
                  @endif
                </td>
                <td>{{ $item->storage_location ?? 'N/A' }}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="7" class="text-right"><strong>Total Cost:</strong></td>
                <td><strong>{{ number_format($shoppingList->total_actual_cost ?? 0, 2) }} TZS</strong></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Print Button -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body text-center">
        <button onclick="window.print()" class="btn btn-primary">
          <i class="fa fa-print"></i> Print Report
        </button>
        <a href="{{ route('admin.purchase-reports.index') }}" class="btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Reports
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
@media print {
  .app-sidebar, .app-header, .app-breadcrumb, .btn, .tile-footer {
    display: none !important;
  }
  .app-content {
    margin-left: 0 !important;
  }
}
</style>
@endsection
