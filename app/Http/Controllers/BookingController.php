<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use App\Models\Guest;
use App\Models\HotelSetting;
use App\Models\Notification;
use App\Mail\BookingConfirmationMail;
use App\Mail\StaffNewBookingMail;
use App\Services\CurrencyExchangeService;
use App\Services\NotificationService;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Show the booking page with search form
     * TEMPORARILY DISABLED - Online booking coming soon
     */
    public function index(Request $request)
    {
        // Online booking is temporarily disabled - show coming soon page
        return view('landing_page_views.booking-coming-soon');
    }

    /**
     * Check availability and return available rooms
     * TEMPORARILY DISABLED - Online booking coming soon
     */
    public function checkAvailability(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Online booking is temporarily unavailable. Please contact us directly to make a reservation.',
        ], 503);
    }

    /**
     * Find and assign an available room of the specified type
     */
    private function assignAvailableRoom($roomType, $checkIn, $checkOut)
    {
        return Room::where('room_type', $roomType)
            ->whereIn('status', ['available', 'occupied', 'to_be_cleaned'])
            ->whereDoesntHave('bookings', function ($query) use ($checkIn, $checkOut) {
                // Block rooms for bookings (pending or confirmed) that are valid
                $query->whereIn('status', ['pending', 'confirmed'])
                      ->where(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in', '<', $checkOut)
                            ->where('check_out', '>', $checkIn);
                      })
                      ->where(function ($q) {
                          // Block if:
                          // 1. Confirmed with paid/partial payment
                          // 2. Confirmed with pending payment and valid deadline
                          // 3. Pending status with valid expiration (not expired)
                          $q->where(function ($statusQ) {
                              // Confirmed bookings
                              $statusQ->where('status', 'confirmed')
                                      ->where(function ($paymentQ) {
                                          $paymentQ->whereIn('payment_status', ['paid', 'partial'])
                                                  ->orWhere(function ($subQ) {
                                                      $subQ->where('payment_status', 'pending')
                                                           ->where(function ($deadlineQ) {
                                                               $deadlineQ->whereNull('payment_deadline')
                                                                        ->orWhere('payment_deadline', '>', Carbon::now());
                                                           });
                                                  });
                                      });
                          })
                          ->orWhere(function ($pendingQ) {
                              // Pending bookings that haven't expired
                              $pendingQ->where('status', 'pending')
                                      ->where(function ($expireQ) {
                                          $expireQ->whereNull('expires_at')
                                                 ->orWhere('expires_at', '>', Carbon::now());
                                      });
                          });
                      });
            })
            ->orderBy('status', 'asc') // available comes before occupied
            ->orderBy('room_number', 'asc') // Assign in order
            ->first();
    }

    /**
     * Store a new booking
     * TEMPORARILY DISABLED - Online booking coming soon
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Online booking is temporarily unavailable. Please contact us directly to make a reservation.',
        ], 503);
    }

    /**
     * OLD STORE METHOD - TEMPORARILY DISABLED
     * This code is preserved for when online booking is re-enabled
     * Commented out to prevent execution
     */
    /*
    private function storeBookingOld(Request $request)
    {
        // Support both room_type (new) and room_id (backward compatibility for admin)
        $validated = $request->validate([
            'room_type' => 'nullable|in:Single,Double,Twins',
            'room_id' => 'nullable|exists:rooms,id', // Keep for backward compatibility
            'guest_name' => 'required|string|max:255',
            'guest_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'country' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:10',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'number_of_guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
            'arrival_time' => 'nullable|string|max:50',
            'booking_for' => 'required|in:me,someone',
            'guest_first_name' => 'nullable|string|max:255|required_if:booking_for,someone',
            'guest_last_name' => 'nullable|string|max:255|required_if:booking_for,someone',
            'main_guest_name' => 'nullable|string|max:255',
            'terms_accepted' => 'required|accepted',
            'airport_pickup_required' => 'nullable|in:0,1,true,false,"0","1","true","false"',
            'flight_number' => 'nullable|string|max:50',
            'airline' => 'nullable|string|max:100',
            'arrival_time_pickup' => 'nullable|date',
            'pickup_passengers' => 'nullable|integer|min:1',
            'luggage_info' => 'nullable|string|max:500',
            'pickup_contact_number' => 'nullable|string|max:20',
        ]);

        // Validate that either room_type or room_id is provided
        if (empty($validated['room_type']) && empty($validated['room_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Either room_type or room_id must be provided.'
            ], 422);
        }

        // Custom validation for airport pickup fields when airport pickup is required
        $airportPickupRequired = filter_var($request->input('airport_pickup_required', false), FILTER_VALIDATE_BOOLEAN);
        if ($airportPickupRequired) {
            $request->validate([
                'flight_number' => 'required|string|max:50',
                'airline' => 'required|string|max:100',
                'arrival_time_pickup' => 'required|date',
                'pickup_passengers' => 'required|integer|min:1',
                'pickup_contact_number' => 'required|string|max:20',
            ], [
                'flight_number.required' => 'The flight number field is required when airport pickup is selected.',
                'airline.required' => 'The airline field is required when airport pickup is selected.',
                'arrival_time_pickup.required' => 'The arrival time pickup field is required when airport pickup is selected.',
                'pickup_passengers.required' => 'The number of passengers field is required when airport pickup is selected.',
                'pickup_contact_number.required' => 'The pickup contact number field is required when airport pickup is selected.',
            ]);
        }

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);

        // Determine room assignment: use room_type (new) or room_id (backward compatibility)
        if (!empty($validated['room_type'])) {
            // New flow: Auto-assign room by type
            $room = $this->assignAvailableRoom($validated['room_type'], $checkIn, $checkOut);
            
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, no ' . $validated['room_type'] . ' rooms are available for the selected dates.'
                ], 422);
            }
        } else {
            // Backward compatibility: Use provided room_id (for admin/manual bookings)
            $room = Room::findOrFail($validated['room_id']);
            
            // Check for date conflicts
            // Block rooms for confirmed bookings that are valid (paid/partial OR pending with valid deadline)
            $hasConflict = Booking::where('room_id', $room->id)
                ->where('status', 'confirmed')
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->where('check_in', '<', $checkOut)
                          ->where('check_out', '>', $checkIn);
                })
                ->where(function ($q) {
                    // Block if paid/partial OR pending with valid deadline
                    $q->whereIn('payment_status', ['paid', 'partial'])
                      ->orWhere(function ($subQ) {
                          $subQ->where('payment_status', 'pending')
                               ->where(function ($deadlineQ) {
                                   $deadlineQ->whereNull('payment_deadline')
                                            ->orWhere('payment_deadline', '>', Carbon::now());
                               });
                      });
                })
                ->exists();

            if ($hasConflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, this room is no longer available for the selected dates.'
                ], 422);
            }
        }
        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = $room->price_per_night * $nights;

        // Get and lock the exchange rate at booking time
        $currencyService = new CurrencyExchangeService();
        $lockedExchangeRate = $currencyService->getUsdToTshRate();

        // Generate unique booking reference
        $bookingReference = 'BK' . strtoupper(Str::random(8));
        
        // Generate unique guest ID (GST-12345 format)
        $guestId = $this->generateGuestId();

        // Calculate payment deadline based on new policy:
        // - Full pre-payment required not later than 30 days prior to arrival
        // - For bookings within 20 days of arrival: 50% required within 48 hours
        $daysUntilArrival = Carbon::now()->diffInDays($checkIn, false);
        $paymentDeadline = null;
        $expiresAt = null;
        
        // For pending payments, set expiration to 10 minutes from now
        // This ensures the booking expires if payment is not completed within the time limit
        $expiresAt = Carbon::now()->addMinutes(10);
        
        if ($daysUntilArrival >= 30) {
            // More than 30 days: Full payment required 30 days before arrival
            $paymentDeadline = $checkIn->copy()->subDays(30);
        } elseif ($daysUntilArrival >= 20) {
            // 20-29 days: Full payment required 30 days before (which is in the past, so use 48 hours)
            $paymentDeadline = Carbon::now()->addHours(48);
        } else {
            // Less than 20 days: 50% required within 48 hours
            $paymentDeadline = Carbon::now()->addHours(48);
        }

        // Auto-generate password from first name (in CAPITALS)
        $password = strtoupper($validated['first_name']);

        // Create or update guest user account
        $user = User::firstOrNew(['email' => $validated['guest_email']]);
        $isNewUser = !$user->exists;
        if (!$user->exists) {
            // Create new user account
            $user->name = $validated['guest_name'];
            $user->email = $validated['guest_email'];
            $user->password = $password; // Laravel will auto-hash due to 'hashed' cast
            $user->role = 'customer';
            $user->save();
        } else {
            // Update existing user password with auto-generated password
            $user->password = $password; // Laravel will auto-hash due to 'hashed' cast
            $user->save();
        }

        // Create the booking
        $booking = Booking::create([
            'room_id' => $room->id, // Use assigned room ID
            'guest_name' => $validated['guest_name'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'guest_email' => $validated['guest_email'],
            'country' => $validated['country'],
            'guest_phone' => $validated['guest_phone'],
            'country_code' => $validated['country_code'],
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'number_of_guests' => $validated['number_of_guests'],
            'special_requests' => $validated['special_requests'] ?? null,
            'arrival_time' => $validated['arrival_time'] ?? null,
            'booking_for' => $validated['booking_for'],
            'guest_first_name' => $validated['guest_first_name'] ?? null,
            'guest_last_name' => $validated['guest_last_name'] ?? null,
            'main_guest_name' => $validated['main_guest_name'] ?? null,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'pending',
            'booking_reference' => $bookingReference,
            'guest_id' => $guestId,
            'expires_at' => $expiresAt,
            'payment_deadline' => $paymentDeadline,
            'airport_pickup_required' => filter_var($validated['airport_pickup_required'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'flight_number' => $validated['flight_number'] ?? null,
            'airline' => $validated['airline'] ?? null,
            'arrival_time_pickup' => !empty($validated['arrival_time_pickup']) ? Carbon::parse($validated['arrival_time_pickup']) : null,
            'pickup_passengers' => $validated['pickup_passengers'] ?? null,
            'luggage_info' => $validated['luggage_info'] ?? null,
            'pickup_contact_number' => $validated['pickup_contact_number'] ?? null,
            'locked_exchange_rate' => $lockedExchangeRate, // Lock exchange rate at booking time
        ]);

        // Send booking confirmation email
        try {
            // Send email (same approach as password reset which works - no MailConfigService)
            Mail::to($validated['guest_email'])->queue(new BookingConfirmationMail($booking, $password));
        } catch (\Exception $e) {
            // Log error but don't fail the booking
            \Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
        }

        // Send welcome email if this is a new user account
        if ($isNewUser) {
            try {
                Mail::to($validated['guest_email'])->queue(new \App\Mail\WelcomeMail($user, $password));
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email: ' . $e->getMessage());
            }
        }

        // Create notification for new booking
        try {
            $notificationService = new NotificationService();
            $notificationService->createBookingNotification($booking->load('room'));
        } catch (\Exception $e) {
            \Log::error('Failed to create booking notification: ' . $e->getMessage());
        }

        // Send email notification to managers and super admins for new booking
        try {
            $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                ->where('is_active', true)
                ->get();
            
                foreach ($managersAndAdmins as $staff) {
                    // Check if user has notifications enabled
                    if ($staff->isNotificationEnabled('booking')) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($staff->email)
                                ->send(new \App\Mail\StaffNewBookingMail($booking->fresh()->load('room')));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send new booking email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                        }
                    }
                }
        } catch (\Exception $e) {
            \Log::error('Failed to send new booking emails to managers/admins: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking submitted successfully! Redirecting to payment...',
            'booking' => $booking->load('room'),
            'booking_reference' => $bookingReference,
            'booking_id' => $booking->id,
            'redirect_url' => route('payment.create', ['booking_id' => $booking->id]),
        ]);
    }
    */

    /**
     * Display all bookings for admin
     */
    public function adminIndex(Request $request)
    {
        $query = Booking::with(['room', 'company'])->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'expired') {
                // Show expired bookings (pending bookings that have expired)
                $query->where(function($q) {
                    $q->where(function($subQ) {
                        // Pending bookings that have expired
                        $subQ->where('status', 'pending')
                             ->where('payment_status', 'pending')
                             ->whereNotNull('expires_at')
                             ->where('expires_at', '<=', Carbon::now());
                    })->orWhere(function($subQ) {
                        // Cancelled bookings with expiration reason
                        $subQ->where('status', 'cancelled')
                             ->whereNotNull('cancellation_reason')
                             ->where('cancellation_reason', 'like', '%expired%');
                    });
                });
            } else {
                $query->where('status', $request->status);
            }
        } else {
            // Exclude expired bookings from main list
            // Only show bookings that haven't expired
            $query->where(function($q) {
                $q->where(function($subQ) {
                    // Not pending with expired timestamp
                    $subQ->where('status', '!=', 'pending')
                         ->orWhere('payment_status', '!=', 'pending')
                         ->orWhereNull('expires_at')
                         ->orWhere('expires_at', '>', Carbon::now());
                });
            });
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by check-in status
        if ($request->has('check_in_status') && $request->check_in_status) {
            $query->where('check_in_status', $request->check_in_status);
        }

        // Filter by booking type (individual or corporate)
        $bookingType = $request->get('type', 'individual'); // Default to individual
        if ($bookingType === 'corporate') {
            $query->where('is_corporate_booking', true);
            
            // Group corporate bookings by company_id
            // Get unique company IDs first
            $companyIds = $query->whereNotNull('company_id')->distinct()->pluck('company_id');
            
            // Get bookings grouped by company
            $groupedBookings = collect();
            foreach ($companyIds as $companyId) {
                $companyBookings = Booking::with(['room', 'company'])
                    ->where('is_corporate_booking', true)
                    ->where('company_id', $companyId);
                
                // Apply filters
                if ($request->has('status') && $request->status) {
                    if ($request->status === 'expired') {
                        $companyBookings->where(function($q) {
                            $q->where(function($subQ) {
                                $subQ->where('status', 'pending')
                                     ->where('payment_status', 'pending')
                                     ->whereNotNull('expires_at')
                                     ->where('expires_at', '<=', Carbon::now());
                            })->orWhere(function($subQ) {
                                $subQ->where('status', 'cancelled')
                                     ->whereNotNull('cancellation_reason')
                                     ->where('cancellation_reason', 'like', '%expired%');
                            });
                        });
                    } else {
                        $companyBookings->where('status', $request->status);
                    }
                } else {
                    $companyBookings->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->where('status', '!=', 'pending')
                                 ->orWhere('payment_status', '!=', 'pending')
                                 ->orWhereNull('expires_at')
                                 ->orWhere('expires_at', '>', Carbon::now());
                        });
                    });
                }
                
                if ($request->has('payment_status') && $request->payment_status) {
                    $companyBookings->where('payment_status', $request->payment_status);
                }
                
                if ($request->has('check_in_status') && $request->check_in_status) {
                    $companyBookings->where('check_in_status', $request->check_in_status);
                }
                
                // Search by guest name, booking reference, or company name
                if ($request->has('search') && $request->search) {
                    $search = $request->search;
                    $companyBookings->where(function($q) use ($search) {
                        $q->where('guest_name', 'like', "%{$search}%")
                          ->orWhere('booking_reference', 'like', "%{$search}%")
                          ->orWhere('guest_email', 'like', "%{$search}%")
                          ->orWhereHas('company', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                          });
                    });
                }
                
                $bookingsForCompany = $companyBookings->orderBy('created_at', 'desc')->get();
                
                if ($bookingsForCompany->count() > 0) {
                    $groupedBookings->push([
                        'company' => $bookingsForCompany->first()->company,
                        'bookings' => $bookingsForCompany,
                        'first_booking' => $bookingsForCompany->first(), // Use first booking for dates, etc.
                    ]);
                }
            }
            
            // Convert to paginator-like structure
            $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
                $groupedBookings->forPage($request->get('page', 1), 20),
                $groupedBookings->count(),
                20,
                $request->get('page', 1),
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Default to individual bookings (is_corporate_booking is false or null)
            $query->where(function($q) {
                $q->where('is_corporate_booking', false)
                  ->orWhereNull('is_corporate_booking');
            });
            
            // Search by guest name or booking reference
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('guest_name', 'like', "%{$search}%")
                      ->orWhere('booking_reference', 'like', "%{$search}%")
                      ->orWhere('guest_email', 'like', "%{$search}%");
                });
            }
            
            $bookings = $query->paginate(20);
        }

        // Get statistics filtered by booking type
        if ($bookingType === 'corporate') {
            // For corporate bookings, count unique companies
            $baseQuery = Booking::where('is_corporate_booking', true);
            
            // Apply status filter if provided
            if ($request->has('status') && $request->status == 'expired') {
                $baseQuery->where(function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('status', 'pending')
                             ->where('payment_status', 'pending')
                             ->whereNotNull('expires_at')
                             ->where('expires_at', '<=', Carbon::now());
                    })->orWhere(function($subQ) {
                        $subQ->where('status', 'cancelled')
                             ->whereNotNull('cancellation_reason')
                             ->where('cancellation_reason', 'like', '%expired%');
                    });
                });
            } else if ($request->has('status') && $request->status) {
                $baseQuery->where('status', $request->status);
            }
            
            // Count unique companies
            $totalCompanies = $baseQuery->whereNotNull('company_id')->distinct('company_id')->count('company_id');
            
            // For other stats, count companies that have bookings matching the criteria
            $confirmedCompanies = (clone $baseQuery)->where('status', 'confirmed')->whereNotNull('company_id')->distinct('company_id')->count('company_id');
            $checkedInCompanies = (clone $baseQuery)->where('check_in_status', 'checked_in')->whereNotNull('company_id')->distinct('company_id')->count('company_id');
            $checkedOutCompanies = (clone $baseQuery)->where('check_in_status', 'checked_out')->whereNotNull('company_id')->distinct('company_id')->count('company_id');
            
            // Also get overall stats for tabs
            $allIndividualQuery = Booking::where(function($q) {
                $q->where('is_corporate_booking', false)
                  ->orWhereNull('is_corporate_booking');
            });
            
            $allCorporateQuery = Booking::where('is_corporate_booking', true);
            
            $stats = [
                'total' => $totalCompanies,
                'individual_total' => $allIndividualQuery->count(),
                'corporate_total' => $allCorporateQuery->whereNotNull('company_id')->distinct('company_id')->count('company_id'),
                'pending' => 0, // Not applicable for corporate view
                'confirmed' => $confirmedCompanies,
                'cancelled' => 0, // Not applicable for corporate view
                'completed' => 0, // Not applicable for corporate view
                'expired' => 0, // Not applicable for corporate view
                'checked_in' => $checkedInCompanies,
                'checked_out' => $checkedOutCompanies,
            ];
        } else {
            // For individual bookings, count individual bookings
            $baseQuery = Booking::where(function($q) {
                $q->where('is_corporate_booking', false)
                  ->orWhereNull('is_corporate_booking');
            });
            
            // Apply status filter if provided
            if ($request->has('status') && $request->status == 'expired') {
                $baseQuery->where(function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('status', 'pending')
                             ->where('payment_status', 'pending')
                             ->whereNotNull('expires_at')
                             ->where('expires_at', '<=', Carbon::now());
                    })->orWhere(function($subQ) {
                        $subQ->where('status', 'cancelled')
                             ->whereNotNull('cancellation_reason')
                             ->where('cancellation_reason', 'like', '%expired%');
                    });
                });
            } else if ($request->has('status') && $request->status) {
                $baseQuery->where('status', $request->status);
            }
            
            // Also get overall stats for tabs
            $allIndividualQuery = clone $baseQuery;
            $allCorporateQuery = Booking::where('is_corporate_booking', true);
            
            $stats = [
                'total' => $baseQuery->count(),
                'individual_total' => $allIndividualQuery->count(),
                'corporate_total' => $allCorporateQuery->whereNotNull('company_id')->distinct('company_id')->count('company_id'),
                'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                'confirmed' => (clone $baseQuery)->where('status', 'confirmed')->count(),
                'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
                'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
                'expired' => (clone $baseQuery)->where('status', 'cancelled')
                    ->whereNotNull('cancellation_reason')
                    ->where('cancellation_reason', 'like', '%expired automatically%')
                    ->count(),
                'checked_in' => (clone $baseQuery)->where('check_in_status', 'checked_in')->count(),
                'checked_out' => (clone $baseQuery)->where('check_in_status', 'checked_out')->count(),
            ];
        }

        return view('dashboard.bookings-list', [
            'bookings' => $bookings,
            'role' => 'manager',
            'userName' => 'Manager',
            'userRole' => 'Manager',
            'filters' => $request->only(['status', 'payment_status', 'check_in_status', 'search', 'type']),
            'stats' => $stats,
            'bookingType' => $bookingType,
        ]);
    }

    /**
     * Get all bookings for a company
     */
    public function getCompanyBookings($companyId)
    {
        try {
            $company = \App\Models\Company::findOrFail($companyId);
            $bookings = Booking::with(['room', 'company'])
                ->where('company_id', $companyId)
                ->where('is_corporate_booking', true)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'email' => $company->email,
                    'phone' => $company->phone,
                    'contact_person' => $company->contact_person,
                    'guider_email' => $company->guider_email,
                    'guider_phone' => $company->guider_phone,
                    'billing_address' => $company->billing_address,
                    'payment_terms' => $company->payment_terms,
                    'is_active' => $company->is_active,
                ],
                'bookings' => $bookings->map(function($booking) {
                    $serviceRequests = $booking->serviceRequests()
                        ->whereIn('status', ['approved', 'completed'])
                        ->get();
                    
                    $totalServiceChargesTsh = $serviceRequests->sum('total_price_tsh');

                    return [
                        'id' => $booking->id,
                        'booking_reference' => $booking->booking_reference,
                        'guest_id' => $booking->guest_id,
                        'guest_name' => $booking->guest_name,
                        'first_name' => $booking->first_name,
                        'last_name' => $booking->last_name,
                        'guest_email' => $booking->guest_email,
                        'guest_phone' => $booking->guest_phone,
                        'country' => $booking->country,
                        'country_code' => $booking->country_code,
                        'guest_type' => $booking->guest_type,
                        'check_in' => $booking->check_in,
                        'check_out' => $booking->check_out,
                        'original_check_out' => $booking->original_check_out,
                        'arrival_time' => $booking->arrival_time,
                        'number_of_guests' => $booking->number_of_guests,
                        'nights' => $booking->check_in->diffInDays($booking->check_out),
                        'total_price' => $booking->total_price,
                        'recommended_price' => $booking->recommended_price,
                        'amount_paid' => $booking->amount_paid,
                        'payment_percentage' => $booking->payment_percentage,
                        'payment_status' => $booking->payment_status,
                        'payment_method' => $booking->payment_method,
                        'payment_provider' => $booking->payment_provider,
                        'payment_transaction_id' => $booking->payment_transaction_id,
                        'payment_responsibility' => $booking->payment_responsibility,
                        'service_charges_tsh' => $totalServiceChargesTsh,
                        'status' => $booking->status,
                        'check_in_status' => $booking->check_in_status,
                        'checked_in_at' => $booking->checked_in_at,
                        'checked_out_at' => $booking->checked_out_at,
                        'special_requests' => $booking->special_requests,
                        'admin_notes' => $booking->admin_notes,
                        'locked_exchange_rate' => $booking->locked_exchange_rate,
                        'created_at' => $booking->created_at,
                        'updated_at' => $booking->updated_at,
                        'room' => [
                            'id' => $booking->room->id ?? null,
                            'room_number' => $booking->room->room_number ?? null,
                            'room_type' => $booking->room->room_type ?? null,
                            'price_per_night' => $booking->room->price_per_night ?? null,
                            'capacity' => $booking->room->capacity ?? null,
                            'bed_type' => $booking->room->bed_type ?? null,
                            'floor_location' => $booking->room->floor_location ?? null,
                        ],
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching company bookings', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch company bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show booking details
     */
    public function show(Booking $booking)
    {
        try {
            // Check if user is customer/guest - verify booking belongs to them
            $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
            if ($user && ($user->role === 'guest' || $user->role === 'customer')) {
                // For customers/guests, verify the booking belongs to them
                if ($booking->guest_email !== $user->email) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access to booking details.'
                    ], 403);
                }
            }
            
            // Load room and service requests if they exist
            $booking->load(['room', 'serviceRequests.service']);
            
            // Ensure financial fields are visible
            $booking->makeVisible(['amount_paid', 'payment_percentage', 'total_service_charges_tsh', 'total_bill_tsh']);
            
            // Format dates as YYYY-MM-DD to avoid timezone issues in JavaScript
            $bookingData = $booking->toArray();
            if (isset($bookingData['check_in']) && $bookingData['check_in']) {
                $bookingData['check_in'] = \Carbon\Carbon::parse($booking->check_in)->format('Y-m-d');
            }
            if (isset($bookingData['check_out']) && $bookingData['check_out']) {
                $bookingData['check_out'] = \Carbon\Carbon::parse($booking->check_out)->format('Y-m-d');
            }
            if (isset($bookingData['original_check_out']) && $bookingData['original_check_out']) {
                $bookingData['original_check_out'] = \Carbon\Carbon::parse($booking->original_check_out)->format('Y-m-d');
            }
            
            // Calculate service charges and paid services
            $serviceRequests = $booking->serviceRequests()->whereIn('status', ['approved', 'completed'])->get();
            $totalServiceChargesTsh = $serviceRequests->sum('total_price_tsh');
            $paidServiceChargesTsh = $serviceRequests->where('payment_status', 'paid')->sum('total_price_tsh');
            
            // Add service charges and total paid to booking data
            $bookingData['service_charges_tsh'] = $totalServiceChargesTsh;
            
            // Return booking even if room doesn't exist (for manual bookings that might not have room assigned yet)
            return response()->json([
                'success' => true,
                'booking' => $bookingData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading booking details', [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load booking details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $updateData = ['status' => $request->status];

        // If cancelling, calculate cancellation fee based on timing
        if ($request->status === 'cancelled') {
            $now = Carbon::now();
            $checkInDate = Carbon::parse($booking->check_in);
            $daysUntilArrival = $now->diffInDays($checkInDate, false);
            
            $cancellationFeePercentage = 0;
            $cancellationFee = 0;
            
            // Calculate cancellation fee based on new policy:
            // - Free cancellation: 14+ days before arrival
            // - 50% fee: 13 days to 3 days (72 hours) before arrival
            // - 100% fee: Within 2 days (48 hours) or no-show
            if ($daysUntilArrival >= 14) {
                // Free cancellation
                $cancellationFeePercentage = 0;
                $cancellationFee = 0;
            } elseif ($daysUntilArrival >= 3) {
                // 13 days to 3 days (72 hours): 50% of deposit
                $cancellationFeePercentage = 50;
                $depositAmount = ($booking->amount_paid ?? ($booking->total_price * ($booking->payment_percentage ?? 50) / 100));
                $cancellationFee = $depositAmount * 0.50;
            } else {
                // Within 2 days (48 hours) or no-show: 100% of deposit
                $cancellationFeePercentage = 100;
                $depositAmount = ($booking->amount_paid ?? ($booking->total_price * ($booking->payment_percentage ?? 50) / 100));
                $cancellationFee = $depositAmount;
            }
            
            $updateData['cancelled_at'] = $now;
            $updateData['payment_status'] = 'cancelled';
            $updateData['cancellation_fee'] = $cancellationFee;
            $updateData['cancellation_fee_percentage'] = $cancellationFeePercentage;
            
            if ($request->has('cancellation_reason') && $request->cancellation_reason) {
                $updateData['cancellation_reason'] = $request->cancellation_reason;
            } elseif (!$booking->cancellation_reason) {
                $updateData['cancellation_reason'] = 'Cancelled by administrator.';
            }
        }

        // If confirming, update payment status if paid
        if ($request->status === 'confirmed' && $booking->payment_status === 'paid') {
            // Already confirmed via payment
        }

        // If completing, ensure payment is marked as paid and check-in status is checked out
        if ($request->status === 'completed') {
            if ($booking->payment_status === 'pending') {
                $updateData['payment_status'] = 'paid';
                $updateData['amount_paid'] = $booking->total_price;
                if (!$booking->paid_at) {
                    $updateData['paid_at'] = now();
                }
            }
            // Also ensure check-in status is checked out
            if ($booking->check_in_status !== 'checked_out') {
                $updateData['check_in_status'] = 'checked_out';
                if (!$booking->checked_out_at) {
                    $updateData['checked_out_at'] = now();
                }
            }
        }

        $booking->update($updateData);
        
        // Mark booking notification as read when booking is confirmed or completed (action taken)
        if (in_array($request->status, ['confirmed', 'completed', 'cancelled'])) {
            try {
                $notificationService = new NotificationService();
                $notificationService->markNotificationAsReadByNotifiable(
                    Booking::class,
                    $booking->id,
                    'booking',
                    'reception'
                );
                $notificationService->markNotificationAsReadByNotifiable(
                    Booking::class,
                    $booking->id,
                    'booking',
                    'manager'
                );
            } catch (\Exception $e) {
                \Log::error('Failed to mark booking notification as read: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully!',
            'booking' => $booking->fresh()->load('room')
        ]);
    }

    /**
     * Update check-in status
     */
    public function updateCheckInStatus(Request $request, Booking $booking)
    {
        // Ensure user is authenticated
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to perform this action.',
            ], 401);
        }
        
        // For staff users, ensure they have manager or reception role
        if ($user instanceof \App\Models\Staff) {
            $userRole = strtolower(trim($user->role ?? ''));
            if (!in_array($userRole, ['manager', 'reception', 'super_admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. You do not have permission to perform this action.',
                ], 403);
            }
        }
        
        $request->validate([
            'check_in_status' => 'required|in:pending,checked_in,checked_out',
        ]);

        $updateData = ['check_in_status' => $request->check_in_status];

        if ($request->check_in_status === 'checked_in') {
            $updateData['checked_in_at'] = now();
            // Auto-update booking status to confirmed if not already
            if ($booking->status === 'pending') {
                $updateData['status'] = 'confirmed';
            }
            
            // Update the booking first to ensure checked_in_at is saved
            $booking->update($updateData);
            $booking->load('room');

            // Update room status to occupied when guest checks in
            if ($booking->room) {
                $booking->room->update(['status' => 'occupied']);
            }
            
            // Send check-in confirmation email (send immediately, not queued)
            try {
                $wifiPassword = \App\Models\HotelSetting::getWifiPassword();
                $wifiNetworkName = \App\Models\HotelSetting::getWifiNetworkName();
                Mail::to($booking->guest_email)->send(new \App\Mail\CheckInConfirmationMail($booking->fresh(), $wifiPassword, $wifiNetworkName));
            } catch (\Exception $e) {
                \Log::error('Failed to send check-in confirmation email: ' . $e->getMessage());
            }

            // Send email notification to managers and super admins for check-in (send immediately)
            try {
                $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                    ->where('is_active', true)
                    ->get();
                
                foreach ($managersAndAdmins as $staff) {
                    // Check if user has notifications enabled
                    if ($staff->isNotificationEnabled('check_in_out')) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($staff->email)
                                ->send(new \App\Mail\StaffCheckInMail($booking->fresh()->load('room')));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send check-in email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send check-in emails to managers/admins: ' . $e->getMessage());
            }
        } elseif ($request->check_in_status === 'checked_out') {
            // Check if there's outstanding balance before allowing checkout
            $currencyService = new CurrencyExchangeService();
            $exchangeRate = $booking->locked_exchange_rate ?? $currencyService->getUsdToTshRate();
            
            // Calculate total bill
            $serviceRequests = $booking->serviceRequests()
                ->whereIn('status', ['approved', 'completed'])
                ->with('service')
                ->get();
            
            $totalServiceChargesTsh = $serviceRequests->sum('total_price_tsh');
            
            // Calculate extension cost if extension was approved
            $extensionCostUsd = 0;
            if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                if ($extensionNights > 0 && $booking->room) {
                    $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                }
            }
            $extensionCostTsh = $extensionCostUsd * $exchangeRate;
            
            // Total bill
            // Note: extensionCostTsh is already included in booking->total_price
            $totalBillTsh = ($booking->total_price * $exchangeRate) + $totalServiceChargesTsh;
            
            // Amount paid
            // Amount paid (Booking deposit + any settled service payments)
            $amountPaidTsh = ($booking->amount_paid ?? 0) * $exchangeRate;
            
            // Add payments for completed/paid services to show correct outstanding balance
            foreach ($serviceRequests as $sr) {
                if ($sr->payment_status === 'paid') {
                    $amountPaidTsh += $sr->total_price_tsh;
                }
            }
            
            // Outstanding balance
            $outstandingBalanceTsh = max(0, $totalBillTsh - $amountPaidTsh);
            $outstandingBalanceUsd = $outstandingBalanceTsh / $exchangeRate;
            
            // Treat very small amounts (less than $0.05 or 50 TZS) as fully paid (rounding differences)
            $minOutstandingThresholdUsd = 0.05;
            $minOutstandingThresholdTsh = 50;
            if ($outstandingBalanceUsd < $minOutstandingThresholdUsd || $outstandingBalanceTsh < $minOutstandingThresholdTsh) {
                // Negligible amount, treat as fully paid
                $outstandingBalanceTsh = 0;
                $outstandingBalanceUsd = 0;
            }
            
            // If there's outstanding balance, prevent checkout
            if ($outstandingBalanceTsh >= $minOutstandingThresholdTsh || $outstandingBalanceUsd >= $minOutstandingThresholdUsd) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot check out. Outstanding balance of $' . number_format($outstandingBalanceUsd, 2) . ' (' . number_format($outstandingBalanceTsh, 2) . ' TZS) must be paid first.',
                ], 400);
            }
            
            $updateData['checked_out_at'] = now();
            // Auto-update booking status to completed
            $updateData['status'] = 'completed';
            // Ensure payment status is paid
            $updateData['payment_status'] = 'paid';
            
            // Update room status to needs cleaning
            if ($booking->room) {
                $booking->room->update(['status' => 'to_be_cleaned']);
                
                // Create cleaning log entry
                \App\Models\RoomCleaningLog::create([
                    'room_id' => $booking->room->id,
                    'status' => 'needs_cleaning',
                ]);
                
                // Send email notification to housekeeper
                $housekeepers = \App\Models\Staff::where('role', 'housekeeper')
                    ->where('is_active', true)
                    ->get();
                
                foreach ($housekeepers as $housekeeper) {
                    if ($housekeeper->isNotificationEnabled('room_cleaning')) {
                        try {
                            \Mail::to($housekeeper->email)->send(
                                new \App\Mail\RoomNeedsCleaningMail($booking->room, $booking)
                            );
                        } catch (\Exception $e) {
                            \Log::error('Failed to send room cleaning notification: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        $booking->update($updateData);

        // If checked out, send confirmation email (send immediately)
        if ($request->check_in_status === 'checked_out') {
            try {
                $booking->load('room');
                // Recalculate totals for email (variables from above scope)
                $totalBillUsd = $booking->total_price;
                Mail::to($booking->guest_email)->send(new \App\Mail\CheckOutConfirmationMail($booking->fresh(), $totalBillUsd, $totalBillTsh, $amountPaidTsh));
            } catch (\Exception $e) {
                \Log::error('Failed to send check-out confirmation email: ' . $e->getMessage());
            }

            // Send email notification to managers and super admins for check-out (send immediately)
            try {
                $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                    ->where('is_active', true)
                    ->get();
                
                foreach ($managersAndAdmins as $staff) {
                    // Check if user has notifications enabled
                    if ($staff->isNotificationEnabled('check_in_out')) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($staff->email)
                                ->send(new \App\Mail\StaffCheckOutMail($booking->fresh()->load('room')));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send check-out email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send check-out emails to managers/admins: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Guest checked out successfully. Room status changed to "Needs Cleaning".',
                'booking' => $booking->fresh()->load('room'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Check-in status updated successfully!',
            'booking' => $booking->fresh()->load('room')
        ]);
    }

    /**
     * Customer dashboard - show bookings and profile
     */
    public function customerDashboard(Request $request)
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to access your dashboard.']);
        }
        
        // Redirect super admins and managers to their respective dashboards
        // Only Staff model has isSuperAdmin, isManager, isReception methods
        if ($user instanceof \App\Models\Staff) {
            if ($user->isSuperAdmin()) {
                return redirect()->route('super_admin.dashboard');
            }
            if ($user->isManager()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isReception()) {
                return redirect()->route('reception.dashboard');
            }
        }
        
        // Get all bookings for this customer
        $allBookings = Booking::where('guest_email', $user->email)
            ->with(['room', 'company'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get active bookings (confirmed, paid or partial, not checked out) OR checked out but not paid
        // Include manual bookings with confirmed status and pending/partial payment
        $activeBookings = Booking::where('guest_email', $user->email)
            ->with(['room', 'company'])
            ->where(function($q) {
                $q->where(function($q2) {
                    $q2->where('status', 'confirmed')
                       ->whereIn('payment_status', ['paid', 'partial', 'pending'])
                       ->where('check_in_status', '!=', 'checked_out');
                })->orWhere(function($q3) {
                    $q3->where('check_in_status', 'checked_out')
                       ->where('payment_status', '!=', 'paid');
                });
            })
            ->with(['room', 'company', 'serviceRequests.service'])
            ->orderBy('check_in', 'asc')
            ->get();
        
        // Recalculate total service charges for each active booking to ensure accuracy
        foreach ($activeBookings as $booking) {
            $calculatedTotal = $booking->serviceRequests()
                ->whereIn('status', ['approved', 'completed'])
                ->sum('total_price_tsh');
            
            // Update if different (fixes any existing data inconsistencies)
            if ($booking->total_service_charges_tsh != $calculatedTotal) {
                $booking->update([
                    'total_service_charges_tsh' => $calculatedTotal
                ]);
            }
        }
        
        // Get pending bookings (awaiting payment)
        // Include both regular pending bookings and confirmed manual bookings with pending payment
        $pendingBookings = Booking::where('guest_email', $user->email)
            ->where(function($q) {
                $q->where(function($q2) {
                    $q2->where('status', 'pending')
                       ->where('payment_status', 'pending');
                })->orWhere(function($q3) {
                    $q3->where('status', 'confirmed')
                       ->where('payment_status', 'pending')
                       ->where('payment_method', 'manual');
                });
            })
            ->with(['room', 'company'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate statistics (completed bookings excluded from dashboard - they're in Booking History page)
        $stats = [
            'total' => $allBookings->count(),
            'active' => $activeBookings->count(),
            'pending' => $pendingBookings->count(),
            'completed' => $allBookings->where('status', 'completed')->count(),
            'total_spent' => $allBookings->where('payment_status', 'paid')->sum('total_price'),
        ];
        
        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        // Get country from most recent booking
        $userCountry = Booking::where('guest_email', $user->email)
            ->whereNotNull('country')
            ->orderBy('created_at', 'desc')
            ->value('country');
        
        // Calculate upcoming check-in/check-out dates
        $upcomingCheckIns = [];
        $upcomingCheckOuts = [];
        $today = \Carbon\Carbon::today();
        
        foreach ($activeBookings as $booking) {
            $checkInDate = \Carbon\Carbon::parse($booking->check_in);
            $checkOutDate = \Carbon\Carbon::parse($booking->check_out);
            
            // Set check-in time (default 2:00 PM if arrival_time not specified)
            if ($booking->arrival_time) {
                $timeParts = explode(':', $booking->arrival_time);
                if (count($timeParts) >= 2) {
                    $checkInDate->setTime((int)$timeParts[0], (int)$timeParts[1], 0);
                } else {
                    $checkInDate->setTime(14, 0, 0); // Default 2:00 PM
                }
            } else {
                $checkInDate->setTime(14, 0, 0); // Default 2:00 PM
            }
            
            // Check-in alerts - show for guests who haven't checked in yet
            // Include bookings within 7 days OR if check-in date has passed but guest hasn't checked in
            if (($booking->check_in_status === 'pending' || $booking->check_in_status === null) && 
                $booking->check_in_status !== 'checked_in' && 
                $booking->check_in_status !== 'checked_out') {
                
                // Show if within 7 days OR if check-in date has passed but not checked in yet
                $daysUntilCheckIn = $today->diffInDays($checkInDate, false); // false = can be negative
                if ($daysUntilCheckIn <= 7 && $daysUntilCheckIn >= -2) { // Show up to 2 days after check-in date
                    $upcomingCheckIns[] = [
                        'booking' => $booking,
                        'date' => $checkInDate,
                        'days_until' => max(0, $daysUntilCheckIn), // Show 0 if past due
                    ];
                }
            }
            
            // Check-out alerts (upcoming within 2 days)
            if ($booking->check_in_status === 'checked_in' && $checkOutDate >= $today && $checkOutDate->diffInDays($today) <= 2) {
                $upcomingCheckOuts[] = [
                    'booking' => $booking,
                    'date' => $checkOutDate,
                    'days_until' => $checkOutDate->diffInDays($today),
                ];
            }
        }
        
        // Get weather data for Moshi (default location)
        $weatherService = new WeatherService();
        $weather = $weatherService->getCurrentWeather();
        
        // Calculate payment summary using locked exchange rate for each booking
        $totalOutstanding = 0;
        foreach ($activeBookings as $booking) {
            $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
            $totalBill = ($booking->total_price * $bookingExchangeRate) + ($booking->total_service_charges_tsh ?? 0);
            $amountPaid = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
            
            // Deduct services that are marked as paid
            $paidServices = $booking->serviceRequests ? 
                $booking->serviceRequests->where('payment_status', 'paid')->sum('total_price_tsh') : 0;
                
            $outstanding = $totalBill - $amountPaid - $paidServices;
            if ($outstanding > 0) {
                $totalOutstanding += $outstanding;
            }
        }
        
        // Get WiFi settings
        $wifiPassword = HotelSetting::getWifiPassword();
        $wifiNetworkName = HotelSetting::getWifiNetworkName();
        
        // Check if guest has any checked-out/completed bookings (to show "Thank You" message)
        $hasCheckedOutBookings = Booking::where('guest_email', $user->email)
            ->where(function($q) {
                $q->where('check_in_status', 'checked_out')
                  ->orWhere('status', 'completed');
            })
            ->exists();
        
        return view('dashboard.customer-dashboard', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'userCountry' => $userCountry,
            'activeBookings' => $activeBookings,
            'pendingBookings' => $pendingBookings,
            'stats' => $stats,
            'exchangeRate' => $exchangeRate,
            'upcomingCheckIns' => $upcomingCheckIns,
            'upcomingCheckOuts' => $upcomingCheckOuts,
            'totalOutstanding' => $totalOutstanding,
            'weather' => $weather,
            'weatherService' => $weatherService,
            'wifiPassword' => $wifiPassword,
            'wifiNetworkName' => $wifiNetworkName,
            'hasCheckedOutBookings' => $hasCheckedOutBookings,
        ]);
    }

    /**
     * Show public booking details (for QR code scanning)
     */
    public function publicBookingDetails(Booking $booking)
    {
        // Load booking with relationships
        $booking->load('room');
        
        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('public.booking-details', [
            'booking' => $booking,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Generate and download guest identity card
     */
    public function downloadIdentityCard(Booking $booking)
    {
        // Verify booking belongs to logged-in customer
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        if ($user && $booking->guest_email !== $user->email) {
            abort(403, 'Unauthorized access.');
        }

        // Load booking with relationships
        $booking->load('room');
        
        // Get guest profile photo
        $guestPhotoUrl = null;
        $guest = \App\Models\Guest::where('email', $booking->guest_email)->first();
        if ($guest && $guest->profile_photo) {
            // Check if file exists in storage
            if (\Storage::disk('public')->exists($guest->profile_photo)) {
                $guestPhotoUrl = asset('storage/' . $guest->profile_photo);
            } elseif (file_exists(public_path('storage/' . $guest->profile_photo))) {
                $guestPhotoUrl = asset('storage/' . $guest->profile_photo);
            }
        }
        
        // Get exchange rate for display
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        return view('dashboard.guest-identity-card', [
            'booking' => $booking,
            'exchangeRate' => $exchangeRate,
            'guestPhotoUrl' => $guestPhotoUrl,
        ]);
    }

    /**
     * Show all bookings for customer
     */
    public function myBookings()
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to access your bookings.']);
        }
        
        // Get all bookings for this customer
        $bookings = Booking::where('guest_email', $user->email)
            ->with(['room', 'company'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-my-bookings', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'bookings' => $bookings,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Show booking history (completed bookings)
     */
    public function bookingHistory()
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to access your booking history.']);
        }
        
        // Get completed bookings
        $bookings = Booking::where('guest_email', $user->email)
            ->where('status', 'completed')
            ->with('room')
            ->orderBy('check_out', 'desc')
            ->paginate(15);
        
        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-booking-history', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'bookings' => $bookings,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Show extension requests for customer
     */
    public function myExtensions()
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to access your extensions.']);
        }
        
        // Check if guest has any checked-in bookings
        $hasCheckedIn = Booking::where('guest_email', $user->email)
            ->where('check_in_status', 'checked_in')
            ->where('status', '!=', 'cancelled')
            ->exists();
        
        // Get all bookings with extension requests (pending, approved, or rejected)
        $bookings = Booking::where('guest_email', $user->email)
            ->whereNotNull('extension_status')
            ->where('extension_status', '!=', '')
            ->with('room')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-extensions', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'bookings' => $bookings,
            'exchangeRate' => $exchangeRate,
            'hasCheckedIn' => $hasCheckedIn,
        ]);
    }

    /**
     * Show payment history for customer
     */
    public function myPayments()
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to access your payments.']);
        }
        
        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        // Get all bookings for this user
        $allBookings = Booking::where('guest_email', $user->email)
            ->with(['room', 'serviceRequests', 'company'])
            ->get();
        
        // Filter bookings based on payment type
        $bookingsToShow = collect();
        $totalPaid = 0;
        $totalBookings = 0;
        
        foreach ($allBookings as $booking) {
            if ($booking->is_corporate_booking) {
                // For corporate bookings, only show if services are self-paid
                if ($booking->payment_responsibility === 'self') {
                    // Check for self-paid services (including room_charge ones)
                    $allServiceRequests = $booking->serviceRequests()
                        ->whereIn('status', ['approved', 'completed'])
                        ->get();
                    
                    $paidServiceRequests = $allServiceRequests->where('payment_status', 'paid');
                    $paidTsh = $paidServiceRequests->sum('total_price_tsh');
                    $totalTsh = $allServiceRequests->sum('total_price_tsh');
                    
                    // Show if there's a payment OR if booking is confirmed (even if no services yet)
                    if ($paidTsh > 0 || $booking->status === 'confirmed') {
                        $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
                        
                        $virtualBooking = clone $booking;
                        $virtualBooking->amount_paid = $paidTsh / $bookingExchangeRate;
                        $virtualBooking->total_price = $totalTsh / $bookingExchangeRate;
                        
                        // Determine status and clear details if no services
                        if ($totalTsh == 0) {
                            $virtualBooking->payment_status = 'paid';
                            $virtualBooking->payment_method = null;
                            $virtualBooking->payment_transaction_id = null;
                            $virtualBooking->paid_at = null;
                        } else {
                            $virtualBooking->payment_status = ($paidTsh >= $totalTsh) ? 'paid' : ($paidTsh > 0 ? 'partial' : 'pending');
                            
                            // Get payment method from service requests
                            $paymentMethods = $paidServiceRequests->pluck('payment_method')->filter();
                            $virtualBooking->payment_method = $paymentMethods->count() > 0 
                                ? $paymentMethods->countBy()->sortDesc()->keys()->first() 
                                : 'cash';
                                
                            $virtualBooking->paid_at = $paidServiceRequests->max('completed_at') ?? $paidServiceRequests->max('updated_at') ?? $booking->created_at;
                            
                            // Clear transaction ID from the main booking (since this is service-only)
                            // unless we actually find a transaction ID in the service requests (assumed not stored there usually)
                            $virtualBooking->payment_transaction_id = null; 
                        }
                        $virtualBooking->is_service_payment_only = true;
                        
                        $bookingsToShow->push($virtualBooking);
                        $totalPaid += ($paidTsh / $bookingExchangeRate);
                        // Count as a booking unless it's strictly pending with debt
                        if ($virtualBooking->payment_status !== 'pending' || $totalTsh == 0) {
                            $totalBookings++;
                        }
                    }
                } else {
                    // Company pays - include confirmed or completed bookings
                    if ($booking->status === 'confirmed' || $booking->status === 'completed' || $booking->payment_status === 'paid') {
                        $bookingsToShow->push($booking);
                        $totalBookings++;
                    }
                }
            } else {
                // For individual bookings, show if they are confirmed (even if unpaid) or have partial/full payments
                if ($booking->status === 'confirmed' || in_array($booking->payment_status, ['paid', 'partial'])) {
                    $bookingsToShow->push($booking);
                    $totalPaid += ($booking->amount_paid ?? 0);
                    if ($booking->payment_status === 'paid' || $booking->payment_status === 'partial') {
                        $totalBookings++;
                    }
                }
            }
        }
        
        // Paginate the filtered bookings
        $currentPage = request()->get('page', 1);
        $perPage = 15;
        $items = $bookingsToShow->sortByDesc(function($booking) {
            return $booking->paid_at ?? $booking->created_at;
        })->values();
        
        $paginatedBookings = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->forPage($currentPage, $perPage),
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        return view('dashboard.customer-payments', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'bookings' => $paginatedBookings,
            'totalPaid' => $totalPaid,
            'totalBookings' => $totalBookings,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Show guest check-in page
     */
    public function showCheckIn(Request $request)
    {
        $bookings = null;
        
        // If user is logged in, get their bookings
        // Include confirmed bookings with paid or partial payment status (for manual bookings)
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        if ($user) {
            $bookings = Booking::where('guest_email', $user->email)
                ->where('status', 'confirmed')
                ->whereIn('payment_status', ['paid', 'partial'])
                ->where('check_in_status', 'pending')
                ->with('room')
                ->orderBy('check_in', 'asc')
                ->get();
        }
        
        return view('landing_page_views.check-in', [
            'bookings' => $bookings
        ]);
    }

    /**
     * Find booking by reference for check-in
     */
    public function findBookingForCheckIn(Request $request)
    {
        $request->validate([
            'booking_reference' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $booking = Booking::where('booking_reference', $request->booking_reference)
            ->where('guest_email', $request->email)
            ->with('room')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found. Please check your booking reference and email address.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'booking' => $booking,
        ]);
    }

    /**
     * Guest self check-in
     */
    public function guestCheckIn(Request $request, Booking $booking)
    {
        // Verify booking reference and email match
        $request->validate([
            'booking_reference' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($booking->booking_reference !== $request->booking_reference || 
            $booking->guest_email !== $request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid booking credentials.',
            ], 403);
        }

        // Check if booking is eligible for check-in
        if ($booking->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Booking is not confirmed. Please contact the hotel for assistance.',
            ], 400);
        }

        if ($booking->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Payment is not completed. Please complete payment first.',
            ], 400);
        }

        if ($booking->check_in_status === 'checked_in') {
            return response()->json([
                'success' => false,
                'message' => 'You are already checked in.',
            ], 400);
        }

        // Date restriction removed for testing purposes - guests can check in at any time
        // Perform check-in
        $booking->update([
            'check_in_status' => 'checked_in',
            'checked_in_at' => now(),
            'status' => 'confirmed', // Ensure status is confirmed
        ]);

        // Update room status to occupied
        if ($booking->room) {
            $booking->room->update(['status' => 'occupied']);
        }

        // Send check-in confirmation email (send immediately)
        try {
            $wifiPassword = \App\Models\HotelSetting::getWifiPassword();
            $wifiNetworkName = \App\Models\HotelSetting::getWifiNetworkName();
            $booking->load('room');
            Mail::to($booking->guest_email)->send(new \App\Mail\CheckInConfirmationMail($booking->fresh(), $wifiPassword, $wifiNetworkName));
        } catch (\Exception $e) {
            \Log::error('Failed to send check-in confirmation email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful! Welcome to PrimeLand Hotel.',
            'booking' => $booking->fresh()->load('room'),
        ]);
    }

    /**
     * Update admin notes
     */
    public function updateNotes(Request $request, Booking $booking)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $booking->update([
            'admin_notes' => $request->admin_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin notes updated successfully!',
            'booking' => $booking->fresh()->load('room')
        ]);
    }

    /**
     * Delete booking
     */
    public function destroy(Booking $booking)
    {
        try {
            // Allow deletion of pending, cancelled bookings, or confirmed bookings that haven't been checked in
            if (!in_array($booking->status, ['pending', 'cancelled']) && $booking->check_in_status === 'checked_in') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete bookings that have been checked in. Please check out the guest first.',
                ], 422);
            }

            $bookingReference = $booking->booking_reference;
            $booking->delete();

            \Log::info('Booking deleted', [
                'booking_reference' => $bookingReference,
                'deleted_by' => auth()->guard('staff')->user()->name ?? 'System'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking deleted successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to delete booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique guest ID in format GST-12345
     */
    private function generateGuestId()
    {
        do {
            $number = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $guestId = 'GST-' . $number;
        } while (Booking::where('guest_id', $guestId)->exists());

        return $guestId;
    }

    /**
     * Show documents center for customer
     */
    public function documents()
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to access your documents.']);
        }
        
        // Get all bookings for this customer
        $bookings = Booking::where('guest_email', $user->email)
            ->with('room')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-documents', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'bookings' => $bookings,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Show booking calendar for customer
     */
    public function bookingCalendar()
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to access your booking calendar.']);
        }
        
        // Get all bookings for this customer (for calendar display)
        $allBookings = Booking::where('guest_email', $user->email)
            ->with(['room', 'company'])
            ->orderBy('check_in', 'asc')
            ->get();
        
        // Filter upcoming bookings (check-out date is today or in the future)
        $today = \Carbon\Carbon::today();
        $bookings = $allBookings->filter(function($booking) use ($today) {
            return $booking->check_out && $booking->check_out->gte($today);
        })->values();
        
        // Format bookings for calendar (use all bookings for calendar, not just upcoming)
        $calendarEvents = $allBookings->map(function($booking) {
            return [
                'id' => $booking->id,
                'title' => $booking->room->room_type ?? 'Room',
                'start' => $booking->check_in->format('Y-m-d'),
                'end' => $booking->check_out->format('Y-m-d'),
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'check_in_status' => $booking->check_in_status,
                'booking_reference' => $booking->booking_reference,
                'room_number' => $booking->room->room_number ?? 'N/A',
            ];
        });
        
        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-calendar', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'bookings' => $bookings,
            'calendarEvents' => $calendarEvents,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Show booking calendar for admin - shows all rooms and their bookings/occupancy
     */
    public function adminCalendar()
    {
        // Get all rooms
        $rooms = Room::orderBy('room_number', 'asc')->get();
        
        // Get all confirmed bookings (active bookings that block rooms)
        $bookings = Booking::where('status', 'confirmed')
            ->with('room')
            ->where(function ($query) {
                // Get bookings that are paid/partial OR pending with valid deadline
                $query->whereIn('payment_status', ['paid', 'partial'])
                      ->orWhere(function ($subQ) {
                          $subQ->where('payment_status', 'pending')
                               ->where(function ($deadlineQ) {
                                   $deadlineQ->whereNull('payment_deadline')
                                            ->orWhere('payment_deadline', '>', Carbon::now());
                               });
                      });
            })
            ->get();
        
        // Format bookings for calendar - group by room
        $calendarEvents = [];
        $roomStatuses = [];
        
        foreach ($rooms as $room) {
            $roomBookings = $bookings->where('room_id', $room->id);
            
            // Determine current room status
            $today = Carbon::today();
            $isOccupied = false;
            $needsCleaning = false;
            $inMaintenance = false;
            
            // Check room status
            if ($room->status === 'occupied') {
                $isOccupied = true;
            } elseif ($room->status === 'to_be_cleaned') {
                $needsCleaning = true;
            } elseif ($room->status === 'in_maintenance') {
                $inMaintenance = true;
            }
            
            // Check if room has active checked-in booking
            $activeBooking = $roomBookings->filter(function($booking) use ($today) {
                $checkIn = Carbon::parse($booking->check_in);
                $checkOut = Carbon::parse($booking->check_out);
                return $checkIn->lte($today) && $checkOut->gte($today) && 
                       $booking->check_in_status === 'checked_in';
            })->first();
            
            if ($activeBooking) {
                $isOccupied = true;
            }
            
            // Store room status
            $roomStatuses[$room->id] = [
                'room_number' => $room->room_number,
                'room_type' => $room->room_type,
                'status' => ($activeBooking || $room->status === 'occupied') ? 'occupied' : $room->status,
                'is_occupied' => $isOccupied,
                'needs_cleaning' => $needsCleaning,
                'in_maintenance' => $inMaintenance,
            ];
            
            // Create calendar events for each booking
            foreach ($roomBookings as $booking) {
                $checkIn = Carbon::parse($booking->check_in);
                $checkOut = Carbon::parse($booking->check_out);
                
                // Determine event color based on status
                $color = '#28a745'; // Green for confirmed
                if ($booking->check_in_status === 'checked_in') {
                    $color = '#dc3545'; // Red for occupied
                } elseif ($booking->check_in_status === 'checked_out') {
                    $color = '#6c757d'; // Gray for completed
                } elseif ($booking->payment_status === 'pending') {
                    $color = '#ffc107'; // Yellow for pending payment
                } elseif ($booking->payment_status === 'partial') {
                    $color = '#17a2b8'; // Blue for partial payment
                }
                
                $calendarEvents[] = [
                    'id' => 'booking_' . $booking->id,
                    'booking_id' => $booking->id,
                    'title' => "Room {$room->room_number} - {$booking->guest_name}",
                    'start' => $checkIn->format('Y-m-d'),
                    'end' => $checkOut->format('Y-m-d'),
                    'color' => $color,
                    'textColor' => '#ffffff',
                    'editable' => $booking->check_in_status !== 'checked_in', // Only allow rescheduling if not checked in
                    'room_id' => $room->id,
                    'room_number' => $room->room_number,
                    'room_type' => $room->room_type,
                    'guest_name' => $booking->guest_name,
                    'booking_reference' => $booking->booking_reference,
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'check_in_status' => $booking->check_in_status,
                    'total_price' => $booking->total_price,
                ];
            }
        }
        
        return view('dashboard.admin-booking-calendar', [
            'role' => 'manager',
            'userName' => 'Manager',
            'userRole' => 'Manager',
            'rooms' => $rooms,
            'calendarEvents' => $calendarEvents,
            'roomStatuses' => $roomStatuses,
        ]);
    }

    /**
     * Send reminder email to guest
     */
    public function sendReminder(Request $request, Booking $booking)
    {
        $request->validate([
            'reminder_type' => 'required|in:checkin,checkout,payment,general,email'
        ]);

        try {
            $reminderType = $request->reminder_type;
            $today = Carbon::today();
            $checkInDate = Carbon::parse($booking->check_in);
            $checkOutDate = Carbon::parse($booking->check_out);
            
            // Handle email reminders
            if ($reminderType === 'email' || $reminderType === 'payment' || $reminderType === 'general') {
                $results = [];
                
                // Send Email
                try {
                    Mail::to($booking->guest_email)->queue(new \App\Mail\ExpirationWarningMail($booking, null, '24h'));
                    $results[] = "Email sent to {$booking->guest_email}";
                } catch (\Exception $e) {
                    \Log::error('Failed to send reminder email: ' . $e->getMessage());
                    $results[] = "Email failed: " . $e->getMessage();
                }
                
                $successCount = count(array_filter($results, function($r) { return strpos($r, 'sent') !== false; }));
                $allSuccess = $successCount === 2;
                
                return response()->json([
                    'success' => $allSuccess,
                    'message' => implode('; ', $results)
                ]);
            }
            
            // Handle email reminders (queued for async processing)
            if ($reminderType === 'checkin') {
                $daysUntil = $today->diffInDays($checkInDate, false);
                Mail::to($booking->guest_email)->queue(new \App\Mail\CheckInReminderMail($booking, $daysUntil));
                $message = "Check-in reminder email sent successfully to {$booking->guest_email}";
            } elseif ($reminderType === 'checkout') {
                $daysUntil = $today->diffInDays($checkOutDate, false);
                Mail::to($booking->guest_email)->queue(new \App\Mail\CheckOutReminderMail($booking, $daysUntil));
                $message = "Check-out reminder email sent successfully to {$booking->guest_email}";
            } elseif ($reminderType === 'payment' || $reminderType === 'email') {
                Mail::to($booking->guest_email)->queue(new \App\Mail\ExpirationWarningMail($booking, null, '24h'));
                $message = "Payment reminder email sent successfully to {$booking->guest_email}";
            } else {
                // General reminder - send booking confirmation as reminder
                // Pass null for password since this is a reminder, not a new booking
                Mail::to($booking->guest_email)->queue(new BookingConfirmationMail($booking, null));
                $message = "Reminder email sent successfully to {$booking->guest_email}";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send reminder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reminder: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show room information for customer's bookings
     */
    public function roomInformation()
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to access your room information.']);
        }
        
        // Get all bookings with rooms for this customer
        $bookings = Booking::where('guest_email', $user->email)
            ->where('status', '!=', 'cancelled')
            ->with(['room', 'company'])
            ->orderBy('check_in', 'desc')
            ->get();
        
        // Get unique rooms
        $rooms = $bookings->pluck('room')->filter()->unique('id')->values();
        
        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-room-information', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'rooms' => $rooms,
            'bookings' => $bookings,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Show quick actions page
     */
    public function quickActions()
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to access quick actions.']);
        }
        
        // Get active bookings (include manual bookings with partial/pending payment)
        $activeBookings = Booking::where('guest_email', $user->email)
            ->where('status', 'confirmed')
            ->whereIn('payment_status', ['paid', 'partial', 'pending'])
            ->where('check_in_status', '!=', 'checked_out')
            ->with(['room', 'serviceRequests.service'])
            ->orderBy('check_in', 'asc')
            ->get();
        
        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-quick-actions', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'activeBookings' => $activeBookings,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Show customer checkout payment page
     */
    public function customerCheckoutPayment(Booking $booking)
    {
        // Verify booking belongs to logged-in customer
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        $userRole = $user->role ?? 'guest';
        if ($userRole === 'guest' && $booking->guest_email !== $user->email) {
            abort(403, 'Unauthorized access.');
        }

        // Verify booking is checked out
        if ($booking->check_in_status !== 'checked_out') {
            abort(404, 'Booking not found or not checked out.');
        }

        // If already paid, redirect back
        if ($booking->payment_status === 'paid') {
            return redirect()->route('customer.dashboard')
                ->with('info', 'This booking has already been paid.');
        }

        // Calculate additional charges only (room booking already paid via PayPal)
        // Additional charges include: services, extensions, transportation
        
        $serviceRequests = $booking->serviceRequests()
            ->whereIn('status', ['approved', 'completed'])
            ->with('service')
            ->get();

        // Use locked exchange rate from booking, or fallback to current rate if not set (for old bookings)
        $exchangeRate = $booking->locked_exchange_rate;
        if (!$exchangeRate) {
            // Fallback for old bookings that don't have locked rate
            $currencyService = new CurrencyExchangeService();
            $exchangeRate = $currencyService->getUsdToTshRate();
        }

        // Calculate extension cost if extension was approved
        $extensionCostUsd = 0;
        $extensionCostTsh = 0;
        $extensionNights = 0;
        
        if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
            $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
            $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
            $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
            
            if ($extensionNights > 0 && $booking->room) {
                $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                $extensionCostTsh = $extensionCostUsd * $exchangeRate;
            }
        }

        // Calculate transportation charges (only if there's a service request)
        $transportationChargesTsh = 0;
        $transportationChargesUsd = 0;
        if ($booking->airport_pickup_required) {
            // Check if there's a service request for airport pickup
            $airportPickupService = $serviceRequests->firstWhere('service.category', 'transport');
            if ($airportPickupService) {
                $transportationChargesTsh = $airportPickupService->total_price_tsh;
                $transportationChargesUsd = $transportationChargesTsh / $exchangeRate;
            }
            // Note: If airport_pickup_required is true but no service request exists,
            // we don't charge for it (it might have been handled separately or already paid)
        }

        // Service charges (excluding transportation which is calculated separately)
        $otherServiceChargesTsh = $serviceRequests
            ->where('service.category', '!=', 'transport')
            ->sum('total_price_tsh');
        
        // For the customer checkout view, we show extensions as "Additional Charges"
        // To avoid double counting since total_price already includes the extension,
        // we use the original room price as the base for room charges logic.
        $originalCheckOutDate = $booking->original_check_out 
            ? \Carbon\Carbon::parse($booking->original_check_out) 
            : \Carbon\Carbon::parse($booking->check_out);
        $originalNights = $booking->check_in->diffInDays($originalCheckOutDate);
        $baseRoomPriceUsd = $booking->room ? ($booking->room->price_per_night * $originalNights) : 0;
        
        // Total additional charges (services + extension + transportation)
        $totalAdditionalChargesTsh = $otherServiceChargesTsh + $extensionCostTsh + $transportationChargesTsh;
        $totalAdditionalChargesUsd = ($otherServiceChargesTsh / $exchangeRate) + $extensionCostUsd + ($transportationChargesTsh / $exchangeRate);

        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        return view('dashboard.customer-checkout-payment', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest',
            'userRole' => 'Customer',
            'booking' => $booking->load('room'),
            'serviceRequests' => $serviceRequests,
            'extensionCostUsd' => $extensionCostUsd,
            'extensionCostTsh' => $extensionCostTsh,
            'extensionNights' => $extensionNights,
            'transportationChargesUsd' => $transportationChargesUsd,
            'transportationChargesTsh' => $transportationChargesTsh,
            'otherServiceChargesTsh' => $otherServiceChargesTsh,
            'totalAdditionalChargesTsh' => $totalAdditionalChargesTsh,
            'totalAdditionalChargesUsd' => $totalAdditionalChargesUsd,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Request checkout extension
     */
    public function requestExtension(Request $request, Booking $booking)
    {
        // Verify booking belongs to logged-in customer
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        if (!$user || $booking->guest_email !== $user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        // Check if booking is active and checked in
        if ($booking->status !== 'confirmed' || $booking->check_in_status !== 'checked_in') {
            return response()->json([
                'success' => false,
                'message' => 'Extension can only be requested for active checked-in bookings.',
            ], 400);
        }

        // Check if there's already a pending extension
        if ($booking->extension_status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending extension request for this booking.',
            ], 400);
        }

        $validated = $request->validate([
            'extension_requested_to' => 'required|date|after:check_out',
            'extension_reason' => 'nullable|string|max:500',
        ]);

        // Calculate additional nights
        $originalCheckOut = Carbon::parse($booking->check_out);
        $newCheckOut = Carbon::parse($validated['extension_requested_to']);
        $additionalNights = $originalCheckOut->diffInDays($newCheckOut);

        // Calculate additional cost
        $room = $booking->room;
        $additionalCost = $room->price_per_night * $additionalNights;

        $booking->update([
            'extension_requested_to' => $validated['extension_requested_to'],
            'extension_status' => 'pending',
            'extension_requested_at' => now(),
            'extension_reason' => $validated['extension_reason'] ?? null,
            'original_check_out' => $booking->check_out, // Store original checkout date
        ]);

        // Notify reception and admin
        try {
            $currentUser = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
            $receptionUsers = \App\Models\Staff::where('role', 'reception')->get();
            $adminUsers = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])->get();
            
            foreach ($receptionUsers as $user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'extension_request',
                    'title' => 'Checkout Extension Request',
                    'message' => ($currentUser->name ?? 'Guest') . ' requested to extend checkout to ' . $newCheckOut->format('M d, Y'),
                    'icon' => 'fa-calendar-plus-o',
                    'color' => 'info',
                    'role' => 'reception',
                    'notifiable_id' => $booking->id,
                    'notifiable_type' => Booking::class,
                    'link' => route('reception.bookings') . '?search=' . $booking->booking_reference,
                ]);
            }
            
            foreach ($adminUsers as $user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'extension_request',
                    'title' => 'Checkout Extension Request',
                    'message' => ($currentUser->name ?? 'Guest') . ' requested to extend checkout to ' . $newCheckOut->format('M d, Y'),
                    'icon' => 'fa-calendar-plus-o',
                    'color' => 'info',
                    'role' => 'manager',
                    'notifiable_id' => $booking->id,
                    'notifiable_type' => Booking::class,
                    'link' => route('admin.bookings.show', $booking),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create extension notification: ' . $e->getMessage());
        }

        // Send email notification for extension request (queued for async processing)
        try {
            \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                ->queue(new \App\Mail\ExtensionRequestSubmittedMail($booking->fresh()->load('room')));
        } catch (\Exception $e) {
            \Log::error('Failed to queue extension request submitted email: ' . $e->getMessage());
        }

        // Send email notification to managers and super admins for extension request
        try {
            $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                ->where('is_active', true)
                ->get();
            
                foreach ($managersAndAdmins as $staff) {
                    // Check if user has notifications enabled
                    if ($staff->isNotificationEnabled('extension_request')) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($staff->email)
                                ->send(new \App\Mail\StaffExtensionRequestMail($booking->fresh()->load('room'), 'submitted'));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send extension request email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                        }
                    }
                }
        } catch (\Exception $e) {
            \Log::error('Failed to send extension request emails to managers/admins: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Extension request submitted successfully. For quick response call: 601 - Reception, 618 - Manager.',
            'booking' => $booking->fresh(),
            'additional_nights' => $additionalNights,
            'cost_difference' => $additionalCost,
            'nights_difference' => $additionalNights,
        ]);
    }

    /**
     * Request decrease in stay (Guest)
     */
    public function requestDecrease(Request $request, Booking $booking)
    {
        // Verify booking belongs to logged-in customer
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        if (!$user || $booking->guest_email !== $user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        // Check if booking is active and checked in
        if ($booking->status !== 'confirmed' || $booking->check_in_status !== 'checked_in') {
            return response()->json([
                'success' => false,
                'message' => 'Decrease can only be requested for active checked-in bookings.',
            ], 400);
        }

        // Check if there's already a pending extension/decrease
        if ($booking->extension_status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending extension/decrease request for this booking.',
            ], 400);
        }

        $validated = $request->validate([
            'decrease_requested_to' => 'required|date|after:' . $booking->check_in->format('Y-m-d'),
            'decrease_reason' => 'nullable|string|max:500',
        ]);

        // Calculate reduced nights
        $currentCheckOut = Carbon::parse($booking->check_out);
        $newCheckOut = Carbon::parse($validated['decrease_requested_to']);
        $checkIn = Carbon::parse($booking->check_in);
        
        // Validate that new check-out is before current check-out (strictly less than)
        if ($newCheckOut->gte($currentCheckOut)) {
            return response()->json([
                'success' => false,
                'message' => 'The new check-out date must be before your current check-out date (' . $currentCheckOut->format('M d, Y') . ').',
            ], 422);
        }
        
        // Validate that new check-out is after check-in (strictly greater than)
        if ($newCheckOut->lte($checkIn)) {
            return response()->json([
                'success' => false,
                'message' => 'The new check-out date must be after your check-in date (' . $checkIn->format('M d, Y') . ').',
            ], 422);
        }
        
        // Note: We allow the new check-out to be exactly one day before current check-out
        // This allows reducing by 1 night, which is a valid decrease request

        // Store original checkout if not already stored
        if (!$booking->original_check_out) {
            $booking->update(['original_check_out' => $booking->check_out]);
        }

        $booking->update([
            'extension_requested_to' => $validated['decrease_requested_to'],
            'extension_status' => 'pending',
            'extension_requested_at' => now(),
            'extension_reason' => $validated['decrease_reason'] ?? null,
            'extension_type' => 'decrease', // Mark as decrease request
        ]);

        // Notify reception and admin
        try {
            $currentUser = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
            $receptionUsers = \App\Models\Staff::where('role', 'reception')->get();
            $adminUsers = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])->get();
            
            foreach ($receptionUsers as $user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'extension_request',
                    'title' => 'Checkout Decrease Request',
                    'message' => ($currentUser->name ?? 'Guest') . ' requested to decrease checkout to ' . $newCheckOut->format('M d, Y'),
                    'icon' => 'fa-calendar-minus-o',
                    'color' => 'warning',
                    'role' => 'reception',
                    'notifiable_id' => $booking->id,
                    'notifiable_type' => Booking::class,
                    'link' => route('reception.bookings') . '?search=' . $booking->booking_reference,
                ]);
            }
            
            foreach ($adminUsers as $user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'extension_request',
                    'title' => 'Checkout Decrease Request',
                    'message' => ($currentUser->name ?? 'Guest') . ' requested to decrease checkout to ' . $newCheckOut->format('M d, Y'),
                    'icon' => 'fa-calendar-minus-o',
                    'color' => 'warning',
                    'role' => 'manager',
                    'notifiable_id' => $booking->id,
                    'notifiable_type' => Booking::class,
                    'link' => route('admin.bookings.show', $booking),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create decrease notification: ' . $e->getMessage());
        }

        // Calculate nights difference for frontend display
        $nightsDifference = $currentCheckOut->diffInDays($newCheckOut, false);

        return response()->json([
            'success' => true,
            'message' => 'Decrease request submitted successfully. For quick response call: 601 - Reception, 618 - Manager.',
            'booking' => $booking->fresh(),
            'nights_difference' => abs($nightsDifference),
            'cost_difference' => 0, // No refund for decreases
        ]);
    }

    /**
     * Directly modify booking dates (Manager/Reception)
     */
    public function modifyBookingDates(Request $request, Booking $booking)
    {
        // Verify user is staff (manager or reception)
        $user = auth()->guard('staff')->user();
        if (!$user || !in_array(strtolower($user->role ?? ''), ['manager', 'reception', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only staff can modify booking dates.',
            ], 403);
        }

        $validated = $request->validate([
            'new_check_out' => 'required|date|after:check_in',
            'reason' => 'nullable|string|max:500',
        ]);

        $currentCheckOut = Carbon::parse($booking->check_out);
        $newCheckOut = Carbon::parse($validated['new_check_out']);
        $checkIn = Carbon::parse($booking->check_in);

        // Validate new check-out date
        if ($newCheckOut->lte($checkIn)) {
            return response()->json([
                'success' => false,
                'message' => 'Check-out date must be after check-in date.',
            ], 400);
        }

        // Store original checkout if not already stored
        if (!$booking->original_check_out) {
            $booking->update(['original_check_out' => $booking->check_out]);
        }

        // Calculate price difference
        $room = $booking->room;
        $currentNights = $checkIn->diffInDays($currentCheckOut);
        $newNights = $checkIn->diffInDays($newCheckOut);
        $nightsDifference = $newNights - $currentNights;
        $priceDifference = $room->price_per_night * $nightsDifference;

        // Update booking
        $updateData = [
            'check_out' => $validated['new_check_out'],
            'total_price' => max(0, $booking->total_price + $priceDifference),
        ];

        // If extending, set extension status to approved
        if ($nightsDifference > 0) {
            $updateData['extension_status'] = 'approved';
            $updateData['extension_requested_to'] = $validated['new_check_out'];
            $updateData['extension_approved_at'] = now();
            if ($validated['reason']) {
                $updateData['extension_admin_notes'] = $validated['reason'];
            }
        } elseif ($nightsDifference < 0) {
            // If decreasing, clear any pending extension requests
            $updateData['extension_status'] = null;
            $updateData['extension_requested_to'] = null;
            $updateData['extension_reason'] = null;
        }

        $booking->update($updateData);
        $booking->refresh()->load('room');

        // Log the modification
        try {
            \Log::info('Booking dates modified by staff', [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'staff_id' => $user->id,
                'staff_name' => $user->name,
                'old_check_out' => $currentCheckOut->format('Y-m-d'),
                'new_check_out' => $newCheckOut->format('Y-m-d'),
                'nights_difference' => $nightsDifference,
                'price_difference' => $priceDifference,
                'reason' => $validated['reason'] ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log booking date modification: ' . $e->getMessage());
        }

        // If extending, send email notification to guest
        if ($nightsDifference > 0) {
            try {
                // Create notification for guest
                $guestUser = \App\Models\Guest::where('email', $booking->guest_email)->first();
                if ($guestUser) {
                    \App\Models\Notification::create([
                        'user_id' => $guestUser->id,
                        'type' => 'extension_approved',
                        'title' => 'Booking Extended',
                        'message' => 'Your booking has been extended. New checkout date: ' . $newCheckOut->format('M d, Y'),
                        'icon' => 'fa-check-circle',
                        'color' => 'success',
                        'role' => 'customer',
                        'notifiable_id' => $booking->id,
                        'notifiable_type' => Booking::class,
                        'link' => route('customer.dashboard'),
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to create extension notification: ' . $e->getMessage());
            }

            // Send email notification (queued for async processing)
            try {
                \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                    ->queue(new \App\Mail\ExtensionRequestStatusMail($booking, 'approved'));
            } catch (\Exception $e) {
                \Log::error('Failed to queue extension approved email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => $nightsDifference > 0 
                ? "Booking extended by {$nightsDifference} night(s). Additional cost: $" . number_format($priceDifference, 2)
                : "Booking decreased by " . abs($nightsDifference) . " night(s). Refund: $" . number_format(abs($priceDifference), 2),
            'booking' => $booking->fresh()->load('room'),
        ]);
    }

    /**
     * Approve or reject extension (Reception/Admin)
     */
    public function handleExtension(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        if ($booking->extension_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'No pending extension request found.',
            ], 400);
        }

        if ($validated['action'] === 'approve') {
            // Calculate cost difference
            $room = $booking->room;
            // Use original_check_out if available, otherwise use current check_out (for backward compatibility)
            $originalCheckOut = $booking->original_check_out 
                ? Carbon::parse($booking->original_check_out) 
                : Carbon::parse($booking->check_out);
            $newCheckOut = Carbon::parse($booking->extension_requested_to);
            
            // Use signed difference to determine type
            $nightsDifference = $originalCheckOut->diffInDays($newCheckOut, false);
            $isExtension = $nightsDifference > 0;
            
            // For extensions, calculate additional cost. For decreases, no refund (cost difference = 0)
            $costDifference = $isExtension ? ($room->price_per_night * abs($nightsDifference)) : 0;
            $requestType = $isExtension ? 'extension' : 'decrease';

            // Update booking - preserve original_check_out if not already set
            $updateData = [
                'check_out' => $booking->extension_requested_to,
                'extension_status' => 'approved',
                'extension_approved_at' => now(),
                'extension_admin_notes' => $validated['admin_notes'] ?? null,
                'total_price' => $booking->total_price + $costDifference,
            ];

            // If there's an additional cost, mark as partial payment so the guest can pay the difference
            if ($costDifference > 0) {
                $updateData['payment_status'] = 'partial';
            }
            
            // Only set original_check_out if it's not already set (for backward compatibility)
            if (!$booking->original_check_out) {
                $updateData['original_check_out'] = $booking->check_out;
            }
            
            $booking->update($updateData);
            
            // Mark extension/decrease request notifications as read (action taken)
            try {
                $notificationService = new NotificationService();
                $notificationService->markNotificationAsReadByNotifiable(
                    Booking::class,
                    $booking->id,
                    'extension_request',
                    'reception'
                );
                $notificationService->markNotificationAsReadByNotifiable(
                    Booking::class,
                    $booking->id,
                    'extension_request',
                    'manager'
                );
            } catch (\Exception $e) {
                \Log::error('Failed to mark request notification as read: ' . $e->getMessage());
            }

            // Notify guest
            try {
                $title = $isExtension ? 'Stay Extension Approved' : 'Stay Decrease Approved';
                $message = $isExtension 
                    ? 'Your request to extend your stay has been approved. New checkout date: ' . $newCheckOut->format('M d, Y')
                    : 'Your request to decrease your stay has been approved. New checkout date: ' . $newCheckOut->format('M d, Y');

                // Find the guest user ID from either User or Guest table
                $guestUserId = \App\Models\User::where('email', $booking->guest_email)->value('id');
                if (!$guestUserId) {
                    $guestUserId = \App\Models\Guest::where('email', $booking->guest_email)->value('id');
                }

                // Only create notification if we found a valid user
                if ($guestUserId) {
                    \App\Models\Notification::create([
                        'user_id' => $guestUserId,
                        'type' => 'extension_approved',
                        'title' => $title,
                        'message' => $message,
                        'icon' => $isExtension ? 'fa-check-circle' : 'fa-calendar-minus-o',
                        'color' => 'success',
                        'role' => 'customer',
                        'notifiable_id' => $booking->id,
                        'notifiable_type' => Booking::class,
                        'link' => route('customer.dashboard'),
                    ]);
                } else {
                    \Log::warning('Could not create extension notification - guest user not found', [
                        'booking_id' => $booking->id,
                        'guest_email' => $booking->guest_email
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to create approval notification: ' . $e->getMessage());
            }

            // Send email notification (queued for async processing)
            try {
                \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                    ->queue(new \App\Mail\ExtensionRequestStatusMail($booking->fresh()->load('room'), 'approved'));
            } catch (\Exception $e) {
                \Log::error('Failed to queue extension approved email: ' . $e->getMessage());
            }

            // Send email notification to managers and super admins for extension approval
            try {
                $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                    ->where('is_active', true)
                    ->get();
                
                    foreach ($managersAndAdmins as $staff) {
                        // Check if user has notifications enabled
                        if ($staff->isNotificationEnabled('extension_request')) {
                            try {
                                \Illuminate\Support\Facades\Mail::to($staff->email)
                                    ->send(new \App\Mail\StaffExtensionRequestMail($booking->fresh()->load('room'), 'approved'));
                            } catch (\Exception $e) {
                                \Log::error('Failed to send extension approval email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                            }
                        }
                    }
            } catch (\Exception $e) {
                \Log::error('Failed to send extension approval emails to managers/admins: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => $nightsDifference > 0 
                    ? 'Extension approved successfully. Additional cost: $' . number_format($costDifference, 2)
                    : 'Decrease approved successfully. Refund: $' . number_format(abs($costDifference), 2),
                'booking' => $booking->fresh(),
                'cost_difference' => $costDifference,
                'nights_difference' => $nightsDifference,
            ]);
        } else {
            // Reject extension
            $booking->update([
                'extension_status' => 'rejected',
                'extension_admin_notes' => $validated['admin_notes'] ?? null,
            ]);
            
            // Mark extension request notifications as read (action taken)
            try {
                $notificationService = new NotificationService();
                $notificationService->markNotificationAsReadByNotifiable(
                    Booking::class,
                    $booking->id,
                    'extension_request',
                    'reception'
                );
                $notificationService->markNotificationAsReadByNotifiable(
                    Booking::class,
                    $booking->id,
                    'extension_request',
                    'manager'
                );
            } catch (\Exception $e) {
                \Log::error('Failed to mark extension request notification as read: ' . $e->getMessage());
            }

            // Notify guest
            try {
                // Find the guest user ID from either User or Guest table
                $guestUserId = \App\Models\User::where('email', $booking->guest_email)->value('id');
                if (!$guestUserId) {
                    $guestUserId = \App\Models\Guest::where('email', $booking->guest_email)->value('id');
                }

                // Only create notification if we found a valid user
                if ($guestUserId) {
                    \App\Models\Notification::create([
                        'user_id' => $guestUserId,
                        'type' => 'extension_rejected',
                        'title' => 'Extension Request Rejected',
                        'message' => 'Your checkout extension request has been rejected.',
                        'icon' => 'fa-times-circle',
                        'color' => 'danger',
                        'role' => 'customer',
                        'notifiable_id' => $booking->id,
                        'notifiable_type' => Booking::class,
                        'link' => route('customer.dashboard'),
                    ]);
                } else {
                    \Log::warning('Could not create extension rejection notification - guest user not found', [
                        'booking_id' => $booking->id,
                        'guest_email' => $booking->guest_email
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to create rejection notification: ' . $e->getMessage());
            }

            // Send email notification (queued for async processing)
            try {
                \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                    ->queue(new \App\Mail\ExtensionRequestStatusMail($booking->fresh()->load('room'), 'rejected'));
            } catch (\Exception $e) {
                \Log::error('Failed to queue extension rejected email: ' . $e->getMessage());
            }

            // Send email notification to managers and super admins for extension rejection
            try {
                $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                    ->where('is_active', true)
                    ->get();
                
                foreach ($managersAndAdmins as $staff) {
                    // Check if user has notifications enabled
                    if ($staff->isNotificationEnabled('extension_request')) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($staff->email)
                                ->send(new \App\Mail\StaffExtensionRequestMail($booking->fresh()->load('room'), 'rejected'));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send extension rejection email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send extension rejection emails to managers/admins: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Extension request rejected.',
                'booking' => $booking->fresh(),
            ]);
        }
    }

    /**
     * Show the form for creating a manual booking
     */
    /**
     * Show the form for creating a corporate booking
     */
    public function createCorporate()
    {
        // Get room types for dropdown
        $roomTypes = Room::select('room_type')
            ->where('status', 'available')
            ->distinct()
            ->orderBy('room_type')
            ->pluck('room_type');
        
        // Get average capacity per room type
        $roomTypeCapacities = Room::select('room_type', DB::raw('AVG(capacity) as avg_capacity'))
            ->where('status', 'available')
            ->groupBy('room_type')
            ->pluck('avg_capacity', 'room_type')
            ->map(function($capacity) {
                return (int) round($capacity);
            })
            ->toArray();
        
        // Default capacities if no rooms found
        $defaultCapacities = [
            'Single' => 1,
            'Double' => 2,
            'Twins' => 2,
        ];
        
        $roomTypeCapacities = array_merge($defaultCapacities, $roomTypeCapacities);
        
        // Get current exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        // Get current user
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        $userName = $user->name ?? 'Manager';
        $userRole = 'Manager';
        
        return view('dashboard.corporate-booking', [
            'roomTypes' => $roomTypes,
            'roomTypeCapacities' => $roomTypeCapacities,
            'userName' => $userName,
            'userRole' => $userRole,
            'exchangeRate' => $exchangeRate
        ]);
    }

    /**
     * Store a corporate booking
     */
    public function storeCorporate(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'company_email' => 'required|email|max:255',
                'company_phone' => 'nullable|string|max:255',
                'guider_name' => 'required|string|max:255',
                'guider_email' => 'required|email|max:255',
                'guider_phone' => 'required|string|max:255',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
                'number_of_guests' => 'required|integer|min:1|max:50',
                'general_notes' => 'nullable|string|max:2000',
                'guests' => 'required|json',
                'rooms' => 'required|json',
                'payment_method' => 'required|in:online,cash,bank,mobile,card,other',
                'payment_provider' => 'required_if:payment_method,mobile,bank,card,online|nullable|string|max:255',
                'payment_reference' => 'required_if:payment_method,online,bank,mobile,card,other|nullable|string|max:255',
                'amount_paid' => 'required|numeric|min:0',
                'total_price' => 'required|numeric|min:0',
                'recommended_price' => 'nullable|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        // Parse JSON data
        $guests = json_decode($validated['guests'], true);
        $rooms = json_decode($validated['rooms'], true);

        if (!is_array($guests) || !is_array($rooms) || count($guests) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid guest or room data.'
            ], 422);
        }

        // Validate that all guests have required fields
        foreach ($guests as $guest) {
            if (empty($guest['full_name']) || empty($guest['email']) || empty($guest['phone']) || empty($guest['room_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'All guests must have full name, email, phone, and room assignment.'
                ], 422);
            }
        }

        // Parse dates
        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights = $checkIn->diffInDays($checkOut);

        // Set locked exchange rate to 1 as we are using TZS exclusively
        $lockedExchangeRate = 1;

        // Create or find company
        $company = \App\Models\Company::firstOrCreate(
            ['email' => $validated['company_email']],
            [
                'name' => $validated['company_name'],
                'phone' => $validated['company_phone'] ?? null,
                'contact_person' => $validated['guider_name'],
                'guider_email' => $validated['guider_email'],
                'guider_phone' => $validated['guider_phone'],
                'is_active' => true,
            ]
        );

        // Update company info if it already existed
        if ($company->wasRecentlyCreated === false) {
            $company->update([
                'name' => $validated['company_name'],
                'phone' => $validated['company_phone'] ?? null,
                'contact_person' => $validated['guider_name'],
                'guider_email' => $validated['guider_email'],
                'guider_phone' => $validated['guider_phone'],
            ]);
        }
        
        // Store general notes (will be included in emails)
        $generalNotes = $validated['general_notes'] ?? null;

        $createdBookings = [];
        $createdGuests = [];
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($guests as $guestData) {
                // Find the room
                $room = Room::find($guestData['room_id']);
                if (!$room) {
                    $errors[] = "Room not found for guest: {$guestData['full_name']}";
                    continue;
                }

                // Check room availability
                $hasConflict = Booking::where('room_id', $room->id)
                    ->where('status', 'confirmed')
                    ->where(function ($query) use ($checkIn, $checkOut) {
                        $query->where(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in', '<', $checkOut)
                              ->where('check_out', '>', $checkIn);
                        });
                    })
                    ->where(function ($q) {
                        $q->whereIn('payment_status', ['paid', 'partial'])
                          ->orWhere(function ($subQ) {
                              $subQ->where('payment_status', 'pending')
                                   ->where(function ($deadlineQ) {
                                       $deadlineQ->whereNull('payment_deadline')
                                                ->orWhere('payment_deadline', '>', Carbon::now());
                                   });
                          });
                    })
                    ->exists();

                if ($hasConflict) {
                    $errors[] = "Room {$room->room_number} is not available for guest: {$guestData['full_name']}";
                    continue;
                }

                // Calculate room cost in USD (room prices are stored in USD per night)
                // Company pays in USD, so we store in USD
                $roomPriceUSD = $room->price_per_night;
                $roomCostUSD = $roomPriceUSD * $nights; // Total cost in USD
                $paymentResponsibility = $guestData['payment_responsibility'] ?? 'company';

                // Generate unique booking reference
                $bookingReference = 'CBK' . strtoupper(Str::random(8));
                $guestId = $this->generateGuestId();

                // Extract first name for password
                $fullName = trim($guestData['full_name']);
                $nameParts = explode(' ', $fullName);
                $firstName = $nameParts[0];
                $password = strtoupper($firstName);

                // Create or update guest account
                $guest = Guest::firstOrNew(['email' => $guestData['email']]);
                $isNewGuest = !$guest->exists;
                
                if (!$guest->exists) {
                    $guest->name = $fullName;
                    $guest->email = $guestData['email'];
                    $guest->password = $password;
                    $guest->phone = $guestData['phone'] ?? null;
                    $guest->country = $guestData['country'] ?? null;
                    $guest->is_active = true;
                    $guest->save();
                } else {
                    $guest->name = $fullName;
                    $guest->password = $password;
                    $guest->phone = $guestData['phone'] ?? $guest->phone;
                    $guest->country = $guestData['country'] ?? $guest->country;
                    $guest->is_active = true;
                    $guest->save();
                }

                $createdGuests[] = $guest;

                // Payment will be distributed proportionally later
                // For now, set initial values
                $paymentStatus = 'pending';
                $amountPaid = 0;
                $paymentPercentage = 0;

                // Determine guest type based on country
                $guestType = 'international';
                $nationality = $guestData['country'] ?? '';
                if (strtolower($nationality) === 'tanzania' || strtolower($nationality) === 'tanzanian') {
                    $guestType = 'tanzanian';
                }

                // Create booking
                $booking = Booking::create([
                    'room_id' => $room->id,
                    'guest_name' => $fullName,
                    'first_name' => $firstName,
                    'last_name' => count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '',
                    'guest_email' => $guestData['email'],
                    'country' => $guestData['country'] ?? null,
                    'guest_type' => $guestType,
                    'guest_phone' => $guestData['phone'],
                    'country_code' => '+255', // Default, can be improved
                    'check_in' => $checkIn->format('Y-m-d'),
                    'check_out' => $checkOut->format('Y-m-d'),
                    'arrival_time' => '14:00', // Default check-in time
                    'number_of_guests' => 1, // Each booking is for one guest
                    'total_price' => $roomCostUSD, // Store in USD (company pays in USD)
                    'recommended_price' => $validated['recommended_price'] ?? $roomCostUSD, // Store in USD
                    'special_requests' => $guestData['special_requests'] ?? null,
                    'status' => 'confirmed',
                    'payment_status' => $paymentStatus,
                    'payment_method' => 'pending', // Will be updated when payment is received
                    'amount_paid' => $amountPaid,
                    'payment_percentage' => $paymentPercentage,
                    'booking_reference' => $bookingReference,
                    'guest_id' => $guestId,
                    'company_id' => $company->id,
                    'payment_responsibility' => $paymentResponsibility,
                    'is_corporate_booking' => true,
                    'locked_exchange_rate' => $lockedExchangeRate,
                ]);

                $createdBookings[] = $booking;

                // Handle special requests notifications for this guest
                if (!empty($guestData['special_requests']) && !empty($guestData['notify_departments'])) {
                    $notificationService = new NotificationService();
                    $departments = $guestData['notify_departments'];
                    
                    // Map department values to role names
                    $roleMapping = [
                        'reception' => 'reception',
                        'bar_keeper' => 'bar_keeper',
                        'head_chef' => 'head_chef',
                    ];
                    
                    foreach ($departments as $department) {
                        $role = $roleMapping[$department] ?? null;
                        if ($role) {
                            // Create notification for the department
                            Notification::create([
                                'type' => 'booking',
                                'title' => 'Special Request from Guest',
                                'message' => "Guest {$fullName} (Room {$room->room_number}) has special requests: " . Str::limit($guestData['special_requests'], 150),
                                'icon' => 'fa-sticky-note',
                                'color' => 'info',
                                'role' => $role,
                                'notifiable_id' => $booking->id,
                                'notifiable_type' => Booking::class,
                                'link' => $role === 'reception' ? route('reception.bookings') : ($role === 'bar_keeper' ? route('bar-keeper.dashboard') : route('chef-master.dashboard')),
                            ]);
                            
                            // Send email notification to all staff members of this department
                            try {
                                $departmentStaff = \App\Models\Staff::where('role', $role)
                                    ->where('is_active', true)
                                    ->get();
                                
                                foreach ($departmentStaff as $staff) {
                                    try {
                                        $emailSubject = "Special Request from Guest - Room {$room->room_number}";
                                        $emailBody = "Dear {$staff->name},\n\n";
                                        $emailBody .= "Guest {$fullName} (Room {$room->room_number}) has submitted special requests:\n\n";
                                        $emailBody .= $guestData['special_requests'] . "\n\n";
                                        $emailBody .= "Check-in: {$checkIn->format('Y-m-d H:i')}\n";
                                        $emailBody .= "Check-out: {$checkOut->format('Y-m-d H:i')}\n\n";
                                        $emailBody .= "Please review and prepare accordingly.\n\n";
                                        $emailBody .= "Booking Reference: {$bookingReference}\n\n";
                                        $emailBody .= "Best regards,\nPrimeLand Hotel System";
                                        
                                        Mail::raw($emailBody, function($message) use ($staff, $emailSubject, $fullName, $room) {
                                            $message->to($staff->email)
                                                ->subject($emailSubject);
                                        });
                                    } catch (\Exception $e) {
                                        \Log::error('Failed to send special request email to staff', [
                                            'staff_email' => $staff->email,
                                            'error' => $e->getMessage()
                                        ]);
                                    }
                                }
                            } catch (\Exception $e) {
                                \Log::error('Failed to send special request notifications', [
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                }

                // Send booking confirmation email to guest
                try {
                    $booking->load('room');
                    // Calculate remaining amount in USD (company pays in USD)
                    $bookingAmountPaid = $booking->amount_paid ?? 0;
                    $remainingAmountUSD = max(0, $roomCostUSD - $bookingAmountPaid);
                    
                    Mail::to($guestData['email'])->send(new BookingConfirmationMail(
                        $booking,
                        $password,
                        $paymentPercentage,
                        $remainingAmountUSD,
                        $generalNotes
                    ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send booking confirmation email to guest', [
                        'booking_reference' => $bookingReference,
                        'guest_email' => $guestData['email'],
                        'error' => $e->getMessage()
                    ]);
                }

                // Send welcome email if new guest
                if ($isNewGuest) {
                    try {
                        $guestForEmail = (object)[
                            'name' => $fullName,
                            'email' => $guestData['email'],
                        ];
                        Mail::to($guestData['email'])->send(new \App\Mail\WelcomeMail($guestForEmail, $password));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send welcome email', [
                            'guest_email' => $guestData['email'],
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            if (count($errors) > 0 && count($createdBookings) === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create bookings: ' . implode('; ', $errors)
                ], 422);
            }

            // Process payment distribution
            $totalAmountPaid = $validated['amount_paid'];
            $totalBookingValue = $validated['total_price'];
            $paymentMethod = $validated['payment_method'];
            $paymentProvider = $validated['payment_provider'] ?? null;
            $paymentReference = $validated['payment_reference'] ?? null;
            
            // Calculate payment distribution
            // Note: Room charges are always company-paid, payment_responsibility only applies to services
            // For now, all room costs go to company, self-paid is 0 (services will be calculated separately)
            $totalCompanyCost = collect($createdBookings)->sum('total_price'); // All room costs are company-paid
            $totalSelfPaidCost = 0; // Services will be calculated separately when guests use them
            
            // Distribute payment proportionally
            foreach ($createdBookings as $booking) {
                $bookingProportion = $totalBookingValue > 0 ? ($booking->total_price / $totalBookingValue) : 0;
                $bookingAmountPaid = $totalAmountPaid * $bookingProportion;
                $bookingPaymentPercentage = $booking->total_price > 0 ? ($bookingAmountPaid / $booking->total_price) * 100 : 0;
                
                // Determine payment status
                if ($bookingPaymentPercentage >= 100) {
                    $bookingPaymentStatus = 'paid';
                } elseif ($bookingPaymentPercentage > 0) {
                    $bookingPaymentStatus = 'partial';
                } else {
                    $bookingPaymentStatus = 'pending';
                }
                
                // Update booking with payment information
                $booking->update([
                    'payment_method' => $paymentMethod,
                    'payment_provider' => $paymentProvider,
                    'payment_transaction_id' => $paymentReference,
                    'amount_paid' => $bookingAmountPaid,
                    'payment_percentage' => $bookingPaymentPercentage,
                    'payment_status' => $bookingPaymentStatus,
                    'paid_at' => $totalAmountPaid > 0 ? now() : null,
                ]);
            }

            DB::commit();

            // Reload bookings with relationships for emails and receipts
            foreach ($createdBookings as $booking) {
                $booking->load('room', 'company');
            }

            // Calculate totals for emails (recalculate after payment distribution)
            $totalCompanyPaid = collect($createdBookings)->filter(function($b) {
                return $b->payment_responsibility === 'company';
            })->sum('amount_paid');
            $totalSelfPaid = collect($createdBookings)->filter(function($b) {
                return $b->payment_responsibility === 'self';
            })->sum('amount_paid');

            // Send booking confirmation emails to all guests (with updated payment info)
            foreach ($createdBookings as $index => $booking) {
                try {
                    // Get guest password from createdGuests array
                    $guestPassword = null;
                    if (isset($createdGuests[$index])) {
                        $nameParts = explode(' ', $booking->guest_name);
                        $guestPassword = strtoupper($nameParts[0]);
                    } else {
                        // Fallback: generate from booking guest name
                        $nameParts = explode(' ', $booking->guest_name);
                        $guestPassword = strtoupper($nameParts[0]);
                    }
                    
                    $bookingPaymentPercentage = $booking->payment_percentage ?? 0;
                    $bookingRemainingAmount = $booking->total_price - ($booking->amount_paid ?? 0);
                    
                    Mail::to($booking->guest_email)->send(new BookingConfirmationMail(
                        $booking,
                        $guestPassword,
                        $bookingPaymentPercentage,
                        $bookingRemainingAmount
                    ));
                    
                    \Log::info('Corporate booking confirmation email sent to guest', [
                        'booking_reference' => $booking->booking_reference,
                        'guest_email' => $booking->guest_email
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send booking confirmation email to guest', [
                        'booking_reference' => $booking->booking_reference,
                        'guest_email' => $booking->guest_email,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Send company invoice email
            try {
                Mail::to($validated['company_email'])->send(new \App\Mail\CompanyInvoiceMail(
                    $company,
                    $createdBookings,
                    $totalCompanyCost,
                    $totalSelfPaidCost,
                    $totalCompanyPaid,
                    $checkIn,
                    $checkOut,
                    $generalNotes
                ));
                \Log::info('Company invoice email sent', [
                    'company_email' => $validated['company_email'],
                    'company_name' => $company->name
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send company invoice email', [
                    'company_id' => $company->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Send guider/leader confirmation email
            try {
                Mail::to($validated['guider_email'])->send(new \App\Mail\GuiderConfirmationMail(
                    $validated['guider_name'],
                    $company,
                    $createdBookings,
                    $checkIn,
                    $checkOut,
                    $generalNotes
                ));
                \Log::info('Guider confirmation email sent', [
                    'guider_email' => $validated['guider_email'],
                    'guider_name' => $validated['guider_name']
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send guider confirmation email', [
                    'guider_email' => $validated['guider_email'],
                    'error' => $e->getMessage()
                ]);
            }

            // Send notification to reception
            try {
                $receptionStaff = \App\Models\Staff::where('role', 'reception')
                    ->where('is_active', true)
                    ->get();

                foreach ($receptionStaff as $staff) {
                    Notification::create([
                        'type' => 'booking',
                        'title' => 'New Corporate Booking',
                        'message' => "Corporate booking created for {$company->name} with " . count($createdBookings) . " guest(s)",
                        'icon' => 'fa-building',
                        'color' => 'info',
                        'role' => 'reception',
                        'notifiable_id' => $company->id,
                        'notifiable_type' => \App\Models\Company::class,
                        'link' => route('admin.bookings.index') . '?company=' . $company->id,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to create reception notification', [
                    'error' => $e->getMessage()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Corporate booking created successfully! ' . count($createdBookings) . ' booking(s) created.',
                'company_id' => $company->id,
                'bookings_count' => count($createdBookings),
                'warnings' => count($errors) > 0 ? $errors : null,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create corporate booking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create corporate booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available rooms for corporate booking (multiple room types)
     */
    public function getCorporateAvailableRooms(Request $request)
    {
        $validated = $request->validate([
            'room_types' => 'required|array',
            'room_types.*' => 'in:Single,Double,Twins',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $roomTypes = $validated['room_types'];

        // Base query for all potentially available rooms
        $baseQuery = Room::whereIn('room_type', $roomTypes)
            ->whereIn('status', ['available', 'occupied', 'to_be_cleaned'])
            ->whereDoesntHave('bookings', function ($query) use ($checkIn, $checkOut) {
                $query->whereIn('status', ['pending', 'confirmed'])
                      ->where(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in', '<', $checkOut)
                            ->where('check_out', '>', $checkIn);
                      })
                      ->where(function ($q) {
                          $q->where(function ($statusQ) {
                              $statusQ->where('status', 'confirmed')
                                      ->where(function ($paymentQ) {
                                          $paymentQ->whereIn('payment_status', ['paid', 'partial'])
                                                  ->orWhere(function ($subQ) {
                                                      $subQ->where('payment_status', 'pending')
                                                           ->where(function ($deadlineQ) {
                                                               $deadlineQ->whereNull('payment_deadline')
                                                                        ->orWhere('payment_deadline', '>', Carbon::now());
                                                           });
                                                  });
                                      });
                          })
                          ->orWhere(function ($pendingQ) {
                              $pendingQ->where('status', 'pending')
                                      ->where(function ($expireQ) {
                                          $expireQ->whereNull('expires_at')
                                                 ->orWhere('expires_at', '>', Carbon::now());
                                      });
                          });
                      });
            });

        $allAvailableRooms = $baseQuery->orderBy('room_type', 'asc')
            ->orderBy('status', 'asc')
            ->orderBy('room_number', 'asc')
            ->get();

        $mapRoom = function ($room) use ($checkIn) {
            $images = $room->images ?? [];
            $firstImage = !empty($images) && is_array($images) ? $images[0] : null;

            // Check for actual active occupancy (guest currently checked in)
            $activeGuestBooking = $room->bookings()
                ->where('check_in_status', 'checked_in')
                ->where('check_out', '>=', Carbon::now()->format('Y-m-d'))
                ->first();

            // Room is available now ONLY if its DB status is 'available' AND no one is actually checked in
            $isAvailableNow = ($room->status === 'available' && !$activeGuestBooking);
            
            // For soon available, find current checkout date
            $checkoutDate = null;
            $isSoonAvailable = !$isAvailableNow;
            
            if ($isSoonAvailable) {
                if ($activeGuestBooking) {
                    $checkoutDate = Carbon::parse($activeGuestBooking->check_out)->format('Y-m-d');
                } else {
                    // Fallback to checking for recently checked out or soon to check out bookings
                    $lastOrNextBooking = $room->bookings()
                        ->whereIn('status', ['confirmed'])
                        ->where('check_out', '<=', $checkIn->format('Y-m-d'))
                        ->where('check_out', '>=', Carbon::now()->format('Y-m-d'))
                        ->orderBy('check_out', 'desc')
                        ->first();

                    if ($lastOrNextBooking) {
                        $checkoutDate = Carbon::parse($lastOrNextBooking->check_out)->format('Y-m-d');
                    } else if ($room->status === 'to_be_cleaned') {
                        $checkoutDate = 'Today';
                    }
                }
            }

            // Logic for selectable:
            // If check-in is today, and room is NOT available now (due to cleaning OR current occupancy), disallow selection.
            $canSelect = true;
            if ($checkIn->isToday() && !$isAvailableNow) {
                $canSelect = false;
            }

            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'room_type' => $room->room_type,
                'price_per_night' => $room->price_per_night,
                'capacity' => $room->capacity ?? 1,
                'bed_type' => $room->bed_type ?? null,
                'floor_location' => $room->floor_location ?? null,
                'image' => $firstImage,
                'images' => $images,
                'is_available_now' => $isAvailableNow,
                'is_soon_available' => $isSoonAvailable,
                'checkout_date' => $checkoutDate,
                'status' => $activeGuestBooking ? 'occupied' : $room->status, // Use active status if checked in
                'can_select' => $canSelect,
            ];
        };

        return response()->json([
            'success' => true,
            'available_rooms' => $allAvailableRooms->map($mapRoom)->values(),
        ]);
    }

    /**
     * Get available rooms for a given room type and date range
     */
    public function getAvailableRooms(Request $request)
    {
        $validated = $request->validate([
            'room_type' => 'required|in:Single,Double,Twins',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);

        // Base query for all potentially available rooms (available, occupied, to_be_cleaned)
        $baseQuery = Room::whereIn('status', ['available', 'occupied', 'to_be_cleaned'])
            ->whereDoesntHave('bookings', function ($query) use ($checkIn, $checkOut) {
                $query->whereIn('status', ['pending', 'confirmed'])
                      ->where(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in', '<', $checkOut)
                            ->where('check_out', '>', $checkIn);
                      })
                      ->where(function ($q) {
                          $q->where(function ($statusQ) {
                              $statusQ->where('status', 'confirmed')
                                      ->where(function ($paymentQ) {
                                          $paymentQ->whereIn('payment_status', ['paid', 'partial'])
                                                  ->orWhere(function ($subQ) {
                                                      $subQ->where('payment_status', 'pending')
                                                           ->where(function ($deadlineQ) {
                                                               $deadlineQ->whereNull('payment_deadline')
                                                                        ->orWhere('payment_deadline', '>', Carbon::now());
                                                           });
                                                  });
                                      });
                          })
                          ->orWhere(function ($pendingQ) {
                              $pendingQ->where('status', 'pending')
                                      ->where(function ($expireQ) {
                                          $expireQ->whereNull('expires_at')
                                                 ->orWhere('expires_at', '>', Carbon::now());
                                      });
                          });
                      });
            });

        // Get rooms for the selected type
        $allRoomsOfType = (clone $baseQuery)->where('room_type', $validated['room_type'])
            ->orderBy('status', 'asc') // available first
            ->orderBy('room_number', 'asc')
            ->get();

        // Get rooms for other types
        $allOtherRooms = (clone $baseQuery)->where('room_type', '!=', $validated['room_type'])
            ->orderBy('room_type', 'asc')
            ->orderBy('status', 'asc')
            ->orderBy('room_number', 'asc')
            ->get();

        $mapRoom = function ($room) use ($checkIn) {
            $images = $room->images ?? [];
            $firstImage = !empty($images) && is_array($images) ? $images[0] : null;

            // Check for actual active occupancy (guest currently checked in)
            $activeGuestBooking = $room->bookings()
                ->where('check_in_status', 'checked_in')
                ->where('check_out', '>=', Carbon::now()->format('Y-m-d'))
                ->first();

            // Room is available now ONLY if its DB status is 'available' AND no one is actually checked in
            $isAvailableNow = ($room->status === 'available' && !$activeGuestBooking);
            
            // For soon available, find current checkout date
            $checkoutDate = null;
            $isSoonAvailable = !$isAvailableNow;
            
            if ($isSoonAvailable) {
                if ($activeGuestBooking) {
                    $checkoutDate = Carbon::parse($activeGuestBooking->check_out)->format('Y-m-d');
                } else {
                    // Fallback to checking for recently checked out or soon to check out bookings
                    $lastOrNextBooking = $room->bookings()
                        ->whereIn('status', ['confirmed'])
                        ->where('check_out', '<=', $checkIn->format('Y-m-d'))
                        ->where('check_out', '>=', Carbon::now()->format('Y-m-d'))
                        ->orderBy('check_out', 'desc')
                        ->first();

                    if ($lastOrNextBooking) {
                        $checkoutDate = Carbon::parse($lastOrNextBooking->check_out)->format('Y-m-d');
                    } else if ($room->status === 'to_be_cleaned') {
                        $checkoutDate = 'Today';
                    }
                }
            }

            // Logic for selectable:
            // If check-in is today, and room is NOT available now (due to cleaning OR current occupancy), disallow selection.
            $canSelect = true;
            if ($checkIn->isToday() && !$isAvailableNow) {
                $canSelect = false;
            }

            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'room_type' => $room->room_type,
                'price_per_night' => $room->price_per_night,
                'capacity' => $room->capacity ?? 1,
                'bed_type' => $room->bed_type ?? null,
                'floor_location' => $room->floor_location ?? null,
                'image' => $firstImage,
                'images' => $images,
                'is_available_now' => $isAvailableNow,
                'is_soon_available' => $isSoonAvailable,
                'checkout_date' => $checkoutDate,
                'status' => $activeGuestBooking ? 'occupied' : $room->status, // Use active status if checked in
                'can_select' => $canSelect,
            ];
        };

        return response()->json([
            'success' => true,
            'available_rooms' => $allRoomsOfType->map($mapRoom)->values(),
            'other_available_rooms' => $allOtherRooms->map($mapRoom)->values(),
        ]);
    }

    public function createManual()
    {
        // Get room types for dropdown - include any type that has potentially bookable rooms
        $roomTypes = Room::select('room_type')
            ->whereIn('status', ['available', 'occupied', 'to_be_cleaned'])
            ->distinct()
            ->orderBy('room_type')
            ->pluck('room_type');
        
        // Get average capacity per room type
        $roomTypeCapacities = Room::select('room_type', DB::raw('AVG(capacity) as avg_capacity'))
            ->whereIn('status', ['available', 'occupied', 'to_be_cleaned'])
            ->groupBy('room_type')
            ->pluck('avg_capacity', 'room_type')
            ->map(function($capacity) {
                return (int) round($capacity); // Round to nearest integer
            })
            ->toArray();
        
        // Default capacities if no rooms found
        $defaultCapacities = [
            'Single' => 1,
            'Double' => 2,
            'Twins' => 2,
        ];
        
        // Merge defaults with actual data
        $roomTypeCapacities = array_merge($defaultCapacities, $roomTypeCapacities);
        
        // Get current exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        // Determine role based on route name
        $routeName = request()->route()->getName() ?? '';
        $isReception = str_starts_with($routeName, 'reception.');
        $role = $isReception ? 'reception' : 'manager';
        
        // Get current user
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        $userName = $user->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff');
        $userRole = $role === 'manager' ? 'Manager' : 'Reception';
        
        return view('dashboard.manual-booking', [
            'roomTypes' => $roomTypes,
            'roomTypeCapacities' => $roomTypeCapacities,
            'role' => $role,
            'userName' => $userName,
            'userRole' => $userRole,
            'exchangeRate' => $exchangeRate
        ]);
    }

    /**
     * Store a manually created booking
     */
    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:255',
            'nationality' => 'required_if:guest_type,international|nullable|string|max:255',
            'country_code' => 'required|string|max:10',
            'guest_type' => 'required|in:tanzanian,international',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_in_time' => 'required|string',
            'check_out' => 'required|date|after:check_in',
            'check_out_time' => 'required|string',
            'number_of_guests' => 'required|integer|min:1|max:10',
            'special_requests' => 'nullable|string',
            'notify_departments' => 'nullable|array',
            'notify_departments.*' => 'in:reception,bar_keeper,head_chef',
            'total_price' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:online,cash,bank,mobile,card,other',
            'payment_provider' => 'required_if:payment_method,mobile,bank,card,online|nullable|string|max:255',
            'payment_reference' => 'required_if:payment_method,online,bank,mobile,card,other|nullable|string|max:255',
        ]);

        // For Tanzanian guests, set nationality to Tanzania if not provided
        if ($validated['guest_type'] === 'tanzanian' && empty($validated['nationality'])) {
            $validated['nationality'] = 'Tanzania';
            $validated['country_code'] = '+255';
        }

        // Combine date and time for check-in and check-out
        $checkIn = Carbon::parse($validated['check_in'] . ' ' . ($validated['check_in_time'] ?? '14:00'));
        $checkOut = Carbon::parse($validated['check_out'] . ' ' . ($validated['check_out_time'] ?? '10:00'));

        // Get the selected room
        $room = Room::findOrFail($validated['room_id']);
        
        // Check if room is available for the selected dates
        $hasConflict = Booking::where('room_id', $room->id)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    $q->where('check_in', '<', $checkOut)
                      ->where('check_out', '>', $checkIn);
                });
            })
            ->where(function ($q) {
                $q->whereIn('payment_status', ['paid', 'partial'])
                  ->orWhere(function ($subQ) {
                      $subQ->where('payment_status', 'pending')
                           ->where(function ($deadlineQ) {
                               $deadlineQ->whereNull('payment_deadline')
                                        ->orWhere('payment_deadline', '>', Carbon::now());
                           });
                  });
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, this room is not available for the selected dates.'
            ], 422);
        }

        // Calculate recommended price (room price * nights)
        $nights = $checkIn->diffInDays($checkOut);
        $recommendedPrice = $room->price_per_night * $nights;

        // Calculate payment amounts from amount_paid
        $totalPrice = $validated['total_price'];
        $amountPaid = $validated['amount_paid'];
        $paymentPercentage = $totalPrice > 0 ? ($amountPaid / $totalPrice) * 100 : 0;
        $remainingAmount = $totalPrice - $amountPaid;

        // Generate unique booking reference
        $bookingReference = 'BK' . strtoupper(Str::random(8));
        
        // Generate unique guest ID
        $guestId = $this->generateGuestId();

        // Set locked exchange rate to 1 as we are using TZS exclusively
        $lockedExchangeRate = 1;

        // Calculate payment deadline based on new policy
        $daysUntilArrival = Carbon::now()->diffInDays($checkIn, false);
        $paymentDeadline = null;
        
        if ($daysUntilArrival >= 30) {
            // More than 30 days: Full payment required 30 days before arrival
            $paymentDeadline = $checkIn->copy()->subDays(30);
        } elseif ($daysUntilArrival >= 20) {
            // 20-29 days: Full payment required 30 days before (which is in the past, so use 48 hours)
            $paymentDeadline = Carbon::now()->addHours(48);
        } else {
            // Less than 20 days: 50% required within 48 hours
            $paymentDeadline = Carbon::now()->addHours(48);
        }

        // Extract first name from full name for password
        $fullName = trim($validated['full_name']);
        $nameParts = explode(' ', $fullName);
        $firstName = $nameParts[0]; // First word is the first name
        
        // Create password (first name in CAPITALS)
        $password = strtoupper($firstName);

        // Create or update guest account (Guest model only - users table is not used)
        // Note: Guest model has 'password' => 'hashed' cast, so we don't need Hash::make()
        $guest = Guest::firstOrNew(['email' => $validated['guest_email']]);
        $isNewGuest = !$guest->exists;
        if (!$guest->exists) {
            $guest->name = $fullName;
            $guest->email = $validated['guest_email'];
            $guest->password = $password; // Laravel will auto-hash due to 'hashed' cast
            $guest->phone = $validated['guest_phone'] ?? null;
            $guest->country = $validated['nationality'] ?? null;
            $guest->is_active = true;
            $guest->save();
        } else {
            // Update existing guest info and password
            $guest->name = $fullName;
            $guest->password = $password; // Laravel will auto-hash due to 'hashed' cast
            $guest->phone = $validated['guest_phone'] ?? $guest->phone;
            $guest->country = $validated['nationality'] ?? $guest->country;
            $guest->is_active = true;
            $guest->save();
        }

        // Determine payment status
        $paymentStatus = $paymentPercentage >= 100 ? 'paid' : 'partial';

        // Determine payment status
        $paymentStatus = $paymentPercentage >= 100 ? 'paid' : 'partial';

        // Create the booking
        $booking = Booking::create([
            'room_id' => $room->id,
            'guest_name' => $fullName,
            'first_name' => $firstName,
            'last_name' => count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '',
            'guest_email' => $validated['guest_email'],
            'country' => $validated['nationality'],
            'guest_type' => $validated['guest_type'],
            'guest_phone' => $validated['guest_phone'],
            'country_code' => $validated['country_code'],
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'arrival_time' => $checkIn->format('H:i'),
            'number_of_guests' => $validated['number_of_guests'],
            'special_requests' => $validated['special_requests'] ?? null,
            'total_price' => $totalPrice,
            'recommended_price' => $recommendedPrice,
            'status' => 'confirmed',
            'payment_status' => $paymentStatus,
            'payment_method' => $validated['payment_method'],
            'payment_provider' => $validated['payment_provider'] ?? null,
            'payment_transaction_id' => $validated['payment_reference'] ?? null,
            'amount_paid' => $amountPaid,
            'payment_percentage' => $paymentPercentage,
            'paid_at' => now(),
            'booking_reference' => $bookingReference,
            'guest_id' => $guestId,
            'payment_deadline' => $paymentDeadline,
            'locked_exchange_rate' => $lockedExchangeRate,
        ]);

        // Send booking confirmation email
        $emailSent = false;
        $emailError = null;
        try {
            // Reload booking with room relationship to ensure it's available
            $booking->load('room');
            
            // Send email immediately (not queued) to ensure it's sent right away
            Mail::to($validated['guest_email'])->send(new BookingConfirmationMail($booking, $password, $paymentPercentage, $remainingAmount));
            $emailSent = true;
            \Log::info('Manual booking confirmation email sent successfully', [
                'booking_reference' => $bookingReference,
                'guest_email' => $validated['guest_email'],
                'guest_name' => $fullName
            ]);
        } catch (\Exception $e) {
            $emailError = $e->getMessage();
            
            // Create user-friendly error message
            $userFriendlyError = 'Unable to connect to email server.';
            if (str_contains($emailError, 'Connection could not be established')) {
                $userFriendlyError = 'Email server is unreachable. Please check SMTP settings or contact administrator.';
            } elseif (str_contains($emailError, 'timeout') || str_contains($emailError, 'timed out')) {
                $userFriendlyError = 'Email server connection timed out. Please try again later or contact administrator.';
            } elseif (str_contains($emailError, 'authentication') || str_contains($emailError, 'credentials')) {
                $userFriendlyError = 'Email authentication failed. Please check SMTP credentials.';
            }
            
            \Log::error('Failed to send manual booking confirmation email', [
                'booking_reference' => $bookingReference,
                'guest_email' => $validated['guest_email'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $emailError = $userFriendlyError;
        }


        // Send welcome email if this is a new guest account
        \Log::info('Checking welcome email condition', [
            'booking_reference' => $bookingReference,
            'guest_email' => $validated['guest_email'],
            'is_new_guest' => $isNewGuest
        ]);
        
        if ($isNewGuest) {
            try {
                // Create a guest-like object for the welcome email
                $guestForEmail = (object)[
                    'name' => $fullName,
                    'email' => $validated['guest_email'],
                ];
                // Send welcome email immediately (not queued)
                Mail::to($validated['guest_email'])->send(new \App\Mail\WelcomeMail($guestForEmail, $password));
                \Log::info('Welcome email sent successfully for manual booking', [
                    'booking_reference' => $bookingReference,
                    'guest_email' => $validated['guest_email'],
                    'guest_name' => $fullName,
                    'is_new_guest' => $isNewGuest
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email for manual booking', [
                    'booking_reference' => $bookingReference,
                    'guest_email' => $validated['guest_email'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } else {
            \Log::info('Welcome email skipped - guest already exists', [
                'booking_reference' => $bookingReference,
                'guest_email' => $validated['guest_email'],
                'is_new_guest' => $isNewGuest
            ]);
        }

        // Send email notification to reception staff
        try {
            $receptionStaff = \App\Models\Staff::where('role', 'reception')
                ->where('is_active', true)
                ->get();
            
            foreach ($receptionStaff as $staff) {
                try {
                    Mail::to($staff->email)->send(new \App\Mail\StaffNewBookingMail($booking->load('room')));
                } catch (\Exception $e) {
                    \Log::error('Failed to send booking email to reception staff: ' . $staff->email . ' - ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send booking emails to reception staff: ' . $e->getMessage());
        }

        // Send email notification to managers
        try {
            $managers = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                ->where('is_active', true)
                ->get();
            
            foreach ($managers as $manager) {
                try {
                    Mail::to($manager->email)->send(new \App\Mail\StaffNewBookingMail($booking->load('room')));
                } catch (\Exception $e) {
                    \Log::error('Failed to send booking email to manager: ' . $manager->email . ' - ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send booking emails to managers: ' . $e->getMessage());
        }

        // Create notification
        try {
            $notificationService = new NotificationService();
            $notificationService->createBookingNotification($booking->load('room'));
        } catch (\Exception $e) {
            \Log::error('Failed to create booking notification: ' . $e->getMessage());
        }

        // Send special requests notifications to selected departments
        if (!empty($validated['special_requests']) && !empty($validated['notify_departments'])) {
            try {
                $notificationService = new NotificationService();
                $departments = $validated['notify_departments'];
                
                // Map department values to role names
                $roleMapping = [
                    'reception' => 'reception',
                    'bar_keeper' => 'bar_keeper',
                    'head_chef' => 'head_chef',
                ];
                
                foreach ($departments as $department) {
                    $role = $roleMapping[$department] ?? null;
                    if ($role) {
                        // Create notification for the department
                        Notification::create([
                            'type' => 'booking',
                            'title' => 'Special Request from Guest',
                            'message' => "Guest {$fullName} (Room {$room->room_number}) has special requests: " . Str::limit($validated['special_requests'], 150),
                            'icon' => 'fa-sticky-note',
                            'color' => 'info',
                            'role' => $role,
                            'notifiable_id' => $booking->id,
                            'notifiable_type' => Booking::class,
                            'link' => $role === 'reception' ? route('reception.bookings') : ($role === 'bar_keeper' ? route('bar-keeper.dashboard') : route('chef-master.dashboard')),
                        ]);
                        
                        // Send email notification to all staff members of this department
                        try {
                            $departmentStaff = \App\Models\Staff::where('role', $role)
                                ->where('is_active', true)
                                ->get();
                            
                            foreach ($departmentStaff as $staff) {
                                try {
                                    // Create a simple email notification
                                    $emailSubject = "Special Request from Guest - Room {$room->room_number}";
                                    $emailBody = "Dear {$staff->name},\n\n";
                                    $emailBody .= "Guest {$fullName} (Room {$room->room_number}) has submitted special requests:\n\n";
                                    $emailBody .= $validated['special_requests'] . "\n\n";
                                    $emailBody .= "Check-in: {$checkIn->format('Y-m-d H:i')}\n";
                                    $emailBody .= "Check-out: {$checkOut->format('Y-m-d H:i')}\n\n";
                                    $emailBody .= "Please review and prepare accordingly.\n\n";
                                    $emailBody .= "Booking Reference: {$bookingReference}\n\n";
                                    $emailBody .= "Best regards,\nPrimeLand Hotel System";
                                    
                                    Mail::raw($emailBody, function($message) use ($staff, $emailSubject, $fullName, $room) {
                                        $message->to($staff->email)
                                                ->subject($emailSubject);
                                    });
                                    
                                    \Log::info('Special request email sent to department staff', [
                                        'staff_email' => $staff->email,
                                        'role' => $role,
                                        'booking_reference' => $bookingReference
                                    ]);
                                } catch (\Exception $e) {
                                    \Log::error('Failed to send special request email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::error('Failed to send special request emails to department: ' . $e->getMessage());
                        }
                    }
                }
                
                \Log::info('Special requests notifications sent to departments', [
                    'booking_reference' => $bookingReference,
                    'departments' => $departments,
                    'guest_name' => $fullName
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send special requests notifications: ' . $e->getMessage());
            }
        }

        $message = 'Booking created successfully!';
        if ($emailSent) {
            $message .= ' Email notifications have been sent to guest, reception, and manager.';
        } else {
            if ($emailError) {
                $message .= ' ' . $emailError;
            } else {
                $message .= ' Email sending failed. Please check email configuration.';
            }
        }
        
        // Generate receipt URL
        $receiptUrl = route('payment.receipt.download', $booking->id);
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'booking' => $booking->load('room'),
            'booking_reference' => $bookingReference,
            'email_sent' => $emailSent,
            'email_error' => $emailError,
            'receipt_url' => $receiptUrl,
        ]);
    }

    /**
     * Show customer support page
     */
    public function customerSupport()
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('customer.login')->withErrors(['email' => 'Please login to access support.']);
        }
        
        return view('dashboard.customer-support', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
        ]);
    }
    /**
     * Show customer restaurant ordering page
     */
    public function restaurantService(Request $request)
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        // Find the active checked-in booking to associate orders with
        $activeBooking = \App\Models\Booking::where('guest_email', $user->email)
            ->where('check_in_status', 'checked_in')
            ->orderBy('check_in', 'desc')
            ->first();

        // 1. Get available drinks and calculate stock levels
        $barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'spirits', 'wines', 'cocktails', 'hot_beverages'];
        
        // Fetch all completed transfers to build historical stock
        $allTransfers = \App\Models\StockTransfer::where('status', 'completed')->get();
        // Fetch all completed sales to deduct from stock
        $allSales = \App\Models\ServiceRequest::where('status', 'completed')
            ->whereHas('service', function($q) use ($barCategories) {
                $q->whereIn('category', $barCategories);
            })->get();

        // Build a stock mapping [variant_id => current_stock_in_pics]
        $stockLevels = [];
        foreach ($allTransfers as $t) {
            $vid = $t->product_variant_id;
            if (!isset($stockLevels[$vid])) $stockLevels[$vid] = 0;
            
            $itemsPerPkg = $t->productVariant->items_per_package ?? 1;
            $pics = ($t->quantity_unit === 'packages') ? ($t->quantity_transferred * $itemsPerPkg) : $t->quantity_transferred;
            $stockLevels[$vid] += $pics;
        }

        foreach ($allSales as $s) {
            $meta = $s->service_specific_data;
            $vid = $meta['product_variant_id'] ?? null;
            if ($vid && isset($stockLevels[$vid])) {
                $variant = \App\Models\ProductVariant::find($vid);
                if ($variant) {
                    $unitPrice = (float)$s->unit_price_tsh;
                    $isPicSale = abs($unitPrice - (float)$variant->selling_price_per_pic) < 100;
                    if ($isPicSale) {
                        $stockLevels[$vid] -= $s->quantity;
                    } else {
                        $servingsPerPic = $variant->servings_per_pic > 0 ? $variant->servings_per_pic : 1;
                        $stockLevels[$vid] -= ($s->quantity / $servingsPerPic);
                    }
                }
            }
        }

        // Only show products that have at least one variant with stock > 0
        $products = \App\Models\Product::whereIn('category', $barCategories)
            ->with(['variants'])
            ->get();

        $drinks = [];
        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                $options = [];
                
                // Option A: Sell as Bottle (PIC)
                if ($variant->can_sell_as_pic && $variant->selling_price_per_pic > 0) {
                    $options[] = [
                        'type' => 'Bottle',
                        'method' => 'pic',
                        'price' => (float)$variant->selling_price_per_pic
                    ];
                }

                // Option B: Sell as Glass / Tot / Serving
                if ($variant->can_sell_as_serving && $variant->selling_price_per_serving > 0) {
                    $options[] = [
                        'type' => $variant->selling_unit_name ?? 'Glass',
                        'method' => 'serving',
                        'price' => (float)$variant->selling_price_per_serving
                    ];
                }
                
                // Fallback for items that don't have the new PIC fields set up yet
                if (empty($options)) {
                    $latestReceipt = \App\Models\StockReceipt::where('product_variant_id', $variant->id)
                        ->orderBy('received_date', 'desc')
                        ->first();
                    $price = $latestReceipt ? $latestReceipt->selling_price_per_bottle : 0;
                    
                    if ($price <= 0) {
                        $service = \App\Models\Service::where('name', 'LIKE', '%' . $product->name . '%')->first();
                        $price = $service ? $service->price_tsh : 0;
                    }

                    if ($price > 0) {
                        $options[] = [
                            'type' => 'Bottle',
                            'method' => 'pic',
                            'price' => (float)$price
                        ];
                    }
                }

                if (!empty($options)) {
                    $currentStock = $stockLevels[$variant->id] ?? 0;
                    // Use variant name if available, otherwise product name
                    $displayName = $variant->variant_name ?: $product->name;
                    $displayName .= ($variant->measurement ? ' (' . $variant->measurement . ')' : '');
                    
                    $drinks[] = (object)[
                        'id' => $product->id,
                        'variant_id' => $variant->id,
                        'name' => $displayName,
                        'is_product' => true, // Flag to distinguish
                        'category' => $product->category,
                        'image' => $variant->image ?: $product->image, // Also prioritize variant image
                        'options' => $options,
                        'in_stock' => $currentStock > 0,
                        'current_stock' => $currentStock,
                        'servings_per_pic' => $variant->servings_per_pic > 0 ? (float)$variant->servings_per_pic : 1
                    ];
                }
            }
        }

        // 2. Also get any Services that are drinks but might not be in Products
        $serviceDrinks = \App\Models\Service::whereIn('category', $barCategories)
            ->where('is_active', true)
            ->get();

        foreach ($serviceDrinks as $service) {
            $alreadyAdded = false;
            foreach ($drinks as $drink) {
                // Determine if this service matches an existing product in the list (fuzzy match)
                if (isset($drink->is_product) && (str_contains(strtolower($service->name), strtolower($drink->name)) || str_contains(strtolower($drink->name), strtolower($service->name)))) {
                    $alreadyAdded = true;
                    break;
                }
            }

            if (!$alreadyAdded) {
                // Ensure structured options for service-based drinks
                $options = [[
                    'type' => 'Unit',
                    'method' => 'pic',
                    'price' => (float)$service->price_tsh
                ]];

                $drinks[] = (object)[
                    'id' => $service->id,
                    'variant_id' => null, // Services don't have variants
                    'name' => $service->name,
                    'is_product' => false,
                    'item_type' => 'Unit',
                    'category' => $service->category,
                    'price_tsh' => (float)$service->price_tsh,
                    'image' => null,
                    'is_product' => false,
                    'selling_method' => 'pic',
                    'options' => $options,
                    'in_stock' => true, // Services are assumed always available unless inactive
                    'current_stock' => 999
                ];
            }
        }

        // 3. Get all available Food Recipes
        $recipes = \App\Models\Recipe::where('is_available', true)->get();
        
        $foodItems = [];
        foreach ($recipes as $recipe) {
            $foodItems[] = [
                'id' => $recipe->id,
                'name' => $recipe->name,
                'description' => $recipe->description ?? 'Chef Special',
                'price' => $recipe->selling_price,
                'category' => $recipe->category ?? 'Food',
                'image' => $recipe->image,
                'prep_time' => $recipe->prep_time ?? 0,
                'cook_time' => $recipe->cook_time ?? 0,
            ];
        }

        return view('dashboard.customer-restaurant', compact('activeBooking', 'drinks', 'foodItems'));
    }

    /**
     * Search for existing guests for auto-fill
     */
    public function searchGuests(Request $request)
    {
        $query = $request->get('q');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $guests = Guest::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        // Attach last booking info
        $guests->transform(function ($guest) {
            $lastBooking = Booking::where('guest_email', $guest->email)
                ->orderBy('check_in', 'desc')
                ->first();
            
            if ($lastBooking) {
                $guest->last_booking_date = \Carbon\Carbon::parse($lastBooking->check_in)->format('M d, Y');
                $guest->last_booking_details = [
                    'room' => $lastBooking->room_number ?? ($lastBooking->room ? $lastBooking->room->room_number : 'N/A'),
                    'type' => $lastBooking->room_type ?? ($lastBooking->room ? $lastBooking->room->room_type : 'N/A'),
                    'dates' => \Carbon\Carbon::parse($lastBooking->check_in)->format('M d') . ' - ' . \Carbon\Carbon::parse($lastBooking->check_out)->format('M d, Y'),
                    'status' => $lastBooking->status,
                    'total_price' => number_format($lastBooking->total_price, 2)
                ];
            } else {
                $guest->last_booking_date = 'Never';
                $guest->last_booking_details = null;
            }
            return $guest;
        });

        return response()->json($guests);
    }

    /**
     * Search for existing companies for auto-fill
     */
    public function searchCompanies(Request $request)
    {
        $query = $request->get('q');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $companies = \App\Models\Company::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($companies);
    }
}
