@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-money"></i> Cash Flow Report</h1>
    <p>Track cash inflows and outflows</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="#">Cash Flow</a></li>
  </ul>
</div>

<!-- Report Filter -->
<div class="row mb-3 report-filter-section">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Generate Report</h3>
      </div>
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.reports.cash-flow') }}" id="reportForm">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="report_type"><strong>Report Type:</strong></label>
                <select name="report_type" id="report_type" class="form-control" onchange="toggleDateInputs()">
                  <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Daily</option>
                  <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Weekly</option>
                  <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Monthly</option>
                  <option value="yearly" {{ $reportType == 'yearly' ? 'selected' : '' }}>Yearly</option>
                  <option value="custom" {{ $reportType == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
              </div>
            </div>
            <div class="col-md-3" id="singleDateContainer">
              <div class="form-group">
                <label for="date"><strong>Select Date:</strong></label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $reportDate }}">
              </div>
            </div>
            <div class="col-md-3" id="startDateContainer" style="display: {{ $reportType == 'custom' ? 'block' : 'none' }};">
              <div class="form-group">
                <label for="start_date"><strong>Start Date:</strong></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
              </div>
            </div>
            <div class="col-md-3" id="endDateContainer" style="display: {{ $reportType == 'custom' ? 'block' : 'none' }};">
              <div class="form-group">
                <label for="end_date"><strong>End Date:</strong></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
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

@include('dashboard.reports.partials.receipt-styles')

<div class="receipt-report-container">
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
        <p style="margin-top: 15px; font-size: 16px; font-weight: bold; color: #e07632;">Cash Flow Report</p>
    </div>
    
    <div class="receipt-report-number">
        <strong>Report #:</strong> CASH-RPT-{{ date('Ymd') }}-{{ strtoupper(substr(md5('cash' . now()), 0, 6)) }}
    </div>
    
    <div class="receipt-report-title">
        CASH FLOW REPORT
    </div>
    
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
    
    <div class="receipt-two-column">
        <div class="receipt-column">
            <div class="receipt-info-section">
                <h3>Report Information</h3>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Report Type:</span>
                    <span class="receipt-info-value"><strong>{{ ucfirst($reportType) }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Period:</span>
                    <span class="receipt-info-value">{{ $dateRange['label'] }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Generated on:</span>
                    <span class="receipt-info-value">{{ \Carbon\Carbon::now()->format('F d, Y \a\t g:i A') }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Generated by:</span>
                    <span class="receipt-info-value">{{ $userName }} ({{ $userRole }})</span>
                </div>
            </div>
        </div>
        
        <div class="receipt-column">
            <div class="receipt-info-section">
                <h3>Cash Flow Summary</h3>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Cash Collections:</span>
                    <span class="receipt-info-value" style="color: #28a745;"><strong>{{ number_format($cashCollectionsTZS, 0) }} TZS</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Non-Cash Collections:</span>
                    <span class="receipt-info-value">{{ number_format($nonCashCollectionsTZS, 0) }} TZS</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Total Collections:</span>
                    <span class="receipt-info-value" style="color: #e07632; font-size: 16px;"><strong>{{ number_format($cashCollectionsTZS + $nonCashCollectionsTZS, 0) }} TZS</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Pending Payments:</span>
                    <span class="receipt-info-value" style="color: #ffc107;">{{ number_format($pendingAmountTZS, 0) }} TZS</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Outstanding Receivables:</span>
                    <span class="receipt-info-value" style="color: #dc3545;">{{ number_format($outstandingTZS, 0) }} TZS</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="receipt-info-section">
        <h3>Payment Method Distribution</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th style="text-align: center;">Transactions</th>
                    <th style="text-align: right;">Amount (TZS)</th>
                    <th style="text-align: right;">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalTransactions = $paymentMethodDistribution->sum('count');
                    $totalAmount = $paymentMethodDistribution->sum('total_tzs');
                @endphp
                @foreach($paymentMethodDistribution as $item)
                <tr>
                    <td><strong>{{ ucfirst($item['method']) }}</strong></td>
                    <td style="text-align: center;">{{ $item['count'] }}</td>
                    <td style="text-align: right;">{{ number_format($item['total_tzs'], 0) }}</td>
                    <td style="text-align: right;">
                        @if($totalAmount > 0)
                            {{ number_format(($item['total_tzs'] / $totalAmount) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td style="text-align: right;"><strong>TOTAL:</strong></td>
                    <td style="text-align: center;"><strong>{{ $totalTransactions }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #e07632; font-size: 14px;">{{ number_format($totalAmount, 0) }} TZS</strong></td>
                    <td style="text-align: right;"><strong>100%</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    @if($outstandingTZS > 0)
    <div class="recommendation-box warning">
        <h4><i class="fa fa-exclamation-triangle"></i> Outstanding Receivables</h4>
        <p>There are outstanding receivables of {{ number_format($outstandingTZS, 0) }} TZS. Consider following up on partial payments to improve cash flow.</p>
    </div>
    @endif
    
    <div class="receipt-footer">
        <p><strong>Umoj Lutheran Hostel</strong></p>
        <p>This report has been generated automatically by the Umoj Lutheran Hostel Management System.</p>
        <p style="margin-top: 15px;">This is an official report. Please keep this for your records.</p>
        <p style="margin-top: 10px; font-size: 9px;">Generated on: {{ now()->format('F d, Y \a\t g:i A') }}</p>
        <p class="powered-by">Powered By <strong>EmCa Technologies</strong></p>
    </div>
</div>

@endsection

@section('scripts')
<script>
function toggleDateInputs() {
  const reportType = document.getElementById('report_type').value;
  const singleDateContainer = document.getElementById('singleDateContainer');
  const startDateContainer = document.getElementById('startDateContainer');
  const endDateContainer = document.getElementById('endDateContainer');
  
  if (reportType === 'custom') {
    singleDateContainer.style.display = 'none';
    startDateContainer.style.display = 'block';
    endDateContainer.style.display = 'block';
  } else {
    singleDateContainer.style.display = 'block';
    startDateContainer.style.display = 'none';
    endDateContainer.style.display = 'none';
  }
}

function setQuickReport(reportType, date) {
  document.getElementById('report_type').value = reportType;
  document.getElementById('date').value = date;
  document.getElementById('reportForm').submit();
}

function printReport() {
  window.print();
}
</script>
@endsection
