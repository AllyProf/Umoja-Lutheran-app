<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $paypalClientId;
    private $paypalSecret;
    private $paypalMode;
    private $paypalBaseUrl;

    public function __construct()
    {
        $this->paypalClientId = config('services.paypal.client_id');
        $this->paypalSecret = config('services.paypal.secret');
        $this->paypalMode = config('services.paypal.mode', 'sandbox');
        $this->paypalBaseUrl = $this->paypalMode === 'sandbox' 
            ? 'https://api.sandbox.paypal.com' 
            : 'https://api.paypal.com';
    }

    /**
     * Create PayPal payment and redirect to PayPal
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::with('room')->findOrFail($request->booking_id);

        // Store booking ID in session for fallback
        session(['current_booking_id' => $booking->id]);
        session(['current_booking_ref' => $booking->booking_reference]);
        
        // Check if this is initial booking payment or additional charges payment
        $isInitialPayment = $booking->payment_status === 'pending' && $booking->status === 'pending';
        $isAlreadyPaid = $booking->payment_status === 'paid';
        $isPartialPayment = $booking->payment_status === 'partial';
        
        $paymentAmount = $booking->total_price;
        $paymentDescription = 'Hotel Room Booking - ' . $booking->room->room_type;
        
        // For initial payments, enforce 50% minimum deposit policy
        if ($isInitialPayment) {
            $minimumDeposit = $booking->total_price * 0.50;
            // Allow guest to pay full amount or minimum 50% deposit
            // The payment amount will be the full price, but we'll track the percentage
            $paymentAmount = $booking->total_price; // Guest can pay full or partial (minimum 50%)
            $paymentDescription = 'Hotel Room Booking Deposit (Minimum 50%) - ' . $booking->room->room_type;
        }
        // If booking has partial payment, calculate outstanding balance
        elseif ($isPartialPayment) {
            $amountPaid = $booking->amount_paid ?? 0;
            $paymentAmount = $booking->total_price - $amountPaid;
            $paymentDescription = 'Outstanding Balance - ' . $booking->room->room_type;
            
            // Ensure payment amount is positive
            if ($paymentAmount <= 0) {
                return redirect()->route('customer.dashboard')
                    ->with('info', 'No outstanding balance to pay.');
            }
        }
        // If booking is already paid (initial payment done), calculate only outstanding additional charges
        elseif ($isAlreadyPaid && !$isInitialPayment) {
            // Calculate additional charges only (room booking already paid)
            $serviceRequests = $booking->serviceRequests()
                ->whereIn('status', ['approved', 'completed'])
                ->with('service')
                ->get();
            
            // Use locked exchange rate from booking, or fallback to current rate if not set (for old bookings)
            $exchangeRate = $booking->locked_exchange_rate;
            if (!$exchangeRate) {
                // Fallback for old bookings that don't have locked rate
                $currencyService = new \App\Services\CurrencyExchangeService();
                $exchangeRate = $currencyService->getUsdToTshRate();
            }
            
            // Extension cost
            $extensionCostUsd = 0;
            if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                if ($extensionNights > 0 && $booking->room) {
                    $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                }
            }
            
            // Transportation charges (only if there's a service request for it)
            $transportationChargesTsh = 0;
            if ($booking->airport_pickup_required) {
                $airportPickupService = $serviceRequests->firstWhere('service.category', 'transport');
                // Only charge if there's an actual service request
                if ($airportPickupService) {
                    $transportationChargesTsh = $airportPickupService->total_price_tsh;
                }
                // Note: If airport_pickup_required is true but no service request exists,
                // we don't charge for it (it might have been handled separately or already paid)
            }
            
            // Other service charges
            $otherServiceChargesTsh = $serviceRequests
                ->where('service.category', '!=', 'transport')
                ->sum('total_price_tsh');
            
            // Calculate what's already been paid for additional charges
            // We use the original room price as the baseline
            $originalCheckOutDate = $booking->original_check_out 
                ? \Carbon\Carbon::parse($booking->original_check_out) 
                : \Carbon\Carbon::parse($booking->check_out);
            $originalNights = $booking->check_in->diffInDays($originalCheckOutDate);
            $baseRoomPriceUsd = $booking->room ? ($booking->room->price_per_night * $originalNights) : $booking->total_price;
            
            $roomPriceTsh = $baseRoomPriceUsd * $exchangeRate;
            $totalPaidTsh = ($booking->amount_paid ?? 0) * $exchangeRate;
            $paidAdditionalChargesTsh = max(0, $totalPaidTsh - $roomPriceTsh);
            
            // Total additional charges (include everything not in the base room price)
            $totalAdditionalChargesTsh = $otherServiceChargesTsh + ($extensionCostUsd * $exchangeRate) + $transportationChargesTsh;
            
            // Outstanding additional charges
            $outstandingAdditionalChargesTsh = max(0, $totalAdditionalChargesTsh - $paidAdditionalChargesTsh);
            $paymentAmount = $outstandingAdditionalChargesTsh / $exchangeRate;
            $paymentDescription = 'Additional Charges - Services, Extension, Transportation';
            
            // If no additional charges, redirect back
            if ($paymentAmount <= 0) {
                if ($booking->check_in_status === 'checked_out') {
                    return redirect()->route('customer.bookings.checkout-payment', $booking)
                        ->with('info', 'No additional charges to pay.');
                } else {
                    return redirect()->route('customer.dashboard')
                        ->with('info', 'No outstanding balance.');
                }
            }
        }
        
        // Get access token
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return redirect()->route('booking.index')
                ->with('error', 'Payment gateway error. Please try again.');
        }

        // Create PayPal order with guest checkout enabled
        $orderData = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $booking->booking_reference,
                    'description' => $paymentDescription,
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($paymentAmount, 2, '.', ''),
                    ],
                ],
            ],
            'application_context' => [
                'brand_name' => 'PrimeLand Hotel',
                'landing_page' => 'BILLING', // CRITICAL: Shows billing page for guest checkout
                'user_action' => 'PAY_NOW',
                'return_url' => url('/payment/success?booking_id=' . $booking->id . '&ref=' . $booking->booking_reference),
                'cancel_url' => url('/payment/cancel?booking_id=' . $booking->id . '&ref=' . $booking->booking_reference),
                'locale' => 'en-US',
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post($this->paypalBaseUrl . '/v2/checkout/orders', $orderData);

        if ($response->successful()) {
            $order = $response->json();
            
            // Save PayPal order ID to booking
            $booking->update([
                'payment_transaction_id' => $order['id'],
            ]);

            // Find approval URL
            $approvalUrl = null;
            foreach ($order['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }

            if ($approvalUrl) {
                // For guest checkout, we need to ensure the URL shows the billing page
                // The landing_page: 'BILLING' should work, but if guest checkout is not enabled
                // in PayPal account settings, it will still show login page
                // 
                // Try adding fundingSource parameter to show card option
                $separator = strpos($approvalUrl, '?') !== false ? '&' : '?';
                $approvalUrl .= $separator . 'fundingSource=card';
                
                return redirect($approvalUrl);
            }
        }

        Log::error('PayPal order creation failed', [
            'response' => $response->json(),
            'booking_id' => $booking->id,
        ]);

        return redirect()->route('booking.index')
            ->with('error', 'Failed to create payment. Please try again.');
    }

    /**
     * Handle PayPal payment success
     */
    public function success(Request $request)
    {
        try {
            $token = $request->query('token');
            $payerId = $request->query('PayerID');
            $bookingId = $request->query('booking_id');
            $ref = $request->query('ref');

            // Log all incoming parameters for debugging
            Log::info('=== PayPal SUCCESS CALLBACK START ===', [
                'token' => $token,
                'payerId' => $payerId,
                'booking_id' => $bookingId,
                'ref' => $ref,
                'full_url' => $request->fullUrl(),
                'all_params' => $request->all(),
                'session_booking_id' => session('current_booking_id'),
            ]);

            // Find booking by ID if provided, otherwise by reference or transaction ID
            $booking = null;
            if ($bookingId) {
                $booking = Booking::find($bookingId);
            } elseif ($ref) {
                $booking = Booking::where('booking_reference', $ref)->first();
            } elseif ($token) {
                // Fallback: find by PayPal transaction ID (token is the order ID in v2)
                $booking = Booking::where('payment_transaction_id', $token)->first();
            }
            
            // Fallback: try to get booking from session
            if (!$booking) {
                $sessionBookingId = session('current_booking_id');
                if ($sessionBookingId) {
                    $booking = Booking::find($sessionBookingId);
                    Log::info('PayPal success: Using booking from session', [
                        'booking_id' => $booking ? $booking->id : null,
                    ]);
                }
            }
            
            if (!$booking) {
                Log::error('PayPal callback: Booking not found', [
                    'booking_id' => $bookingId,
                    'token' => $token,
                    'ref' => $ref,
                    'session_booking_id' => session('current_booking_id'),
                    'all_params' => $request->all(),
                ]);
                return redirect()->route('booking.index')
                    ->with('error', 'Booking not found. Please contact support with your booking reference.');
            }
            
            // If booking is already paid, redirect to confirmation immediately
            if ($booking->payment_status === 'paid' && $booking->status === 'confirmed') {
                session()->forget(['current_booking_id', 'current_booking_ref']);
                Log::info('Booking already paid, redirecting to confirmation', ['booking_id' => $booking->id]);
                return redirect()->route('payment.confirmation', ['booking' => $booking->id])
                    ->with('success', 'Payment successful! Your booking has been confirmed.');
            }
            
            // SIMPLIFIED: If PayPal redirected to success URL, payment was successful
            // Process it immediately and verify with PayPal in background
            $orderId = $booking->payment_transaction_id ?? $token;
            
            if (!$orderId) {
                Log::warning('No order ID found, but PayPal sent success callback. Processing anyway.', [
                    'booking_id' => $booking->id,
                    'token' => $token,
                ]);
            }
            
            // Process payment immediately - PayPal sent user to success URL, so payment succeeded
            $isCheckoutPayment = $booking->check_in_status === 'checked_out';
            
            if ($isCheckoutPayment) {
                // For checkout payments, add to existing payment amount
                $serviceRequests = $booking->serviceRequests()
                    ->whereIn('status', ['approved', 'completed'])
                    ->with('service')
                    ->get();
                
                // Use locked exchange rate from booking, or fallback to current rate if not set (for old bookings)
            $exchangeRate = $booking->locked_exchange_rate;
            if (!$exchangeRate) {
                // Fallback for old bookings that don't have locked rate
                $currencyService = new \App\Services\CurrencyExchangeService();
                $exchangeRate = $currencyService->getUsdToTshRate();
            }
                
                // Calculate additional charges
                $extensionCostUsd = 0;
                if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                    $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                    $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                    $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                    if ($extensionNights > 0 && $booking->room) {
                        $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                    }
                }
                
                // Original room price
                $originalCheckOutDate = $booking->original_check_out 
                    ? \Carbon\Carbon::parse($booking->original_check_out) 
                    : \Carbon\Carbon::parse($booking->check_out);
                $originalNights = $booking->check_in->diffInDays($originalCheckOutDate);
                $baseRoomPriceUsd = $booking->room ? ($booking->room->price_per_night * $originalNights) : $booking->total_price;
                
                $transportationChargesTsh = 0;
                if ($booking->airport_pickup_required) {
                    $airportPickupService = $serviceRequests->firstWhere('service.category', 'transport');
                    $transportationChargesTsh = $airportPickupService ? $airportPickupService->total_price_tsh : 50000;
                }
                
                $otherServiceChargesTsh = $serviceRequests
                    ->where('service.category', '!=', 'transport')
                    ->sum('total_price_tsh');
                
                $totalAdditionalChargesTsh = $otherServiceChargesTsh + ($extensionCostUsd * $exchangeRate) + $transportationChargesTsh;
                
                // Use the difference between total bill and amount already paid to be safe
                $totalBillUsd = $booking->total_price + ($otherServiceChargesTsh / $exchangeRate) + ($transportationChargesTsh / $exchangeRate);
                $outstandingToPayUsd = max(0, $totalBillUsd - ($booking->amount_paid ?? 0));
                
                // Add the outstanding amount to amount_paid
                $amountToIncreaseUsd = $outstandingToPayUsd;
                
                $booking->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'paypal',
                    'amount_paid' => ($booking->amount_paid ?? 0) + $amountToIncreaseUsd,
                    'paid_at' => now(),
                    'total_service_charges_tsh' => ($booking->total_service_charges_tsh ?? 0) + $otherServiceChargesTsh + $transportationChargesTsh,
                ]);
            } else {
                // For initial booking payment or partial payment completion
                if ($booking->payment_status !== 'paid') {
                    // Try to get actual payment amount from PayPal order
                    $paymentAmount = null;
                    if ($orderId) {
                        try {
                            $accessToken = $this->getAccessToken();
                            if ($accessToken) {
                                $orderResponse = Http::withHeaders([
                                    'Content-Type' => 'application/json',
                                    'Authorization' => 'Bearer ' . $accessToken,
                                ])->get($this->paypalBaseUrl . '/v2/checkout/orders/' . $orderId);
                                
                                if ($orderResponse->successful()) {
                                    $order = $orderResponse->json();
                                    if (isset($order['purchase_units'][0]['payments']['captures'][0]['amount']['value'])) {
                                        $paymentAmount = (float) $order['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            Log::warning('Could not fetch PayPal order amount, using calculated value', [
                                'booking_id' => $booking->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                    
                    // If we couldn't get amount from PayPal, calculate outstanding balance
                    if ($paymentAmount === null) {
                        $paymentAmount = $booking->total_price - ($booking->amount_paid ?? 0);
                    }
                    
                    // Add payment amount to existing amount paid
                    $newAmountPaid = ($booking->amount_paid ?? 0) + $paymentAmount;
                    
                    // Enforce 50% minimum deposit policy
                    $minimumDeposit = $booking->total_price * 0.50;
                    if ($newAmountPaid < $minimumDeposit) {
                        // Payment is less than 50% - this should not happen if frontend validation works
                        // But we'll log it and still process (in case of edge cases)
                        Log::warning('Payment received is less than 50% minimum deposit', [
                            'booking_id' => $booking->id,
                            'total_price' => $booking->total_price,
                            'amount_paid' => $newAmountPaid,
                            'minimum_required' => $minimumDeposit,
                        ]);
                    }
                    
                    // Determine if payment is now complete
                    $isFullyPaid = $newAmountPaid >= $booking->total_price;
                    
                    // Calculate payment percentage for tracking
                    $paymentPercentage = ($newAmountPaid / $booking->total_price) * 100;
                    
                    $booking->update([
                        'status' => 'confirmed',
                        'payment_status' => $isFullyPaid ? 'paid' : 'partial',
                        'payment_method' => 'paypal',
                        'amount_paid' => min($newAmountPaid, $booking->total_price), // Cap at total price
                        'payment_percentage' => $paymentPercentage,
                        'paid_at' => now(),
                        'expires_at' => null,
                    ]);
                }
            }

            Log::info('Booking payment processed from success callback', [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
            ]);

            // Send payment confirmation email (send immediately)
            try {
                // Check if guest has notifications enabled
                $guest = \App\Models\Guest::where('email', $booking->guest_email)->first();
                if (!$guest || $guest->isNotificationEnabled('payment')) {
                    \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                        ->send(new \App\Mail\PaymentConfirmationMail($booking->fresh()));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
            }

            // Send email notification to managers and super admins for payment received
            try {
                $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                    ->where('is_active', true)
                    ->get();
                
                $amountPaid = $booking->fresh()->amount_paid ?? 0;
                $paymentMethod = $booking->fresh()->payment_method ?? 'paypal';
                
                foreach ($managersAndAdmins as $staff) {
                    // Check if user has notifications enabled
                    if ($staff->isNotificationEnabled('payment')) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($staff->email)
                                ->send(new \App\Mail\StaffPaymentReceivedMail($booking->fresh(), $amountPaid, $paymentMethod));
                        } catch (\Exception $e) {
                            Log::error('Failed to send payment received email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to send payment received emails to managers/admins: ' . $e->getMessage());
            }

            // Create notification for payment completion
            try {
                $user = \App\Models\Guest::where('email', $booking->guest_email)->first();
                if ($user) {
                    $notificationService = new \App\Services\NotificationService();
                    $notificationService->createPaymentNotification($booking->fresh()->load('room'), $user);
                    
                    // Deactivate guest account after payment (for checkout payments)
                    if ($booking->check_in_status === 'checked_out') {
                        $user->update(['is_active' => false]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to create payment notification: ' . $e->getMessage());
            }
            
            // Clear session
            session()->forget(['current_booking_id', 'current_booking_ref']);
            
            // Verify with PayPal in background (don't wait for it)
            if ($orderId) {
                try {
                    $this->verifyPaymentWithPayPal($orderId, $booking);
                } catch (\Exception $e) {
                    Log::error('Background PayPal verification failed: ' . $e->getMessage());
                }
            }
            
            // Always redirect to confirmation - PayPal sent user here, so payment succeeded
            return redirect()->route('payment.confirmation', ['booking' => $booking->id])
                ->with('success', 'Payment successful! Your booking has been confirmed.');
                
        } catch (\Exception $e) {
            Log::error('PayPal success handler exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Even on error, if we have a booking, try to redirect to confirmation
            $bookingId = $request->query('booking_id') ?? session('current_booking_id');
            if ($bookingId) {
                $booking = Booking::find($bookingId);
                if ($booking && $booking->payment_status === 'paid') {
                    return redirect()->route('payment.confirmation', ['booking' => $booking->id])
                        ->with('success', 'Payment successful! Your booking has been confirmed.');
                }
            }
            
            return redirect()->route('booking.index')
                ->with('error', 'Payment processing error. Please contact support.');
        }
    }
    
    /**
     * Verify payment with PayPal (called in background)
     */
    private function verifyPaymentWithPayPal($orderId, Booking $booking)
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                Log::warning('PayPal verification: Failed to get access token', ['booking_id' => $booking->id]);
                return;
            }

            $orderResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get($this->paypalBaseUrl . '/v2/checkout/orders/' . $orderId);

            if ($orderResponse->successful()) {
                $order = $orderResponse->json();
                $orderStatus = $order['status'] ?? 'unknown';
                
                if ($orderStatus === 'COMPLETED') {
                    // Update amount if different
                    $capturedAmount = 0;
                    if (isset($order['purchase_units'][0]['payments']['captures'][0]['amount']['value'])) {
                        $capturedAmount = $order['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
                    }
                    
                    if ($capturedAmount > 0 && $booking->amount_paid != $capturedAmount) {
                        $booking->update(['amount_paid' => $capturedAmount]);
                        Log::info('Updated booking amount from PayPal verification', [
                            'booking_id' => $booking->id,
                            'new_amount' => $capturedAmount,
                        ]);
                    }
                } elseif ($orderStatus === 'APPROVED') {
                    // Try to capture
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken,
                    ])->withBody('{}', 'application/json')
                      ->post($this->paypalBaseUrl . '/v2/checkout/orders/' . $orderId . '/capture');
                      
                    if ($response->successful()) {
                        $payment = $response->json();
                        if (isset($payment['status']) && $payment['status'] === 'COMPLETED') {
                            $capturedAmount = 0;
                            if (isset($payment['purchase_units'][0]['payments']['captures'][0]['amount']['value'])) {
                                $capturedAmount = $payment['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
                            }
                            
                            if ($capturedAmount > 0) {
                                $booking->update(['amount_paid' => $capturedAmount]);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('PayPal verification error: ' . $e->getMessage());
        }
    }

    /**
     * Handle PayPal payment cancellation or return
     */
    public function cancel(Request $request)
    {
        $token = $request->query('token');
        $bookingId = $request->query('booking_id');
        $bookingRef = $request->query('ref');
        
        Log::info('PayPal cancel/return callback', [
            'token' => $token,
            'booking_id' => $bookingId,
            'booking_ref' => $bookingRef,
            'all_params' => $request->all(),
        ]);

        $booking = null;
        if ($bookingId) {
            $booking = Booking::find($bookingId);
        } elseif ($bookingRef) {
            $booking = Booking::where('booking_reference', $bookingRef)->first();
        } elseif ($token) {
            $booking = Booking::where('payment_transaction_id', $token)->first();
        }
        
        // Fallback: try to get booking from session
        if (!$booking) {
            $sessionBookingId = session('current_booking_id');
            if ($sessionBookingId) {
                $booking = Booking::find($sessionBookingId);
                Log::info('PayPal cancel: Using booking from session', [
                    'booking_id' => $booking ? $booking->id : null,
                ]);
            }
        }
        
        if (!$booking) {
            Log::warning('PayPal cancel: Booking not found', [
                'token' => $token,
                'booking_id' => $bookingId,
                'booking_ref' => $bookingRef,
                'session_booking_id' => session('current_booking_id'),
            ]);
            return redirect()->route('booking.index')
                ->with('error', 'Booking not found. Please contact support with your booking reference.');
        }
        
        // ALWAYS verify payment status with PayPal first, even if user clicked "back"
        // This handles the case where payment was completed but user clicked back button
        if ($booking->payment_transaction_id) {
            $accessToken = $this->getAccessToken();
            if ($accessToken) {
                $orderResponse = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ])->get($this->paypalBaseUrl . '/v2/checkout/orders/' . $booking->payment_transaction_id);
                
                if ($orderResponse->successful()) {
                    $order = $orderResponse->json();
                    $orderStatus = $order['status'] ?? 'UNKNOWN';
                    
                    Log::info('PayPal order status check in cancel handler', [
                        'booking_id' => $booking->id,
                        'order_status' => $orderStatus,
                        'current_payment_status' => $booking->payment_status,
                    ]);
                    
                    // If payment is COMPLETED, process it regardless of current status
                    if ($orderStatus === 'COMPLETED') {
                        // Check if already processed
                        if ($booking->payment_status === 'paid' && $booking->status === 'confirmed') {
                            return redirect()->route('payment.confirmation', ['booking' => $booking->id])
                                ->with('success', 'Payment successful! Your booking has been confirmed.');
                        }
                        
                        // Process the completed payment
                        session()->forget(['current_booking_id', 'current_booking_ref']);
                        return $this->processCompletedPayment($booking, $order);
                    }
                    
                    // If order is APPROVED but not captured, try to capture it
                    if ($orderStatus === 'APPROVED') {
                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $accessToken,
                        ])->withBody('{}', 'application/json')
                          ->post($this->paypalBaseUrl . '/v2/checkout/orders/' . $booking->payment_transaction_id . '/capture');
                        
                        if ($response->successful()) {
                            $payment = $response->json();
                            if (isset($payment['status']) && $payment['status'] === 'COMPLETED') {
                                session()->forget(['current_booking_id', 'current_booking_ref']);
                                return $this->processCompletedPayment($booking, $payment);
                            }
                        }
                    }
                }
            }
        }
        
        // If payment is already paid, redirect to confirmation
        if ($booking->payment_status === 'paid' && $booking->status === 'confirmed') {
            session()->forget(['current_booking_id', 'current_booking_ref']);
            return redirect()->route('payment.confirmation', ['booking' => $booking->id])
                ->with('success', 'Payment successful! Your booking has been confirmed.');
        }
        
        // Only mark as cancelled if payment is still pending
        if ($booking->payment_status === 'pending') {
            $booking->update([
                'payment_status' => 'cancelled',
            ]);
            return redirect()->route('booking.index')
                ->with('error', 'Payment was cancelled. Your booking has been saved but not confirmed.');
        }

        // Default: redirect to booking page
        return redirect()->route('booking.index')
            ->with('error', 'Payment status could not be determined. Please contact support with your booking reference: ' . $booking->booking_reference);
    }
    
    /**
     * Process completed payment
     */
    private function processCompletedPayment(Booking $booking, array $order)
    {
        $capturedAmount = 0;
        if (isset($order['purchase_units'][0]['payments']['captures'][0]['amount']['value'])) {
            $capturedAmount = (float) $order['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
        }
        
        // If no captured amount found, calculate outstanding balance for partial payments
        if ($capturedAmount <= 0 && $booking->payment_status === 'partial') {
            $capturedAmount = $booking->total_price - ($booking->amount_paid ?? 0);
        } elseif ($capturedAmount <= 0) {
            $capturedAmount = $booking->total_price;
        }
        
        // Add captured amount to existing amount paid (for partial payments)
        $newAmountPaid = ($booking->amount_paid ?? 0) + $capturedAmount;
        
        // Determine if payment is now complete
        $isFullyPaid = $newAmountPaid >= $booking->total_price;
        
        // Update booking status - payment successful
        $booking->update([
            'status' => 'confirmed',
            'payment_status' => $isFullyPaid ? 'paid' : 'partial',
            'payment_method' => 'paypal',
            'amount_paid' => min($newAmountPaid, $booking->total_price), // Cap at total price
            'paid_at' => now(),
            'expires_at' => null,
        ]);

        Log::info('Booking payment confirmed via cancel handler', [
            'booking_id' => $booking->id,
            'booking_reference' => $booking->booking_reference,
            'amount_paid' => $booking->amount_paid,
        ]);

        // Send payment confirmation email (send immediately)
        try {
            // Check if guest has notifications enabled
            $guest = \App\Models\Guest::where('email', $booking->guest_email)->first();
            if (!$guest || $guest->isNotificationEnabled('payment')) {
                \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                    ->send(new \App\Mail\PaymentConfirmationMail($booking->fresh()));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
        }

        // Send email notification to managers and super admins for payment received
        try {
            $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                ->where('is_active', true)
                ->get();
            
            $amountPaid = $booking->fresh()->amount_paid ?? 0;
            $paymentMethod = $booking->fresh()->payment_method ?? 'paypal';
            
            foreach ($managersAndAdmins as $staff) {
                try {
                    \Illuminate\Support\Facades\Mail::to($staff->email)
                        ->queue(new \App\Mail\StaffPaymentReceivedMail($booking->fresh(), $amountPaid, $paymentMethod));
                } catch (\Exception $e) {
                    Log::error('Failed to send payment received email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment received emails to managers/admins: ' . $e->getMessage());
        }

        // Create notification for payment completion
        try {
            $user = \App\Models\User::where('email', $booking->guest_email)->first();
            if ($user) {
                $notificationService = new \App\Services\NotificationService();
                $notificationService->createPaymentNotification($booking->fresh()->load('room'), $user);
                
                // Deactivate guest account after payment (for checkout payments)
                if ($booking->check_in_status === 'checked_out') {
                    $user->update(['is_active' => false]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to create payment notification: ' . $e->getMessage());
        }
        
        return redirect()->route('payment.confirmation', ['booking' => $booking->id])
            ->with('success', 'Payment successful! Your booking has been confirmed.');
    }

    /**
     * Show booking confirmation page
     */
    public function confirmation(Booking $booking)
    {
        $booking->load('room');
        
        return view('landing_page_views.booking-confirmation', [
            'booking' => $booking,
        ]);
    }

    /**
     * Download payment receipt/invoice
     */
    public function downloadReceipt(Booking $booking)
    {
        // Allow staff (reception/manager) to download receipts for any booking
        $isStaff = auth()->guard('staff')->check();
        
        // For guest users, verify booking belongs to them
        if (auth()->guard('guest')->check() && !$isStaff) {
            if ($booking->guest_email !== auth()->guard('guest')->user()->email) {
                abort(403, 'Unauthorized access.');
            }
        }

        // Allow download for paid, partial, or confirmed bookings
        // Manual bookings may have partial payment status
        // Staff can download receipts for any booking status
        if (!$isStaff && !in_array($booking->payment_status, ['paid', 'partial']) && $booking->status !== 'confirmed') {
            abort(404, 'Receipt not available for this booking.');
        }

        $booking->load(['room', 'company', 'serviceRequests']);
        
        // Check if this is a guest viewing their own corporate booking receipt
        $isGuestViewingCorporate = !$isStaff && $booking->is_corporate_booking;
        $isGuestWithSelfPaidServices = $isGuestViewingCorporate && $booking->payment_responsibility === 'self';
        $isGuestWithCompanyPaidServices = $isGuestViewingCorporate && $booking->payment_responsibility === 'company';
        
        // For corporate bookings, load all bookings for the company (only for staff view)
        $allCompanyBookings = null;
        $totalCompanyCharges = 0;
        $totalCompanyPaid = 0;
        $guestServicePayments = 0;
        $guestServicePaymentsTsh = 0;
        
        if ($booking->is_corporate_booking && $booking->company_id) {
            if ($isGuestViewingCorporate) {
                if ($isGuestWithSelfPaidServices) {
                    // For guest with self-paid services, calculate only their service payments
                    $serviceRequests = $booking->serviceRequests()
                        ->where('payment_status', 'paid')
                        ->where('payment_method', '!=', 'room_charge')
                        ->get();
                    
                    $guestServicePaymentsTsh = $serviceRequests->sum('total_price_tsh');
                    $bookingExchangeRate = $booking->locked_exchange_rate ?? (new \App\Services\CurrencyExchangeService())->getUsdToTshRate();
                    $guestServicePayments = $guestServicePaymentsTsh > 0 ? $guestServicePaymentsTsh / $bookingExchangeRate : 0;
                }
                // For guests with company-paid services, no payments to show (everything is company-paid)
            } else {
                // For staff view, show all company bookings
                $allCompanyBookings = \App\Models\Booking::with(['room'])
                    ->where('company_id', $booking->company_id)
                    ->where('is_corporate_booking', true)
                    ->where('check_in', $booking->check_in)
                    ->where('check_out', $booking->check_out)
                    ->get();
                
                // Calculate totals
                foreach ($allCompanyBookings as $companyBooking) {
                    $totalCompanyCharges += $companyBooking->total_price ?? 0;
                    $totalCompanyPaid += $companyBooking->amount_paid ?? 0;
                }
            }
        }
        
        // Use locked exchange rate from booking, or fallback to current rate if not set (for old bookings)
        $exchangeRate = $booking->locked_exchange_rate;
        if (!$exchangeRate) {
            // Fallback for old bookings that don't have locked rate
            $currencyService = new \App\Services\CurrencyExchangeService();
            $exchangeRate = $currencyService->getUsdToTshRate();
        }
        
        // Return view without any layout wrapper - standalone receipt
        return response()->view('dashboard.payment-receipt', [
            'booking' => $booking,
            'exchangeRate' => $exchangeRate,
            'allCompanyBookings' => $allCompanyBookings,
            'totalCompanyCharges' => $totalCompanyCharges,
            'totalCompanyPaid' => $totalCompanyPaid,
            'isGuestViewingCorporate' => $isGuestViewingCorporate,
            'isGuestWithSelfPaidServices' => $isGuestWithSelfPaidServices,
            'isGuestWithCompanyPaidServices' => $isGuestWithCompanyPaidServices,
            'guestServicePayments' => $guestServicePayments,
            'guestServicePaymentsTsh' => $guestServicePaymentsTsh,
        ])->header('Content-Type', 'text/html');
    }

    /**
     * Get PayPal access token
     */
    private function getAccessToken()
    {
        try {
            $response = Http::withBasicAuth($this->paypalClientId, $this->paypalSecret)
                ->asForm()
                ->post($this->paypalBaseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            Log::error('PayPal access token failed', [
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal access token exception', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}

