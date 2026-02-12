@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-smile-o"></i> Guest Satisfaction Report</h1>
    <p>Analyze feedback, ratings, and satisfaction trends</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="#">Guest Satisfaction</a></li>
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
        <form method="GET" action="{{ route('admin.reports.guest-satisfaction') }}" id="reportForm">
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
        <p style="margin-top: 15px; font-size: 16px; font-weight: bold; color: #e07632;">Guest Satisfaction Report</p>
    </div>
    
    <div class="receipt-report-number">
        <strong>Report #:</strong> SATIS-RPT-{{ date('Ymd') }}-{{ strtoupper(substr(md5('satisfaction' . now()), 0, 6)) }}
    </div>
    
    <div class="receipt-report-title">
        GUEST SATISFACTION REPORT
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
                <h3>Satisfaction Summary</h3>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Total Feedbacks:</span>
                    <span class="receipt-info-value"><strong>{{ $totalFeedbacks }}</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Average Rating:</span>
                    <span class="receipt-info-value" style="color: {{ $averageRating >= 4 ? '#28a745' : ($averageRating >= 3 ? '#ffc107' : '#dc3545') }}; font-size: 20px;"><strong>{{ $averageRating }}/5.0</strong></span>
                </div>
                <div class="receipt-info-row">
                    <span class="receipt-info-label">Response Rate:</span>
                    <span class="receipt-info-value">{{ $responseRate }}%</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Rating Distribution -->
    <div class="receipt-info-section">
        <h3>Rating Distribution</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Rating</th>
                    <th style="text-align: center;">Count</th>
                    <th style="text-align: center;">Percentage</th>
                    <th>Visual</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalRatings = $ratingDistribution->sum();
                @endphp
                @foreach($ratingDistribution->sortKeysDesc() as $rating => $count)
                <tr>
                    <td>
                        <strong>
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $rating)
                                    <i class="fa fa-star" style="color: #ffc107;"></i>
                                @else
                                    <i class="fa fa-star-o" style="color: #ccc;"></i>
                                @endif
                            @endfor
                            ({{ $rating }})
                        </strong>
                    </td>
                    <td style="text-align: center;">{{ $count }}</td>
                    <td style="text-align: center;">
                        @if($totalRatings > 0)
                            {{ number_format(($count / $totalRatings) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                    <td>
                        @if($totalRatings > 0)
                            @php
                                $percentage = ($count / $totalRatings) * 100;
                            @endphp
                            <div style="background-color: #f0f0f0; height: 20px; width: 100%; border-radius: 10px; overflow: hidden;">
                                <div style="background-color: {{ $rating >= 4 ? '#28a745' : ($rating >= 3 ? '#ffc107' : '#dc3545') }}; height: 100%; width: {{ $percentage }}%;"></div>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Category Ratings -->
    @if(array_sum($averageCategoryRatings) > 0)
    <div class="receipt-info-section">
        <h3>Category Ratings</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th style="text-align: center;">Average Rating</th>
                    <th>Visual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($averageCategoryRatings as $category => $rating)
                @if($rating > 0)
                <tr>
                    <td><strong>{{ ucfirst(str_replace('_', ' ', $category)) }}</strong></td>
                    <td style="text-align: center; color: {{ $rating >= 4 ? '#28a745' : ($rating >= 3 ? '#ffc107' : '#dc3545') }}; font-size: 16px;"><strong>{{ number_format($rating, 2) }}/5.0</strong></td>
                    <td>
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($rating))
                                <i class="fa fa-star" style="color: #ffc107;"></i>
                            @else
                                <i class="fa fa-star-o" style="color: #ccc;"></i>
                            @endif
                        @endfor
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Satisfaction by Room Type -->
    @if($satisfactionByRoomType->count() > 0)
    <div class="receipt-info-section">
        <h3>Satisfaction by Room Type</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th style="text-align: center;">Feedbacks</th>
                    <th style="text-align: center;">Average Rating</th>
                </tr>
            </thead>
            <tbody>
                @foreach($satisfactionByRoomType as $roomType => $data)
                <tr>
                    <td><strong>{{ $roomType }}</strong></td>
                    <td style="text-align: center;">{{ $data['count'] }}</td>
                    <td style="text-align: center; color: {{ $data['average'] >= 4 ? '#28a745' : ($data['average'] >= 3 ? '#ffc107' : '#dc3545') }};"><strong>{{ $data['average'] }}/5.0</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Recent Feedbacks -->
    @if($feedbacks->count() > 0)
    <div class="receipt-info-section">
        <h3>Recent Feedbacks</h3>
        <table class="receipt-details-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Guest</th>
                    <th style="text-align: center;">Rating</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($feedbacks as $feedback)
                <tr>
                    <td>{{ $feedback->created_at->format('M d, Y') }}</td>
                    <td><strong>{{ $feedback->guest_name }}</strong></td>
                    <td style="text-align: center;">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $feedback->rating)
                                <i class="fa fa-star" style="color: #ffc107;"></i>
                            @else
                                <i class="fa fa-star-o" style="color: #ccc;"></i>
                            @endif
                        @endfor
                        <br><small>({{ $feedback->rating }}/5)</small>
                    </td>
                    <td>
                        @if($feedback->comment)
                            <small>{{ Str::limit($feedback->comment, 100) }}</small>
                        @else
                            <small class="text-muted">No comment</small>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Recommendations -->
    @if($averageRating < 3)
    <div class="recommendation-box danger">
        <h4><i class="fa fa-exclamation-triangle"></i> Low Satisfaction Alert</h4>
        <p>Average rating is {{ $averageRating }}/5.0, which is below acceptable standards. Immediate action required to improve guest experience.</p>
    </div>
    @elseif($averageRating >= 4.5)
    <div class="recommendation-box success">
        <h4><i class="fa fa-check-circle"></i> Excellent Satisfaction</h4>
        <p>Average rating of {{ $averageRating }}/5.0 indicates excellent guest satisfaction. Maintain current service standards.</p>
    </div>
    @endif
    
    @if($responseRate < 30)
    <div class="recommendation-box warning">
        <h4><i class="fa fa-info-circle"></i> Low Response Rate</h4>
        <p>Response rate is {{ $responseRate }}%. Consider implementing feedback collection strategies to increase participation.</p>
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
