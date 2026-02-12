@extends('dashboard.layouts.app')

@section('content')
<div class="app-title d-print-none">
  <div>
    <h1><i class="fa fa-cutlery"></i> Kitchen & Production Reports</h1>
    <p>View kitchen inventory, consumption, and meal production reports</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('chef-master.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Kitchen Reports</li>
  </ul>
</div>

<!-- Report Type and Date Filter -->
<div class="row mb-3 d-print-none">
  <div class="col-md-12">
    <div class="tile shadow-sm border-0">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Generate Report</h3>
      </div>
      <div class="tile-body">
        <form method="GET" action="{{ route('chef-master.reports') }}" id="reportForm">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="date_type"><strong>Report Type:</strong></label>
                <select name="date_type" id="date_type" class="form-control">
                  <option value="daily" {{ ($reportType ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily Stock Sheet</option>
                  <option value="weekly" {{ ($reportType ?? '') == 'weekly' ? 'selected' : '' }}>Weekly Summary</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="date"><strong>Select Date:</strong></label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $date->toDateString() }}">
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block shadow-sm">
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
                    Today
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickReport('weekly', '{{ today()->format('Y-m-d') }}')">
                    This Week
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

<style>
.receipt-report-container {
    max-width: 900px;
    margin: 30px auto;
    background: #fff;
    padding: 40px;
    border: 1px solid #ddd;
    position: relative;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.receipt-report-container::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    width: 600px;
    height: 600px;
    background-image: url('{{ asset('dashboard_assets/images/Logo.png') }}');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    opacity: 0.08;
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
}

.receipt-report-header h1 {
    color: #e07632;
    font-size: 32px;
    margin-bottom: 5px;
    font-weight: 900;
    letter-spacing: 2px;
}

.receipt-report-header p {
    color: #444;
    font-size: 14px;
    margin: 2px 0;
}

.receipt-report-title {
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 30px;
    color: #333;
    text-transform: uppercase;
    text-decoration: underline;
}

.receipt-report-number {
    text-align: right;
    margin-bottom: 20px;
    color: #666;
    font-size: 11px;
}

.print-button-section {
    text-align: center;
    margin-bottom: 30px;
}

.print-button-section button {
    background-color: #e07632;
    color: #fff;
    border: none;
    padding: 12px 40px;
    font-size: 15px;
    cursor: pointer;
    border-radius: 4px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(224, 118, 50, 0.3);
    transition: all 0.3s ease;
}

.print-button-section button:hover {
    background-color: #c86528;
    transform: translateY(-2px);
}

.receipt-info-section {
    margin-bottom: 30px;
}

.receipt-info-section h3 {
    color: #e07632;
    font-size: 15px;
    border-bottom: 2px solid #e07632;
    padding-bottom: 8px;
    margin-bottom: 15px;
    font-weight: bold;
    text-transform: uppercase;
}

.receipt-info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.receipt-info-label {
    font-weight: bold;
    color: #555;
}

.receipt-info-value {
    color: #333;
    text-align: right;
}

.receipt-two-column {
    display: flex;
    gap: 40px;
    margin-bottom: 30px;
}

.receipt-column {
    flex: 1;
}

.receipt-details-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
}

.receipt-details-table th {
    background-color: #f8f9fa;
    color: #333;
    padding: 10px 12px;
    text-align: center;
    border: 1px solid #333;
    font-weight: bold;
    font-size: 11px;
    text-transform: uppercase;
}

.receipt-details-table td {
    padding: 10px 12px;
    border: 1px solid #333;
    font-size: 11px;
}

.receipt-details-table tbody tr.category-row {
    background-color: #fcece2 !important;
}

.total-row {
    background-color: #f8f9fa;
    font-weight: bold;
    font-size: 13px;
}

.status-badge {
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: bold;
    font-size: 9px;
    text-transform: uppercase;
}

.receipt-footer {
    margin-top: 50px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    text-align: center;
    color: #666;
    font-size: 11px;
}

.signature-grid {
    display: flex;
    justify-content: space-between;
    margin-top: 60px;
    padding: 0 40px;
}

.signature-box {
    width: 200px;
    text-align: center;
}

.signature-line {
    border-top: 2px solid #333;
    margin-bottom: 10px;
}

@media print {
    @page { margin: 5mm; size: A4; }
    body { background: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .d-print-none, .app-header, .app-sidebar, .app-title, .tile:has(form) { display: none !important; }
    
    .receipt-report-container { 
        border: none !important; 
        box-shadow: none !important; 
        max-width: 100% !important; 
        margin: 0 !important; 
        padding: 0 !important; 
    }
    
    .receipt-report-container::before { opacity: 0.05 !important; }
    
    /* Compact Layout for Print */
    .receipt-report-header { margin-bottom: 10px !important; padding-bottom: 5px !important; border-bottom-width: 2px !important; }
    .receipt-report-header h1 { font-size: 24px !important; margin-bottom: 2px !important; }
    .receipt-report-header p { font-size: 10px !important; margin: 0 !important; }
    .receipt-report-header .logo-container img { max-height: 50px !important; }
    
    .receipt-report-title { margin-bottom: 10px !important; font-size: 16px !important; margin-top: 5px !important; }
    .receipt-report-number { margin-bottom: 10px !important; font-size: 9px !important; }
    
    .receipt-info-section { margin-bottom: 10px !important; page-break-inside: avoid; }
    .receipt-info-section h3 { font-size: 12px !important; margin-bottom: 5px !important; padding-bottom: 3px !important; }
    
    .receipt-two-column { margin-bottom: 10px !important; gap: 20px !important; }
    
    .receipt-details-table { border-collapse: collapse !important; width: 100% !important; }
    .receipt-details-table th { 
        padding: 4px 6px !important; 
        font-size: 9px !important; 
        border: 1px solid #000 !important; 
        background-color: #f8f9fa !important; 
        -webkit-print-color-adjust: exact !important; 
    }
    .receipt-details-table td { 
        padding: 4px 6px !important; 
        font-size: 9px !important; 
        border: 1px solid #000 !important; 
    }
    
    .receipt-details-table tbody tr.category-row { 
        background-color: #fcece2 !important; 
        -webkit-print-color-adjust: exact !important;
    }
    .receipt-details-table tbody tr.category-row td {
        background-color: #fcece2 !important; 
    }

    .total-row td { 
        font-size: 10px !important; 
        padding: 5px !important; 
        border: 1px solid #000 !important; 
        background-color: #f0f0f0 !important;
        -webkit-print-color-adjust: exact !important;
    }
    
    /* Summary Section Compact */
    .receipt-info-section[style*="margin-top: 40px"] { margin-top: 15px !important; padding: 10px !important; }
    .receipt-info-section h3[style*="border-bottom: none"] { margin-bottom: 5px !important; }
    
    .signature-grid { margin-top: 20px !important; padding: 0 20px !important; }
    .signature-box { width: 150px !important; }
    .receipt-footer { margin-top: 20px !important; padding-top: 10px !important; }
}

.verified-header-stamp {
    display: inline-block;
    color: #28a745;
    border: 4px solid #28a745;
    padding: 2px 10px;
    font-weight: 900;
    font-size: 20px;
    text-transform: uppercase;
    transform: rotate(-10deg);
    opacity: 0.9;
    margin-left: 15px;
    vertical-align: middle;
    letter-spacing: 2px;
    border-radius: 4px;
}
</style>

<div class="receipt-report-container">
    <!-- Header -->
    <div class="receipt-report-header">
        <div class="logo-container">
            <img src="{{ asset('dashboard_assets/images/Logo.png') }}" alt="PrimeLand Logo">
        </div>
        <h1>PRIMELAND HOTEL</h1>
        <div style="line-height: 1.6;">
            <p><strong>Location:</strong> Sokoine Road - Moshi, Kilimanjaro - Tanzania</p>
            <p><strong>Mobile/WhatsApp:</strong> 0677-155-156 / +255 677-155-157</p>
            <p><strong>Email:</strong> info@primelandhotel.co.tz / infoprimelandhotel@gmail.com</p>
        </div>
        <p style="margin-top: 15px; font-size: 18px; font-weight: bold; color: #e07632;">Kitchen Operations Report</p>
    </div>
    
    <!-- Report Number -->
    <div class="receipt-report-number">
        <strong>Report #:</strong> KIT-RPT-{{ date('Ymd') }}-{{ strtoupper(substr(md5('kitchen' . $startDate . $endDate), 0, 6)) }}
    </div>
    
    <!-- Receipt Title -->
    <div class="receipt-report-title">
        @if($reportType == 'weekly')
            WEEKLY KITCHEN INVENTORY & PRODUCTION SUMMARY
        @else
            DAILY KITCHEN STOCK & PRODUCTION SHEET
        @endif
        <span class="verified-header-stamp">OFFICIAL</span>
    </div>
    
    <!-- Print Button (D-Print-None) -->
    <div class="print-button-section d-print-none">
        <button onclick="window.print()">
            <i class="fa fa-print"></i> Print / Save as PDF
        </button>
    </div>
    
    <!-- Report Information -->
    <div class="receipt-two-column">
        <div class="receipt-column">
            <div class="receipt-info-section">
                <h3>Report Information</h3>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Report Type:</span>
                    <span class="receipt-info-value"><strong>{{ $reportType == 'weekly' ? 'Weekly Summary' : 'Daily Stock Sheet' }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Period Start:</span>
                    <span class="receipt-info-value">{{ $startDate->format('F d, Y') }}</span>
                </div>
                @if($reportType == 'weekly')
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Period End:</span>
                    <span class="receipt-info-value">{{ $endDate->format('F d, Y') }}</span>
                </div>
                @endif
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Generated By:</span>
                    <span class="receipt-info-value">{{ auth('staff')->user()->name }}</span>
                </div>
            </div>
        </div>
        
        <div class="receipt-column">
            <div class="receipt-info-section">
                <h3>Production Statistics</h3>
                @php 
                    $totalProdQty = $productionData->sum('total_qty'); 
                    $totalRev = $productionData->sum('total_revenue');
                    $eventQty = isset($ceremonyUsage) ? $ceremonyUsage->sum('quantity') : 0;
                    $eventRev = isset($ceremonyUsage) ? $ceremonyUsage->sum('total_price_tsh') : 0;
                @endphp
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Total Portions Sold:</span>
                    <span class="receipt-info-value" style="color: #28a745;"><strong>{{ number_format($totalProdQty + $eventQty) }} Items</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Menu Items Sold:</span>
                    <span class="receipt-info-value" style="color: #666;"><strong>{{ $productionData->count() + (isset($ceremonyUsage) ? $ceremonyUsage->groupBy('service_id')->count() : 0) }} Variants</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Food Revenue:</span>
                    <span class="receipt-info-value" style="color: #e07632; font-size: 16px;"><strong>{{ number_format($totalRev + $eventRev) }} TZS</strong></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 1. Stock Movements -->
    <div class="receipt-info-section">
        <h3>1. Inventory Stock Movements</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th style="text-align: left;">Item / Ingredient</th>
                    <th>Opening Stock</th>
                    <th>New Receipts</th>
                    <th>Usage / Consumed</th>
                    <th>Lost / Spoiled</th>
                    <th>Expiry</th>
                    <th style="text-align: right;">Closing Balance</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $groupedReport = collect($reportData)->groupBy(function($item) {
                        return match($item->category) {
                            'meat_poultry' => 'Meat & Poultry',
                            'seafood' => 'Seafood & Fish',
                            'vegetables' => 'Vegetables & Fruits',
                            'dairy' => 'Dairy & Eggs',
                            'pantry_baking' => 'Pantry & Baking',
                            'oils_fats' => 'Oils & Fats',
                            'other' => 'Other Supplies',
                            default => ucfirst(str_replace('_', ' ', $item->category ?? 'Other'))
                        };
                    });
                @endphp

                @foreach($groupedReport as $category => $items)
                <tr class="category-row">
                    <td colspan="7"><strong>{{ $category }}</strong></td>
                    <td style="text-align: right;"><small>{{ $items->count() }} items</small></td>
                </tr>
                @foreach($items as $item)
                <tr>
                    <td style="padding-left: 20px;">
                        {{ $item->name }} <small class="text-muted">({{ $item->unit }})</small>
                        @if($item->closing_stock <= 0)
                            <span class="status-badge" style="color: #dc3545;">[OUT]</span>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ number_format($item->opening_stock, 2) }}</td>
                    <td style="text-align: center; color: #007bff;">{{ number_format($item->received, 2) }}</td>
                    <td style="text-align: center; color: #6c757d;">{{ number_format($item->usage, 2) }}</td>
                    <td style="text-align: center; color: #dc3545;">{{ number_format($item->lost, 2) }}</td>
                    <td style="text-align: center;">
                        @if($item->expiry_date)
                            @php
                                $daysToExpiry = now()->diffInDays($item->expiry_date, false);
                            @endphp
                            <span class="{{ $daysToExpiry <= 10 ? 'text-danger font-weight-bold' : '' }}">
                                {{ $item->expiry_date->format('d M, Y') }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td style="text-align: right; background-color: #f8f9fa;"><strong>{{ number_format($item->closing_stock, 2) }}</strong></td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- 2. Sales Summary -->
    <div class="receipt-info-section">
        <h3>2. Direct Sales / Production (Walk-in / Room Service)</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th style="text-align: left; width: 120px;">Time / Status</th>
                    <th style="text-align: left;">Guest / Destination</th>
                    <th style="text-align: left;">Items Ordered</th>
                    <th style="text-align: center; width: 60px;">Qty</th>
                    <th style="text-align: right; width: 100px;">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedProduction = collect($productionData)->groupBy('destinations');
                @endphp

                @forelse($groupedProduction as $destName => $orders)
                    @php
                        $firstOrder = $orders->first();
                        $groupTotal = $orders->sum('total_revenue');
                    @endphp
                    <tr>
                        <td rowspan="{{ $orders->count() + 1 }}" style="vertical-align: top;">
                            <span class="font-weight-bold">{{ $firstOrder->time }}</span><br>
                            @if($firstOrder->payment_status === 'paid')
                                <span class="status-badge" style="color: #28a745; border: 1px solid #28a745;">PAID</span>
                                <br><small>{{ strtoupper(str_replace('_', ' ', $firstOrder->payment_method ?? 'CASH')) }}</small>
                                @if($firstOrder->payment_reference)
                                    <br><small class="text-muted">{{ $firstOrder->payment_reference }}</small>
                                @endif
                            @elseif($firstOrder->payment_status === 'room_charge')
                                <span class="status-badge" style="color: #17a2b8; border: 1px solid #17a2b8;">ROOM CHARGE</span>
                            @else
                                <span class="status-badge" style="color: #dc3545; border: 1px solid #dc3545;">PENDING</span>
                            @endif
                        </td>
                        <td rowspan="{{ $orders->count() + 1 }}" style="vertical-align: top;">
                            <strong>{{ $firstOrder->guest_label }}</strong><br>
                            <small class="text-muted">{{ $destName }}</small>
                        </td>
                    </tr>
                    @foreach($orders as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td style="text-align: center;">{{ $item->total_qty }}</td>
                        <td style="text-align: right;">{{ number_format($item->total_revenue) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #fcfcfc; border-bottom: 2px solid #eee;">
                        <td colspan="4" style="text-align: right; font-weight: bold; padding: 5px 12px;">SUBTOTAL:</td>
                        <td style="text-align: right; font-weight: bold; padding: 5px 12px; color: #e07632;">{{ number_format($groupTotal) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 30px;">No production records found for this period.</td>
                    </tr>
                @endforelse

                @if($productionData->count() > 0)
                <tr class="total-row" style="background-color: #f8f9fa;">
                    <td colspan="4" style="text-align: right; font-size: 14px;"><strong>TOTAL DIRECT SALES:</strong></td>
                    <td style="text-align: right; color: #e07632; font-size: 16px;"><strong>{{ number_format($totalRev) }} TZS</strong></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- 3. Ceremony / Event Consumption -->
    @if(isset($ceremonyUsage) && $ceremonyUsage->count() > 0)
    <div class="receipt-info-section">
        <h3>3. Ceremony / Event Consumption</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th style="text-align: left;">Reference / Guest</th>
                    <th style="text-align: left;">Item</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: left;">Status / Payment</th>
                    <th style="text-align: right;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ceremonyUsage as $usage)
                <tr>
                    <td>
                        <strong>{{ $usage->dayService->service_reference ?? 'N/A' }}</strong><br>
                        <small>{{ $usage->dayService->guest_name ?? 'Unknown' }}</small>
                    </td>
                    <td>
                        {{ $usage->service_specific_data['item_name'] ?? $usage->service->name ?? 'Unknown Item' }}<br>
                        <small class="text-muted">{{ $usage->created_at->format('H:i') }}</small>
                    </td>
                    <td style="text-align: center;"><strong>{{ $usage->quantity }}</strong></td>
                    <td style="font-size: 10px;">
                        @if($usage->payment_status === 'paid' || $usage->status === 'completed')
                            <span class="status-badge" style="color: #28a745; border: 1px solid #28a745; padding: 1px 4px;">PAID</span>
                            @if($usage->payment_method)
                                <br><span class="text-dark">{{ ucfirst(str_replace('_', ' ', $usage->payment_method)) }}</span>
                            @endif
                            @if($usage->payment_provider)
                                <span class="text-muted">({{ $usage->payment_provider }})</span>
                            @endif
                        @else
                            <span class="status-badge" style="color: #dc3545; border: 1px solid #dc3545; padding: 1px 4px;">UNPAID</span>
                        @endif
                    </td>
                    <td style="text-align: right;">{{ number_format($usage->total_price_tsh) }}</td>
                </tr>
                @endforeach
                
                @php
                    $totalEvents = $ceremonyUsage->sum('total_price_tsh');
                    $paidEvents = $ceremonyUsage->where('payment_status', 'paid')->sum('total_price_tsh');
                    $unpaidEvents = $totalEvents - $paidEvents;
                @endphp
                
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">TOTAL EVENTS CONSUMPTION:</td>
                    <td style="text-align: right; color: #17a2b8;">{{ number_format($totalEvents) }} TZS</td>
                </tr>
                
                <!-- Payment Breakdown Summary -->
                <tr style="background-color: #fff;">
                    <td colspan="5" style="padding: 15px; border: 1px solid #ddd;">
                        <div style="display: flex; justify-content: flex-end; gap: 20px;">
                            <div style="text-align: right;">
                                <small class="text-muted">PAID AMOUNT</small><br>
                                <strong style="color: #28a745;">{{ number_format($paidEvents) }} TZS</strong>
                            </div>
                            <div style="text-align: right;">
                                <small class="text-muted">UNPAID AMOUNT</small><br>
                                <strong style="color: #dc3545;">{{ number_format($unpaidEvents) }} TZS</strong>
                            </div>
                            <div style="text-align: right; border-left: 2px solid #eee; padding-left: 20px;">
                                <small class="text-muted">TOTAL FOR DAY</small><br>
                                <strong style="color: #333;">{{ number_format($totalEvents) }} TZS</strong>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <!-- 4. Global Financial Summary -->
    <div class="receipt-info-section" style="margin-top: 40px; background-color: #f8f9fa; border: 1px solid #ddd; padding: 20px;">
        <h3 style="border-bottom: none; margin-bottom: 20px; text-align: center; color: #333;">DAILY FINANCIAL SUMMARY</h3>
        
        <div style="display: flex; justify-content: space-around; align-items: center;">
            <div style="text-align: center;">
                <span style="font-size: 12px; color: #666; text-transform: uppercase;">Direct Production</span><br>
                <strong style="font-size: 18px; color: #007bff;">{{ number_format($totalRev) }} TZS</strong>
            </div>
            <div style="font-size: 20px; color: #999;">+</div>
            <div style="text-align: center;">
                <span style="font-size: 12px; color: #666; text-transform: uppercase;">Events Consumption</span><br>
                <strong style="font-size: 18px; color: #17a2b8;">{{ isset($ceremonyUsage) ? number_format($ceremonyUsage->sum('total_price_tsh')) : '0' }} TZS</strong>
            </div>
            <div style="font-size: 20px; color: #999;">=</div>
            <div style="text-align: center; border: 2px solid #28a745; padding: 10px 20px; background: #fff; border-radius: 5px;">
                <span style="font-size: 12px; color: #28a745; text-transform: uppercase; font-weight: bold;">TOTAL KITCHEN REVENUE</span><br>
                <strong style="font-size: 24px; color: #28a745;">{{ isset($ceremonyUsage) ? number_format($totalRev + $ceremonyUsage->sum('total_price_tsh')) : number_format($totalRev) }} TZS</strong>
            </div>
        </div>
    </div>
    
    <!-- Signature Grid -->
    <div class="signature-grid">
        <div class="signature-box">
            <div class="signature-line"></div>
            <strong>CHEF MASTER</strong>
            <p style="font-size: 11px; margin-top: 5px;">(Signature & Date)</p>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <strong>HOTEL MANAGER</strong>
            <p style="font-size: 11px; margin-top: 5px;">(Signature & Date)</p>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="receipt-footer">
        <p><strong>PrimeLand Hotel Management System</strong></p>
        <p>This is an official kitchen production and stock report generated for auditing purposes.</p>
        <p class="powered-by" style="color: #940000; margin-top: 15px; font-weight: bold;">Powered By EmCa Technologies</p>
    </div>
</div>

@endsection

@section('scripts')
<script>
function setQuickReport(dateType, date) {
    document.getElementById('date_type').value = dateType;
    document.getElementById('date').value = date;
    document.getElementById('reportForm').submit();
}
</script>
@endsection
