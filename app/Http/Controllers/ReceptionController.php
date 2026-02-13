<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReceptionController extends Controller
{
    /**
     * Determine the role based on current route
     */
    private function getRole()
    {
        $routeName = request()->route()->getName() ?? '';
        return str_starts_with($routeName, 'admin.') ? 'manager' : 'reception';
    }

    /**
     * Show all bookings for reception
     */
    /**
     * Show all bookings for reception
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['room', 'company', 'serviceRequests'])->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status && $request->status !== 'all') {
            if ($request->status === 'expired') {
                // Show expired bookings (pending bookings that have expired)
                $query->where(function($q) {
                    $q->where(function($subQ) {
                        // Pending bookings that have expired
                        $subQ->where('status', 'pending')
                             ->where('payment_status', 'pending')
                             ->whereNotNull('expires_at')
                             ->where('expires_at', '<=', \Carbon\Carbon::now());
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
        } elseif (!$request->has('status') || !$request->status || $request->status === 'all') {
            // Exclude expired bookings from main list unless specifically requested
            $query->where(function($q) {
                $q->where(function($subQ) {
                    $subQ->where('status', '!=', 'pending')
                         ->orWhere('payment_status', '!=', 'pending')
                         ->orWhereNull('expires_at')
                         ->orWhere('expires_at', '>', \Carbon\Carbon::now());
                });
            });
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by check-in status
        if ($request->has('check_in_status') && $request->check_in_status && $request->check_in_status !== 'all') {
            $query->where('check_in_status', $request->check_in_status);
        }

        // Filter by booking type (individual or corporate)
        $bookingType = $request->get('type', 'individual'); // Default to individual
        /* if ($bookingType === 'corporate') {
            $query->where('is_corporate_booking', true);
            
            // Group corporate bookings by company_id
            // Get unique company IDs first
            $companyIds = (clone $query)->reorder()->whereNotNull('company_id')->distinct()->pluck('company_id');
            
            // Get bookings grouped by company
            $groupedBookings = collect();
            foreach ($companyIds as $companyId) {
                $companyBookings = Booking::with(['room', 'company', 'serviceRequests'])
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
                                     ->where('expires_at', '<=', \Carbon\Carbon::now());
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
                                 ->orWhere('expires_at', '>', \Carbon\Carbon::now());
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
        } else { */
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


        // Get statistics filtered by booking type
        /* if ($bookingType === 'corporate') {
            // For corporate bookings, count unique companies
            $baseQuery = Booking::where('is_corporate_booking', true);
            
            // Apply status filter if provided
            if ($request->has('status') && $request->status == 'expired') {
                $baseQuery->where(function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('status', 'pending')
                             ->where('payment_status', 'pending')
                             ->whereNotNull('expires_at')
                             ->where('expires_at', '<=', \Carbon\Carbon::now());
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
        } else { */
            // For individual bookings, count individual bookings
            // Base query for individual bookings
            $baseQuery = Booking::where(function($q) {
                $q->where('is_corporate_booking', false)
                  ->orWhereNull('is_corporate_booking');
            });

            
            // Synchronize stats with all current filters
            $statsQuery = clone $baseQuery;
            
            // 1. Apply status filter (excluding expired)
            if ($request->has('status') && $request->status && $request->status !== 'all') {
                if ($request->status === 'expired') {
                    $statsQuery->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->where('status', 'pending')
                                 ->where('payment_status', 'pending')
                                 ->whereNotNull('expires_at')
                                 ->where('expires_at', '<=', \Carbon\Carbon::now());
                        })->orWhere(function($subQ) {
                            $subQ->where('status', 'cancelled')
                                 ->whereNotNull('cancellation_reason')
                                 ->where('cancellation_reason', 'like', '%expired%');
                        });
                    });
                } else {
                    $statsQuery->where('status', $request->status);
                }
            } else {
                // Default view (no expired)
                $statsQuery->where(function($q) {
                    $q->where('status', '!=', 'pending')
                         ->orWhere('payment_status', '!=', 'pending')
                         ->orWhereNull('expires_at')
                         ->orWhere('expires_at', '>', \Carbon\Carbon::now());
                });
            }

            // 2. Apply payment status filter
            if ($request->has('payment_status') && $request->payment_status && $request->payment_status !== 'all') {
                $statsQuery->where('payment_status', $request->payment_status);
            }

            // 3. Apply check-in status filter
            if ($request->has('check_in_status') && $request->check_in_status && $request->check_in_status !== 'all') {
                $statsQuery->where('check_in_status', $request->check_in_status);
            }

            // 4. Apply search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $statsQuery->where(function($q) use ($search) {
                    $q->where('guest_name', 'like', "%{$search}%")
                      ->orWhere('booking_reference', 'like', "%{$search}%")
                      ->orWhere('guest_email', 'like', "%{$search}%");
                });
            }
            
            // Also get global totals for the tabs (always unfiltered by status/payment/check-in)
            $allIndividualTotal = Booking::where(function($q) {
                $q->where('is_corporate_booking', false)
                  ->orWhereNull('is_corporate_booking');
            })->count();
            
            $allCorporateTotal = Booking::where('is_corporate_booking', true)
                ->whereNotNull('company_id')
                ->distinct('company_id')
                ->count('company_id');
            
            $stats = [
                'total' => $statsQuery->count(),
                'individual_total' => $allIndividualTotal,
                'corporate_total' => $allCorporateTotal,
                'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
                'confirmed' => (clone $statsQuery)->where('status', 'confirmed')->count(),
                'cancelled' => (clone $statsQuery)->where('status', 'cancelled')->count(),
                'completed' => (clone $statsQuery)->where('status', 'completed')->count(),
                'expired' => (clone $statsQuery)->where('status', 'cancelled')
                    ->whereNotNull('cancellation_reason')
                    ->where('cancellation_reason', 'like', '%expired automatically%')
                    ->count(),
                'checked_in' => (clone $statsQuery)->where('check_in_status', 'checked_in')->count(),
                'checked_out' => (clone $statsQuery)->where('check_in_status', 'checked_out')->count(),
            ];


        $role = $this->getRole();
        
        return view('dashboard.bookings-list', [
            'bookings' => $bookings,
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'filters' => $request->only(['status', 'payment_status', 'check_in_status', 'search', 'type']),
            'stats' => $stats,
            'bookingType' => $bookingType,
        ]);
    }

    /**
     * Show new reservation form
     */
    public function newReservation()
    {
        $rooms = Room::orderBy('room_number')->get();
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-new-reservation', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'rooms' => $rooms,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Show check-in page
     */
    public function checkIn(Request $request)
    {
        // Filter by booking type (individual or corporate)
        $bookingType = $request->get('type', 'individual'); // Default to individual
        
        $query = Booking::with('room')
            ->where('status', 'confirmed')
            ->whereIn('payment_status', ['paid', 'partial'])
            ->where(function($q) {
                // Include paid bookings
                $q->where('payment_status', 'paid')
                  // Or partial payments where amount_paid > 0
                  ->orWhere(function($subQ) {
                      $subQ->where('payment_status', 'partial')
                           ->whereNotNull('amount_paid')
                           ->where('amount_paid', '>', 0);
                  });
            })
            ->where('check_in_status', 'pending');

        // Filter by booking type
        if ($bookingType === 'corporate') {
            $query->where('is_corporate_booking', true);
            
            // Group corporate bookings by company_id
            $companyIds = $query->whereNotNull('company_id')->distinct()->pluck('company_id');
            
            $groupedBookings = collect();
            foreach ($companyIds as $companyId) {
                $companyBookings = Booking::with(['room', 'company', 'serviceRequests'])
                    ->where('is_corporate_booking', true)
                    ->where('company_id', $companyId)
                    ->where('status', 'confirmed')
                    ->whereIn('payment_status', ['paid', 'partial'])
                    ->where(function($q) {
                        $q->where('payment_status', 'paid')
                          ->orWhere(function($subQ) {
                              $subQ->where('payment_status', 'partial')
                                   ->whereNotNull('amount_paid')
                                   ->where('amount_paid', '>', 0);
                          });
                    })
                    ->where('check_in_status', 'pending');
                
                // Search functionality
                if ($request->has('search') && $request->search) {
                    $search = $request->search;
                    $companyBookings->where(function($q) use ($search) {
                        $q->where('booking_reference', 'like', "%{$search}%")
                          ->orWhere('guest_name', 'like', "%{$search}%")
                          ->orWhere('guest_email', 'like', "%{$search}%")
                          ->orWhereHas('company', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                          });
                    });
                }
                
                // Filter by check-in date
                if ($request->has('check_in_date') && $request->check_in_date) {
                    $companyBookings->whereDate('check_in', '<=', $request->check_in_date);
                } else {
                    $companyBookings->whereDate('check_in', '<=', Carbon::today()->addDay());
                }
                
                $bookingsForCompany = $companyBookings->orderBy('check_in', 'asc')->get();
                
                if ($bookingsForCompany->count() > 0) {
                    $groupedBookings->push([
                        'company' => $bookingsForCompany->first()->company,
                        'bookings' => $bookingsForCompany,
                        'first_booking' => $bookingsForCompany->first(),
                    ]);
                }
            }
            
            // Paginate grouped bookings manually
            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
            $perPage = 20;
            $currentItems = $groupedBookings->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $groupedBookings->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Individual bookings
            $query->where('is_corporate_booking', false);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('booking_reference', 'like', "%{$search}%")
                      ->orWhere('guest_name', 'like', "%{$search}%")
                      ->orWhere('guest_email', 'like', "%{$search}%");
                });
            }

            // Filter by check-in date - show customers 1 day before check-in date
            if ($request->has('check_in_date') && $request->check_in_date) {
                // Show bookings where check-in date is on or before the selected date
                $query->whereDate('check_in', '<=', $request->check_in_date);
            } else {
                // Default: show bookings where check-in is today or tomorrow (1 day before)
                $query->whereDate('check_in', '<=', Carbon::today()->addDay());
            }

            $bookings = $query->orderBy('check_in', 'asc')->paginate(20);
        }

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        // Calculate statistics
        $stats = [
            'individual_total' => Booking::where('is_corporate_booking', false)
                ->where('status', 'confirmed')
                ->whereIn('payment_status', ['paid', 'partial'])
                ->where('check_in_status', 'pending')
                ->where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere(function($subQ) {
                          $subQ->where('payment_status', 'partial')
                               ->whereNotNull('amount_paid')
                               ->where('amount_paid', '>', 0);
                      });
                })
                ->count(),
            'corporate_total' => Booking::where('is_corporate_booking', true)
                ->where('status', 'confirmed')
                ->whereIn('payment_status', ['paid', 'partial'])
                ->where('check_in_status', 'pending')
                ->where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere(function($subQ) {
                          $subQ->where('payment_status', 'partial')
                               ->whereNotNull('amount_paid')
                               ->where('amount_paid', '>', 0);
                      });
                })
                ->distinct('company_id')
                ->count('company_id'),
        ];

        $role = $this->getRole();
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        return view('dashboard.reception-check-in', [
            'role' => $role,
            'userName' => $user->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'user' => $user,
            'bookings' => $bookings,
            'bookingType' => $bookingType,
            'exchangeRate' => $exchangeRate,
            'filters' => $request->only(['search', 'check_in_date']),
            'stats' => $stats,
        ]);
    }

    /**
     * Show check-out page
     */
    public function checkOut(Request $request)
    {
        // Filter by booking type (individual or corporate)
        $bookingType = $request->get('type', 'individual'); // Default to individual
        
        // Show all guests who are checked in OR checked out but not paid
        // Include bookings that are checked in (ready for check-out)
        $query = Booking::with('room')
            ->where('status', 'confirmed') // Only show confirmed bookings
            ->where(function($q) {
                $q->where('check_in_status', 'checked_in')
                  ->orWhere(function($q2) {
                      $q2->where('check_in_status', 'checked_out')
                         ->where('payment_status', '!=', 'paid');
                  });
            });

        // Filter by booking type
        if ($bookingType === 'corporate') {
            $query->where('is_corporate_booking', true);
            
            // Group corporate bookings by company_id
            $companyIds = $query->whereNotNull('company_id')->distinct()->pluck('company_id');
            
            $groupedBookings = collect();
            foreach ($companyIds as $companyId) {
                $companyBookings = Booking::with(['room', 'company', 'serviceRequests.service'])
                    ->where('is_corporate_booking', true)
                    ->where('company_id', $companyId)
                    ->where('status', 'confirmed') // Only show confirmed bookings
                    ->where(function($q) {
                        // Show anyone who is checked in or recently checked out
                        $q->where('check_in_status', 'checked_in')
                          ->orWhere('check_in_status', 'checked_out');
                    });
                
                // Search functionality
                if ($request->has('search') && $request->search) {
                    $search = $request->search;
                    $companyBookings->where(function($q) use ($search) {
                        $q->where('booking_reference', 'like', "%{$search}%")
                          ->orWhere('guest_name', 'like', "%{$search}%")
                          ->orWhere('guest_email', 'like', "%{$search}%")
                          ->orWhereHas('company', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                          });
                    });
                }
                
                // Filter by check-out date (optional - if provided)
                if ($request->has('check_out_date') && $request->check_out_date) {
                    $companyBookings->whereDate('check_out', '<=', $request->check_out_date);
                }
                // Show all checked-in bookings regardless of check-out date
                
                $bookingsForCompany = $companyBookings->orderBy('check_out', 'asc')->get();
                
                if ($bookingsForCompany->count() > 0) {
                    $groupedBookings->push([
                        'company' => $bookingsForCompany->first()->company,
                        'bookings' => $bookingsForCompany,
                        'first_booking' => $bookingsForCompany->first(),
                    ]);
                }
            }
            
            // Calculate outstanding balances before pagination
            $currencyService = new CurrencyExchangeService();
            $exchangeRate = $currencyService->getUsdToTshRate();
            
            // Use map to transform the collection and add totals
            $groupedBookings = $groupedBookings->map(function ($group) use ($exchangeRate) {
                $totalOutstandingTsh = 0;
                $totalOutstandingUsd = 0;
                
                foreach ($group['bookings'] as $booking) {
                    // Use locked exchange rate from booking, or fallback to current rate
                    $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
                    
                    // Calculate service charges
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
                    $extensionCostTsh = $extensionCostUsd * $bookingExchangeRate;
                    
                    // Check payment responsibility - if self-paid, exclude service charges from company bill
                    $paymentResponsibility = $booking->payment_responsibility ?? 'company';
                    $companyResponsibleServiceChargesTsh = 0;
                    
                    if ($paymentResponsibility === 'self') {
                        // Guest pays for services - exclude from company bill
                        $companyResponsibleServiceChargesTsh = 0;
                    } else {
                        // Company pays for services
                        $companyResponsibleServiceChargesTsh = $totalServiceChargesTsh;
                    }
                    
                    // Company's total bill (room + company-responsible services + extensions)
                    // Note: extensionCostTsh is already included in booking->total_price
                    $companyBillTsh = ($booking->total_price * $bookingExchangeRate) + $companyResponsibleServiceChargesTsh;
                    
                    // Identify what portion of amount_paid was for guest-paid services
                    // Logic: If payment_responsibility is 'self', only services paid via 'cash' (bar) or any method at reception 
                    // should be considered part of the booking's amount_paid field and thus subtracted from the company's credit.
                    // For now, let's look at services that are 'paid' and NOT 'room_charge'.
                    $paidServiceRequests = $serviceRequests->where('payment_status', 'paid');
                    
                    // We need to know which of these 'paid' services were recorded into 'amount_paid'.
                    // Based on our system: Reception payments always update it. 
                    // Bar payments update it if they are 'cash' (and now everything else after the fix).
                    $servicePaymentsInAmountPaidTsh = $paidServiceRequests->filter(function($sr) {
                        // If it's paid and not charged to room, it was a direct payment.
                        // We check if it was likely recorded in the booking's amount_paid.
                        return true; // Conservative approach: assume all direct service payments are in amount_paid
                    })->sum('total_price_tsh');
                    
                    // Total amount recorded in the booking (includes room payments + guest service payments)
                    $totalPaidTsh = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
                    
                    // Calculate what the Company/Room-Payer has contributed
                    if ($paymentResponsibility === 'self') {
                        // Company only cares about the room. They should have paid $TotalRoom - $GuestServicePayments
                        // But wait! $amount_paid is just a sum. 
                        // If guest paid $11.77 and company paid $140, amount_paid = $151.77.
                        // Then companyPaid = 151.77 - 11.77 = $140. Correct.
                        // HOWEVER, if guest paid $11.77 at bar (mobile) and it DIR NOT update amount_paid,
                        // then amount_paid = $140. Then companyPaid = 140 - 11.77 = $128.23. WRONG.
                        
                        // FIX: We only subtract $paidServiceChargesTsh if they are actually in $amount_paid.
                        // Since we can't be 100% sure for old data, we'll try to guess.
                        // If $amount_paid < $room_price, it's likely the guest payment isn't in there yet or room isn't paid.
                        
                        $companyPaidTsh = $totalPaidTsh; 
                        // If we treat amount_paid as the company's ledger, then anything in there is company money.
                        // But if guest pays at reception, it goes in there.
                        // Let's use a more defensive check:
                        $roomPriceTsh = $booking->total_price * $bookingExchangeRate + $extensionCostTsh;
                        if ($totalPaidTsh > $roomPriceTsh) {
                            // If they've paid more than the room, the surplus is likely guest service payments
                            $companyPaidTsh = $roomPriceTsh;
                        }
                    } else {
                        $companyPaidTsh = $totalPaidTsh;
                    }
                    
                    // Company's outstanding balance
                    $companyOutstandingBalanceTsh = max(0, $companyBillTsh - $companyPaidTsh);
                    $companyOutstandingBalanceUsd = $companyOutstandingBalanceTsh / $bookingExchangeRate;
                    
                    // Guest's outstanding balance (only what guest is responsible for)
                    if ($paymentResponsibility === 'self') {
                        // Guest owes only their UNPAID service charges
                        $unpaidServiceRequests = $serviceRequests->filter(fn($sr) => ($sr->payment_status ?? 'pending') !== 'paid');
                        $guestOutstandingBalanceTsh = $unpaidServiceRequests->sum('total_price_tsh');
                        $guestOutstandingBalanceUsd = $guestOutstandingBalanceTsh / $bookingExchangeRate;
                    } else {
                        $guestOutstandingBalanceTsh = 0;
                        $guestOutstandingBalanceUsd = 0;
                    }
                    
                    // Total bill for display (room + services)
                    // Note: total_price already includes extensions if they were approved
                    $totalBillTsh = ($booking->total_price * $bookingExchangeRate) + $totalServiceChargesTsh;
                    $totalBillUsd = $totalBillTsh / $bookingExchangeRate;
                    
                    // Treat very small amounts (less than $0.05 or 50 TZS) as fully paid (rounding differences)
                    $minOutstandingThresholdUsd = 0.05;
                    $minOutstandingThresholdTsh = 50;
                    if ($companyOutstandingBalanceUsd < $minOutstandingThresholdUsd || $companyOutstandingBalanceTsh < $minOutstandingThresholdTsh) {
                        $companyOutstandingBalanceTsh = 0;
                        $companyOutstandingBalanceUsd = 0;
                    }
                    // Apply threshold to guest outstanding balance too
                    if ($guestOutstandingBalanceUsd < $minOutstandingThresholdUsd || $guestOutstandingBalanceTsh < $minOutstandingThresholdTsh) {
                        $guestOutstandingBalanceTsh = 0;
                        $guestOutstandingBalanceUsd = 0;
                    }

                    // Self-healing: Finalize status if checked out and balance is cleared
                    if ($companyOutstandingBalanceTsh == 0 && $guestOutstandingBalanceTsh == 0 && 
                        $booking->check_in_status === 'checked_out' && $booking->payment_status !== 'paid') {
                        $booking->update(['payment_status' => 'paid']);
                    }
                    
                    // Add to booking object for view
                    // Show guest's outstanding (only services if self-paid, 0 if company-paid)
                    $booking->outstanding_balance_tsh = $guestOutstandingBalanceTsh;
                    $booking->outstanding_balance_usd = $guestOutstandingBalanceUsd;
                    $booking->total_bill_tsh = $totalBillTsh;
                    $booking->total_bill_usd = $totalBillUsd;
                    
                    // Store company's portion separately for the view
                    $booking->company_outstanding_balance_tsh = $companyOutstandingBalanceTsh;
                    $booking->company_outstanding_balance_usd = $companyOutstandingBalanceUsd;
                    $booking->guest_outstanding_balance_tsh = $guestOutstandingBalanceTsh;
                    $booking->guest_outstanding_balance_usd = $guestOutstandingBalanceUsd;
                    
                    // Accumulate only company's outstanding balance for the group total
                    $totalOutstandingTsh += $companyOutstandingBalanceTsh;
                    $totalOutstandingUsd += $companyOutstandingBalanceUsd;
                }
                
                // Add group totals to the group array
                $group['total_outstanding_tsh'] = $totalOutstandingTsh;
                $group['total_outstanding_usd'] = $totalOutstandingUsd;
                
                return $group;
            });
            
            // Paginate grouped bookings manually
            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
            $perPage = 20;
            $currentItems = $groupedBookings->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $groupedBookings->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Individual bookings
            $query->where('is_corporate_booking', false);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('booking_reference', 'like', "%{$search}%")
                      ->orWhere('guest_name', 'like', "%{$search}%")
                      ->orWhere('guest_email', 'like', "%{$search}%");
                });
            }

            // Filter by check-out date (optional - if provided)
            if ($request->has('check_out_date') && $request->check_out_date) {
                // Show bookings where check-out date is on or before the selected date
                $query->whereDate('check_out', '<=', $request->check_out_date);
            }
            // Show all checked-in bookings regardless of check-out date

            $bookings = $query->with(['room', 'serviceRequests.service'])->orderBy('check_out', 'asc')->paginate(20);
        }

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        // Calculate outstanding balance for each booking
        if ($bookingType === 'corporate') {
            // Outstanding balances already calculated before pagination
        } else {
            // For individual bookings
            foreach ($bookings as $booking) {
                // Use locked exchange rate from booking, or fallback to current rate
                $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
                
                // Calculate total bill (room + services + extensions)
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
                $extensionCostTsh = $extensionCostUsd * $bookingExchangeRate;
                
                // Total bill
                // Note: extensionCostTsh is already included in booking->total_price
                $totalBillTsh = ($booking->total_price * $bookingExchangeRate) + $totalServiceChargesTsh;
                
                // Amount paid (Booking deposit + any settled service payments)
                $amountPaidTsh = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
                
                // Add payments for completed/paid services to show correct outstanding balance
                foreach ($serviceRequests as $sr) {
                    if ($sr->payment_status === 'paid') {
                        $amountPaidTsh += $sr->total_price_tsh;
                    }
                }
                
                // Outstanding balance
                $outstandingBalanceTsh = max(0, $totalBillTsh - $amountPaidTsh);
                $outstandingBalanceUsd = $outstandingBalanceTsh / $bookingExchangeRate;
                
                // Treat very small amounts (less than $0.05 or 50 TZS) as fully paid (rounding differences)
                $minOutstandingThresholdUsd = 0.05;
                $minOutstandingThresholdTsh = 50;
                if ($outstandingBalanceUsd < $minOutstandingThresholdUsd || $outstandingBalanceTsh < $minOutstandingThresholdTsh) {
                    $outstandingBalanceTsh = 0;
                    $outstandingBalanceUsd = 0;
                    
                    // Self-healing: Finalize status if checked out and balance is cleared
                    if ($booking->check_in_status === 'checked_out' && $booking->payment_status !== 'paid') {
                        $booking->update(['payment_status' => 'paid']);
                        // Refresh booking to get updated status for display
                        $booking->refresh();
                    }
                }
                
                // Add to booking object for view
                $booking->outstanding_balance_tsh = $outstandingBalanceTsh;
                $booking->outstanding_balance_usd = $outstandingBalanceUsd;
                $booking->total_bill_tsh = $totalBillTsh;
                $booking->total_bill_usd = $totalBillTsh / $bookingExchangeRate;
            }
        }

        // Calculate statistics
        $stats = [
            'individual_total' => Booking::where('is_corporate_booking', false)
                ->where(function($q) {
                    $q->where('check_in_status', 'checked_in')
                      ->orWhere(function($q2) {
                          $q2->where('check_in_status', 'checked_out')
                             ->where('payment_status', '!=', 'paid');
                      });
                })
                ->count(),
            'corporate_total' => Booking::where('is_corporate_booking', true)
                ->where(function($q) {
                    $q->where('check_in_status', 'checked_in')
                      ->orWhere(function($q2) {
                          $q2->where('check_in_status', 'checked_out')
                             ->where('payment_status', '!=', 'paid');
                      });
                })
                ->distinct('company_id')
                ->count('company_id'),
        ];

        $role = $this->getRole();
        return view('dashboard.reception-check-out', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'bookings' => $bookings,
            'bookingType' => $bookingType,
            'exchangeRate' => $exchangeRate,
            'filters' => $request->only(['search', 'check_out_date']),
            'stats' => $stats,
        ]);
    }

    /**
     * Show active reservations
     */
    public function activeReservations(Request $request)
    {
        $query = Booking::with(['room', 'serviceRequests.service'])
            ->where('status', 'confirmed')
            ->whereIn('payment_status', ['paid', 'partial'])
            ->where(function($q) {
                // Include paid bookings
                $q->where('payment_status', 'paid')
                  // Or partial payments where amount_paid > 0
                  ->orWhere(function($subQ) {
                      $subQ->where('payment_status', 'partial')
                           ->whereNotNull('amount_paid')
                           ->where('amount_paid', '>', 0);
                  });
            })
            ->where('check_in_status', '!=', 'checked_out');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('check_in', 'asc')->paginate(20);

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-active-reservations', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'bookings' => $bookings,
            'exchangeRate' => $exchangeRate,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show guests list
     */
    public function guests(Request $request)
    {
        $query = Booking::select('guest_name', 'guest_email', 'guest_phone', 'country', 'country_code')
            ->selectRaw('MAX(created_at) as last_booking')
            ->selectRaw('COUNT(*) as total_bookings')
            ->groupBy('guest_email', 'guest_name', 'guest_phone', 'country', 'country_code');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%");
            });
        }

        $guests = $query->orderBy('last_booking', 'desc')->paginate(20);

        $role = $this->getRole();
        return view('dashboard.reception-guests', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'guests' => $guests,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show room status
     */
    public function roomStatus()
    {
        $today = today();
        
        // Load all bookings (confirmed paid/partial, and pending bookings for future dates)
        $rooms = Room::with(['bookings' => function($query) use ($today) {
            $query->with(['serviceRequests.service'])
                  ->where(function($q) use ($today) {
                // Confirmed and paid/partial bookings for current/upcoming dates
                $q->where(function($subQ) use ($today) {
                    $subQ->where('status', 'confirmed')
                          ->whereIn('payment_status', ['paid', 'partial'])
                          ->where(function($paymentQ) {
                              // Include paid bookings
                              $paymentQ->where('payment_status', 'paid')
                                      // Or partial payments where amount_paid > 0
                                      ->orWhere(function($partialQ) {
                                          $partialQ->where('payment_status', 'partial')
                                                   ->whereNotNull('amount_paid')
                                                   ->where('amount_paid', '>', 0);
                                      });
                          })
                          ->where('check_in_status', '!=', 'checked_out')
                          ->whereDate('check_in', '<=', $today)
                          ->whereDate('check_out', '>=', $today);
                })
                // OR pending bookings (waiting for payment) for future dates
                ->orWhere(function($subQ) use ($today) {
                    $subQ->where('status', 'pending')
                          ->where('payment_status', 'pending')
                          ->whereDate('check_in', '>=', $today)
                          ->whereNull('cancelled_at');
                })
                // OR confirmed paid/partial bookings for future dates (upcoming check-ins)
                ->orWhere(function($subQ) use ($today) {
                    $subQ->where('status', 'confirmed')
                          ->whereIn('payment_status', ['paid', 'partial'])
                          ->where(function($paymentQ) {
                              // Include paid bookings
                              $paymentQ->where('payment_status', 'paid')
                                      // Or partial payments where amount_paid > 0
                                      ->orWhere(function($partialQ) {
                                          $partialQ->where('payment_status', 'partial')
                                                   ->whereNotNull('amount_paid')
                                                   ->where('amount_paid', '>', 0);
                                      });
                          })
                          ->where('check_in_status', 'pending')
                          ->whereDate('check_in', '>=', $today);
                });
            });
        }])->orderBy('room_number')->get();

        // Calculate room status
        $rooms = $rooms->map(function($room) use ($today) {
            // Active bookings (checked in AND today is between check-in and check-out dates)
            $activeBookings = $room->bookings->filter(function($booking) use ($today) {
                if ($booking->check_in_status !== 'checked_in') {
                    return false;
                }
                $checkIn = Carbon::parse($booking->check_in);
                $checkOut = Carbon::parse($booking->check_out);
                // Room is occupied only if today is between check-in and check-out dates
                return $today->gte($checkIn) && $today->lte($checkOut);
            });
            $room->is_occupied = $activeBookings->count() > 0;
            $room->current_booking = $activeBookings->first();
            
            // Upcoming bookings (future check-ins or pending payment bookings)
            $upcomingBookings = $room->bookings->filter(function($booking) use ($today) {
                $checkInDate = \Carbon\Carbon::parse($booking->check_in);
                return $checkInDate->gte($today) && 
                       ($booking->check_in_status === 'pending' || 
                        ($booking->status === 'pending' && $booking->payment_status === 'pending'));
            })->sortBy('check_in')->first();
            $room->upcoming_checkin = $upcomingBookings;
            
            // Pending payment booking (for status display) - only show if check-in is within 3 days
            $pendingPaymentBooking = $room->bookings->filter(function($booking) use ($today) {
                $checkInDate = \Carbon\Carbon::parse($booking->check_in);
                $daysUntilCheckIn = $today->diffInDays($checkInDate, false);
                return $booking->status === 'pending' && 
                       $booking->payment_status === 'pending' &&
                       $checkInDate->gte($today) &&
                       $daysUntilCheckIn <= 3 && // Only show if check-in is within 3 days
                       is_null($booking->cancelled_at);
            })->sortBy('check_in')->first();
            $room->pending_payment_booking = $pendingPaymentBooking;
            
            // Check if room has any bookings that affect current availability (today or within next 3 days)
            $room->has_immediate_booking = false;
            if ($room->upcoming_checkin) {
                $checkInDate = \Carbon\Carbon::parse($room->upcoming_checkin->check_in);
                $daysUntilCheckIn = $today->diffInDays($checkInDate, false);
                // Only mark as having immediate booking if check-in is today or within 3 days
                if ($daysUntilCheckIn <= 3) {
                    $room->has_immediate_booking = true;
                }
            }
            
            // Get last checked out booking for rooms that need cleaning
            $room->last_checked_out_booking = $room->bookings()
                ->where('check_in_status', 'checked_out')
                ->orderBy('checked_out_at', 'desc')
                ->first();
            
            return $room;
        });

        // Calculate statistics
        $stats = [
            'total' => $rooms->count(),
            'available' => $rooms->filter(function($room) {
                // Room is available if not occupied and doesn't have immediate bookings (within 3 days)
                return $room->status === 'available' && !$room->is_occupied && !$room->has_immediate_booking;
            })->count(),
            'occupied' => $rooms->filter(function($room) {
                return $room->is_occupied;
            })->count(),
            'needs_cleaning' => $rooms->filter(function($room) {
                return $room->status === 'to_be_cleaned';
            })->count(),
            'maintenance' => $rooms->filter(function($room) {
                return $room->status === 'maintenance';
            })->count(),
            'reserved' => $rooms->filter(function($room) {
                // Reserved if has immediate booking (within 3 days) and not occupied
                return $room->has_immediate_booking && !$room->is_occupied;
            })->count(),
            'waiting_payment' => $rooms->filter(function($room) {
                // Waiting for payment if has immediate booking with pending payment
                return $room->has_immediate_booking && 
                       (($room->pending_payment_booking) || 
                        ($room->upcoming_checkin && $room->upcoming_checkin->payment_status === 'pending'));
            })->count(),
        ];

        // Get rooms with check-out today or overdue
        $roomsWithUrgentCheckout = $rooms->filter(function($room) use ($today) {
            if ($room->current_booking) {
                $checkOutDate = \Carbon\Carbon::parse($room->current_booking->check_out);
                return $checkOutDate->lte($today);
            }
            return false;
        })->pluck('id')->toArray();

        // Get rooms with upcoming check-ins (next 24 hours)
        $tomorrow = \Carbon\Carbon::today()->addDay();
        $roomsWithUpcomingCheckin = $rooms->filter(function($room) use ($today, $tomorrow) {
            if ($room->upcoming_checkin) {
                $checkInDate = \Carbon\Carbon::parse($room->upcoming_checkin->check_in);
                return $checkInDate->between($today, $tomorrow);
            }
            return false;
        })->pluck('id')->toArray();

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-room-status', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'rooms' => $rooms,
            'exchangeRate' => $exchangeRate,
            'stats' => $stats,
            'roomsWithUrgentCheckout' => $roomsWithUrgentCheckout,
            'roomsWithUpcomingCheckin' => $roomsWithUpcomingCheckin,
        ]);
    }

    /**
     * Show rooms that need cleaning
     * Includes: rooms with status 'to_be_cleaned' AND rooms with check-out tomorrow (1 day before)
     */
    public function roomsNeedsCleaning(Request $request)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::today()->addDay();
        
        // Get rooms that need cleaning:
        // 1. Rooms with status 'to_be_cleaned' (already checked out)
        // 2. Rooms with bookings checking out tomorrow (1 day before check-out)
        $query = Room::where(function($q) use ($tomorrow) {
            // Rooms already marked as needing cleaning
            $q->where('status', 'to_be_cleaned')
              // OR rooms with bookings checking out tomorrow
              ->orWhereHas('bookings', function($bookingQuery) use ($tomorrow) {
                  $bookingQuery->where('status', 'confirmed')
                               ->whereIn('payment_status', ['paid', 'partial'])
                               ->where(function($paymentQ) {
                                   $paymentQ->where('payment_status', 'paid')
                                           ->orWhere(function($partialQ) {
                                               $partialQ->where('payment_status', 'partial')
                                                        ->whereNotNull('amount_paid')
                                                        ->where('amount_paid', '>', 0);
                                           });
                               })
                               ->where('check_in_status', 'checked_in')
                               ->whereDate('check_out', $tomorrow);
              });
        })
        ->with(['bookings' => function($q) use ($tomorrow) {
            $q->where(function($bookingQ) use ($tomorrow) {
                // Get checked out bookings (for rooms already cleaned)
                $bookingQ->where('check_in_status', 'checked_out')
                         ->orderBy('checked_out_at', 'desc')
                         ->limit(1);
            })
            ->orWhere(function($tomorrowQ) use ($tomorrow) {
                // Get bookings checking out tomorrow
                $tomorrowQ->where('status', 'confirmed')
                         ->whereIn('payment_status', ['paid', 'partial'])
                         ->where(function($paymentQ) {
                             $paymentQ->where('payment_status', 'paid')
                                     ->orWhere(function($partialQ) {
                                         $partialQ->where('payment_status', 'partial')
                                                  ->whereNotNull('amount_paid')
                                                  ->where('amount_paid', '>', 0);
                                     });
                         })
                         ->where('check_in_status', 'checked_in')
                         ->whereDate('check_out', $tomorrow);
            });
        }])
        ->orderBy('room_number', 'asc');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhere('room_type', 'like', "%{$search}%");
            });
        }

        $rooms = $query->paginate(20);

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-rooms-cleaning', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'rooms' => $rooms,
            'exchangeRate' => $exchangeRate,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Mark room as cleaned (available)
     */
    public function markRoomCleaned(Room $room)
    {
        $room->update(['status' => 'available']);

        return response()->json([
            'success' => true,
            'message' => 'Room marked as cleaned and available for booking.',
            'room' => $room->fresh(),
        ]);
    }

    /**
     * Show checkout payment page
     */
    public function checkoutPayment(Booking $booking)
    {
        // Verify booking is checked out
        if ($booking->check_in_status !== 'checked_out') {
            abort(404, 'Booking not found or not checked out.');
        }

        // Calculate additional charges only (room booking already paid via PayPal)
        // Additional charges include: services, extensions, transportation
        
        $serviceRequests = $booking->serviceRequests()
            ->whereIn('status', ['approved', 'completed'])
            ->with('service')
            ->get();

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

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
        
        // Total additional charges (services + extension + transportation)
        // For corporate bookings with self-paid responsibility, these are the guest's ONLY charges
        // (Room is company-paid)
        if ($booking->is_corporate_booking) {
            if ($booking->payment_responsibility === 'self') {
                $totalAdditionalChargesTsh = $otherServiceChargesTsh + $transportationChargesTsh;
            } else {
                $totalAdditionalChargesTsh = 0; // Guest pays nothing
            }
        } else {
            $totalAdditionalChargesTsh = $otherServiceChargesTsh + $transportationChargesTsh;
        }
        $totalAdditionalChargesUsd = $totalAdditionalChargesTsh / $exchangeRate;

        $role = $this->getRole();
        return view('dashboard.reception-checkout-payment', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
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
     * Process cash payment for outstanding balance
     */
    public function processCashPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,mobile,bank,card',
            'payment_provider' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        // Calculate outstanding balance
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $booking->locked_exchange_rate ?? $currencyService->getUsdToTshRate();
        
        // Check if this is a corporate booking
        $isCorporate = $booking->is_corporate_booking ?? false;
        $paymentResponsibility = $booking->payment_responsibility ?? 'self';
        
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
        
        // Calculate total bill for the whole booking (Room + Services)
        // Note: extensionCostTsh is already included in booking->total_price
        $totalBookingBillTsh = ($booking->total_price * $exchangeRate) + $totalServiceChargesTsh;
        
        // Calculate outstanding balance for THIS SPECIFIC payment action
        if ($isCorporate) {
            if ($paymentResponsibility === 'self') {
                // For a self-paying corporate guest, they only owe for services and extensions.
                // Room is already "paid" or "promised" by company.
                // Their "portion" of the paid amount is anything above the room price.
                $roomPriceTsh = $booking->total_price * $exchangeRate;
                $currentPaidTsh = ($booking->amount_paid ?? 0) * $exchangeRate;
                
                // Debt = (Room + Services + Extensions) - CurrentPaid
                // BUT we only want the guest to see their own debt.
                // If they haven't paid anything, their debt is just (Services + Extensions).
                $guestBillTsh = $totalServiceChargesTsh;
                $guestAlreadyPaidTsh = max(0, $currentPaidTsh - $roomPriceTsh);
                $outstandingBalanceTsh = max(0, $guestBillTsh - $guestAlreadyPaidTsh);
            } else {
                // If company pays everything, guest owes 0 at reception.
                $outstandingBalanceTsh = 0;
            }
        } else {
            // Individual booking - they owe everything
            $amountPaidTsh = ($booking->amount_paid ?? 0) * $exchangeRate;
            $outstandingBalanceTsh = max(0, $totalBookingBillTsh - $amountPaidTsh);
        }
        
        $outstandingBalanceUsd = $outstandingBalanceTsh / $exchangeRate;
        $paymentAmountUsd = (float) $request->amount;
        $paymentAmountTsh = $paymentAmountUsd * $exchangeRate;
        
        // Check if this payment covers the remainder of the guest's debt
        $isGuestPortionCleared = ($paymentAmountTsh >= $outstandingBalanceTsh - 50);
        
        // Update money
        $newAmountPaidUsd = ($booking->amount_paid ?? 0) + $paymentAmountUsd;
        $newAmountPaidTsh = $newAmountPaidUsd * $exchangeRate;
        
        // Calculate remaining balance after this payment
        $remainingBalanceTsh = max(0, $outstandingBalanceTsh - $paymentAmountTsh);
        $remainingBalanceUsd = $remainingBalanceTsh / $exchangeRate;
        
        // Threshold for considering fully paid (50 TZS or $0.05)
        $minOutstandingThresholdUsd = 0.05;
        $minOutstandingThresholdTsh = 50;
        
        // Check if fully paid: remaining balance is below threshold
        $isFullyPaid = ($remainingBalanceTsh < $minOutstandingThresholdTsh) || 
                       ($remainingBalanceUsd < $minOutstandingThresholdUsd);
        
        // For corporate bookings with self-paid responsibility, check guest portion
        if ($isCorporate && $paymentResponsibility === 'self') {
            // Guest portion is cleared if remaining balance is below threshold
            $isGuestPortionCleared = $isFullyPaid;
        }
        
        // Mark services as paid if this payout covers them
        if ($isCorporate && $paymentResponsibility === 'self' && $isGuestPortionCleared) {
            foreach($serviceRequests as $sr) {
                if (($sr->payment_status ?? 'pending') !== 'paid') {
                    $sr->update([
                        'payment_status' => 'paid',
                        'payment_method' => $request->payment_method,
                        'completed_at' => $sr->completed_at ?? now()
                    ]);
                }
            }
        }
        
        // If booking is checked out and fully paid, ensure payment_status is 'paid'
        $finalPaymentStatus = 'partial';
        if ($isFullyPaid) {
            $finalPaymentStatus = 'paid';
        } elseif ($booking->check_in_status === 'checked_out' && $remainingBalanceTsh < $minOutstandingThresholdTsh) {
            // If checked out and balance cleared, mark as paid
            $finalPaymentStatus = 'paid';
            $isFullyPaid = true;
        }
        
        $booking->update([
            'payment_status' => $finalPaymentStatus,
            'payment_method' => $request->payment_method,
            'payment_provider' => $request->payment_provider ?? null,
            'payment_transaction_id' => $request->payment_reference ?? null,
            'amount_paid' => $newAmountPaidUsd,
            'paid_at' => $booking->paid_at ?? now(),
            'total_service_charges_tsh' => $totalServiceChargesTsh,
        ]);

        // Send SMS notification to Guest (Receipt Confirmation)
        if ($booking->guest_phone) {
            try {
                $smsService = app(\App\Services\SmsService::class);
                $smsMessage = "Hi " . ($booking->first_name ?? 'Guest') . ", we have received your payment of Tsh " . number_format($paymentAmountTsh, 0, '.', '') . "/= via " . strtoupper($request->payment_method) . ". Your current total paid is Tsh " . number_format($newAmountPaidTsh, 0, '.', '') . "/=. Thank you!";
                $smsService->sendSms($booking->guest_phone, $smsMessage);
            } catch (\Exception $e) {
                \Log::error("Failed to send payment receipt SMS to guest: " . $e->getMessage());
            }
        }

        // Send SMS to managers
        try {
            $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                ->where('is_active', true)
                ->get();
            
            foreach ($managersAndAdmins as $staff) {
                if ($staff->phone && $staff->isNotificationEnabled('payment')) {
                    try {
                        $smsService = app(\App\Services\SmsService::class);
                        $smsMessage = "POS Payment: " . ($booking->guest_name ?? 'Guest') . " paid Tsh " . number_format($paymentAmountTsh, 0, '.', '') . "/= via " . strtoupper($request->payment_method) . " (Ref: {$booking->booking_reference})";
                        $smsService->sendSms($staff->phone, $smsMessage);
                    } catch (\Exception $e) {
                        \Log::error("Failed to send POS payment SMS to manager: " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send POS payment SMS to managers: ' . $e->getMessage());
        }
        
        // Only deactivate guest account if they have checked out
        // If still checked in, keep account active so they can access dashboard and services
        if ($booking->check_in_status === 'checked_out') {
            $user = \App\Models\Guest::where('email', $booking->guest_email)->first();
            if ($user) {
                $user->update(['is_active' => false]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Payment received successfully. Guest can now check out.',
            'redirect' => route('reception.reservations.check-out'),
        ]);
    }

    /**
     * Process checkout payment
     */
    public function processCheckoutPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|in:paypal,cash',
        ]);

        // Verify booking is checked out
        if ($booking->check_in_status !== 'checked_out') {
            return response()->json([
                'success' => false,
                'message' => 'Booking is not checked out.',
            ], 400);
        }

        // Calculate additional charges only (room booking already paid)
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
        
        // Calculate extension cost
        $extensionCostUsd = 0;
        $extensionCostTsh = 0;
        
        if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
            $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
            $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
            $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
            
            if ($extensionNights > 0 && $booking->room) {
                $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                $extensionCostTsh = $extensionCostUsd * $exchangeRate;
            }
        }
        
        // Calculate transportation charges
        $transportationChargesTsh = 0;
        if ($booking->airport_pickup_required) {
            $airportPickupService = $serviceRequests->firstWhere('service.category', 'transport');
            if ($airportPickupService) {
                $transportationChargesTsh = $airportPickupService->total_price_tsh;
            } else {
                $transportationChargesTsh = 50000; // Default price
            }
        }
        
        // Other service charges (excluding transportation)
        $otherServiceChargesTsh = $serviceRequests
            ->where('service.category', '!=', 'transport')
            ->sum('total_price_tsh');
        
        // Total additional charges
        $totalAdditionalChargesTsh = $otherServiceChargesTsh + $extensionCostTsh + $transportationChargesTsh;
        $totalAdditionalChargesUsd = $totalAdditionalChargesTsh / $exchangeRate;

        if ($request->payment_method === 'cash') {
            // Mark additional charges as paid with cash
            // Handle corporate vs individual
            if ($booking->is_corporate_booking) {
                // Mark services as paid
                foreach($serviceRequests as $sr) {
                    if ($sr->payment_status !== 'paid') {
                        $sr->update([
                            'payment_status' => 'paid',
                            'payment_method' => 'cash',
                            'completed_at' => now()
                        ]);
                    }
                }
                
                $booking->update([
                    'payment_status' => $booking->payment_status === 'paid' ? 'paid' : 'partial',
                    'payment_method' => $booking->payment_method ?? 'cash',
                    'amount_paid' => ($booking->amount_paid ?? 0) + $totalAdditionalChargesUsd,
                    'paid_at' => $booking->paid_at ?? now(),
                    'total_service_charges_tsh' => ($booking->total_service_charges_tsh ?? 0) + $otherServiceChargesTsh + $transportationChargesTsh,
                ]);
            } else {
                // Individual marking
                $booking->update([
                    'payment_status' => 'paid', 
                    'payment_method' => 'cash',
                    'amount_paid' => ($booking->amount_paid ?? 0) + $totalAdditionalChargesUsd,
                    'paid_at' => now(),
                    'total_service_charges_tsh' => ($booking->total_service_charges_tsh ?? 0) + $otherServiceChargesTsh + $transportationChargesTsh,
                ]);
            }

            // Deactivate guest account
            $user = \App\Models\Guest::where('email', $booking->guest_email)->first();
            if ($user) {
                $user->update(['is_active' => false]);
            }

            // Send SMS notification to Guest (Receipt Confirmation)
            if ($booking->guest_phone) {
                try {
                    $smsService = app(\App\Services\SmsService::class);
                    $smsMessage = "Hi " . ($booking->first_name ?? 'Guest') . ", we have received your payment of Tsh " . number_format($totalAdditionalChargesTsh, 0, '.', '') . "/= via " . strtoupper($request->payment_method) . ". Your current total paid is Tsh " . number_format($booking->amount_paid * ($booking->locked_exchange_rate ?? $exchangeRate), 0, '.', '') . "/=. Thank you!";
                    $smsService->sendSms($booking->guest_phone, $smsMessage);
                } catch (\Exception $e) {
                    \Log::error("Failed to send checkout payment SMS to guest: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Additional charges paid successfully. Guest account has been deactivated.',
                'redirect' => route('reception.reservations.check-out'),
            ]);
        } else {
            // This shouldn't happen as we only allow cash now, but handle it
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment method. Please use cash payment.',
            ], 400);
        }
    }

    /**
     * Show payments
     */
    public function payments(Request $request)
    {
        // Include both paid and partial payments
        $query = Booking::with('room')
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->orderByRaw('COALESCE(paid_at, created_at) DESC');

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range (use paid_at if available, otherwise created_at)
        if ($request->has('date_from') && $request->date_from) {
            $query->where(function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    $subQ->whereNotNull('paid_at')
                         ->whereDate('paid_at', '>=', $request->date_from);
                })->orWhere(function($subQ) use ($request) {
                    $subQ->whereNull('paid_at')
                         ->whereDate('created_at', '>=', $request->date_from);
                });
            });
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where(function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    $subQ->whereNotNull('paid_at')
                         ->whereDate('paid_at', '<=', $request->date_to);
                })->orWhere(function($subQ) use ($request) {
                    $subQ->whereNull('paid_at')
                         ->whereDate('created_at', '<=', $request->date_to);
                });
            });
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhere('payment_transaction_id', 'like', "%{$search}%");
            });
        }

        $payments = $query->paginate(20);

        // Statistics (include partial payments)
        $today = Carbon::today();
        $stats = [
            'total_paid' => Booking::whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->sum('amount_paid'),
            'total_paid_today' => Booking::whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->where(function($q) use ($today) {
                    $q->where(function($subQ) use ($today) {
                        $subQ->whereNotNull('paid_at')
                             ->whereDate('paid_at', $today);
                    })->orWhere(function($subQ) use ($today) {
                        $subQ->whereNull('paid_at')
                             ->whereDate('created_at', $today);
                    });
                })
                ->sum('amount_paid'),
            'total_payments_today' => Booking::whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->where(function($q) use ($today) {
                    $q->where(function($subQ) use ($today) {
                        $subQ->whereNotNull('paid_at')
                             ->whereDate('paid_at', $today);
                    })->orWhere(function($subQ) use ($today) {
                        $subQ->whereNull('paid_at')
                             ->whereDate('created_at', $today);
                    });
                })
                ->count(),
        ];

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-payments', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'payments' => $payments,
            'exchangeRate' => $exchangeRate,
            'stats' => $stats,
            'filters' => $request->only(['payment_status', 'date_from', 'date_to', 'search']),
        ]);
    }

    /**
     * Show daily reports
     */
    /**
     * Show extension requests page
     */
    public function extensionRequests(Request $request)
    {
        $query = Booking::with('room')
            ->whereNotNull('extension_status')
            ->orderBy('extension_requested_at', 'desc');

        // Filter by extension status
        if ($request->has('status') && $request->status) {
            $query->where('extension_status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        $extensions = $query->paginate(20);

        // Statistics
        $stats = [
            'pending' => Booking::where('extension_status', 'pending')->count(),
            'approved' => Booking::where('extension_status', 'approved')->count(),
            'rejected' => Booking::where('extension_status', 'rejected')->count(),
            'total' => Booking::whereNotNull('extension_status')->count(),
        ];

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        $role = $this->getRole();
        return view('dashboard.reception-extension-requests', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'extensions' => $extensions,
            'exchangeRate' => $exchangeRate,
            'stats' => $stats,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function reports(Request $request)
    {
        $reportType = $request->get('report_type', 'daily');
        $reportDate = $request->get('date', today()->format('Y-m-d'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $download = $request->get('download');
        
        // Calculate date range based on report type
        $dateRange = $this->calculateDateRange($reportType, $reportDate, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];
        
        // Get bookings statistics for the period
        $bookings = [
            'checked_in' => Booking::whereBetween('checked_in_at', [$start, $end])->count(),
            'checked_out' => Booking::whereBetween('checked_out_at', [$start, $end])->count(),
            'new_bookings' => Booking::whereBetween('created_at', [$start, $end])->count(),
            'confirmed' => Booking::whereBetween('created_at', [$start, $end])
                ->where('status', 'confirmed')
                ->count(),
        ];

        // Get payments statistics (include both paid and partial payments)
        $paymentsQuery = Booking::whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->where(function($q) use ($start, $end) {
                // Use paid_at if available, otherwise created_at
                $q->where(function($subQ) use ($start, $end) {
                    $subQ->whereNotNull('paid_at')
                         ->whereDate('paid_at', '>=', $start->format('Y-m-d'))
                         ->whereDate('paid_at', '<=', $end->format('Y-m-d'));
                })->orWhere(function($subQ) use ($start, $end) {
                    $subQ->whereNull('paid_at')
                         ->whereDate('created_at', '>=', $start->format('Y-m-d'))
                         ->whereDate('created_at', '<=', $end->format('Y-m-d'));
                });
            });
        
        $payments = [
            'total_revenue' => $paymentsQuery->get()->sum(function($booking) {
                return $booking->amount_paid ?? 0;
            }),
            'total_count' => $paymentsQuery->count(),
        ];

        // Get service requests statistics
        $serviceRequests = [
            'total' => \App\Models\ServiceRequest::whereBetween('requested_at', [$start, $end])->count(),
            'pending' => \App\Models\ServiceRequest::whereBetween('requested_at', [$start, $end])
                ->where('status', 'pending')
                ->count(),
            'approved' => \App\Models\ServiceRequest::whereBetween('requested_at', [$start, $end])
                ->where('status', 'approved')
                ->count(),
            'completed' => \App\Models\ServiceRequest::whereBetween('completed_at', [$start, $end])
                ->where('status', 'completed')
                ->count(),
            'revenue' => \App\Models\ServiceRequest::whereBetween('completed_at', [$start, $end])
                ->where('status', 'completed')
                ->sum('total_price_tsh'),
        ];

        // Get recent bookings for the period
        $recentBookings = Booking::with('room')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        // Handle download request
        if ($download) {
            return $this->downloadReport($dateRange, $bookings, $payments, $serviceRequests, $recentBookings, $exchangeRate);
        }

        $role = $this->getRole();
        return view('dashboard.reception-reports', [
            'role' => $role,
            'userName' => auth()->user()->name ?? ($role === 'manager' ? 'Manager' : 'Reception Staff'),
            'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
            'reportType' => $reportType,
            'reportDate' => $reportDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateRange' => $dateRange,
            'bookings' => $bookings,
            'payments' => $payments,
            'serviceRequests' => $serviceRequests,
            'recentBookings' => $recentBookings,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Download report as HTML (with checkout-bill layout)
     */
    private function downloadReport($dateRange, $bookings, $payments, $serviceRequests, $recentBookings, $exchangeRate)
    {
        $filename = 'report_' . str_replace(' ', '_', strtolower($dateRange['label'])) . '_' . date('Y-m-d_His') . '.html';
        
        $html = view('dashboard.report-download', [
            'dateRange' => $dateRange,
            'bookings' => $bookings,
            'payments' => $payments,
            'serviceRequests' => $serviceRequests,
            'recentBookings' => $recentBookings,
            'exchangeRate' => $exchangeRate,
        ])->render();

        return response($html, 200)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    /**
     * Calculate date range based on report type
     */
    private function calculateDateRange($reportType, $reportDate = null, $startDate = null, $endDate = null)
    {
        $today = Carbon::today();
        
        switch ($reportType) {
            case 'daily':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfDay(),
                    'end' => $date->copy()->endOfDay(),
                    'label' => $date->format('F d, Y')
                ];
                
            case 'weekly':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfWeek(),
                    'end' => $date->copy()->endOfWeek(),
                    'label' => $date->copy()->startOfWeek()->format('M d') . ' - ' . $date->copy()->endOfWeek()->format('M d, Y')
                ];
                
            case 'monthly':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfMonth(),
                    'end' => $date->copy()->endOfMonth(),
                    'label' => $date->format('F Y')
                ];
                
            case 'yearly':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfYear(),
                    'end' => $date->copy()->endOfYear(),
                    'label' => $date->format('Y')
                ];
                
            case 'custom':
                if ($startDate && $endDate) {
                    $start = Carbon::parse($startDate)->startOfDay();
                    $end = Carbon::parse($endDate)->endOfDay();
                    return [
                        'start' => $start,
                        'end' => $end,
                        'label' => $start->format('M d, Y') . ' - ' . $end->format('M d, Y')
                    ];
                }
                // Fallback to today if custom dates not provided
                return [
                    'start' => $today->copy()->startOfDay(),
                    'end' => $today->copy()->endOfDay(),
                    'label' => $today->format('F d, Y')
                ];
                
            default:
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfDay(),
                    'end' => $date->copy()->endOfDay(),
                    'label' => $date->format('F d, Y')
                ];
        }
    }

    /**
     * Check out all guests from a company group
     */
    public function checkoutCompanyGroup($companyId)
    {
        $company = \App\Models\Company::findOrFail($companyId);
        
        // Get all checked-in bookings for this company
        $bookings = \App\Models\Booking::where('company_id', $companyId)
            ->where('is_corporate_booking', true)
            ->where('check_in_status', 'checked_in')
            ->get();
        
        if ($bookings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No checked-in bookings found for this company.',
            ], 400);
        }
        
        // Check if all bookings have outstanding balance < 50 TZS
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        $hasOutstanding = false;
        foreach ($bookings as $booking) {
            $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
            
            $serviceRequests = $booking->serviceRequests()
                ->whereIn('status', ['approved', 'completed'])
                ->get();
            
            $totalServiceChargesTsh = $serviceRequests->sum('total_price_tsh');
            
            // Check payment responsibility - if self-paid, exclude service charges from company bill
            $paymentResponsibility = $booking->payment_responsibility ?? 'company';
            $companyResponsibleServiceChargesTsh = 0;
            
            if ($paymentResponsibility === 'self') {
                // Guest pays for services - exclude from company bill
                $companyResponsibleServiceChargesTsh = 0;
            } else {
                // Company pays for services
                $companyResponsibleServiceChargesTsh = $totalServiceChargesTsh;
            }
            
            $extensionCostUsd = 0;
            if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                if ($extensionNights > 0 && $booking->room) {
                    $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                }
            }
            $extensionCostTsh = $extensionCostUsd * $bookingExchangeRate;
            
            // Company's total bill (only what company is responsible for)
            // Note: extensionCostTsh is already included in booking->total_price
            $companyBillTsh = ($booking->total_price * $bookingExchangeRate) + $companyResponsibleServiceChargesTsh;
            $amountPaidTsh = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
            $outstandingBalanceTsh = max(0, $companyBillTsh - $amountPaidTsh);
            
            // Treat very small amounts as fully paid
            if ($outstandingBalanceTsh >= 50) {
                $hasOutstanding = true;
                break;
            }
        }
        
        if ($hasOutstanding) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot check out. Some bookings have outstanding balance. Please process payments first.',
            ], 400);
        }
        
        // Check out all bookings
        foreach ($bookings as $booking) {
            $booking->update([
                'check_in_status' => 'checked_out',
                'checked_out_at' => now(),
                'status' => 'completed',
            ]);

            // Mark room as needing cleaning
            if ($booking->room) {
                $booking->room->update(['status' => 'to_be_cleaned']);
                
                // Create cleaning log entry
                \App\Models\RoomCleaningLog::create([
                    'room_id' => $booking->room->id,
                    'status' => 'needs_cleaning',
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'All guests from ' . $company->name . ' have been checked out successfully!',
            'redirect' => route('reception.reservations.check-out', ['type' => 'corporate']),
        ]);
    }

    /**
     * Process payment for all bookings in a company group
     */
    public function processCompanyPayment(Request $request, $companyId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,mobile,bank,card',
            'payment_provider' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
        ]);
        
        $company = \App\Models\Company::findOrFail($companyId);
        
        // Get all checked-in bookings for this company
        $bookings = \App\Models\Booking::where('company_id', $companyId)
            ->where('is_corporate_booking', true)
            ->where(function($q) {
                $q->where('check_in_status', 'checked_in')
                  ->orWhere(function($q2) {
                      $q2->where('check_in_status', 'checked_out')
                         ->where('payment_status', '!=', 'paid');
                  });
            })
            ->get();
        
        if ($bookings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No bookings found for this company.',
            ], 400);
        }
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        // Calculate total outstanding balance
        $companyBookingsData = [];
        $totalCompanyOutstandingUsd = 0;
        
        foreach ($bookings as $booking) {
            $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
            $serviceRequests = $booking->serviceRequests()->whereIn('status', ['approved', 'completed'])->get();
            $paymentResponsibility = $booking->payment_responsibility ?? 'company';
            $companyServiceChargesTsh = ($paymentResponsibility === 'self') ? 0 : $serviceRequests->sum('total_price_tsh');
            
            $extensionCostUsd = 0;
            if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                $nights = \Carbon\Carbon::parse($booking->original_check_out)->diffInDays($booking->extension_requested_to);
                if ($nights > 0 && $booking->room) $extensionCostUsd = $booking->room->price_per_night * $nights;
            }
            
            $companyBillTsh = ($booking->total_price * $bookingExchangeRate) + $companyServiceChargesTsh;
            $totalPaidTsh = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
            
            // Calculate company's contribution using the same capping logic as in the view
            $companyPaidTsh = $totalPaidTsh;
            if ($paymentResponsibility === 'self') {
                $roomPriceTsh = $booking->total_price * $bookingExchangeRate;
                if ($totalPaidTsh > $roomPriceTsh) {
                    // Cap the company's "paid" portion at the room price, 
                    // assuming surplus payments were for guest services
                    $companyPaidTsh = $roomPriceTsh;
                }
            }
            
            $outstandingTsh = max(0, $companyBillTsh - $companyPaidTsh);
            $outstandingUsd = $outstandingTsh / $bookingExchangeRate;
            
            if ($outstandingTsh > 50) {
                $companyBookingsData[] = [
                    'booking' => $booking,
                    'outstanding_usd' => $outstandingUsd,
                    'total_service_charges_tsh' => $serviceRequests->sum('total_price_tsh'),
                    'responsibility' => $paymentResponsibility,
                    'bookingExchangeRate' => $bookingExchangeRate
                ];
                $totalCompanyOutstandingUsd += $outstandingUsd;
            }
        }
        
        $paymentAmountUsd = (float) $request->amount;
        
        // Relaxed validation: Allow any payment up to the total outstanding (allowing partial payments)
        if ($paymentAmountUsd > $totalCompanyOutstandingUsd + 0.1) {
             return response()->json([
                'success' => false,
                'message' => 'Payment amount ($' . number_format($paymentAmountUsd, 2) . ') exceed company outstanding balance ($'.number_format($totalCompanyOutstandingUsd, 2).').',
            ], 400);
        }
        
        // Process payments
        $remainingPaymentUsd = $paymentAmountUsd;
        foreach ($companyBookingsData as $data) {
            if ($remainingPaymentUsd <= 0) break;

            $booking = $data['booking'];
            $bookingExchangeRate = $data['bookingExchangeRate'];
            
            // Pay as much as possible for this booking
            $payForThisBookingUsd = min($remainingPaymentUsd, $data['outstanding_usd']);
            $remainingPaymentUsd -= $payForThisBookingUsd;
            
            // Increment amount_paid
            $newAmountPaidUsd = ($booking->amount_paid ?? 0) + $payForThisBookingUsd;
            
            // Check if fully paid (including services if company-responsible)
            $totalServiceChargesTsh = $data['total_service_charges_tsh'];
            $totalBillTsh = ($booking->total_price * $bookingExchangeRate) + ($data['responsibility'] === 'company' ? $totalServiceChargesTsh : 0);
            
            // For 'self' responsibility, fully paid means room is paid
            if ($data['responsibility'] === 'self') {
                $totalBillTsh = ($booking->total_price * $bookingExchangeRate);
            }

            $isFullyPaid = ( ($newAmountPaidUsd * $bookingExchangeRate) >= ($totalBillTsh - 50) );
            
            $booking->update([
                'payment_status' => $isFullyPaid ? 'paid' : 'partial',
                'payment_method' => $request->payment_method,
                'payment_provider' => $request->payment_provider ?? null,
                'payment_transaction_id' => $request->payment_reference ?? null,
                'amount_paid' => $newAmountPaidUsd,
                'paid_at' => $booking->paid_at ?? now(),
                'total_service_charges_tsh' => $totalServiceChargesTsh,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Company bill paid successfully!',
        ]);
    }

    /**
     * Generate a consolidated bill for a company group
     */
    public function companyGroupBill($companyId)
    {
        try {
            $company = \App\Models\Company::findOrFail($companyId);
            
            // Get all confirmed bookings for this company that are checked in or recently checked out
            $bookings = Booking::with(['room', 'serviceRequests.service'])
                ->where('company_id', $companyId)
                ->where('is_corporate_booking', true)
                ->where('status', 'confirmed')
                ->where(function($q) {
                    $q->where('check_in_status', 'checked_in')
                      ->orWhere('check_in_status', 'checked_out');
                })
                ->orderBy('check_in', 'asc')
                ->get();

            if ($bookings->isEmpty()) {
                return redirect()->back()->with('error', 'No active bookings found for this company.');
            }

            $currencyService = new CurrencyExchangeService();
            $exchangeRate = $currencyService->getUsdToTshRate();
            
            $groupData = [
                'company' => $company,
                'bookings' => [],
                'totals' => [
                    'room_price_usd' => 0,
                    'extension_cost_usd' => 0,
                    'service_charges_tsh' => 0,
                    'total_bill_tsh' => 0,
                    'amount_paid_tsh' => 0,
                    'outstanding_tsh' => 0,
                ]
            ];

            foreach ($bookings as $booking) {
                $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
                
                // Calculate service charges
                $serviceRequests = $booking->serviceRequests()
                    ->whereIn('status', ['approved', 'completed'])
                    ->get();
                
                // Check payment responsibility
                $paymentResponsibility = $booking->payment_responsibility ?? 'company';
                
                $companyResponsibleServiceTsh = ($paymentResponsibility === 'company') 
                    ? $serviceRequests->sum('total_price_tsh') 
                    : 0;
                
                $guestResponsibleServiceTsh = ($paymentResponsibility === 'self') 
                    ? $serviceRequests->sum('total_price_tsh') 
                    : 0;

                // Extension cost
                $extensionCostUsd = 0;
                if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                    $originalCheckOut = Carbon::parse($booking->original_check_out);
                    $requestedCheckOut = Carbon::parse($booking->extension_requested_to);
                    $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                    if ($extensionNights > 0 && $booking->room) {
                        $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                    }
                }
                $extensionCostTsh = $extensionCostUsd * $bookingExchangeRate;
                
                // Total room bill (including extensions)
                $roomBillUsd = $booking->total_price; 
                $roomBillTsh = $roomBillUsd * $bookingExchangeRate;

                // Company's bill for THIS booking
                $companyBookingBillTsh = $roomBillTsh + $companyResponsibleServiceTsh;
                
                $totalPaidTsh = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
                
                $companyBookingPaidTsh = $totalPaidTsh;
                if ($paymentResponsibility === 'self' && $totalPaidTsh > $roomBillTsh) {
                    $companyBookingPaidTsh = $roomBillTsh;
                }

                $companyBookingOutstandingTsh = max(0, $companyBookingBillTsh - $companyBookingPaidTsh);

                // Threshold handling (Hide rounding errors under 50 TZS)
                if ($companyBookingOutstandingTsh < 50) {
                    $companyBookingOutstandingTsh = 0;
                    $companyBookingPaidTsh = $companyBookingBillTsh; // Adjust display to show full payment
                }

                // Add to group list
                $groupData['bookings'][] = [
                    'booking' => $booking,
                    'room_bill_usd' => $roomBillUsd,
                    'room_bill_tsh' => $roomBillTsh,
                    'service_charges_tsh' => $companyResponsibleServiceTsh,
                    'guest_charges_tsh' => $guestResponsibleServiceTsh,
                    'total_bill_tsh' => $companyBookingBillTsh,
                    'amount_paid_tsh' => $companyBookingPaidTsh,
                    'outstanding_tsh' => $companyBookingOutstandingTsh,
                ];

                // Update totals
                $groupData['totals']['room_price_usd'] += $roomBillUsd;
                $groupData['totals']['service_charges_tsh'] += $companyResponsibleServiceTsh;
                $groupData['totals']['total_bill_tsh'] += $companyBookingBillTsh;
                $groupData['totals']['amount_paid_tsh'] += $companyBookingPaidTsh;
                $groupData['totals']['outstanding_tsh'] += $companyBookingOutstandingTsh;
            }

            $role = $this->getRole();
            return view('dashboard.company-group-bill', [
                'role' => $role,
                'userName' => auth()->user()->name ?? 'Staff',
                'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
                'groupData' => $groupData,
                'exchangeRate' => $exchangeRate,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating company group bill: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate bill: ' . $e->getMessage());
        }
    }
}


