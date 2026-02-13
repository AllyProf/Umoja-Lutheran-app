@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-file-text"></i> Reports</h1>
    <p>View operational reports</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'manager' ? route('admin.dashboard') : route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
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
        <form method="GET" action="{{ $role === 'manager' ? route('admin.reports.daily') : route('reception.reports') }}" id="reportForm">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="report_type"><strong>Report Type:</strong></label>
                <select name="report_type" id="report_type" class="form-control" onchange="toggleDateInputs()">
                  <option value="daily" {{ ($reportType ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily</option>
                  <option value="weekly" {{ ($reportType ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                  <option value="monthly" {{ ($reportType ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
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

<!-- Report Display (Receipt Style) -->
@if(isset($dateRange))
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

.receipt-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}

.receipt-stat-box {
    background-color: #f8f9fa;
    padding: 15px;
    border-left: 4px solid #e07632;
}

.receipt-stat-label {
    font-size: 12px;
    color: #666;
    margin-bottom: 5px;
}

.receipt-stat-value {
    font-size: 22px;
    font-weight: bold;
    color: #e07632;
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
    
    /* Hide non-report elements */
    .app-title,
    .app-breadcrumb,
    .tile:has(form),
    .alert-info:not(.receipt-container .alert-info) {
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
    }
    
    .receipt-report-header p {
        font-size: 11px !important;
    }
    
    .receipt-report-title {
        font-size: 16px !important;
    }
    
    .receipt-info-section h3 {
        font-size: 12px !important;
    }
    
    .receipt-info-row {
        font-size: 10px !important;
    }
    
    .receipt-details-table {
        font-size: 10px !important;
    }
    
    .receipt-details-table th,
    .receipt-details-table td {
        padding: 8px !important;
        font-size: 10px !important;
    }
    
    /* Ensure watermark is visible in print */
    .receipt-report-container::before {
        opacity: 0.1 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
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
        <p style="margin-top: 15px; font-size: 16px; font-weight: bold; color: #e07632;">Operational Report</p>
    </div>
    
    <!-- Report Number -->
    <div class="receipt-report-number">
        <strong>Report #:</strong> OPR-RPT-{{ date('Ymd') }}-{{ strtoupper(substr(md5($dateRange['label'] . now()), 0, 6)) }}
    </div>
    
    <!-- Receipt Title -->
    <div class="receipt-report-title">
        OPERATIONAL REPORT
    </div>
    
    <!-- Print Button -->
    <div class="print-button-section">
        <button onclick="downloadReport()">
            <i class="fa fa-download"></i> Download Report
        </button>
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
                <h3>Statistics Summary</h3>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Checked In:</span>
                    <span class="receipt-info-value" style="color: #007bff;"><strong>{{ $bookings['checked_in'] ?? 0 }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Checked Out:</span>
                    <span class="receipt-info-value" style="color: #dc3545;"><strong>{{ $bookings['checked_out'] ?? 0 }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">New Bookings:</span>
                    <span class="receipt-info-value" style="color: #28a745;"><strong>{{ $bookings['new_bookings'] ?? 0 }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Total Revenue:</span>
                    <span class="receipt-info-value" style="color: #e07632; font-size: 16px;"><strong>{{ number_format(($payments['total_revenue'] ?? 0) * $exchangeRate, 2) }} TZS</strong></span>
                </div>

            </div>
        </div>
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
                    <td style="text-align: right;"><strong style="color: #e07632; font-size: 14px;">{{ number_format($serviceRequests['revenue'] ?? 0, 2) }} TZS</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Recent Bookings -->
    @if($recentBookings->count() > 0)
    <div class="receipt-info-section">
        <h3>Recent Bookings</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Booking Reference</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th style="text-align: right;">Total Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBookings as $booking)
                <tr>
                    <td><strong>{{ $booking->booking_reference }}</strong></td>
                    <td>{{ $booking->guest_name }}</td>
                    <td>
                        {{ $booking->room->room_type ?? 'N/A' }}<br>
                        <small style="color: #999;">{{ $booking->room->room_number ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $booking->check_in->format('M d, Y') }}</td>
                    <td>{{ $booking->check_out->format('M d, Y') }}</td>
                    <td style="text-align: right;">
                        <strong>{{ number_format($booking->total_price * $exchangeRate, 0) }} TZS</strong><br>

                    </td>
                    <td>
                        @if($booking->status === 'confirmed')
                            <span class="badge badge-success">Confirmed</span>
                        @elseif($booking->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($booking->status) }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
<!-- Statistics (Default view when no report data) -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-sign-in fa-2x"></i>
      <div class="info">
        <h4>Checked In</h4>
        <p><b>0</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-sign-out fa-2x"></i>
      <div class="info">
        <h4>Checked Out</h4>
        <p><b>0</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-calendar fa-2x"></i>
      <div class="info">
        <h4>New Bookings</h4>
        <p><b>0</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-money fa-2x"></i>
      <div class="info">
        <h4>Revenue</h4>
        <p><b>0 TZS</b></p>
      </div>
    </div>
  </div>
</div>
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

function downloadReport() {
  const reportType = document.getElementById('report_type').value;
  const date = document.getElementById('date').value;
  const startDate = document.getElementById('start_date').value;
  const endDate = document.getElementById('end_date').value;
  
  // Build download URL with current report parameters
  let url = '{{ $role === 'manager' ? route("admin.reports.daily") : route("reception.reports") }}?download=1';
  url += '&report_type=' + encodeURIComponent(reportType);
  
  if (reportType === 'custom') {
    if (startDate) url += '&start_date=' + encodeURIComponent(startDate);
    if (endDate) url += '&end_date=' + encodeURIComponent(endDate);
  } else {
    if (date) url += '&date=' + encodeURIComponent(date);
  }
  
  // Use fetch to download the file
  fetch(url)
    .then(response => response.blob())
    .then(blob => {
      // Create a blob URL and trigger download
      const blobUrl = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = blobUrl;
      link.download = 'operational_report_' + new Date().getTime() + '.html';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      window.URL.revokeObjectURL(blobUrl);
    })
    .catch(error => {
      console.error('Download error:', error);
      // Fallback: open in new window
      window.open(url, '_blank');
    });
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




