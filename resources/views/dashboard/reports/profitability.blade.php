@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-calculator"></i> Profitability Analysis Report</h1>
    <p>Profit margins and cost analysis</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="#">Profitability</a></li>
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
        <form method="GET" action="{{ route('admin.reports.profitability') }}" id="reportForm">
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

<!-- Include receipt report styles -->
@include('dashboard.reports.partials.receipt-styles')

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
        <p style="margin-top: 15px; font-size: 16px; font-weight: bold; color: #e07632;">Profitability Analysis Report</p>
    </div>
    
    <!-- Report Number -->
    <div class="receipt-report-number">
        <strong>Report #:</strong> PROF-RPT-{{ date('Ymd') }}-{{ strtoupper(substr(md5('prof' . now()), 0, 6)) }}
    </div>
    
    <!-- Receipt Title -->
    <div class="receipt-report-title">
        PROFITABILITY ANALYSIS REPORT
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
                    <span class="receipt-info-value"><strong>{{ ucfirst($reportType) }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Period:</span>
                    <span class="receipt-info-value">{{ $dateRange['label'] }}</span>
                </div>
                @php
                    $genDate = \Carbon\Carbon::now();
                    $genDateStr = $genDate->toFormattedDateString();
                    $genHour = $genDate->hour;
                    $genMinute = str_pad($genDate->minute, 2, '0', STR_PAD_LEFT);
                    $genAmpm = $genDate->format('A');
                    if ($genHour > 12) {
                        $genHour = $genHour - 12;
                    } elseif ($genHour == 0) {
                        $genHour = 12;
                    }
                    $genTimeStr = $genHour . ':' . $genMinute . ' ' . $genAmpm;
                @endphp
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Generated on:</span>
                    <span class="receipt-info-value">{{ $genDateStr }} at {{ $genTimeStr }}</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Generated by:</span>
                    <span class="receipt-info-value">{{ $userName }} ({{ $userRole }})</span>
                </div>
            </div>
        </div>
        
        <div class="receipt-column">
            <div class="receipt-info-section">
                <h3>Financial Summary</h3>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Room Revenue:</span>
                    <span class="receipt-info-value"><strong>{{ number_format($roomRevenueTZS, 0) }} TZS</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Service Revenue:</span>
                    <span class="receipt-info-value">{{ number_format($serviceRevenueTZS, 0) }} TZS</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Day Services Revenue:</span>
                    <span class="receipt-info-value">{{ number_format($dayServiceRevenueTZS, 0) }} TZS</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Total Hotel Revenue:</span>
                    <span class="receipt-info-value" style="color: #28a745;"><strong>{{ number_format($totalHotelIncomeTZS, 0) }} TZS</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Cost of Goods Sold:</span>
                    <span class="receipt-info-value" style="color: #dc3545;">{{ number_format($cogsTZS, 0) }} TZS</span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Gross Profit:</span>
                    <span class="receipt-info-value" style="color: #e07632; font-size: 16px;"><strong>{{ number_format($grossProfitTZS, 0) }} TZS</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Profit Margin:</span>
                    <span class="receipt-info-value" style="color: {{ $grossProfitMargin >= 30 ? '#28a745' : ($grossProfitMargin >= 20 ? '#ffc107' : '#dc3545') }}; font-size: 16px;"><strong>{{ $grossProfitMargin }}%</strong></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profitability Analysis -->
    <div class="receipt-info-section">
        <h3>Profitability by Room Type</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th style="text-align: center;">Bookings</th>
                    <th style="text-align: right;">Total Revenue (TZS)</th>
                    <th style="text-align: right;">Average Revenue (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($profitabilityByRoomType as $roomType => $data)
                <tr>
                    <td><strong>{{ $roomType }}</strong></td>
                    <td style="text-align: center;">{{ $data["count"] ?? 0 }}</td>
                    <td style="text-align: right;"><strong>{{ number_format($data["revenue"] ?? 0, 0) }}</strong></td>
                    <td style="text-align: right;">{{ number_format($data["average_revenue"] ?? 0, 0) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No data available for this period.</td>
                </tr>
                @endforelse
                @php
                    $totalBookings = 0;
                    foreach ($profitabilityByRoomType as $roomType => $data) {
                        $totalBookings += $data["count"] ?? 0;
                    }
                @endphp
                <tr class="total-row">
                    <td style="text-align: right;"><strong>TOTAL:</strong></td>
                    <td style="text-align: center;"><strong>{{ $totalBookings }}</strong></td>
                    <td style="text-align: right;"><strong style="color: #e07632; font-size: 14px;">{{ number_format($roomRevenueTZS, 0) }} TZS</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($totalBookings > 0 ? $roomRevenueTZS / $totalBookings : 0, 0) }} TZS</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Recommendations -->
    @if($grossProfitMargin < 20)
    <div class="recommendation-box warning">
        <h4><i class="fa fa-exclamation-triangle"></i> Low Profit Margin Alert</h4>
        <p>Current profit margin is {{ $grossProfitMargin }}%, which is below the recommended 20-30% range. Consider reviewing costs and pricing strategies.</p>
    </div>
    @elseif($grossProfitMargin >= 30)
    <div class="recommendation-box success">
        <h4><i class="fa fa-check-circle"></i> Excellent Profit Margin</h4>
        <p>Current profit margin of {{ $grossProfitMargin }}% is excellent. Maintain current operational efficiency.</p>
    </div>
    @endif
    
    <!-- Footer -->
    @php
        $now = \Carbon\Carbon::now();
        $generatedDate = $now->toFormattedDateString();
        $hour = $now->hour;
        $minute = str_pad($now->minute, 2, '0', STR_PAD_LEFT);
        $ampm = $now->format('A');
        if ($hour > 12) {
            $hour = $hour - 12;
        } elseif ($hour == 0) {
            $hour = 12;
        }
        $generatedTime = $hour . ':' . $minute . ' ' . $ampm;
    @endphp
    <div class="receipt-footer">
        <p><strong>Umoj Lutheran Hostel</strong></p>
        <p>This report has been generated automatically by the Umoj Lutheran Hostel Management System.</p>
        <p style="margin-top: 15px;">This is an official report. Please keep this for your records.</p>
        <p style="margin-top: 10px; font-size: 9px;">Generated on: {{ $generatedDate }} at {{ $generatedTime }}</p>
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
