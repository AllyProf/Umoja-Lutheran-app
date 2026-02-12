@extends('dashboard.layouts.app')

@section('content')
<div class="app-title no-print">
  <div>
    <h1><i class="fa fa-bed"></i> Housekeeping Reports</h1>
    <p>View housekeeping inventory and operations reports</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('housekeeper.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
  </ul>
</div>

<!-- Report Type and Date Filter -->
<div class="row mb-3 no-print">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Generate Report</h3>
      </div>
      <div class="tile-body">
        <form method="GET" action="{{ route('housekeeper.reports') }}" id="reportForm">
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

.receipt-details-table tbody tr:hover {
    background-color: #f0f0f0;
}

.total-row {
    background-color: #f0f0f0 !important;
    font-weight: bold;
}

.receipt-footer {
    text-align: center;
    margin-top: 40px;
    padding-top: 20px;
    border-top: 2px solid #e07632;
    color: #666;
    font-size: 11px;
}

.receipt-footer .powered-by {
    margin-top: 15px;
    font-size: 10px;
    color: #999;
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
    .no-print, .print-button-section, .app-header, .app-sidebar, .app-title { display: none !important; }
    
    .receipt-report-container { 
        border: none !important; 
        box-shadow: none !important; 
        max-width: 100% !important; 
        margin: 0 !important; 
        padding: 0 !important; 
    }
    
    .receipt-report-header { margin-bottom: 10px !important; padding-bottom: 5px !important; border-bottom-width: 2px !important; }
    .receipt-report-header h1 { font-size: 24px !important; margin-bottom: 2px !important; }
    .receipt-report-number { margin-bottom: 10px !important; font-size: 9px !important; text-align: left; }
    
    .receipt-details-table { border-collapse: collapse !important; width: 100% !important; }
    .receipt-details-table th, .receipt-details-table td {
        border: 1px solid #000 !important;
        padding: 4px 6px !important;
        font-size: 10px !important;
    }
    .receipt-details-table th { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact !important; }
    
    .receipt-info-row { margin-bottom: 5px !important; }
    
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
        </div>
        <p style="margin-top: 15px; font-size: 16px; font-weight: bold; color: #e07632;">Housekeeping Inventory Report</p>
    </div>
    
    <!-- Report Number -->
    <div class="receipt-report-number">
        <strong>Report #:</strong> HK-RPT-{{ date('Ymd') }}-{{ strtoupper(substr(md5('housekeeping' . now()), 0, 6)) }}
    </div>
    
    <!-- Receipt Title -->
    <div class="receipt-report-title">
        INVENTORY OPERATIONS REPORT (HOUSEKEEPING)
        <span class="verified-header-stamp">OFFICIAL</span>
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
                    <span class="receipt-info-label">Date Range:</span>
                    <span class="receipt-info-value">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Generated on:</span>
                    <span class="receipt-info-value">{{ \Carbon\Carbon::now()->format('F d, Y \a\t g:i A') }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Generated by:</span>
                    <span class="receipt-info-value">{{ auth('staff')->user()->name }}</span>
                </div>
            </div>
        </div>
        
        <div class="receipt-column">
            <div class="receipt-info-section">
                <h3>Statistics Summary</h3>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Items Received:</span>
                    <span class="receipt-info-value" style="color: #007bff;">
                        <strong>{{ number_format($totalReceivedItems) }} Items</strong><br>
                        <small style="color: #666;">{{ number_format($totalReceivedQuantity, 2) }} Units</small>
                    </span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Items Consumed:</span>
                    <span class="receipt-info-value" style="color: #dc3545;"><strong>{{ number_format($totalConsumed, 2) }} Units</strong></span>
                </div>

                <div class="receipt-info-row">
                    <span class="receipt-info-label">Low Stock Items:</span>
                    <span class="receipt-info-value" style="color: #dc3545;"><strong>{{ $lowStockItems->count() }}</strong></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Items Received -->
    @if($receivedItems->count() > 0)
    <div class="receipt-info-section">
        <h3>Items Received from Purchases</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receivedItems as $item)
                <tr>
                    <td>{{ $item->received_by_department_at->format('M d, H:i') }}</td>
                    <td><strong>{{ $item->product_name }}</strong></td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->category ?? 'other')) }}</td>
                    <td>{{ number_format($item->purchased_quantity ?? 0, 2) }}</td>
                    <td>{{ $item->unit }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3"><strong>Total</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($totalReceivedQuantity, 2) }}</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Stock Movements -->
    @if($stockMovements->count() > 0)
    <div class="receipt-info-section">
        <h3>Stock Movements</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>Item Name</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Room</th>
                    <th>Performed By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockMovements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('M d, H:i') }}</td>
                    <td><strong>{{ $movement->inventoryItem->name ?? 'N/A' }}</strong></td>
                    <td>
                        @if($movement->movement_type === 'consumption')
                            <span class="badge badge-danger">Consumption</span>
                        @elseif($movement->movement_type === 'transfer')
                            <span class="badge badge-warning">Transfer</span>
                        @endif
                    </td>
                    <td>{{ number_format($movement->quantity, 2) }} {{ $movement->inventoryItem->unit ?? '' }}</td>
                    <td>
                        @if($movement->room)
                            Room {{ $movement->room->room_number }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $movement->performedBy->name ?? 'N/A' }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3"><strong>Total Used</strong></td>
                    <td style="text-align: right;"><strong style="color: #dc3545;">{{ number_format($totalUsed, 2) }} Units</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Current Inventory Status -->
    <div class="receipt-info-section">
        <h3>Current Inventory Status</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th style="text-align: center;">Opening</th>
                    <th style="text-align: center;">Inn</th>
                    <th style="text-align: center;">Consumed</th>
                    <th style="text-align: center;">Expired (Qty)</th>
                    <th style="text-align: center;">Expiry Date</th>
                    <th style="text-align: center;">Closing Stock</th>
                    <th style="text-align: right;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventoryItems as $item)
                <tr>
                    <td><strong>{{ $item->name }}</strong></td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                    <td style="text-align: center;">{{ number_format($item->opening_stock, 2) }}</td>
                    <td style="text-align: center; color: #007bff;">{{ number_format($item->received_in_period, 2) }}</td>
                    <td style="text-align: center; color: #6c757d;">{{ number_format($item->consumed_in_period, 2) }}</td>
                    <td style="text-align: center; color: #dc3545;">{{ number_format($item->expired_in_period, 2) }}</td>
                    <td style="text-align: center;">
                        @if($item->expiry_date)
                            @php 
                                $days = now()->diffInDays(\Carbon\Carbon::parse($item->expiry_date), false);
                            @endphp
                            <span class="{{ $days <= 30 ? 'text-danger font-weight-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($item->expiry_date)->format('d M, Y') }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <strong style="color: {{ $item->closing_stock <= $item->minimum_stock ? '#dc3545' : '#28a745' }};">
                            {{ number_format($item->closing_stock, 2) }}
                        </strong>
                    </td>
                    <td style="text-align: right;">
                        @if($item->closing_stock <= 0)
                            <span class="badge badge-danger">Out of Stock</span>
                        @elseif($item->closing_stock <= $item->minimum_stock)
                            <span class="badge badge-warning">Low</span>
                        @else
                            <span class="badge badge-success">Good</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No inventory items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Low Stock Alert -->
    @if($lowStockItems->count() > 0)
    <div class="receipt-info-section">
        <h3 style="color: #dc3545;">⚠ Low Stock Alert</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th style="text-align: center;">Current Stock</th>
                    <th style="text-align: center;">Minimum Stock</th>
                    <th style="text-align: right;">Deficit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockItems as $item)
                <tr style="background-color: {{ $item->current_stock <= 0 ? '#ffebee' : '#fff3cd' }};">
                    <td><strong>{{ $item->name }}</strong></td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                    <td style="text-align: center; color: {{ $item->current_stock <= 0 ? '#dc3545' : '#ffc107' }};"><strong>{{ number_format($item->current_stock, 2) }} {{ $item->unit }}</strong></td>
                    <td style="text-align: center;">{{ number_format($item->minimum_stock, 2) }} {{ $item->unit }}</td>
                    <td style="text-align: right; color: #dc3545;">
                        <strong>{{ number_format(max(0, $item->minimum_stock - $item->current_stock), 2) }} {{ $item->unit }}</strong>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Signature Grid -->
    <div class="signature-grid">
        <div class="signature-box">
            <div class="signature-line"></div>
            <strong>HOUSEKEEPER</strong>
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
    // This function can be expanded if needed for different date input types
}

function setQuickReport(type, date) {
    document.getElementById('date_type').value = type;
    document.getElementById('date').value = date;
    document.getElementById('reportForm').submit();
}

function printReport() {
    window.print();
}
</script>
@endsection
