@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-car"></i> Parking Service Registration</h1>
            <p>Register parking access for guests</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a
                    href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item"><a href="#">Day Services</a></li>
            <li class="breadcrumb-item"><a href="#">Parking</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Parking Service Registration</h3>
                <div class="tile-body">
                    <form id="parkingForm" method="POST"
                        action="{{ $role === 'reception' ? route('reception.day-services.store') : route('admin.day-services.store') }}">
                        @csrf
                        <input type="hidden" name="service_type" value="parking">
                        <input type="hidden" name="payment_status" value="paid">
                        <input type="hidden" name="guest_type" id="guest_type" value="tanzanian">

                        <h4 class="mb-4 mt-4"><i class="fa fa-user"></i> Guest Information</h4>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="guest_name">Guest Name <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" id="guest_name" name="guest_name"
                                        placeholder="Enter guest name" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vehicle_name">Vehicle Name <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" id="vehicle_name" name="vehicle_name"
                                        placeholder="e.g., Toyota Harrier" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="plate_number">Plate Number <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" id="plate_number" name="plate_number"
                                        placeholder="e.g., T 123 ABC" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="guest_phone">Phone Number</label>
                                    <input class="form-control" type="text" id="guest_phone" name="guest_phone"
                                        placeholder="e.g., +255712345678">
                                </div>
                            </div>
                        </div>

                        <div class="row">
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
                                    <label for="number_of_people">Number of Vehicles <span
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="service_date">Service Date <span class="text-danger">*</span></label>
                                    <input class="form-control" type="date" id="service_date" name="service_date"
                                        value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="service_time">Start Time <span class="text-danger">*</span></label>
                                    <input class="form-control" type="time" id="service_time" name="service_time"
                                        value="{{ \Carbon\Carbon::now()->format('H:i') }}" required
                                        oninput="calculateAmount()">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_time">End Time</label>
                                    <input class="form-control" type="time" id="end_time" name="end_time"
                                        oninput="calculateAmount()">
                                    <small class="text-muted">Optional</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="is_all_day" style="display: block;">All Day?</label>
                                    <div class="toggle-flip">
                                        <label>
                                            <input type="checkbox" id="is_all_day" name="is_all_day" value="1"
                                                onchange="calculateAmount()">
                                            <span class="flip-indecator" data-toggle-on="YES" data-toggle-off="NO"></span>
                                        </label>
                                    </div>
                                    <small class="text-muted">Day + Night</small>
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
                                            value="{{ number_format($parkingService->price_tanzanian, 2, '.', '') }}"
                                            min="0" required>
                                    </div>
                                    <div class="form-check mt-2">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" id="custom_amount"
                                                onchange="calculateAmount()">
                                            <strong>Enter Custom Amount</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted" id="amount_conversion"></small>
                                    <small class="form-text text-info">
                                        <i class="fa fa-info-circle"></i> Recommended:
                                        <span
                                            id="recommended_amount_tzs">{{ number_format($parkingService->price_tanzanian, 2) }}
                                            TZS</span>
                                        @if($parkingService->price_international)
                                            / <span
                                                id="recommended_amount_usd">${{ number_format($parkingService->price_international, 2) }}
                                                USD</span>
                                        @endif
                                        (per vehicle)
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
                                        <option value="online">Online Payment</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="payment_provider_section" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_provider">Payment Provider</label>
                                    <select class="form-control" id="payment_provider" name="payment_provider">
                                        <option value="">Select provider...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_reference">Reference Number</label>
                                    <input class="form-control" type="text" id="payment_reference" name="payment_reference"
                                        placeholder="Transaction ID or Reference Number">
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
                                    value="{{ number_format($parkingService->price_tanzanian, 2, '.', '') }}" min="0"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"
                                placeholder="Vehicle plate number and details..."></textarea>
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
        // Move to global scope so inline oninput can find it
        let numberPeopleInput;
        let amountInput;
        let amountPaidInput;
        let priceTanzanian;
        let priceNight;
        let dayStartTime;
        let dayEndTime;
        let pricingType;

        function calculateAmount() {
            if (!numberPeopleInput) {
                numberPeopleInput = document.getElementById('number_of_people');
                amountInput = document.getElementById('amount');
                amountPaidInput = document.getElementById('amount_paid');
                priceTanzanian = {{ $parkingService->price_tanzanian }};
                priceNight = {{ $parkingService->night_price_tanzanian ?? $parkingService->price_tanzanian }};
                dayStartTime = '{{ $parkingService->day_start_time }}';
                dayEndTime = '{{ $parkingService->day_end_time }}';
                pricingType = '{{ $parkingService->pricing_type }}';
            }

            const numVehicles = parseInt(numberPeopleInput.value) || 1;
            const startTimeStr = document.getElementById('service_time').value;
            const endTimeStr = document.getElementById('end_time').value;
            const isAllDayCheckBox = document.getElementById('is_all_day');

            const timeToMins = (t) => {
                if (!t) return null;

                let hrs, mins;
                const ampmMatch = t.match(/(\d+):(\d+)\s*(AM|PM)/i);

                if (ampmMatch) {
                    hrs = parseInt(ampmMatch[1]);
                    mins = parseInt(ampmMatch[2]);
                    const meridiem = ampmMatch[3].toUpperCase();
                    if (meridiem === 'PM' && hrs < 12) hrs += 12;
                    if (meridiem === 'AM' && hrs === 12) hrs = 0;
                } else {
                    const p = t.split(':');
                    hrs = parseInt(p[0]);
                    mins = parseInt(p[1]);
                }
                return hrs * 60 + mins;
            };

            const startMins = timeToMins(startTimeStr);
            const endMins = timeToMins(endTimeStr);
            const dayStartMins = timeToMins(dayStartTime);
            const dayEndMins = timeToMins(dayEndTime);

            let currentPrice = priceTanzanian;

            // Check if duration spans Day/Night boundary
            let spansBoth = false;
            if (startMins !== null && endMins !== null) {
                const startInDay = (startMins >= dayStartMins && startMins <= dayEndMins);
                const endInDay = (endMins >= dayStartMins && endMins <= dayEndMins);

                if (startInDay !== endInDay) {
                    spansBoth = true;
                }
                console.log(`Start: ${startMins}, End: ${endMins}, DayStart: ${dayStartMins}, DayEnd: ${dayEndMins}, Spans: ${spansBoth}`);
            }

            if ((isAllDayCheckBox && isAllDayCheckBox.checked) || spansBoth) {
                currentPrice = priceTanzanian + priceNight;
                if (isAllDayCheckBox && spansBoth) isAllDayCheckBox.checked = true;

                const label = spansBoth ? ' TZS (Day + Night Span Applied)' : ' TZS (All Day: Day + Night)';
                document.getElementById('recommended_amount_tzs').innerText =
                    new Intl.NumberFormat().format(currentPrice) + label;
            } else if (startMins !== null) {
                if (startMins >= dayStartMins && startMins <= dayEndMins) {
                    currentPrice = priceTanzanian;
                    document.getElementById('recommended_amount_tzs').innerText =
                        new Intl.NumberFormat().format(priceTanzanian) + ' TZS (Day)';
                } else {
                    currentPrice = priceNight;
                    document.getElementById('recommended_amount_tzs').innerText =
                        new Intl.NumberFormat().format(priceNight) + ' TZS (Night)';
                }
            }

            let calculatedAmount = 0;

            if (pricingType === 'per_person') {
                calculatedAmount = currentPrice * numVehicles;
            } else {
                calculatedAmount = currentPrice;
            }

            const customAmountCheckBox = document.getElementById('custom_amount');
            if (customAmountCheckBox && customAmountCheckBox.checked) {
                // If custom amount is checked, don't overwrite manual changes
                return;
            }

            if (amountInput) {
                amountInput.value = calculatedAmount.toFixed(2);
                amountPaidInput.value = calculatedAmount.toFixed(2);
            }
        }

        $(document).ready(function () {
            const form = document.getElementById('parkingForm');
            numberPeopleInput = document.getElementById('number_of_people');
            amountInput = document.getElementById('amount');
            amountPaidInput = document.getElementById('amount_paid');
            const paymentMethodSelect = document.getElementById('payment_method');
            const paymentProviderSection = document.getElementById('payment_provider_section');
            const paymentProviderSelect = document.getElementById('payment_provider');
            const currencySymbol = document.getElementById('currency_symbol');
            const paidCurrencySymbol = document.getElementById('paid_currency_symbol');

            priceTanzanian = {{ $parkingService->price_tanzanian }};
            priceNight = {{ $parkingService->night_price_tanzanian ?? $parkingService->price_tanzanian }};
            dayStartTime = '{{ $parkingService->day_start_time }}';
            dayEndTime = '{{ $parkingService->day_end_time }}';
            pricingType = '{{ $parkingService->pricing_type }}';

            // Initial calculation
            calculateAmount();

            function updatePaymentProvider() {
                const paymentMethod = paymentMethodSelect.value;
                if (paymentMethod === 'mobile') {
                    paymentProviderSection.style.display = 'block';
                    paymentProviderSelect.innerHTML = '<option value="">Select provider...</option><option value="M-PESA">M-PESA</option><option value="HALOPESA">HALOPESA</option><option value="AIRTEL MONEY">AIRTEL MONEY</option>';
                } else if (paymentMethod === 'bank') {
                    paymentProviderSection.style.display = 'block';
                    paymentProviderSelect.innerHTML = '<option value="">Select provider...</option><option value="NMB">NMB</option><option value="CRDB">CRDB</option>';
                } else {
                    paymentProviderSection.style.display = 'none';
                }
            }

            numberPeopleInput.addEventListener('input', calculateAmount);
            document.getElementById('service_time').addEventListener('input', calculateAmount);
            document.getElementById('service_time').addEventListener('change', calculateAmount);
            paymentMethodSelect.addEventListener('change', updatePaymentProvider);

            amountInput.addEventListener('input', function () {
                amountPaidInput.value = amountInput.value;
            });

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Registering...';

                const alertDiv = document.getElementById('formAlert');
                alertDiv.innerHTML = '';

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                    .then(response => {
                        if (response.status === 403) {
                            throw new Error("Session expired or permission denied (403). Please refresh the page.");
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Open receipt first
                            if (data.receipt_url) {
                                try {
                                    window.open(data.receipt_url, '_blank');
                                } catch (e) { console.error("Popup blocked", e); }
                            }

                            swal({
                                title: "Success!",
                                text: "Redirecting...",
                                type: "success",
                                timer: 600,
                                showConfirmButton: false
                            });

                            setTimeout(function () {
                                window.location.assign('{{ $role === "reception" ? route("reception.day-services.index") : route("admin.day-services.index") }}');
                            }, 600);
                        } else {
                            alertDiv.innerHTML = '<div class="alert alert-danger">' + (data.message || 'An error occurred.') + '</div>';
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alertDiv.innerHTML = '<div class="alert alert-danger">' + (error.message || 'An error occurred. Please try again.') + '</div>';
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });

            calculateAmount();
        });
    </script>
@endsection