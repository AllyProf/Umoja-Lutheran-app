@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-leaf"></i> Garden Service Registration</h1>
            <p>Register garden access for guests</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a
                    href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item"><a href="#">Day Services</a></li>
            <li class="breadcrumb-item"><a href="#">Garden</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Garden Service Registration</h3>
                <div class="tile-body">
                    <form id="gardenForm" method="POST"
                        action="{{ $role === 'reception' ? route('reception.day-services.store') : route('admin.day-services.store') }}">
                        @csrf
                        <input type="hidden" name="service_type" value="garden">
                        <input type="hidden" name="payment_status" value="paid">
                        <input type="hidden" name="guest_type" id="guest_type" value="tanzanian">

                        <h4 class="mb-4 mt-4"><i class="fa fa-user"></i> Guest Information</h4>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="guest_name">Guest Name <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" id="guest_name" name="guest_name"
                                        placeholder="Enter guest name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="guest_phone">Phone Number</label>
                                    <input class="form-control" type="text" id="guest_phone" name="guest_phone"
                                        placeholder="e.g., +255712345678">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="guest_email">Email</label>
                                    <input class="form-control" type="email" id="guest_email" name="guest_email"
                                        placeholder="guest@example.com">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="number_of_people">Number of People <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="number" id="number_of_people" name="number_of_people"
                                        value="1" min="1" required oninput="calculateAmount()">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="adult_quantity" name="adult_quantity" value="0">
                        <input type="hidden" id="child_quantity" name="child_quantity" value="0">

                        <h4 class="mb-4 mt-4"><i class="fa fa-calendar"></i> Service Date & Time</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="service_date">Service Date <span class="text-danger">*</span></label>
                                    <input class="form-control" type="date" id="service_date" name="service_date"
                                        value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="service_time">Service Time <span class="text-danger">*</span></label>
                                    <input class="form-control" type="time" id="service_time" name="service_time"
                                        value="{{ \Carbon\Carbon::now()->format('H:i') }}" required>
                                </div>
                            </div>
                        </div>

                        <h4 class="mb-4 mt-4"><i class="fa fa-dollar"></i> Payment Information</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="currency_symbol">TZS</span>
                                        </div>
                                        <input class="form-control" type="number" step="0.01" id="amount" name="amount"
                                            value="{{ number_format($gardenService->price_tanzanian, 2, '.', '') }}" min="0"
                                            required>
                                    </div>
                                    <small class="form-text text-info">
                                        <i class="fa fa-info-circle"></i> Recommended:
                                        <span
                                            id="recommended_amount_tzs">{{ number_format($gardenService->price_tanzanian, 2) }}
                                            TZS</span>
                                        @if($gardenService->price_international)
                                            / <span
                                                id="recommended_amount_usd">${{ number_format($gardenService->price_international, 2) }}
                                                USD</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-control" id="payment_method" name="payment_method" required>
                                        <option value="">Select payment method...</option>
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="mobile">Mobile Money</option>
                                        <option value="bank">Bank Transfer</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="amount_paid">Amount Paid <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="paid_currency_symbol">TZS</span>
                                </div>
                                <input class="form-control" type="number" step="0.01" id="amount_paid" name="amount_paid"
                                    value="{{ number_format($gardenService->price_tanzanian, 2, '.', '') }}" min="0"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"
                                placeholder="Event details or guest preferences..."></textarea>
                        </div>

                        <div id="formAlert"></div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> Register & Verify Payment
                            </button>
                            <a href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}"
                                class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            const form = document.getElementById('gardenForm');
            const numberPeopleInput = document.getElementById('number_of_people');
            const amountInput = document.getElementById('amount');
            const amountPaidInput = document.getElementById('amount_paid');
            const paymentMethodSelect = document.getElementById('payment_method');

            const priceTanzanian = {{ $gardenService->price_tanzanian }};
            const pricingType = '{{ $gardenService->pricing_type }}';

            function calculateAmount() {
                const numPeople = parseInt(numberPeopleInput.value) || 1;
                let calculatedAmount = 0;

                if (pricingType === 'per_person') {
                    calculatedAmount = priceTanzanian * numPeople;
                } else {
                    calculatedAmount = priceTanzanian;
                }

                amountInput.value = calculatedAmount.toFixed(2);
                amountPaidInput.value = calculatedAmount.toFixed(2);
            }

            numberPeopleInput.addEventListener('input', calculateAmount);

            amountInput.addEventListener('input', function () {
                amountPaidInput.value = amountInput.value;
            });

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            swal({
                                title: "Success!",
                                text: data.message,
                                type: "success",
                                showCancelButton: true,
                                confirmButtonClass: "btn-success",
                                confirmButtonText: "View Receipt",
                                cancelButtonText: "View All Services",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            }, function (isConfirm) {
                                if (isConfirm) {
                                    if (data.receipt_url) {
                                        window.open(data.receipt_url, '_blank');
                                    }
                                    swal({
                                        title: "What's next?",
                                        text: "The receipt is opening in a new tab.",
                                        type: "info",
                                        showCancelButton: true,
                                        confirmButtonText: "Register Another",
                                        cancelButtonText: "Back to List",
                                    }, function (isNextConfirm) {
                                        if (isNextConfirm) {
                                            location.reload();
                                        } else {
                                            window.location.assign('{{ $role === "reception" ? route("reception.day-services.index") : route("admin.day-services.index") }}');
                                        }
                                    });
                                } else {
                                    window.location.assign('{{ $role === "reception" ? route("reception.day-services.index") : route("admin.day-services.index") }}');
                                }
                            });
                        } else {
                            swal("Error!", data.message || "An error occurred.", "error");
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fa fa-check"></i> Register & Verify Payment';
                        }
                    });
            });

            calculateAmount();
        });
    </script>
@endsection