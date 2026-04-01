@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-video-camera"></i> Projector Registration</h1>
            <p>Register projector service for guests</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a
                    href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item"><a href="#">Day Services</a></li>
            <li class="breadcrumb-item"><a href="#">Projector</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Projector Service Registration</h3>
                <div class="tile-body">
                    <form id="projectorForm" method="POST"
                        action="{{ $role === 'reception' ? route('reception.day-services.store') : route('admin.day-services.store') }}">
                        @csrf
                        <input type="hidden" name="service_type" value="projector">
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
                                    <label for="organization">Organization / Company / Department</label>
                                    <input class="form-control" type="text" id="organization" name="organization"
                                        placeholder="Enter organization name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <input type="hidden" id="number_of_people" name="number_of_people" value="1">
                            </div>
                        </div>
                        <input type="hidden" id="adult_quantity" name="adult_quantity" value="0">
                        <input type="hidden" id="child_quantity" name="child_quantity" value="0">

                        <h4 class="mb-4 mt-4"><i class="fa fa-calendar"></i> Service Date & Time</h4>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="service_date">Service Date <span class="text-danger">*</span></label>
                                    <input class="form-control" type="date" id="service_date" name="service_date"
                                        value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="service_time">Start Time <span class="text-danger">*</span></label>
                                    <input class="form-control" type="time" id="service_time" name="service_time"
                                        value="{{ \Carbon\Carbon::now()->format('H:i') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_time">End Time</label>
                                    <input class="form-control" type="time" id="end_time" name="end_time">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="room_name">Location / Room</label>
                                    <select class="form-control" id="room_name" name="room_name">
                                        <option value="">Select room...</option>
                                        <option value="Ebenezer Hall">Ebenezer Hall</option>
                                        <option value="Ukumbi wa kati">Ukumbi wa kati</option>
                                        <option value="Seminer Room">Seminer Room</option>
                                        <option value="Board Room">Board Room</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="duration">Duration</label>
                                    <input class="form-control" type="text" id="duration" name="duration"
                                        placeholder="e.g., 2 hours">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="purpose">Purpose</label>
                                    <input class="form-control" type="text" id="purpose" name="purpose"
                                        placeholder="e.g., training, presentation">
                                </div>
                            </div>
                        </div>

                        <h4 class="mb-4 mt-4"><i class="fa fa-dollar"></i> Payment Information</h4>

                        <div class="form-group">
                            <label class="font-weight-bold">Payment Status <span class="text-danger">*</span></label>
                            <div class="d-flex p-3 border rounded bg-light">
                                <div class="custom-control custom-radio custom-control-inline mr-4">
                                    <input type="radio" id="status_paid" name="payment_status" value="paid"
                                        class="custom-control-input" checked onchange="togglePaymentFields()">
                                    <label class="custom-control-label text-success font-weight-bold" for="status_paid">
                                        <i class="fa fa-check-circle"></i> Paid Now
                                    </label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="status_pending" name="payment_status" value="pending"
                                        class="custom-control-input" onchange="togglePaymentFields()">
                                    <label class="custom-control-label text-warning font-weight-bold" for="status_pending">
                                        <i class="fa fa-clock-o"></i> Pay Later (Pending)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="payment_details_section">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Amount <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="currency_symbol">TZS</span>
                                            </div>
                                            <input class="form-control" type="number" step="0.01" id="amount" name="amount"
                                                value="{{ number_format($projectorService->price_tanzanian, 2, '.', '') }}"
                                                min="0" required>
                                        </div>
                                        <small class="form-text text-info">
                                            <i class="fa fa-info-circle"></i> Recommended:
                                            <span
                                                id="recommended_amount_tzs">{{ number_format($projectorService->price_tanzanian, 2) }}
                                                TZS</span>
                                            @if($projectorService->price_international)
                                                / <span
                                                    id="recommended_amount_usd">${{ number_format($projectorService->price_international, 2) }}
                                                    USD</span>
                                            @endif
                                            ({{ str_replace('_', ' ', $projectorService->pricing_type) }})
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_method">Payment Method <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" id="payment_method" name="payment_method" required>
                                            <option value="">Select payment method...</option>
                                            <option value="cash">Cash</option>
                                            <option value="card">Card</option>
                                            <option value="mobile">Mobile Money</option>
                                            <option value="bank">Bank Transfer</option>
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
                                    <input class="form-control" type="number" step="0.01" id="amount_paid"
                                        name="amount_paid"
                                        value="{{ number_format($projectorService->price_tanzanian, 2, '.', '') }}" min="0"
                                        required>
                                </div>
                            </div>

                            <div class="row" id="payment_provider_fields" style="display:none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_provider">Payment Provider (Optional)</label>
                                        <input class="form-control" type="text" id="payment_provider"
                                            name="payment_provider" placeholder="e.g., M-Pesa, CRDB, NMB">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_reference">Transaction Reference</label>
                                        <input class="form-control" type="text" id="payment_reference"
                                            name="payment_reference" placeholder="Enter reference number">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"
                                placeholder="Any additional information..."></textarea>
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
        let numberPeopleInput;
        let amountInput;
        let amountPaidInput;
        let priceTanzanian;
        let pricingType;

        function calculateDuration() {
            const startTime = document.getElementById('service_time').value;
            const endTime = document.getElementById('end_time').value;
            const durationInput = document.getElementById('duration');

            if (startTime && endTime) {
                const start = new Date(`2000-01-01T${startTime}:00`);
                const end = new Date(`2000-01-01T${endTime}:00`);

                if (end > start) {
                    const diffMs = end - start;
                    const diffHrs = diffMs / (1000 * 60 * 60);
                    const roundedHrs = Math.round(diffHrs * 10) / 10;
                    durationInput.value = `${roundedHrs} ${roundedHrs === 1 ? 'hour' : 'hours'}`;
                } else {
                    durationInput.value = '';
                }
            }
        }

        function calculateAmount() {
            if (!numberPeopleInput) {
                numberPeopleInput = document.getElementById('number_of_people');
                amountInput = document.getElementById('amount');
                amountPaidInput = document.getElementById('amount_paid');
                priceTanzanian = {{ $projectorService->price_tanzanian }};
                pricingType = '{{ $projectorService->pricing_type }}';
            }

            const units = parseInt(numberPeopleInput.value) || 1;
            let calculatedAmount = 0;

            if (pricingType === 'per_person' || pricingType === 'per_hour' || pricingType === 'per_item') {
                calculatedAmount = priceTanzanian * units;
            } else {
                calculatedAmount = priceTanzanian;
            }

            amountInput.value = calculatedAmount.toFixed(2);

            document.getElementById('recommended_amount_tzs').innerText =
                new Intl.NumberFormat().format(calculatedAmount) + ' TZS';

            const isPaid = document.getElementById('status_paid').checked;
            if (isPaid) {
                amountPaidInput.value = calculatedAmount.toFixed(2);
            } else {
                amountPaidInput.value = "0.00";
            }
        }

        window.calculateAmount = calculateAmount;
        window.calculateDuration = calculateDuration;

        window.togglePaymentFields = function () {
            const isPaid = document.getElementById('status_paid').checked;
            const paymentMethod = document.getElementById('payment_method').value;
            const amountPaidInput = document.getElementById('amount_paid');
            const amountInput = document.getElementById('amount');

            if (isPaid) {
                $('#payment_details_section').show();
                amountPaidInput.value = amountInput.value;
                document.getElementById('payment_method').required = true;

                if (paymentMethod === 'cash' || !paymentMethod) {
                    $('#payment_provider_fields').hide();
                } else {
                    $('#payment_provider_fields').show();
                }
            } else {
                $('#payment_details_section').hide();
                $('#payment_provider_fields').hide();
                amountPaidInput.value = "0.00";
                document.getElementById('payment_method').required = false;
            }
        };

        $(document).ready(function () {
            const form = document.getElementById('projectorForm');

            numberPeopleInput = document.getElementById('number_of_people');
            amountInput = document.getElementById('amount');
            amountPaidInput = document.getElementById('amount_paid');
            priceTanzanian = {{ $projectorService->price_tanzanian }};
            pricingType = '{{ $projectorService->pricing_type }}';

            document.getElementById('payment_method').addEventListener('change', togglePaymentFields);

            if (numberPeopleInput) numberPeopleInput.addEventListener('input', calculateAmount);

            if (amountInput) amountInput.addEventListener('input', function () {
                const isPaid = document.getElementById('status_paid').checked;
                if (isPaid) {
                    amountPaidInput.value = amountInput.value;
                }
            });

            const serviceTimeInput = document.getElementById('service_time');
            if (serviceTimeInput) {
                serviceTimeInput.addEventListener('input', calculateDuration);
                serviceTimeInput.addEventListener('change', calculateDuration);
            }

            const endTimeInput = document.getElementById('end_time');
            if (endTimeInput) {
                endTimeInput.addEventListener('input', calculateDuration);
                endTimeInput.addEventListener('change', calculateDuration);
            }

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
                                        confirmButtonText: "Back to List"
                                    }, function () {
                                        window.location.href = data.redirect || '{{ $role === "reception" ? route("reception.day-services.index") : route("admin.day-services.index") }}';
                                    });
                                } else {
                                    window.location.href = data.redirect || '{{ $role === "reception" ? route("reception.day-services.index") : route("admin.day-services.index") }}';
                                }
                            });
                        } else {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fa fa-check"></i> Register & Verify Payment';

                            swal("Error!", data.message || "An error occurred during registration.", "error");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fa fa-check"></i> Register & Verify Payment';
                        swal("Error!", "A connection error occurred. Please try again.", "error");
                    });
            });

            togglePaymentFields();
            calculateDuration();
            calculateAmount();
        });
    </script>
@endsection