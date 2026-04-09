@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-money-bill"></i> Daily Financial Report</h1>
            <p>Unified reception income summary for handover</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a
                    href="{{ $role === 'manager' ? route('admin.dashboard') : route('reception.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item"><a href="#">Financial Report</a></li>
        </ul>
    </div>

    <div class="row mb-3 no-print">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <form method="GET" action="{{ route('reception.reports.financial') }}" class="form-inline">
                        <label class="mr-2"><strong>Report Date:</strong></label>
                        <input type="date" name="date" class="form-control mr-3" value="{{ $reportDate }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-refresh"></i> Update Report
                        </button>
                        <button type="button" class="btn btn-info ml-2" onclick="window.print()">
                            <i class="fa fa-print"></i> Print Report
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .receipt-container {
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            .app-title,
            .app-sidebar,
            .app-header {
                display: none !important;
            }

            .app-content {
                margin: 0 !important;
                padding: 0 !important;
            }
        }

        .summary-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
        }

        .bg-cash {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .bg-mobile {
            background: linear-gradient(135deg, #e07632, #c86528);
        }

        .bg-bank {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .bg-total {
            background: linear-gradient(135deg, #343a40, #212529);
        }
    </style>

    <div class="receipt-container">
        <div class="tile">
            <div class="text-center mb-4">
                <h2 style="color: #940000;">Umoja Lutheran Hostel</h2>
                <h4>Daily Reception Financial Report</h4>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($reportDate)->format('F d, Y') }}</p>
            </div>

            <!-- Summary Statistics -->
            <div class="row">
                <div class="col-md-3">
                    <div class="summary-card bg-cash">
                        <h6>CASH IN HAND</h6>
                        <h3>{{ number_format($summary['cash'] ?? 0, 0) }} TZS</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card bg-mobile">
                        <h6>MOBILE MONEY</h6>
                        <h3>{{ number_format($summary['mpesa'] ?? 0, 0) }} TZS</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card bg-bank">
                        <h6>BANK / CARD</h6>
                        <h3>{{ number_format(($summary['bank'] ?? 0) + ($summary['card'] ?? 0), 0) }} TZS</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card bg-total">
                        <h6>TOTAL REVENUE</h6>
                        <h3>{{ number_format(array_sum($summary), 0) }} TZS</h3>
                    </div>
                </div>
            </div>

            <!-- Section 1: Bookings -->
            <div class="mt-4">
                <h5>1. Room Booking Payments</h5>
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Ref</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Method</th>
                            <th class="text-right">Amount (USD)</th>
                            <th class="text-right">Amount (TZS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookingPayments as $payment)
                            <tr>
                                <td>{{ $payment->booking_reference }}</td>
                                <td>{{ $payment->guest_name }}</td>
                                <td>{{ $payment->room->room_number ?? 'N/A' }}</td>
                                <td><span class="badge badge-info">{{ strtoupper($payment->payment_method) }}</span></td>
                                <td class="text-right">${{ number_format($payment->amount_paid, 2) }}</td>
                                <td class="text-right">{{ number_format($payment->amount_paid * $exchangeRate, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No record found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($bookingPayments->count() > 0)
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td colspan="5" class="text-right">Subtotal Booking:</td>
                                <td class="text-right text-primary">
                                    {{ number_format($bookingPayments->sum('amount_paid') * $exchangeRate, 0) }} TZS</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Section 2: Service Requests -->
            <div class="mt-4">
                <h5>2. Extra Services (Restaurant / Bar / Walk-in)</h5>
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Service</th>
                            <th>Reference</th>
                            <th>Method</th>
                            <th class="text-right">Amount (TZS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicePayments as $payment)
                            <tr>
                                <td>{{ $payment->service->service_name ?? 'Item' }}</td>
                                <td>{{ $payment->booking->booking_reference ?? ($payment->is_walk_in ? 'Walk-in' : 'N/A') }}
                                </td>
                                <td><span class="badge badge-info">{{ strtoupper($payment->payment_method) }}</span></td>
                                <td class="text-right">{{ number_format($payment->total_price_tsh, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No record found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($servicePayments->count() > 0)
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td colspan="3" class="text-right">Subtotal Services:</td>
                                <td class="text-right text-primary">
                                    {{ number_format($servicePayments->sum('total_price_tsh'), 0) }} TZS</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Section 3: Day Services -->
            <div class="mt-4">
                <h5>3. Day Services (Swimming, Parking, Conference)</h5>
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Guest</th>
                            <th>Method</th>
                            <th class="text-right">Amount (TZS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dayServicePayments as $payment)
                            <tr>
                                <td>{{ $payment->service_type_name }}</td>
                                <td>{{ $payment->service_reference }}</td>
                                <td>{{ $payment->guest_name }}</td>
                                <td><span class="badge badge-info">{{ strtoupper($payment->payment_method) }}</span></td>
                                <td class="text-right">{{ number_format($payment->amount_paid, 0) }} TZS</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No record found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($dayServicePayments->count() > 0)
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td colspan="4" class="text-right">Subtotal Day Services:</td>
                                <td class="text-right text-primary">
                                    {{ number_format($dayServicePayments->sum('amount_paid'), 0) }} TZS</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Final Revenue Totals per Method -->
            <div class="mt-5 border-top pt-3">
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <h5 class="text-right mb-3">Revenue Grouped by Method</h5>
                        <table class="table table-sm">
                            @foreach($summary as $method => $total)
                                @if($total > 0)
                                    <tr>
                                        <th class="text-right">{{ strtoupper($method) }}:</th>
                                        <td class="text-right" width="150">{{ number_format($total, 0) }} TZS</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr class="table-dark">
                                <th class="text-right">GRAND TOTAL:</th>
                                <th class="text-right" style="font-size: 1.2rem;">
                                    {{ number_format(array_sum($summary), 0) }} TZS</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <p class="mb-5">Generated By:</p>
                        <div style="border-top: 1px solid black; width: 80%; margin: 0 auto;"></div>
                        <p>{{ $userName }} ({{ $userRole }})</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <p class="mb-5">Verified By (Accountant):</p>
                        <div style="border-top: 1px solid black; width: 80%; margin: 0 auto;"></div>
                        <p>Signature & Date</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <p class="mb-5">Received By (Manager):</p>
                        <div style="border-top: 1px solid black; width: 80%; margin: 0 auto;"></div>
                        <p>Signature & Date</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" style="color: #666; font-size: 0.8rem;">
                <p>Report generated at {{ now()->format('M d, Y H:i:s') }}</p>
                <p>Powered by EmCa Technologies</p>
            </div>
        </div>
    </div>
@endsection