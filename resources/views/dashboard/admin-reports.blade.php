@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-bar-chart"></i> Reports</h1>
    <p>Comprehensive hotel performance reports</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
  </ul>
</div>

<!-- Report Type and Date Filter -->
<div class="row mb-3 report-filter-section">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Generate Report</h3>
      </div>
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.reports') }}" id="reportForm">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="report_type"><strong>Report Type:</strong></label>
                <select name="report_type" id="report_type" class="form-control" onchange="toggleDateInputs()">
                  <option value="daily" {{ ($reportType ?? 'monthly') == 'daily' ? 'selected' : '' }}>Daily</option>
                  <option value="weekly" {{ ($reportType ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                  <option value="monthly" {{ ($reportType ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                  <option value="yearly" {{ ($reportType ?? '') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                  <option value="custom" {{ ($reportType ?? '') == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
              </div>
            </div>
            <div class="col-md-3" id="singleDateContainer">
              <div class="form-group">
                <label for="date"><strong>Select Date:</strong></label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $reportDate ?? today()->format('Y-m-d') }}">
              </div>
            </div>
            <div class="col-md-3" id="startDateContainer" style="display: none;">
              <div class="form-group">
                <label for="start_date"><strong>Start Date:</strong></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate ?? '' }}">
              </div>
            </div>
            <div class="col-md-3" id="endDateContainer" style="display: none;">
              <div class="form-group">
                <label for="end_date"><strong>End Date:</strong></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate ?? '' }}">
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

.receipt-details-table .total-row {
    background-color: #f8f9fa !important;
    font-weight: bold;
}

.receipt-details-table .total-row td {
    font-size: 13px;
    padding: 15px 12px;
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

.amount-highlight {
    font-size: 20px;
    color: #e07632;
    font-weight: bold;
}

.text-right {
    text-align: right;
}

.recommendation-box {
    padding: 15px;
    margin-bottom: 15px;
    border-left: 4px solid;
    border-radius: 4px;
    background-color: #f8f9fa;
}

.recommendation-box.success {
    border-left-color: #28a745;
    background-color: #d4edda;
}

.recommendation-box.warning {
    border-left-color: #ffc107;
    background-color: #fff3cd;
}

.recommendation-box.danger {
    border-left-color: #dc3545;
    background-color: #f8d7da;
}

.recommendation-box.info {
    border-left-color: #17a2b8;
    background-color: #d1ecf1;
}

.recommendation-box h4 {
    margin: 0 0 8px 0;
    font-size: 14px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 8px;
}

.recommendation-box p {
    margin: 0;
    font-size: 12px;
    color: #333;
}

/* Print Styles */
@media print {
    /* Remove all margins and padding */
    @page {
        margin: 10mm !important;
        padding: 0 !important;
        size: A4;
    }
    
    /* Remove all page headers and footers */
    @page {
        @top-left { content: none !important; }
        @top-center { content: none !important; }
        @top-right { content: none !important; }
        @bottom-left { content: none !important; }
        @bottom-center { content: none !important; }
        @bottom-right { content: none !important; }
    }
    
    body {
        padding: 0 !important;
        margin: 0 !important;
        font-size: 11px !important;
        line-height: 1.4 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    html {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Hide all non-report elements */
    .app-title,
    .app-breadcrumb,
    .report-filter-section,
    .report-filter-section * {
        display: none !important;
    }
    
    /* Hide print button when printing */
    .print-button-section {
        display: none !important;
    }
    
    /* Ensure receipt container is visible and properly formatted */
    .receipt-report-container {
        border: none !important;
        padding: 10px 15px !important;
        margin: 0 !important;
        box-shadow: none !important;
        max-width: 100% !important;
        page-break-inside: avoid !important;
    }
    
    /* Optimize font sizes for print */
    .receipt-report-header h1 {
        font-size: 22px !important;
        margin-bottom: 3px !important;
    }
    
    .receipt-report-header p {
        font-size: 11px !important;
        margin: 2px 0 !important;
    }
    
    .receipt-report-title {
        font-size: 16px !important;
        margin-bottom: 15px !important;
    }
    
    .receipt-info-section {
        margin-bottom: 15px !important;
        page-break-inside: avoid !important;
    }
    
    .receipt-info-section h3 {
        font-size: 12px !important;
        padding-bottom: 5px !important;
        margin-bottom: 8px !important;
    }
    
    .receipt-info-row {
        margin-bottom: 5px !important;
        padding: 2px 0 !important;
        font-size: 10px !important;
    }
    
    .receipt-two-column {
        gap: 15px !important;
        margin-bottom: 15px !important;
    }
    
    .receipt-details-table {
        margin-bottom: 12px !important;
        font-size: 10px !important;
    }
    
    .receipt-details-table th,
    .receipt-details-table td {
        padding: 8px !important;
        font-size: 10px !important;
    }
    
    .receipt-details-table .total-row td {
        padding: 10px 8px !important;
        font-size: 11px !important;
    }
    
    .receipt-footer {
        margin-top: 20px !important;
        padding-top: 10px !important;
        font-size: 9px !important;
    }
    
    .receipt-footer p {
        margin: 3px 0 !important;
    }
    
    .receipt-report-number {
        margin-bottom: 10px !important;
        font-size: 10px !important;
    }
    
    .amount-highlight {
        font-size: 16px !important;
    }
    
    .recommendation-box {
        margin-bottom: 10px !important;
        padding: 10px !important;
    }
    
    .recommendation-box h4 {
        font-size: 12px !important;
        margin-bottom: 5px !important;
    }
    
    .recommendation-box p {
        font-size: 11px !important;
    }
    
    /* Prevent page breaks inside sections */
    .receipt-two-column,
    .receipt-info-section,
    .recommendation-box {
        page-break-inside: avoid !important;
    }
    
    /* Hide any browser-added headers/footers */
    body::before,
    body::after,
    html::before,
    html::after {
        display: none !important;
        content: none !important;
    }
    
    /* Prevent URL from being printed */
    a[href]:after,
    a[href]:before {
        content: none !important;
    }
    
    /* Ensure clean print output */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Remove any background elements except watermark */
    body > * {
        background: white !important;
    }
    
    /* Ensure watermark is visible in print */
    .receipt-report-container::before {
        opacity: 0.1 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>

<!-- Report Display (Receipt Style) -->
@if(isset($dateRange))
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
        <p style="margin-top: 15px; font-size: 16px; font-weight: bold; color: #e07632;">General Performance Report</p>
    </div>
    
    <!-- Report Number -->
    <div class="receipt-report-number">
        <strong>Report #:</strong> GEN-RPT-{{ date('Ymd') }}-{{ strtoupper(substr(md5(now()), 0, 6)) }}
    </div>
    
    <!-- Receipt Title -->
    <div class="receipt-report-title">
        COMPREHENSIVE HOTEL PERFORMANCE REPORT
    </div>
    
    <!-- Print Button -->
    <div class="print-button-section">
        <button onclick="window.print()">
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
                    <span class="receipt-info-label">Report Period:</span>
                    <span class="receipt-info-value"><strong>{{ $dateRange['label'] }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">From Date:</span>
                    <span class="receipt-info-value">{{ $dateRange['start']->format('F d, Y') }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">To Date:</span>
                    <span class="receipt-info-value">{{ $dateRange['end']->format('F d, Y') }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Generated on:</span>
                    <span class="receipt-info-value">{{ \Carbon\Carbon::now()->format('F d, Y \a\t g:i A') }}</span>
                </div>
            </div>
        </div>
        
        <div class="receipt-column">
            <div class="receipt-info-section">
                <h3>Overall Statistics</h3>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Total Bookings:</span>
                    <span class="receipt-info-value" style="color: #007bff;"><strong>{{ $stats['total_bookings'] ?? 0 }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Total Rooms:</span>
                    <span class="receipt-info-value" style="color: #17a2b8;"><strong>{{ $stats['total_rooms'] ?? 0 }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Occupancy Rate:</span>
                    <span class="receipt-info-value" style="color: #28a745;"><strong>{{ $stats['occupancy_rate'] ?? 0 }}%</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Total Revenue:</span>
                    <span class="receipt-info-value"><span class="amount-highlight">{{ number_format($totalRevenueAll ?? ($stats['total_revenue'] ?? 0), 0) }} TZS</span></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Operational Statistics -->
    <div class="receipt-info-section">
        <h3>Operational Statistics</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th style="text-align: right;">Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Checked In</td>
                    <td style="text-align: right;"><strong style="color: #007bff;">{{ $operationalStats['checked_in'] ?? 0 }}</strong></td>
                </tr>
                <tr>
                    <td>Checked Out</td>
                    <td style="text-align: right;"><strong style="color: #dc3545;">{{ $operationalStats['checked_out'] ?? 0 }}</strong></td>
                </tr>
                <tr>
                    <td>New Bookings</td>
                    <td style="text-align: right;"><strong style="color: #28a745;">{{ $stats['total_bookings'] ?? 0 }}</strong></td>
                </tr>
                <tr>
                    <td>Confirmed Bookings</td>
                    <td style="text-align: right;"><strong style="color: #28a745;">{{ $stats['confirmed_bookings'] ?? 0 }}</strong></td>
                </tr>
                <tr>
                    <td>Cancelled Bookings</td>
                    <td style="text-align: right;"><strong style="color: #dc3545;">{{ $stats['cancelled_bookings'] ?? 0 }}</strong></td>
                </tr>
                <tr>
                    <td>Occupancy Rate</td>
                    <td style="text-align: right;"><strong style="color: #17a2b8;">{{ $stats['occupancy_rate'] ?? 0 }}%</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Revenue Breakdown -->
    <div class="receipt-info-section">
        <h3>Revenue Breakdown</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Revenue Source</th>
                    <th style="text-align: right;">Amount (TZS)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Room Bookings</td>
                    <td style="text-align: right;"><strong>{{ number_format($stats['total_revenue'] ?? 0, 0) }} TZS</strong></td>
                </tr>
                <tr>
                    <td>Day Services</td>
                    <td style="text-align: right;"><strong>{{ number_format($dayServicesRevenue ?? 0, 0) }} TZS</strong></td>
                </tr>
                <tr>
                    <td>Service Requests</td>
                    <td style="text-align: right;"><strong>{{ number_format($serviceRequests['revenue'] ?? 0, 0) }} TZS</strong></td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Revenue</strong></td>
                    <td style="text-align: right;"><strong style="color: #e07632; font-size: 16px;">{{ number_format($totalRevenueAll ?? ($stats['total_revenue'] ?? 0), 0) }} TZS</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Day Services Statistics -->
    <div class="receipt-info-section">
        <h3>Day Services Statistics</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Service Type</th>
                    <th style="text-align: right;">Total</th>
                    <th style="text-align: right;">Paid</th>
                    <th style="text-align: right;">Pending</th>
                    <th style="text-align: right;">Revenue (TZS)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Swimming</td>
                    <td style="text-align: right;"><strong>{{ $dayServicesByType['swimming']['total'] ?? 0 }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #28a745;">{{ $dayServicesByType['swimming']['paid'] ?? 0 }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #ffc107;">{{ ($dayServicesByType['swimming']['total'] ?? 0) - ($dayServicesByType['swimming']['paid'] ?? 0) }}</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($dayServicesByType['swimming']['revenue'] ?? 0, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Swimming with Bucket</td>
                    <td style="text-align: right;"><strong>{{ $dayServicesByType['swimming_with_bucket']['total'] ?? 0 }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #28a745;">{{ $dayServicesByType['swimming_with_bucket']['paid'] ?? 0 }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #ffc107;">{{ ($dayServicesByType['swimming_with_bucket']['total'] ?? 0) - ($dayServicesByType['swimming_with_bucket']['paid'] ?? 0) }}</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($dayServicesByType['swimming_with_bucket']['revenue'] ?? 0, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Ceremony</td>
                    <td style="text-align: right;"><strong>{{ $dayServicesByType['ceremony']['total'] ?? 0 }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #28a745;">{{ $dayServicesByType['ceremony']['paid'] ?? 0 }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #ffc107;">{{ ($dayServicesByType['ceremony']['total'] ?? 0) - ($dayServicesByType['ceremony']['paid'] ?? 0) }}</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($dayServicesByType['ceremony']['revenue'] ?? 0, 2) }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Day Services</strong></td>
                    <td style="text-align: right;"><strong>{{ $dayServicesStats['total'] ?? 0 }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #28a745;">{{ $dayServicesStats['paid'] ?? 0 }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #ffc107;">{{ $dayServicesStats['pending'] ?? 0 }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #e07632; font-size: 14px;">{{ number_format($dayServicesRevenue ?? 0, 2) }} TZS</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Service Requests Statistics -->
    <div class="receipt-info-section">
        <h3>Service Requests</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th style="text-align: right;">Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Requests</td>
                    <td style="text-align: right;"><strong>{{ $serviceRequests['total'] ?? 0 }}</strong></td>
                </tr>
                <tr>
                    <td>Pending</td>
                    <td style="text-align: right;"><strong style="color: #ffc107;">{{ $serviceRequests['pending'] ?? 0 }}</strong></td>
                </tr>
                <tr>
                    <td>Approved</td>
                    <td style="text-align: right;"><strong style="color: #17a2b8;">{{ $serviceRequests['approved'] ?? 0 }}</strong></td>
                </tr>
                <tr>
                    <td>Completed</td>
                    <td style="text-align: right;"><strong style="color: #28a745;">{{ $serviceRequests['completed'] ?? 0 }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td><strong>Service Revenue</strong></td>
                    <td style="text-align: right;"><strong style="color: #e07632; font-size: 14px;">{{ number_format($serviceRequests['revenue'] ?? 0, 0) }} TZS</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Bar & Restaurant Consumption -->
    <div class="receipt-info-section">
        <h3>Bar & Restaurant Consumption</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th style="text-align: right;">Total Items</th>
                    <th style="text-align: right;">Paid</th>
                    <th style="text-align: right;">Unpaid</th>
                    <th style="text-align: right;">Revenue (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Get all service requests (bar/restaurant consumption)
                    $allConsumption = \App\Models\ServiceRequest::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                        ->where('is_walk_in', true)
                        ->get();
                    
                    $ceremonyConsumption = $allConsumption->whereNotNull('day_service_id');
                    $walkInConsumption = $allConsumption->whereNull('day_service_id');
                    
                    $ceremonyTotal = $ceremonyConsumption->count();
                    $ceremonyPaid = $ceremonyConsumption->where('payment_status', 'paid')->count();
                    $ceremonyUnpaid = $ceremonyConsumption->where('payment_status', 'pending')->count();
                    $ceremonyRevenue = $ceremonyConsumption->where('payment_status', 'paid')->sum('total_price_tsh');
                    
                    $walkInTotal = $walkInConsumption->count();
                    $walkInPaid = $walkInConsumption->where('payment_status', 'paid')->count();
                    $walkInUnpaid = $walkInConsumption->where('payment_status', 'pending')->count();
                    $walkInRevenue = $walkInConsumption->where('payment_status', 'paid')->sum('total_price_tsh');
                    
                    $totalConsumptionRevenue = $ceremonyRevenue + $walkInRevenue;
                @endphp
                <tr>
                    <td>Ceremony Consumption</td>
                    <td style="text-align: right;"><strong>{{ $ceremonyTotal }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #28a745;">{{ $ceremonyPaid }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #dc3545;">{{ $ceremonyUnpaid }}</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($ceremonyRevenue, 0) }}</strong></td>
                </tr>
                <tr>
                    <td>Walk-in Sales</td>
                    <td style="text-align: right;"><strong>{{ $walkInTotal }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #28a745;">{{ $walkInPaid }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #dc3545;">{{ $walkInUnpaid }}</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($walkInRevenue, 0) }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Bar/Restaurant</strong></td>
                    <td style="text-align: right;"><strong>{{ $ceremonyTotal + $walkInTotal }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #28a745;">{{ $ceremonyPaid + $walkInPaid }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #dc3545;">{{ $ceremonyUnpaid + $walkInUnpaid }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #e07632; font-size: 14px;">{{ number_format($totalConsumptionRevenue, 0) }} TZS</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Monthly Comparison -->
    <div class="receipt-info-section">
        <h3>Monthly Comparison</h3>
        <div class="receipt-two-column">
            <div class="receipt-column">
                <div class="receipt-info-row">
                    <span class="receipt-info-label">This Month Bookings:</span>
                    <span class="receipt-info-value" style="color: #ffc107;"><strong>{{ $stats['month_bookings'] ?? 0 }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Last Month Bookings:</span>
                    <span class="receipt-info-value" style="color: #666;">{{ $stats['last_month_bookings'] ?? 0 }}</span>
                </div>
            </div>
            <div class="receipt-column">
                <div class="receipt-info-row">
                    <span class="receipt-info-label">This Month Revenue:</span>
                    <span class="receipt-info-value" style="color: #e07632;"><strong>{{ number_format($stats['month_revenue'] ?? 0, 0) }} TZS</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Last Month Revenue:</span>
                    <span class="receipt-info-value" style="color: #666;">{{ number_format($stats['last_month_revenue'] ?? 0, 0) }} TZS</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Performing Rooms -->
    @if(isset($topRooms) && $topRooms->count() > 0)
    <div class="receipt-info-section">
        <h3>Top Performing Rooms</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Room Number</th>
                    <th>Room Type</th>
                    <th style="text-align: right;">Total Bookings</th>
                    <th style="text-align: right;">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topRooms as $index => $roomData)
                <tr>
                    <td>
                        @if($index == 0)
                            <span style="color: #ffc107; font-weight: bold; font-size: 16px;">🥇</span>
                        @elseif($index == 1)
                            <span style="color: #6c757d; font-weight: bold; font-size: 16px;">🥈</span>
                        @elseif($index == 2)
                            <span style="color: #17a2b8; font-weight: bold; font-size: 16px;">🥉</span>
                        @else
                            <strong>#{{ $index + 1 }}</strong>
                        @endif
                    </td>
                    <td><strong>{{ $roomData->room->room_number ?? 'N/A' }}</strong></td>
                    <td>{{ $roomData->room->room_type ?? 'N/A' }}</td>
                    <td style="text-align: right;"><strong>{{ $roomData->booking_count }}</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($roomData->total_revenue ?? 0, 0) }} TZS</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- System Recommendations -->
    @if(isset($recommendations) && count($recommendations) > 0)
    <div class="receipt-info-section">
        <h3>System Recommendations</h3>
        @foreach($recommendations as $recommendation)
        <div class="recommendation-box {{ $recommendation['type'] }}">
            <h4>
                <i class="fa {{ $recommendation['icon'] }}"></i>
                {{ $recommendation['title'] }}
            </h4>
            <p>{{ $recommendation['message'] }}</p>
        </div>
        @endforeach
    </div>
    @endif
    
    <!-- Footer -->
    <div class="receipt-footer">
        <p><strong>Umoj Lutheran Hostel</strong></p>
        <p>This report has been generated automatically by the Umoj Lutheran Hostel Management System.</p>
        <p style="margin-top: 15px;">This is an official report. Please keep this for your records.</p>
        <p style="margin-top: 10px; font-size: 9px;">Generated on: {{ now()->format('F d, Y \a\t g:i A') }}</p>
        <p class="powered-by">Powered By <strong>EmCa Techonologies</strong></p>
    </div>
</div>
@else
<div class="row mb-3">
  <div class="col-md-12">
    <div class="alert alert-info text-center">
      <i class="fa fa-info-circle"></i> Please select a report type and date above to generate a report.
    </div>
  </div>
</div>
@endif

@endsection

@section('scripts')
<script>
function toggleDateInputs() {
  const reportType = document.getElementById('report_type').value;
  const singleDateContainer = document.getElementById('singleDateContainer');
  const startDateContainer = document.getElementById('startDateContainer');
  const endDateContainer = document.getElementById('endDateContainer');
  const dateInput = document.getElementById('date');
  const startDateInput = document.getElementById('start_date');
  const endDateInput = document.getElementById('end_date');
  
  if (reportType === 'custom') {
    singleDateContainer.style.display = 'none';
    startDateContainer.style.display = 'block';
    endDateContainer.style.display = 'block';
    // Clear single date when switching to custom
    dateInput.value = '';
    // Set default dates if empty
    if (!startDateInput.value) {
      const today = new Date();
      const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
      startDateInput.value = firstDay.toISOString().split('T')[0];
    }
    if (!endDateInput.value) {
      endDateInput.value = new Date().toISOString().split('T')[0];
    }
  } else {
    singleDateContainer.style.display = 'block';
    startDateContainer.style.display = 'none';
    endDateContainer.style.display = 'none';
    // Clear custom dates when switching away from custom
    startDateInput.value = '';
    endDateInput.value = '';
    
    // Set appropriate date input based on report type (only if date is empty)
    if (!dateInput.value) {
      if (reportType === 'weekly') {
        // Set to start of current week
        const today = new Date();
        const dayOfWeek = today.getDay();
        const diff = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
        const monday = new Date(today.setDate(diff));
        dateInput.value = monday.toISOString().split('T')[0];
      } else if (reportType === 'monthly') {
        // Set to first day of current month
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        dateInput.value = firstDay.toISOString().split('T')[0];
      } else if (reportType === 'yearly') {
        // Set to first day of current year
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), 0, 1);
        dateInput.value = firstDay.toISOString().split('T')[0];
      } else if (reportType === 'daily') {
        // Set to today
        dateInput.value = new Date().toISOString().split('T')[0];
      }
    }
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  toggleDateInputs();
});

function setQuickReport(reportType, date) {
  document.getElementById('report_type').value = reportType;
  document.getElementById('date').value = date;
  toggleDateInputs();
  document.getElementById('reportForm').submit();
}
</script>
@endsection
