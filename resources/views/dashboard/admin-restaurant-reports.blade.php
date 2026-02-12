
@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-cutlery"></i> Kitchen & Food Reports</h1>
    <p>View detailed operational reports for kitchen, food, and restaurant operations</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="#">Kitchen & Food Reports</a></li>
  </ul>
</div>

<!-- Report Type and Date Filter -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Generate Report</h3>
      </div>
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.restaurants.reports') }}" id="reportForm">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="date_type"><strong>Report Type:</strong></label>
                <select name="date_type" id="date_type" class="form-control" onchange="toggleDateInputs()">
                  <option value="daily" {{ ($dateType ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily</option>
                  <option value="weekly" {{ ($dateType ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                  <option value="monthly" {{ ($dateType ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                  <option value="yearly" {{ ($dateType ?? '') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
              </div>
            </div>
            <div class="col-md-3" id="singleDateContainer">
              <div class="form-group">
                <label for="date"><strong>Select Date:</strong></label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $customDate ?? today()->format('Y-m-d') }}">
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fa fa-file-text"></i> Generate Report
                </button>
              </div>
            </div>
          </div>
          
           <div class="row mt-3">
            <div class="col-md-12">
              <div class="form-group mb-0">
                <label><strong>Quick Actions:</strong></label>
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickReport('daily', '{{ today()->format('Y-m-d') }}')">
                    <i class="fa fa-calendar-day"></i> Today
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickReport('weekly', '{{ today()->format('Y-m-d') }}')">
                    <i class="fa fa-calendar-week"></i> This Week
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickReport('monthly', '{{ today()->format('Y-m-d') }}')">
                    <i class="fa fa-calendar"></i> This Month
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickReport('yearly', '{{ today()->format('Y-m-d') }}')">
                    <i class="fa fa-calendar-alt"></i> This Year
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Report Display (Receipt Style) -->
<style>
.receipt-report-container {
    max-width: 800px;
    margin: 30px auto;
    background: #fff;
    padding: 30px;
    border: 1px solid #ddd;
    position: relative;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.receipt-report-container::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    width: 500px;
    height: 500px;
    background-image: url('{{ asset('royal-master/image/logo/Logo.png') }}');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    opacity: 0.12;
    z-index: 0;
    pointer-events: none;
    filter: grayscale(100%) brightness(1.2);
}

.receipt-report-container > * {
    position: relative;
    z-index: 1;
}

.receipt-report-header {
    text-align: center;
    border-bottom: 3px solid #e07632;
    padding-bottom: 20px;
    margin-bottom: 30px;
}

.receipt-report-header .logo-container {
    margin-bottom: 15px;
}

.receipt-report-header .logo-container img {
    max-height: 80px;
    max-width: 300px;
    height: auto;
    width: auto;
}

.receipt-report-header h1 {
    color: #e07632;
    font-size: 28px;
    margin-bottom: 5px;
    font-weight: bold;
}

.receipt-report-header p {
    color: #666;
    font-size: 14px;
    margin: 2px 0;
}

.receipt-report-title {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 30px;
    color: #333;
}

.receipt-report-number {
    text-align: right;
    margin-bottom: 20px;
    color: #666;
    font-size: 11px;
}

.print-button-section {
    text-align: center;
    margin: 30px 0;
}

.print-button-section button {
    background-color: #e07632;
    color: #fff;
    border: none;
    padding: 12px 30px;
    font-size: 14px;
    cursor: pointer;
    border-radius: 4px;
    font-weight: bold;
    margin: 0 5px;
}

.print-button-section button:hover {
    background-color: #c86528;
}

.receipt-info-section {
    margin-bottom: 25px;
}

.receipt-info-section h3 {
    color: #e07632;
    font-size: 14px;
    border-bottom: 2px solid #e07632;
    padding-bottom: 8px;
    margin-bottom: 15px;
    font-weight: bold;
}

.receipt-info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding: 5px 0;
}

.receipt-info-label {
    font-weight: bold;
    color: #555;
    width: 45%;
}

.receipt-info-value {
    color: #333;
    width: 55%;
    text-align: right;
}

.receipt-two-column {
    display: flex;
    gap: 30px;
    margin-bottom: 25px;
}

.receipt-column {
    flex: 1;
}

.receipt-details-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.receipt-details-table th {
    background-color: #f8f9fa;
    color: #333;
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
    font-weight: bold;
    font-size: 11px;
}

.receipt-details-table td {
    padding: 12px;
    border: 1px solid #ddd;
    font-size: 11px;
}

.receipt-details-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.receipt-footer {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    text-align: center;
    color: #666;
    font-size: 10px;
}

.receipt-footer p {
    margin: 5px 0;
}

.receipt-footer .powered-by {
    color: #940000;
    font-weight: bold;
    margin-top: 15px;
    font-size: 9px;
}

/* Print Styles */
@media print {
    @page {
        margin: 10mm !important;
        size: A4;
    }
    
    /* Hide ALL non-report elements */
    .app-header,
    .app-sidebar,
    .app-sidebar__overlay,
    .app-title,
    .app-breadcrumb,
    .tile:has(form),
    .alert-info:not(.receipt-container .alert-info),
    body > *:not(.app-content),
    .app-content > *:not(.receipt-report-container) {
        display: none !important;
    }
    
    /* Hide print button when printing */
    .print-button-section {
        display: none !important;
    }
    
    /* Force body and main content to be visible */
    body {
        background: white !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .app-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Ensure receipt container is visible and properly formatted */
    .receipt-report-container {
        display: block !important;
        visibility: visible !important;
        border: none !important;
        padding: 10px 15px !important;
        margin: 0 !important;
        box-shadow: none !important;
        max-width: 100% !important;
        page-break-inside: avoid !important;
        background: white !important;
    }
    
    /* Optimize font sizes for print */
    .receipt-report-header h1 {
        font-size: 22px !important;
    }
    
    .receipt-report-header p {
        font-size: 11px !important;
    }
    
    .receipt-report-title {
        font-size: 16px !important;
    }
    
    .receipt-info-section h3 {
        font-size: 12px !important;
        page-break-after: avoid !important;
    }
    
    .receipt-info-row {
        font-size: 10px !important;
    }
    
    .receipt-details-table {
        font-size: 9px !important;
        page-break-inside: auto !important;
    }
    
    .receipt-details-table thead {
        display: table-header-group !important;
    }
    
    .receipt-details-table th,
    .receipt-details-table td {
        padding: 6px 4px !important;
        font-size: 9px !important;
    }
    
    .receipt-details-table tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }
    
    /* Ensure watermark is visible in print */
    .receipt-report-container::before {
        opacity: 0.08 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Preserve colors in print */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}

/* Mobile Responsiveness */
@media screen and (max-width: 600px) {
    .receipt-report-container {
        padding: 15px;
        margin: 10px auto;
        border: none;
        box-shadow: none;
    }
    
    .receipt-report-header h1 {
        font-size: 20px;
    }
    
    .receipt-report-header p {
        font-size: 11px;
    }
    
    .receipt-report-header .logo-container img {
        max-height: 60px;
    }
    
    .receipt-two-column {
        flex-direction: column;
        gap: 20px;
    }
    
    .receipt-info-row {
        flex-wrap: wrap;
    }
    
    .receipt-info-label {
        width: 100%;
        text-align: left;
        margin-bottom: 2px;
    }
    
    .receipt-info-value {
        width: 100%;
        text-align: left;
        font-weight: bold;
    }
    
    .receipt-report-title {
        font-size: 16px;
    }
    
    .print-button-section button {
        width: 100%;
        margin: 5px 0;
    }
    
    .receipt-details-table {
        display: block;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .receipt-report-container::before {
        width: 300px;
        height: 300px;
    }
    
    .receipt-report-number {
        text-align: center;
        margin-bottom: 15px;
    }
}
</style>

<div class="receipt-report-container">
    <!-- Header -->
    <div class="receipt-report-header">
        <div class="logo-container">
            <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="Umoj Lutheran Hostel Logo" onerror="this.style.display='none';">
        </div>
        <h1>Umoj Lutheran Hostel</h1>
        <div style="margin-top: 10px; margin-bottom: 10px; line-height: 1.8;">
            <p style="margin: 5px 0; font-size: 13px; color: #555;">
                <strong>Location:</strong> Sokoine Road-Moshi Kilimanjaro-Tanzania
            </p>
            <p style="margin: 5px 0; font-size: 13px; color: #555;">
                <strong>Mobile/WhatsApp:</strong> 0677-155-156 / +255 677-155-157
            </p>
            <p style="margin: 5px 0; font-size: 13px; color: #555;">
                <strong>Email:</strong> info@Umoj Lutheran Hostelhotel.co.tz / infoUmoj Lutheran Hostelhotel@gmail.com
            </p>
        </div>
        <p style="margin-top: 15px; font-size: 16px; font-weight: bold; color: #e07632;">Kitchen & Food Management Report</p>
    </div>
    
    <!-- Report Number -->
    <div class="receipt-report-number">
        <strong>Report #:</strong> RST-RPT-{{ date('Ymd') }}-{{ strtoupper(substr(md5('res' . now()), 0, 6)) }}
    </div>
    
    <!-- Receipt Title -->
    <div class="receipt-report-title">
        OPERATIONAL REPORT (KITCHEN & FOOD)
    </div>
    
    <!-- Print Button -->
    <div class="print-button-section">
        <button onclick="printReport()">
            <i class="fa fa-print"></i> Print / Save as PDF
        </button>
        <div style="font-size: 11px; color: #333; margin-top: 10px; background: #fff3cd; padding: 10px; border-radius: 4px; border-left: 3px solid #ffc107;">
            <strong><i class="fa fa-info-circle"></i> Important:</strong> To remove URLs and headers from the printed report:<br>
            <strong>Chrome/Edge:</strong> In print dialog → More settings → Uncheck "Headers and footers"<br>
            <strong>Firefox:</strong> In print dialog → More Settings → Headers & Footers → Select "--blank--"<br>
            <strong>Safari:</strong> In print options → Uncheck "Print headers and footers"
        </div>
    </div>
    
    <!-- Report Information -->
    <div class="receipt-two-column">
        <div class="receipt-column">
            <div class="receipt-info-section">
                <h3>Report Information</h3>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Report Type:</span>
                    <span class="receipt-info-value"><strong>{{ ucfirst($dateType) }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Date Sequence:</span>
                    <span class="receipt-info-value">{{ \Carbon\Carbon::parse($customDate)->format('F d, Y') }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Generated on:</span>
                    <span class="receipt-info-value">{{ \Carbon\Carbon::now()->format('F d, Y \a\t g:i A') }}</span>
                </div>
                 <div class="receipt-info-row">
                    <span class="receipt-info-label">Generated by:</span>
                    <span class="receipt-info-value">{{ auth('staff')->user()->name }} (Manager)</span>
                </div>
            </div>
        </div>
        
        <div class="receipt-column">
            <div class="receipt-info-section">
                <h3>Statistics Summary {{ $role === 'head_chef' || \App\Services\RolePermissionService::hasRole(auth()->guard('staff')->user(), 'head_chef') ? '(Kitchen)' : '(All Outlets)' }}</h3>
                @if($role !== 'head_chef' && !\App\Services\RolePermissionService::hasRole(auth()->guard('staff')->user(), 'head_chef'))
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Recieved Stock:</span>
                    <span class="receipt-info-value" style="color: #007bff;"><strong>{{ number_format($totalReceivedBottles) }} Bottles</strong></span>
                </div>
                @endif
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Items Sold:</span>
                    <span class="receipt-info-value" style="color: #28a745;"><strong>{{ number_format($totalSoldItems) }} Units</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Sales Count:</span>
                    <span class="receipt-info-value" style="color: #666;"><strong>{{ $soldOrders->count() }} Orders</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">{{ $role === 'head_chef' || \App\Services\RolePermissionService::hasRole(auth()->guard('staff')->user(), 'head_chef') ? 'Kitchen' : 'Restaurant' }} Revenue:</span>
                    <span class="receipt-info-value" style="color: #e07632; font-size: 16px;"><strong>{{ number_format($totalRevenue) }} TZS</strong></span>
                </div>
                @if($role !== 'head_chef' && !\App\Services\RolePermissionService::hasRole(auth()->guard('staff')->user(), 'head_chef'))
                 <div class="receipt-info-row">
                    <span class="receipt-info-label">Issues (Exp/Dng):</span>
                    <span class="receipt-info-value" style="color: #dc3545;"><strong>{{ $totalExpired + $totalBroken }}</strong></span>
                </div>
                @endif
            </div>
        </div>
    </div>
    
        
    <!-- Sales Breakdown (Detailed Log) -->
    <div class="receipt-info-section">
        <h3>Detailed Sales Log (Kitchen & Bar)</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Completed At</th>
                    <th>Guest / Room</th>
                    <th>Item Name</th>
                    <th>Qty</th>
                    <th>Served By</th>
                    <th style="text-align: right;">Total (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($soldOrders as $order)
                @php
                    $foodId = $order->service_specific_data['food_id'] ?? null;
                    $recipe = $foodId ? \App\Models\Recipe::find($foodId) : null;
                    $itemName = $recipe ? $recipe->name : ($order->service_specific_data['item_name'] ?? $order->service->name);
                @endphp
                <tr>
                    <td>{{ $order->completed_at ? $order->completed_at->format('M d, H:i') : '-' }}</td>
                    <td>
                        <strong>{{ $order->booking->guest_name ?? 'Walk-in' }}</strong><br>
                        <small>Room: {{ $order->booking->room->room_number ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $itemName }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td>{{ $order->approvedBy->name ?? 'Staff' }}</td>
                    <td style="text-align: right;"><strong>{{ number_format($order->total_price_tsh) }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No sales recorded for this period.</td>
                </tr>
                @endforelse
                <tr class="total-row" style="background-color: #f8f9fa; border-top: 2px solid #e07632;">
                    <td colspan="5" style="text-align: right;"><strong>TOTAL REVENUE:</strong></td>
                    <td style="text-align: right;"><strong style="color: #e07632; font-size: 14px;">{{ number_format($totalRevenue) }} TZS</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Received Stock -->
    @if($receivedTransfers->count() > 0)
    <div class="receipt-info-section">
        <h3>Stock Received Log</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Product</th>
                    <th>Variant</th>
                    <th>Quantity</th>
                    <th>Received By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receivedTransfers as $transfer)
                @php
                    $itemsPerPackage = $transfer->productVariant->items_per_package ?? 1;
                    $bottles = ($transfer->quantity_unit == 'packages') 
                        ? $transfer->quantity_transferred * $itemsPerPackage 
                        : $transfer->quantity_transferred;
                @endphp
                <tr>
                    <td>{{ $transfer->received_at->format('M d H:i') }}</td>
                    <td>{{ $transfer->product->name }}</td>
                    <td>
                        {{ $transfer->productVariant->measurement }}<br>
                        <small style="color: #999;">{{ $transfer->quantity_unit }}</small>
                    </td>
                    <td>
                        <strong>{{ $transfer->quantity_transferred }} {{ $transfer->quantity_unit }}</strong><br>
                        <small style="color: #666;">({{ $bottles }} bottles)</small>
                    </td>
                    <td>{{ $transfer->receivedBy->name ?? 'Unknown' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Physical Stock Status (Manager Only - Bar/Restaurant Drinks) -->
    @if($role !== 'head_chef' && !\App\Services\RolePermissionService::hasRole(auth()->guard('staff')->user(), 'head_chef'))
    <div class="receipt-info-section">
        <h3>Physical Stock Status (Current - All Outlets)</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Variant</th>
                    <th style="text-align: center;">In (Tot)</th>
                    <th style="text-align: center;">Out (Est)</th>
                    <th style="text-align: right;">Stock</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($physicalStock) && count($physicalStock) > 0)
                    @foreach($physicalStock as $item)
                    <tr>
                        <td>{{ $item['product_name'] }}</td>
                        <td>{{ $item['variant_name'] }}</td>
                        <td style="text-align: center;">{{ number_format($item['total_received']) }}</td>
                        <td style="text-align: center;">{{ number_format($item['total_sold']) }}</td>
                        <td style="text-align: right;">
                            <strong style="color: {{ $item['current_stock'] <= 5 ? '#dc3545' : '#28a745' }};">
                                {{ number_format($item['current_stock']) }}
                            </strong>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">No physical stock data available.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Ingredient Tracking (Consumption) -->
    <div class="receipt-info-section">
        <h3>Ingredient Track (Kitchen Consumption - This Period)</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Ingredient Name</th>
                    <th style="text-align: center;">Total Consumed</th>
                    <th style="text-align: center;">Available Stock</th>
                    <th>Unit</th>
                    <th style="width: 25%">Usage Context</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingredientConsumption as $con)
                <tr>
                    <td><strong>{{ $con->name }}</strong></td>
                    <td style="text-align: center;">{{ number_format($con->total_consumed, 2) }}</td>
                    <td style="text-align: center;">
                        <strong style="color: {{ $con->available_stock <= 5 ? '#dc3545' : '#28a745' }};">
                            {{ number_format($con->available_stock, 2) }}
                        </strong>
                    </td>
                    <td>{{ $con->unit }}</td>
                    <td><small class="text-muted">Deducted from recipe meals served</small></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No ingredient consumption recorded for this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Complete Kitchen Inventory -->
    <div class="receipt-info-section">
        <h3>Complete Kitchen Inventory (All Ingredients)</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Ingredient Name</th>
                    <th>Category</th>
                    <th style="text-align: center;">Total Received</th>
                    <th style="text-align: center;">Total Consumed</th>
                    <th style="text-align: center;">Available Stock</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allKitchenIngredients as $ingredient)
                <tr>
                    <td><strong>{{ $ingredient->name }}</strong></td>
                    <td><small class="text-muted">{{ $ingredient->category }}</small></td>
                    <td style="text-align: center;">{{ number_format($ingredient->total_received, 2) }}</td>
                    <td style="text-align: center;">{{ number_format($ingredient->total_consumed, 2) }}</td>
                    <td style="text-align: center;">
                        <strong style="color: {{ $ingredient->available_stock <= 5 ? '#dc3545' : ($ingredient->available_stock <= 20 ? '#ffc107' : '#28a745') }};">
                            {{ number_format($ingredient->available_stock, 2) }}
                        </strong>
                    </td>
                    <td>{{ $ingredient->unit }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No kitchen ingredients inventory available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-left: 3px solid #e07632;">
            <small>
                <strong>Legend:</strong> 
                <span style="color: #dc3545;">● Critical (≤5)</span> | 
                <span style="color: #ffc107;">● Low (≤20)</span> | 
                <span style="color: #28a745;">● Good (>20)</span>
            </small>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="receipt-footer">
        <p><strong>Umoj Lutheran Hostel</strong></p>
        <p>This report has been generated automatically by the Umoj Lutheran Hostel Management System.</p>
        <p style="margin-top: 15px;">This is an official report. Please keep this for your records.</p>
        <p style="margin-top: 10px; font-size: 9px;">Generated on: {{ now()->format('F d, Y \a\t g:i A') }}</p>
        <p class="powered-by">Powered By <strong>EmCa Techonologies</strong></p>
    </div>
</div>

@endsection

@section('scripts')
<script>
function toggleDateInputs() {
  const dateType = document.getElementById('date_type').value;
  const singleDateContainer = document.getElementById('singleDateContainer');
  const dateInput = document.getElementById('date');
  
  if (!dateInput.value) {
      dateInput.value = new Date().toISOString().split('T')[0];
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  toggleDateInputs();
});

function setQuickReport(dateType, date) {
  document.getElementById('date_type').value = dateType;
  document.getElementById('date').value = date;
  document.getElementById('reportForm').submit();
}

function printReport() {
  // Hide elements that shouldn't be printed
  const printButton = document.querySelector('.print-button-section');
  if (printButton) {
    printButton.style.display = 'none';
  }
  
  // Hide the form and other non-report elements
  const appTitle = document.querySelector('.app-title');
  const appBreadcrumb = document.querySelector('.app-breadcrumb');
  const formTiles = document.querySelectorAll('.tile');
  formTiles.forEach(tile => {
    if (tile.querySelector('form')) {
      tile.style.display = 'none';
    }
  });
  
  if (appTitle) appTitle.style.display = 'none';
  if (appBreadcrumb) appBreadcrumb.style.display = 'none';
  
  // Trigger print dialog
  window.print();
  
  // Restore elements after print dialog closes
  setTimeout(function() {
    if (printButton) {
      printButton.style.display = 'block';
    }
    formTiles.forEach(tile => {
      if (tile.querySelector('form')) {
        tile.style.display = '';
      }
    });
    if (appTitle) appTitle.style.display = '';
    if (appBreadcrumb) appBreadcrumb.style.display = '';
  }, 250);
}
</script>
@endsection
