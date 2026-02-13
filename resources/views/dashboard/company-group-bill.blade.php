@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-building"></i> Company Group Bill</h1>
    <p>Company: {{ $groupData['company']->name }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.reservations.check-out', ['type' => 'corporate']) }}">Check Out</a></li>
    <li class="breadcrumb-item">Company Bill</li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <!-- Hotel Header -->
        <div class="text-center mb-4" style="border-bottom: 3px solid #940000; padding-bottom: 20px;">
          <h2 style="color: #940000; margin-bottom: 5px;">Umoj Lutheran Hostel</h2>
          <p style="color: #666; margin: 0; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Corporate Group Invoice / Bill</p>
          <p style="color: #999; font-size: 13px; margin-top: 5px;">Plot No. 123, Arusha, Tanzania | Tel: 0677-155-156</p>
        </div>

        <!-- Bill Metadata -->
        <div class="row mb-4">
          <div class="col-md-6">
            <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">Company Information</h5>
            <div style="background: #fdfaf3; padding: 15px; border-radius: 8px; border: 1px solid #f9ebbe;">
              <table class="table table-sm table-borderless mb-0">
                <tr>
                  <td width="35%"><strong>Company Name:</strong></td>
                  <td><strong>{{ $groupData['company']->name }}</strong></td>
                </tr>
                <tr>
                  <td><strong>Email:</strong></td>
                  <td>{{ $groupData['company']->email ?? 'N/A' }}</td>
                </tr>
                <tr>
                  <td><strong>Phone:</strong></td>
                  <td>{{ $groupData['company']->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                  <td><strong>Address:</strong></td>
                  <td>{{ $groupData['company']->billing_address ?? 'N/A' }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-md-6 text-md-right">
            <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">Invoice Details</h5>
            <div style="padding: 15px;">
              <p style="margin-bottom: 5px;"><strong>Date Generated:</strong> {{ now()->format('F d, Y') }}</p>
              <p style="margin-bottom: 5px;"><strong>Time:</strong> {{ now()->format('g:i A') }}</p>
              <p style="margin-bottom: 5px;"><strong>Status:</strong> 
                @if($groupData['totals']['outstanding_tsh'] <= 50)
                  <span class="badge badge-success">Fully Settled</span>
                @else
                  <span class="badge badge-danger">Outstanding Payment</span>
                @endif
              </p>
            </div>
          </div>
        </div>

        <!-- Breakdown Table -->
        <div class="mb-4">
          <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">Guest Charges Summary</h5>
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead style="background-color: #f8f9fa;">
                <tr>
                  <th>Ref</th>
                  <th>Guest Name</th>
                  <th>Room</th>
                  <th>Stay Period</th>
                  <th>Room Price (TZS)</th>
                  <th>SVC Charges (TZS)</th>
                  <th>Total (TZS)</th>
                  <th>Paid (TZS)</th>
                  <th>Balance (TZS)</th>
                </tr>
              </thead>
              <tbody>
                @foreach($groupData['bookings'] as $item)
                @php
                  $booking = $item['booking'];
                @endphp
                <tr>
                  <td><small>{{ $booking->booking_reference }}</small></td>
                  <td><strong>{{ $booking->guest_name }}</strong></td>
                  <td>{{ $booking->room->room_number }}</td>
                  <td>
                    <small>{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d') }}</small>
                  </td>
                  <td>{{ number_format($item['room_bill_tsh'], 0) }}</td>
                  <td>{{ number_format($item['service_charges_tsh'], 0) }}</td>
                  <td><strong>{{ number_format($item['total_bill_tsh'], 0) }}</strong></td>
                  <td class="text-success">{{ number_format($item['amount_paid_tsh'], 0) }}</td>
                  <td class="{{ $item['outstanding_tsh'] > 50 ? 'text-danger font-weight-bold' : 'text-muted' }}">
                    {{ number_format($item['outstanding_tsh'], 0) }}
                  </td>
                </tr>
                @endforeach
              </tbody>
              <tfoot style="background-color: #f8f9fa; font-size: 1.1em;">
                <tr>
                  <th colspan="4" class="text-right">GRAND TOTALS:</th>
                  <th>{{ number_format($groupData['totals']['total_bill_tsh'] - $groupData['totals']['service_charges_tsh'], 0) }} TZS</th>
                  <th>{{ number_format($groupData['totals']['service_charges_tsh'], 0) }} TZS</th>
                  <th>{{ number_format($groupData['totals']['total_bill_tsh'], 0) }} TZS</th>
                  <th class="text-success">{{ number_format($groupData['totals']['amount_paid_tsh'], 0) }} TZS</th>
                  <th class="text-danger">{{ number_format($groupData['totals']['outstanding_tsh'], 0) }} TZS</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <!-- Final Summary Box -->
        <div class="row mt-5">
          <div class="col-md-6 mb-4">
            <div style="padding: 20px; border: 1px dashed #ddd; border-radius: 10px; height: 100%;">
              <h6 style="text-transform: uppercase; color: #666; font-size: 12px; margin-bottom: 15px;">Payment Notes</h6>
              <p style="font-size: 13px; color: #777;">
                1. Please ensure all payments are made via Bank Transfer or Mobile Money.<br>
                2. Quote Company Name and Invoice Date as payment reference.<br>
                3. This is an official document generated by Umoj Lutheran Hostel Property Management System.
              </p>
              <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px;">
                <p style="font-size: 11px; color: #aaa;">Generated for: <strong>{{ $userName }}</strong> ({{ $userRole }})</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div style="background: #f8f9fa; padding: 25px; border-radius: 10px; border: 2px solid #940000;">
              <table class="table table-borderless mb-0">
                <tr>
                  <td style="font-size: 18px;"><strong>Total Outstanding:</strong></td>
                  <td class="text-right">
                    <h3 style="color: #940000; margin: 0;">{{ number_format($groupData['totals']['outstanding_tsh'], 0) }} TZS</h3>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>

        <!-- Signatures Area -->
        <div class="row mt-5 pt-5 mb-5 no-print" style="border-top: 1px solid #eee;">
          <div class="col-md-4 text-center">
            <div style="border-top: 2px solid #444; width: 80%; margin: 40px auto 10px;"></div>
            <p><strong>RECEPTIONIST SIGNATURE</strong></p>
            <p><small>(Prepared By)</small></p>
          </div>
          <div class="col-md-4 text-center">
            <!-- Space for Stamp -->
            <div style="width: 100px; height: 100px; border: 2px dashed #ccc; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
              <span style="color: #ccc; font-size: 10px; transform: rotate(-20deg);">OFFICIAL STAMP</span>
            </div>
          </div>
          <div class="col-md-4 text-center">
            <div style="border-top: 2px solid #444; width: 80%; margin: 40px auto 10px;"></div>
            <p><strong>MANAGER SIGNATURE</strong></p>
            <p><small>(Approved By)</small></p>
          </div>
        </div>
        
        <!-- Only visible on print -->
        <div class="d-none d-print-block">
            <div class="row mt-5" style="margin-top: 100px !important;">
                <div class="col-6 text-center">
                    <div style="border-top: 2px solid #000; width: 200px; margin: 0 auto;"></div>
                    <p style="margin-top: 10px;">Receptionist Signature</p>
                </div>
                <div class="col-6 text-center">
                    <div style="border-top: 2px solid #000; width: 200px; margin: 0 auto;"></div>
                    <p style="margin-top: 10px;">Manager Signature & Stamp</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-5 no-print">
          <button onclick="window.print()" class="btn btn-primary btn-lg px-5">
            <i class="fa fa-print"></i> Print Group Bill
          </button>
          <a href="{{ route('reception.reservations.check-out', ['type' => 'corporate']) }}" class="btn btn-secondary btn-lg ml-2">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
@media print {
  .app-sidebar, .app-header, .app-title, .breadcrumb, .no-print {
    display: none !important;
  }
  .app-content {
    margin: 0 !important;
    padding: 0 !important;
  }
  .tile {
    border: none !important;
    box-shadow: none !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  body {
    background: white !important;
  }
  .table-responsive {
    overflow: visible !important;
  }
}
</style>
@endsection
