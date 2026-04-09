<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\StoreAnnouncementController;
use App\Http\Controllers\EmergencyAuthController;

// Default landing page redirects to login
// Default landing page shows login directly
Route::get('/', [EmergencyAuthController::class, 'showUnifiedLogin'])->name('index');

// Middleware-free baseline test
Route::get('/ping', function () {
    return "PONG v1.0 - Laravel is alive at " . date('Y-m-d H:i:s');
});

// Debug Route to check session persistence
Route::get('/debug-session', function (Request $request) {
    return [
        'debug_version' => 'v3.5',
        'session_id' => $request->session()->getId(),
        'is_logged_in_staff' => auth()->guard('staff')->check(),
        'is_logged_in_guest' => auth()->guard('guest')->check(),
        'user_id' => auth()->guard('staff')->id() ?? auth()->guard('guest')->id(),
        'session_driver' => config('session.driver'),
        'session_secure' => config('session.secure'),
        'session_all' => $request->session()->all(),
    ];
});

// Landing Pages Routes - DISABLED
// Route::get('/home', function () {
//     return view('landing_page_views.index');
// });

// Route::get('/about-us', function () {
//     return view('landing_page_views.about-us');
// });

// Route::get('/services', function () {
//     return view('landing_page_views.services');
// });

// Route::get('/rooms', function () {
//     return view('landing_page_views.rooms');
// });

// Route::get('/gallery', function () {
//     // ... gallery logic ...
// })->name('gallery.index');

// Route::get('/contact', function () {
//     return view('landing_page_views.contact');
// });

// Newsletter Subscription Route
Route::post('/newsletter/subscribe', [\App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Route::get('/elements', function () {
//     return view('landing_page_views.elements');
// });
// Booking Routes
Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
Route::post('/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.check-availability');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

// Guest Check-in Routes
Route::get('/check-in', [BookingController::class, 'showCheckIn'])->name('check-in.index');
Route::post('/check-in/find', [BookingController::class, 'findBookingForCheckIn'])->name('check-in.find');
Route::post('/check-in/{booking}', [BookingController::class, 'guestCheckIn'])->name('check-in.submit');

// Public Booking Details Route (for QR code scanning)
Route::get('/booking/{booking}/details', [BookingController::class, 'publicBookingDetails'])->name('booking.public.details');

// Payment Routes
use App\Http\Controllers\PaymentController;

Route::get('/payment/create', [PaymentController::class, 'createPayment'])->name('payment.create');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::get('/payment/confirmation/{booking}', [PaymentController::class, 'confirmation'])->name('payment.confirmation');
Route::get('/payment/receipt/{booking}/download', [PaymentController::class, 'downloadReceipt'])->name('payment.receipt.download');

// Exchange Rate Routes (accessible to all authenticated users)
Route::get('/exchange-rates', [\App\Http\Controllers\ExchangeRateController::class, 'index'])->name('exchange-rates')->middleware('check.auth');

// Notification Routes (accessible to all authenticated users)
Route::middleware(['check.auth'])->group(function () {
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/actionable', [\App\Http\Controllers\NotificationController::class, 'getActionable'])->name('notifications.actionable');
});

// Unified Login Routes (for all users)
Route::get('/login', [EmergencyAuthController::class, 'showUnifiedLogin'])->name('login');
Route::post('/login', [EmergencyAuthController::class, 'loginUnified'])->name('login.post');
// OTP Routes (DISABLED - Direct login enabled)
// Route::get('/login/resend-otp', [AuthController::class, 'resendOtp'])->name('login.resend-otp');
// Route::post('/login/verify-otp', [AuthController::class, 'verifyOtp'])->name('login.verify');

// Password Reset Routes
Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'forgotPassword'])->name('password.forgot');

// Manager Dashboard Routes (previously Admin)
Route::prefix('manager')->group(function () {
    // Unified Logout Route for all admin/manager dashboard users
    Route::post('/logout', [App\Http\Controllers\EmergencyAuthController::class, 'logout'])->name('admin.logout');

    // Login routes redirect to unified login
    Route::get('/login', function () {
        return redirect()->route('login');
    })->name('admin.login');
    Route::post('/login', function () {
        return redirect()->route('login');
    })->name('admin.login.post');

    // Protected routes (require authentication)
    // Use 'check.auth' instead of 'auth' to support custom guards (staff/guest)
    Route::middleware(['check.auth', 'role:manager,super_admin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Users Management (Sensitive)
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    });

    // Shared management routes (accessible by manager and reception)
    Route::middleware(['check.auth', 'role:manager,reception,super_admin,accountant,storekeeper'])->group(function () {
        // Payments (Common access)
        Route::get('/payments', [\App\Http\Controllers\AdminController::class, 'payments'])->name('admin.payments');
        Route::get('/payments/reports', [\App\Http\Controllers\AdminController::class, 'paymentReports'])->name('admin.payments.reports');

        // Reports
        Route::get('/reports', [\App\Http\Controllers\AdminController::class, 'reports'])->name('admin.reports');
        Route::get('/reports/index', [\App\Http\Controllers\ReportController::class, 'index'])->name('admin.reports.index');
        // General Hotel Reports
        Route::get('/reports/revenue-breakdown', [\App\Http\Controllers\ReportController::class, 'revenueBreakdown'])->name('admin.reports.revenue-breakdown');
        Route::get('/reports/profitability', [\App\Http\Controllers\ReportController::class, 'profitability'])->name('admin.reports.profitability');
        Route::get('/reports/cash-flow', [\App\Http\Controllers\ReportController::class, 'cashFlow'])->name('admin.reports.cash-flow');
        Route::get('/reports/revenue-forecast', [\App\Http\Controllers\ReportController::class, 'revenueForecast'])->name('admin.reports.revenue-forecast');
        Route::get('/reports/guest-satisfaction', [\App\Http\Controllers\ReportController::class, 'guestSatisfaction'])->name('admin.reports.guest-satisfaction');
        Route::get('/reports/daily-operations', [\App\Http\Controllers\ReportController::class, 'dailyOperations'])->name('admin.reports.daily-operations');
        Route::get('/reports/weekly-performance', [\App\Http\Controllers\ReportController::class, 'weeklyPerformance'])->name('admin.reports.weekly-performance');
        // Booking-Specific Reports
        Route::get('/reports/bookings/room-occupancy', [\App\Http\Controllers\ReportController::class, 'roomOccupancy'])->name('admin.reports.bookings.room-occupancy');
        Route::get('/reports/bookings/performance', [\App\Http\Controllers\ReportController::class, 'bookingPerformance'])->name('admin.reports.bookings.performance');
        // General Report
        Route::get('/reports/general', [\App\Http\Controllers\ReportController::class, 'general'])->name('admin.reports.general');
        // Other Reports
        Route::prefix('reports/other')->name('admin.reports.other.')->group(function () {
            Route::get('/payment-methods', [\App\Http\Controllers\ReportController::class, 'paymentMethods'])->name('payment-methods');
            Route::get('/satisfaction', [\App\Http\Controllers\ReportController::class, 'satisfaction'])->name('satisfaction');
            Route::get('/period-comparison', [\App\Http\Controllers\ReportController::class, 'periodComparison'])->name('period-comparison');
            Route::get('/role-performance', [\App\Http\Controllers\ReportController::class, 'rolePerformance'])->name('role-performance');
            Route::get('/staff-activity', [\App\Http\Controllers\ReportController::class, 'staffActivity'])->name('staff-activity');
            Route::get('/staff-productivity', [\App\Http\Controllers\ReportController::class, 'staffProductivity'])->name('staff-productivity');
            Route::get('/issue-resolution', [\App\Http\Controllers\ReportController::class, 'issueResolution'])->name('issue-resolution');
            Route::get('/service-response-time', [\App\Http\Controllers\ReportController::class, 'serviceResponseTime'])->name('service-response-time');
            Route::get('/stock-valuation', [\App\Http\Controllers\ReportController::class, 'stockValuation'])->name('stock-valuation');
            Route::get('/food-cost-analysis', [\App\Http\Controllers\ReportController::class, 'foodCostAnalysis'])->name('food-cost-analysis');
            Route::get('/bar-sales-analysis', [\App\Http\Controllers\ReportController::class, 'barSalesAnalysis'])->name('bar-sales-analysis');
            Route::get('/menu-performance', [\App\Http\Controllers\ReportController::class, 'menuPerformance'])->name('menu-performance');
            Route::get('/guest-demographics', [\App\Http\Controllers\ReportController::class, 'guestDemographics'])->name('guest-demographics');
        });

        Route::get('/rooms', [RoomController::class, 'index'])->name('admin.rooms.index');
        Route::get('/rooms/create', [RoomController::class, 'create'])->name('admin.rooms.create');
        Route::post('/rooms', [RoomController::class, 'store'])->name('admin.rooms.store');
        Route::post('/rooms/bulk-action', [RoomController::class, 'bulkAction'])->name('admin.rooms.bulk-action');
        Route::get('/rooms/bulk-edit', [RoomController::class, 'bulkEdit'])->name('admin.rooms.bulk-edit');
        Route::post('/rooms/bulk-update', [RoomController::class, 'bulkUpdate'])->name('admin.rooms.bulk-update');
        Route::post('/rooms/change-type', [RoomController::class, 'changeType'])->name('admin.rooms.change-type');
        // Room Status and Cleaning (must come before /rooms/{room} to avoid route conflicts)
        Route::get('/rooms/status', [\App\Http\Controllers\ReceptionController::class, 'roomStatus'])->name('admin.rooms.status');
        Route::get('/rooms/cleaning', [\App\Http\Controllers\ReceptionController::class, 'roomsNeedsCleaning'])->name('admin.rooms.cleaning');
        Route::post('/rooms/{room}/mark-cleaned', [\App\Http\Controllers\ReceptionController::class, 'markRoomCleaned'])->name('admin.rooms.mark-cleaned');
        Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('admin.rooms.show');
        Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('admin.rooms.edit');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('admin.rooms.update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('admin.rooms.destroy');

        // Room Issues management
        Route::get('/room-issues', [\App\Http\Controllers\HousekeeperController::class, 'roomIssues'])->name('admin.rooms.issues');
        Route::post('/room-issues/{issue}/update-status', [\App\Http\Controllers\HousekeeperController::class, 'updateIssueStatus'])->name('admin.rooms.issues.update-status');

        // Bookings Routes
        Route::get('/bookings', [BookingController::class, 'adminIndex'])->name('admin.bookings.index');
        Route::get('/bookings/calendar', [BookingController::class, 'adminCalendar'])->name('admin.bookings.calendar');
        Route::post('/bookings/{booking}/send-reminder', [BookingController::class, 'sendReminder'])->name('admin.bookings.send-reminder');
        Route::get('/bookings/expired', [BookingController::class, 'adminIndex'])->name('admin.bookings.expired');

        // Day Services Routes
        Route::get('/day-services', [\App\Http\Controllers\DayServiceController::class, 'index'])->name('admin.day-services.index');
        Route::get('/day-services/swimming', [\App\Http\Controllers\DayServiceController::class, 'swimmingService'])->name('admin.day-services.swimming');
        Route::get('/day-services/ceremony', [\App\Http\Controllers\DayServiceController::class, 'ceremonyService'])->name('admin.day-services.ceremony');
        Route::get('/day-services/parking', [\App\Http\Controllers\DayServiceController::class, 'parkingService'])->name('admin.day-services.parking');
        Route::get('/day-services/garden', [\App\Http\Controllers\DayServiceController::class, 'gardenService'])->name('admin.day-services.garden');
        Route::get('/day-services/conference', [\App\Http\Controllers\DayServiceController::class, 'conferenceRoomService'])->name('admin.day-services.conference');
        Route::get('/day-services/projector', [\App\Http\Controllers\DayServiceController::class, 'projectorService'])->name('admin.day-services.projector');
        Route::get('/day-services/music', [\App\Http\Controllers\DayServiceController::class, 'musicService'])->name('admin.day-services.music');
        Route::get('/day-services/pending', [\App\Http\Controllers\DayServiceController::class, 'pending'])->name('admin.day-services.pending');
        Route::get('/day-services/reports', [\App\Http\Controllers\DayServiceController::class, 'reports'])->name('admin.day-services.reports');
        Route::get('/day-services/reports/download', [\App\Http\Controllers\DayServiceController::class, 'downloadReport'])->name('admin.day-services.reports.download');
        Route::post('/day-services', [\App\Http\Controllers\DayServiceController::class, 'store'])->name('admin.day-services.store');
        Route::get('/day-services/{dayService}', [\App\Http\Controllers\DayServiceController::class, 'show'])->name('admin.day-services.show');
        Route::post('/day-services/{dayService}/payment', [\App\Http\Controllers\DayServiceController::class, 'processPayment'])->name('admin.day-services.payment');
        Route::get('/day-services/{dayService}/receipt', [\App\Http\Controllers\DayServiceController::class, 'downloadReceipt'])->name('admin.day-services.receipt');
        Route::get('/day-services/{dayService}/docket', [\App\Http\Controllers\DayServiceController::class, 'docket'])->name('admin.day-services.docket');
        Route::post('/day-services/{dayService}/add-items', [\App\Http\Controllers\DayServiceController::class, 'addItems'])->name('admin.day-services.add-items');
        Route::put('/day-services/{dayService}/update-items', [\App\Http\Controllers\DayServiceController::class, 'updateItems'])->name('admin.day-services.update-items');

        // Service Catalog Routes
        Route::get('/service-catalog', [\App\Http\Controllers\ServiceCatalogController::class, 'index'])->name('admin.service-catalog.index');
        Route::get('/service-catalog/create', [\App\Http\Controllers\ServiceCatalogController::class, 'create'])->name('admin.service-catalog.create');
        Route::post('/service-catalog', [\App\Http\Controllers\ServiceCatalogController::class, 'store'])->name('admin.service-catalog.store');
        Route::get('/service-catalog/{serviceCatalog}/edit', [\App\Http\Controllers\ServiceCatalogController::class, 'edit'])->name('admin.service-catalog.edit');
        Route::put('/service-catalog/{serviceCatalog}', [\App\Http\Controllers\ServiceCatalogController::class, 'update'])->name('admin.service-catalog.update');
        Route::delete('/service-catalog/{serviceCatalog}', [\App\Http\Controllers\ServiceCatalogController::class, 'destroy'])->name('admin.service-catalog.destroy');
        Route::get('/service-catalog/active/list', [\App\Http\Controllers\ServiceCatalogController::class, 'getActiveServices'])->name('admin.service-catalog.active');
    }); // end of shared manager/reception middleware group

    // Shared management routes (accessible by manager and reception) - continued
    Route::middleware(['check.auth', 'role:manager,reception,super_admin,accountant,storekeeper'])->group(function () {
        // Room Issues management
        Route::get('/room-issues', [\App\Http\Controllers\HousekeeperController::class, 'roomIssues'])->name('admin.rooms.issues');
        Route::post('/room-issues/{issue}/update-status', [\App\Http\Controllers\HousekeeperController::class, 'updateIssueStatus'])->name('admin.rooms.issues.update-status');

        // Calendar View
        Route::get('/bookings/calendar', [BookingController::class, 'adminCalendar'])->name('admin.bookings.calendar');
    });
}); // end of manager prefix group

// Kitchen & Food Management (Accessible by Manager & Head Chef)
// This group is at the top level (no /manager prefix)
Route::prefix('restaurant/food')->middleware(['check.auth', 'role:manager,head_chef,super_admin,storekeeper,accountant'])->group(function () {
    Route::get('/orders', [\App\Http\Controllers\KitchenOrderController::class, 'index'])->name('admin.restaurants.kitchen.orders');
    Route::get('/orders/history', [\App\Http\Controllers\KitchenOrderController::class, 'history'])->name('admin.restaurants.kitchen.orders.history');
    Route::post('/orders/{serviceRequest}/preparing', [\App\Http\Controllers\KitchenOrderController::class, 'startPreparation'])->name('admin.restaurants.kitchen.orders.preparing');
    Route::post('/orders/{serviceRequest}/complete', [\App\Http\Controllers\KitchenOrderController::class, 'complete'])->name('admin.restaurants.kitchen.orders.complete');
    Route::get('/orders/{serviceRequest}/print-docket', [\App\Http\Controllers\KitchenOrderController::class, 'printDocket'])->name('admin.restaurants.kitchen.orders.print-docket');
    Route::get('/orders/print-group', [\App\Http\Controllers\KitchenOrderController::class, 'printGroupDocket'])->name('admin.restaurants.kitchen.orders.print-group');
});

// Re-open Manager Dashboard Routes
Route::prefix('manager')->group(function () {
    Route::middleware(['check.auth', 'role:manager,head_chef,super_admin,storekeeper,accountant,bar_keeper'])->group(function () {

        // Restaurant Reports
        Route::get('/restaurant-reports', [App\Http\Controllers\AdminController::class, 'restaurantReports'])->name('admin.restaurants.reports');


        // Housekeeping Inventory (Manager View)
        Route::get('/housekeeping-inventory', [\App\Http\Controllers\HousekeeperController::class, 'managerInventoryView'])->name('admin.housekeeping-inventory');

        // Low Stock View
        Route::get('/low-stock', [\App\Http\Controllers\AdminController::class, 'lowStock'])->name('admin.low-stock');

        // Suppliers
        Route::get('/restaurants/suppliers', [\App\Http\Controllers\SupplierController::class, 'index'])->name('admin.suppliers.index');
        Route::get('/restaurants/suppliers/create', [\App\Http\Controllers\SupplierController::class, 'create'])->name('admin.suppliers.create');
        Route::post('/restaurants/suppliers', [\App\Http\Controllers\SupplierController::class, 'store'])->name('admin.suppliers.store');
        Route::get('/restaurants/suppliers/{supplier}/edit', [\App\Http\Controllers\SupplierController::class, 'edit'])->name('admin.suppliers.edit');
        Route::put('/restaurants/suppliers/{supplier}', [\App\Http\Controllers\SupplierController::class, 'update'])->name('admin.suppliers.update');
        Route::delete('/restaurants/suppliers/{supplier}', [\App\Http\Controllers\SupplierController::class, 'destroy'])->name('admin.suppliers.destroy');

        // Products
        Route::get('/restaurants/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/restaurants/products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/restaurants/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/restaurants/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('admin.products.show');
        Route::get('/restaurants/products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/restaurants/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/restaurants/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('admin.products.destroy');
        Route::delete('/restaurants/products/variants/{variant}', [\App\Http\Controllers\ProductController::class, 'destroyVariant'])->name('admin.products.variants.destroy');

        // Product Variant Serving Configuration (PIC-based)
        Route::get('/restaurants/products/variants/{variant}/configure-serving', [\App\Http\Controllers\ProductController::class, 'configureServing'])->name('admin.products.configure-serving');
        Route::put('/restaurants/products/variants/{variant}/update-serving', [\App\Http\Controllers\ProductController::class, 'updateServing'])->name('admin.products.update-serving');


        Route::get('/restaurants/stock-receipts/create', [\App\Http\Controllers\StockReceiptController::class, 'create'])->name('admin.stock-receipts.create');
        Route::post('/restaurants/stock-receipts', [\App\Http\Controllers\StockReceiptController::class, 'store'])->name('admin.stock-receipts.store');
        Route::get('/restaurants/stock-receipts/products/{product}/variants', [\App\Http\Controllers\StockReceiptController::class, 'getProductVariants'])->name('admin.stock-receipts.get-variants');
        Route::post('/restaurants/stock-receipts/calculate', [\App\Http\Controllers\StockReceiptController::class, 'calculateTotals'])->name('admin.stock-receipts.calculate');
        Route::get('/restaurants/stock-receipts/{stockReceipt}/download', [\App\Http\Controllers\StockReceiptController::class, 'download'])->name('admin.stock-receipts.download');

        // Shopping List Management
        Route::get('/restaurants/shopping-list', [\App\Http\Controllers\KitchenController::class, 'index'])->name('admin.restaurants.shopping-list.index');
        Route::get('/restaurants/shopping-list/create', [\App\Http\Controllers\KitchenController::class, 'create'])->name('admin.restaurants.shopping-list.create');
        Route::post('/restaurants/shopping-list', [\App\Http\Controllers\KitchenController::class, 'store'])->name('admin.restaurants.shopping-list.store');
        // Purchased Items - Must be before {shoppingList} route to avoid route conflict
        Route::get('/restaurants/shopping-list/purchased-items', [\App\Http\Controllers\KitchenController::class, 'purchasedItems'])->name('admin.restaurants.shopping-list.purchased-items');
        // Transfers - Must be before {shoppingList} route to avoid route conflict
        Route::get('/restaurants/shopping-list/transfers', [\App\Http\Controllers\KitchenController::class, 'transfers'])->name('admin.restaurants.shopping-list.transfers');
        Route::post('/restaurants/shopping-list/bulk-transfer', [\App\Http\Controllers\KitchenController::class, 'bulkTransfer'])->name('admin.restaurants.shopping-list.bulk-transfer');
        Route::get('/restaurants/shopping-list/{shoppingList}', [\App\Http\Controllers\KitchenController::class, 'show'])->name('admin.restaurants.shopping-list.show');
        Route::get('/restaurants/shopping-list/{shoppingList}/edit', [\App\Http\Controllers\KitchenController::class, 'edit'])->name('admin.restaurants.shopping-list.edit');
        Route::put('/restaurants/shopping-list/{shoppingList}', [\App\Http\Controllers\KitchenController::class, 'update'])->name('admin.restaurants.shopping-list.update');
        Route::delete('/restaurants/shopping-list/{shoppingList}', [\App\Http\Controllers\KitchenController::class, 'destroy'])->name('admin.restaurants.shopping-list.destroy');
        Route::get('/restaurants/shopping-list/{shoppingList}/download', [\App\Http\Controllers\KitchenController::class, 'download'])->name('admin.restaurants.shopping-list.download');
        Route::get('/restaurants/shopping-list/{shoppingList}/receiving-report', [\App\Http\Controllers\KitchenController::class, 'receivingReport'])->name('admin.restaurants.shopping-list.receiving-report');
        Route::get('/restaurants/shopping-list/{shoppingList}/record', [\App\Http\Controllers\KitchenController::class, 'recordPurchaseView'])->name('admin.restaurants.shopping-list.record');
        Route::put('/restaurants/shopping-list/{shoppingList}/record', [\App\Http\Controllers\KitchenController::class, 'updatePurchase'])->name('admin.restaurants.shopping-list.update-purchase');
        Route::post('/restaurants/shopping-list/{shoppingList}/manager-approve', [\App\Http\Controllers\KitchenController::class, 'managerApprove'])->name('admin.shopping-list.manager-approve');
        Route::get('/restaurants/shopping-list/{shoppingList}/transfer', [\App\Http\Controllers\KitchenController::class, 'transferItems'])->name('admin.restaurants.shopping-list.transfer');
        Route::post('/restaurants/shopping-list/{shoppingList}/transfer', [\App\Http\Controllers\KitchenController::class, 'processTransfer'])->name('admin.restaurants.shopping-list.process-transfer');


        Route::get('/restaurants/stock-transfers', [\App\Http\Controllers\StockTransferController::class, 'index'])->name('admin.stock-transfers.index');
        Route::get('/restaurants/stock-transfers/create', [\App\Http\Controllers\StockTransferController::class, 'create'])->name('admin.stock-transfers.create');
        Route::post('/restaurants/stock-transfers', [\App\Http\Controllers\StockTransferController::class, 'store'])->name('admin.stock-transfers.store');
        Route::get('/restaurants/stock-transfers/{stockTransfer}', [\App\Http\Controllers\StockTransferController::class, 'show'])->name('admin.stock-transfers.show');
        Route::put('/restaurants/stock-transfers/{stockTransfer}/status', [\App\Http\Controllers\StockTransferController::class, 'updateStatus'])->name('admin.stock-transfers.update-status');
        Route::get('/restaurants/stock-transfers/{stockTransfer}/download', [\App\Http\Controllers\StockTransferController::class, 'download'])->name('admin.stock-transfers.download');

        // Recipes (Shared: Manager, Chef, Bar Keeper, Storekeeper, Accountant)
        Route::put('/restaurants/recipes/{recipe}/update-price', [\App\Http\Controllers\RecipeController::class, 'updatePrice'])->name('admin.recipes.update-price');
        Route::get('/restaurants/recipes/ajax-get-stock', [\App\Http\Controllers\RecipeController::class, 'ajaxGetStock'])->name('admin.recipes.ajax-get-stock');
        Route::resource('/restaurants/recipes', \App\Http\Controllers\RecipeController::class, ['as' => 'admin']);

        // Purchase Request Management (Manager)
        Route::get('/purchase-requests', [\App\Http\Controllers\PurchaseRequestController::class, 'index'])->name('admin.purchase-requests.index');
        // Received Items (Manager) - Must be before {purchaseRequest} route to avoid route conflict
        Route::get('/purchase-requests/received', [\App\Http\Controllers\PurchaseRequestController::class, 'receivedItems'])->name('admin.purchase-requests.received');
        Route::get('/purchase-requests/deadline', [\App\Http\Controllers\PurchaseRequestController::class, 'showDeadline'])->name('admin.purchase-requests.deadline');
        Route::post('/purchase-requests/deadline', [\App\Http\Controllers\PurchaseRequestController::class, 'updateDeadline'])->name('admin.purchase-requests.update-deadline');
        Route::get('/purchase-requests/{purchaseRequest}', [\App\Http\Controllers\PurchaseRequestController::class, 'show'])->name('admin.purchase-requests.show');
        Route::put('/purchase-requests/{purchaseRequest}', [\App\Http\Controllers\PurchaseRequestController::class, 'update'])->name('admin.purchase-requests.update');
        Route::post('/purchase-requests/{purchaseRequest}/approve', [\App\Http\Controllers\PurchaseRequestController::class, 'approve'])->name('admin.purchase-requests.approve');
        Route::post('/purchase-requests/bulk-approve', [\App\Http\Controllers\PurchaseRequestController::class, 'bulkApprove'])->name('admin.purchase-requests.bulk-approve');
        Route::post('/purchase-requests/{purchaseRequest}/reject', [\App\Http\Controllers\PurchaseRequestController::class, 'reject'])->name('admin.purchase-requests.reject');
        Route::post('/purchase-requests/add-to-shopping-list', [\App\Http\Controllers\PurchaseRequestController::class, 'addToShoppingList'])->name('admin.purchase-requests.add-to-shopping-list');

        // Purchase Reports (Manager)
        Route::get('/purchase-reports', [\App\Http\Controllers\PurchaseReportController::class, 'index'])->name('admin.purchase-reports.index');
        Route::get('/purchase-reports/{shoppingList}', [\App\Http\Controllers\PurchaseReportController::class, 'show'])->name('admin.purchase-reports.show');
    });

    // Mirror Bookings and common operations for Reception and Manager
    Route::middleware(['check.auth', 'role:manager,reception,super_admin,accountant,storekeeper'])->group(function () {
        // Search routes for returning guests and companies
        Route::get('/bookings/search/guests', [BookingController::class, 'searchGuests'])->name('admin.bookings.search.guests');
        Route::get('/bookings/search/companies', [BookingController::class, 'searchCompanies'])->name('admin.bookings.search.companies');

        Route::get('/bookings/manual/create', [BookingController::class, 'createManual'])->name('admin.bookings.manual.create');
        Route::post('/bookings/manual/store', [BookingController::class, 'storeManual'])->name('admin.bookings.manual.store');
        Route::get('/bookings/corporate/create', [BookingController::class, 'createCorporate'])->name('admin.bookings.corporate.create');
        Route::post('/bookings/corporate/store', [BookingController::class, 'storeCorporate'])->name('admin.bookings.corporate.store');
        Route::get('/bookings/corporate/available-rooms', [BookingController::class, 'getCorporateAvailableRooms'])->name('admin.bookings.corporate.available-rooms');
        Route::get('/bookings/company/{company}', [BookingController::class, 'getCompanyBookings'])->name('admin.bookings.company');
        Route::get('/bookings/available-rooms', [BookingController::class, 'getAvailableRooms'])->name('admin.bookings.available-rooms');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('admin.bookings.show');
        Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('admin.bookings.update-status');
        Route::put('/bookings/{booking}/notes', [BookingController::class, 'updateNotes'])->name('admin.bookings.update-notes');
        Route::post('/bookings/{booking}/extension', [BookingController::class, 'handleExtension'])->name('admin.bookings.extension');
        Route::put('/bookings/{booking}/modify-dates', [BookingController::class, 'modifyBookingDates'])->name('admin.bookings.modify-dates');
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('admin.bookings.destroy');

        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('admin.profile');
        Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('admin.profile.update');
        Route::post('/profile/update-photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('admin.profile.update-photo');
        Route::post('/profile/update-password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('admin.profile.update-password');
        Route::post('/profile/update-notifications', [\App\Http\Controllers\ProfileController::class, 'updateNotificationPreferences'])->name('admin.profile.update-notifications');

        // Issue Reports
        Route::get('/issues', [\App\Http\Controllers\IssueReportController::class, 'adminIndex'])->name('admin.issues.index');
        Route::get('/issues/{issue}', [\App\Http\Controllers\IssueReportController::class, 'adminShow'])->name('admin.issues.show');
        Route::put('/issues/{issue}', [\App\Http\Controllers\IssueReportController::class, 'adminUpdate'])->name('admin.issues.update');

        // WiFi Settings
        Route::get('/wifi-settings', [\App\Http\Controllers\AdminController::class, 'wifiSettings'])->name('admin.wifi-settings');
        Route::post('/wifi-settings', [\App\Http\Controllers\AdminController::class, 'updateWifiSettings'])->name('admin.wifi-settings.update');
        Route::put('/wifi-settings/room/{room}', [\App\Http\Controllers\AdminController::class, 'updateRoomWifiSettings'])->name('admin.wifi-settings.room.update');

        // Hotel Settings
        Route::get('/settings/hotel', [\App\Http\Controllers\AdminController::class, 'hotelSettings'])->name('admin.settings.hotel');
        Route::post('/settings/hotel', [\App\Http\Controllers\AdminController::class, 'updateHotelSettings'])->name('admin.settings.hotel.update');

        // Room Settings
        Route::get('/settings/rooms', [\App\Http\Controllers\AdminController::class, 'roomSettings'])->name('admin.settings.rooms');
        Route::post('/settings/rooms', [\App\Http\Controllers\AdminController::class, 'updateRoomSettings'])->name('admin.settings.rooms.update');
        // Pricing Settings - Disabled
        // Route::get('/settings/pricing', [\App\Http\Controllers\AdminController::class, 'pricingSettings'])->name('admin.settings.pricing');
        // Route::post('/settings/pricing', [\App\Http\Controllers\AdminController::class, 'updatePricingSettings'])->name('admin.settings.pricing.update');

        // Feedback Analysis
        Route::get('/feedback', [\App\Http\Controllers\AdminController::class, 'feedbackAnalysis'])->name('admin.feedback');

        // Services Management
        Route::get('/services', [\App\Http\Controllers\AdminController::class, 'services'])->name('admin.services.index');
        Route::get('/services/create', [\App\Http\Controllers\AdminController::class, 'createService'])->name('admin.services.create');
        Route::post('/services', [\App\Http\Controllers\AdminController::class, 'storeService'])->name('admin.services.store');
        Route::get('/services/{service}/edit', [\App\Http\Controllers\AdminController::class, 'editService'])->name('admin.services.edit');
        Route::put('/services/{service}', [\App\Http\Controllers\AdminController::class, 'updateService'])->name('admin.services.update');
        Route::delete('/services/{service}', [\App\Http\Controllers\AdminController::class, 'deleteService'])->name('admin.services.delete');

        // Newsletter Subscriptions
        Route::get('/newsletter/subscriptions', [\App\Http\Controllers\NewsletterController::class, 'index'])->name('admin.newsletter.subscriptions');
        Route::post('/newsletter/subscriptions/{id}/toggle', [\App\Http\Controllers\NewsletterController::class, 'toggleStatus'])->name('admin.newsletter.toggle');
        Route::delete('/newsletter/subscriptions/{id}', [\App\Http\Controllers\NewsletterController::class, 'destroy'])->name('admin.newsletter.destroy');
        Route::get('/newsletter/subscriptions/export', [\App\Http\Controllers\NewsletterController::class, 'export'])->name('admin.newsletter.export');

        // Extension requests (Manager/Reception)
        Route::get('/extension-requests', [\App\Http\Controllers\AdminController::class, 'extensionRequests'])->name('admin.extension-requests');
    });

    // Strictly Manager Only Routes
    Route::middleware(['check.auth', 'role:manager,super_admin'])->group(function () {
        // Hotel Settings
        Route::get('/settings/hotel', [\App\Http\Controllers\AdminController::class, 'hotelSettings'])->name('admin.settings.hotel');
        Route::post('/settings/hotel', [\App\Http\Controllers\AdminController::class, 'updateHotelSettings'])->name('admin.settings.hotel.update');

        // Room Settings
        Route::get('/settings/rooms', [\App\Http\Controllers\AdminController::class, 'roomSettings'])->name('admin.settings.rooms');
        Route::post('/settings/rooms', [\App\Http\Controllers\AdminController::class, 'updateRoomSettings'])->name('admin.settings.rooms.update');

        // Service Requests (Reception Operations) - Moved here as it was previously in the manager/reception/super_admin group
        Route::get('/service-requests', [\App\Http\Controllers\ServiceRequestController::class, 'receptionIndex'])->name('admin.service-requests');
        Route::put('/service-requests/{serviceRequest}/status', [\App\Http\Controllers\ServiceRequestController::class, 'updateStatus'])->name('admin.service-requests.update');

        // Reservations (Reception Operations)
        Route::get('/reservations/check-in', [\App\Http\Controllers\ReceptionController::class, 'checkIn'])->name('admin.reservations.check-in');
        Route::get('/reservations/check-out', [\App\Http\Controllers\ReceptionController::class, 'checkOut'])->name('admin.reservations.check-out');
        Route::get('/reservations/active', [\App\Http\Controllers\ReceptionController::class, 'activeReservations'])->name('admin.reservations.active');

        // Guests (Reception Operations)
        Route::get('/guests', [\App\Http\Controllers\ReceptionController::class, 'guests'])->name('admin.guests');

        // Daily Reports (Reception Operations) - Allow both manager and reception
        // Routes accessible by both manager and reception
        Route::middleware(['check.auth', 'role:manager,reception'])->group(function () {
            // Check-in route accessible by both manager and reception
            Route::put('/bookings/{booking}/check-in', [BookingController::class, 'updateCheckInStatus'])->name('admin.bookings.update-checkin');

            Route::get('/reports/daily', [\App\Http\Controllers\ReceptionController::class, 'reports'])->name('admin.reports.daily');
        });

        // Checkout Payment (Reception Operations)
        Route::get('/checkout-payment/{booking}', [\App\Http\Controllers\ReceptionController::class, 'checkoutPayment'])->name('admin.checkout-payment');
        Route::post('/checkout-payment/{booking}/process', [\App\Http\Controllers\ReceptionController::class, 'processCheckoutPayment'])->name('admin.checkout-payment.process');
        Route::post('/checkout-payment/{booking}/cash', [\App\Http\Controllers\ReceptionController::class, 'processCashPayment'])->name('admin.checkout-payment.cash');

        // Corporate Group Checkout
        Route::post('/bookings/checkout-company-group/{company}', [\App\Http\Controllers\ReceptionController::class, 'checkoutCompanyGroup'])->name('admin.bookings.checkout-company-group');
        // Checkout Bill (Manager/Reception Operations)
        Route::get('/bookings/{booking}/checkout-bill', [\App\Http\Controllers\ServiceRequestController::class, 'generateCheckoutBill'])->name('admin.bookings.checkout-bill');
    });
});

// Super Admin Dashboard Routes
Route::prefix('super-admin')->group(function () {
    // Login routes redirect to unified login
    Route::get('/login', function () {
        return redirect()->route('login');
    })->name('super_admin.login');
    Route::post('/login', function () {
        return redirect()->route('login');
    })->name('super_admin.login.post');

    // Protected routes (require authentication and super admin role)
    // Note: 'auth' middleware checks default guard, but we use 'staff' guard
    // The 'super_admin' middleware will handle authentication check
    Route::middleware(['super_admin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('super_admin.dashboard');

        // User Management
        Route::get('/users', [\App\Http\Controllers\SuperAdminController::class, 'users'])->name('super_admin.users');
        Route::get('/users/create', [\App\Http\Controllers\SuperAdminController::class, 'createUser'])->name('super_admin.users.create');
        Route::post('/users', [\App\Http\Controllers\SuperAdminController::class, 'storeUser'])->name('super_admin.users.store');
        Route::get('/users/{id}/edit', [\App\Http\Controllers\SuperAdminController::class, 'editUser'])->name('super_admin.users.edit');
        Route::put('/users/{id}', [\App\Http\Controllers\SuperAdminController::class, 'updateUser'])->name('super_admin.users.update');
        Route::post('/users/{id}/reset-password', [\App\Http\Controllers\SuperAdminController::class, 'resetPassword'])->name('super_admin.users.reset-password');
        Route::delete('/users/{id}', [\App\Http\Controllers\SuperAdminController::class, 'deleteUser'])->name('super_admin.users.delete');

        // Roles Management
        Route::get('/roles', [\App\Http\Controllers\SuperAdminController::class, 'roles'])->name('super_admin.roles');
        Route::post('/roles', [\App\Http\Controllers\SuperAdminController::class, 'storeRole'])->name('super_admin.roles.store');
        Route::put('/roles/{role}', [\App\Http\Controllers\SuperAdminController::class, 'updateRole'])->name('super_admin.roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\SuperAdminController::class, 'deleteRole'])->name('super_admin.roles.delete');
        Route::post('/roles/{role}/permissions', [\App\Http\Controllers\SuperAdminController::class, 'assignPermissions'])->name('super_admin.roles.assign-permissions');

        // Permissions Management
        Route::get('/permissions', [\App\Http\Controllers\SuperAdminController::class, 'permissions'])->name('super_admin.permissions');
        Route::post('/permissions', [\App\Http\Controllers\SuperAdminController::class, 'storePermission'])->name('super_admin.permissions.store');
        Route::put('/permissions/{permission}', [\App\Http\Controllers\SuperAdminController::class, 'updatePermission'])->name('super_admin.permissions.update');

        // Activity Logs
        Route::get('/activity-logs', [\App\Http\Controllers\SuperAdminController::class, 'activityLogs'])->name('super_admin.activity-logs');
        Route::get('/activity-logs/export', [\App\Http\Controllers\SuperAdminController::class, 'exportActivityLogs'])->name('super_admin.activity-logs.export');

        // System Logs
        Route::get('/system-logs', [\App\Http\Controllers\SuperAdminController::class, 'systemLogs'])->name('super_admin.system-logs');

        // Log Management
        Route::post('/logs/clear', [\App\Http\Controllers\SuperAdminController::class, 'clearLogs'])->name('super_admin.logs.clear');

        // System Settings
        Route::get('/system-settings', [\App\Http\Controllers\SuperAdminController::class, 'systemSettings'])->name('super_admin.system-settings');
        Route::post('/system-settings', [\App\Http\Controllers\SuperAdminController::class, 'updateSystemSettings'])->name('super_admin.system-settings.update');


        // Failed Login Attempts
        Route::get('/failed-login-attempts', [\App\Http\Controllers\SuperAdminController::class, 'failedLoginAttempts'])->name('super_admin.failed-login-attempts');
        Route::post('/failed-login-attempts/block-ip/{ipAddress}', [\App\Http\Controllers\SuperAdminController::class, 'blockIp'])->name('super_admin.block-ip');
        Route::post('/failed-login-attempts/unblock-ip/{ipAddress}', [\App\Http\Controllers\SuperAdminController::class, 'unblockIp'])->name('super_admin.unblock-ip');

        // Active Sessions & Force Logout
        Route::get('/active-sessions', [\App\Http\Controllers\SuperAdminController::class, 'activeSessions'])->name('super_admin.active-sessions');
        Route::post('/force-logout/{sessionId}', [\App\Http\Controllers\SuperAdminController::class, 'forceLogout'])->name('super_admin.force-logout');
        Route::post('/force-logout-user/{userId}', [\App\Http\Controllers\SuperAdminController::class, 'forceLogoutUser'])->name('super_admin.force-logout-user');

        // Cache Management
        Route::get('/cache-management', [\App\Http\Controllers\SuperAdminController::class, 'cacheManagement'])->name('super_admin.cache-management');
        Route::post('/cache-management/clear', [\App\Http\Controllers\SuperAdminController::class, 'clearCache'])->name('super_admin.clear-cache');

        // Profile
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('super_admin.profile');
        Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('super_admin.profile.update');
        Route::post('/profile/update-photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('super_admin.profile.update-photo');
        Route::post('/profile/update-password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('super_admin.profile.update-password');
        Route::post('/profile/update-notifications', [\App\Http\Controllers\ProfileController::class, 'updateNotificationPreferences'])->name('super_admin.profile.update-notifications');

        Route::post('/logout', [AuthController::class, 'logout'])->name('super_admin.logout');
    });
});

// Reception Dashboard Routes
Route::prefix('reception')->group(function () {
    // Login routes redirect to unified login
    Route::get('/login', function () {
        return redirect()->route('login');
    })->name('reception.login');
    Route::post('/login', function () {
        return redirect()->route('login');
    })->name('reception.login.post');

    // Protected routes (require authentication)
    // Use 'check.auth' instead of 'auth' to support custom guards (staff/guest)
    Route::middleware(['check.auth', 'role:reception,manager,accountant'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ServiceRequestController::class, 'receptionDashboard'])->name('reception.dashboard');

        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('reception.profile');
        Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('reception.profile.update');
        Route::post('/profile/update-photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('reception.profile.update-photo');
        Route::post('/profile/update-password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('reception.profile.update-password');
        Route::post('/profile/update-notifications', [\App\Http\Controllers\ProfileController::class, 'updateNotificationPreferences'])->name('reception.profile.update-notifications');

        Route::post('/logout', [AuthController::class, 'logout'])->name('reception.logout');

        // Service Requests Routes
        Route::get('/service-requests', [\App\Http\Controllers\ServiceRequestController::class, 'receptionIndex'])->name('reception.service-requests');
        Route::put('/service-requests/{serviceRequest}/status', [\App\Http\Controllers\ServiceRequestController::class, 'updateStatus'])->name('reception.service-requests.update');

        // Reception Menu Routes
        Route::get('/bookings', [\App\Http\Controllers\ReceptionController::class, 'bookings'])->name('reception.bookings');
        Route::get('/reservations/new', [\App\Http\Controllers\ReceptionController::class, 'newReservation'])->name('reception.reservations.new');
        Route::get('/reservations/check-in', [\App\Http\Controllers\ReceptionController::class, 'checkIn'])->name('reception.reservations.check-in');
        Route::get('/reservations/check-out', [\App\Http\Controllers\ReceptionController::class, 'checkOut'])->name('reception.reservations.check-out');
        Route::get('/reservations/active', [\App\Http\Controllers\ReceptionController::class, 'activeReservations'])->name('reception.reservations.active');
        Route::get('/guests', [\App\Http\Controllers\ReceptionController::class, 'guests'])->name('reception.guests');
        Route::get('/rooms', [\App\Http\Controllers\ReceptionController::class, 'roomStatus'])->name('reception.rooms');
        Route::get('/payments', [\App\Http\Controllers\ReceptionController::class, 'payments'])->name('reception.payments');
        Route::get('/reports', [\App\Http\Controllers\ReceptionController::class, 'reports'])->name('reception.reports');
        Route::get('/reports/financial', [\App\Http\Controllers\ReceptionController::class, 'dailyFinancialReport'])->name('reception.reports.financial');

        // Reception Booking Operations
        Route::get('/bookings/manual/create', [\App\Http\Controllers\BookingController::class, 'createManual'])->name('reception.bookings.manual.create');
        Route::post('/bookings/manual/store', [\App\Http\Controllers\BookingController::class, 'storeManual'])->name('reception.bookings.manual.store');
        Route::get('/bookings/available-rooms', [\App\Http\Controllers\BookingController::class, 'getAvailableRooms'])->name('reception.bookings.available-rooms');
        Route::get('/bookings/{booking}', [\App\Http\Controllers\BookingController::class, 'show'])->name('reception.bookings.show');
        Route::put('/bookings/{booking}/check-in', [\App\Http\Controllers\BookingController::class, 'updateCheckInStatus'])->name('reception.bookings.update-checkin');
        Route::put('/bookings/{booking}/status', [\App\Http\Controllers\BookingController::class, 'updateStatus'])->name('reception.bookings.update-status');
        Route::get('/bookings/{booking}/checkout-bill', [\App\Http\Controllers\ServiceRequestController::class, 'generateCheckoutBill'])->name('reception.bookings.checkout-bill');
        Route::post('/bookings/{booking}/extension', [\App\Http\Controllers\BookingController::class, 'handleExtension'])->name('reception.bookings.extension');
        Route::put('/bookings/{booking}/modify-dates', [\App\Http\Controllers\BookingController::class, 'modifyBookingDates'])->name('reception.bookings.modify-dates');

        // Day Services Routes
        Route::get('/day-services', [\App\Http\Controllers\DayServiceController::class, 'index'])->name('reception.day-services.index');
        Route::get('/day-services/swimming', [\App\Http\Controllers\DayServiceController::class, 'swimmingService'])->name('reception.day-services.swimming');
        Route::get('/day-services/ceremony', [\App\Http\Controllers\DayServiceController::class, 'ceremonyService'])->name('reception.day-services.ceremony');
        Route::get('/day-services/parking', [\App\Http\Controllers\DayServiceController::class, 'parkingService'])->name('reception.day-services.parking');
        Route::get('/day-services/garden', [\App\Http\Controllers\DayServiceController::class, 'gardenService'])->name('reception.day-services.garden');
        Route::get('/day-services/conference', [\App\Http\Controllers\DayServiceController::class, 'conferenceRoomService'])->name('reception.day-services.conference');
        Route::get('/day-services/projector', [\App\Http\Controllers\DayServiceController::class, 'projectorService'])->name('reception.day-services.projector');
        Route::get('/day-services/music', [\App\Http\Controllers\DayServiceController::class, 'musicService'])->name('reception.day-services.music');
        Route::get('/day-services/pending', [\App\Http\Controllers\DayServiceController::class, 'pending'])->name('reception.day-services.pending');
        Route::get('/day-services/reports', [\App\Http\Controllers\DayServiceController::class, 'reports'])->name('reception.day-services.reports');
        Route::get('/day-services/reports/download', [\App\Http\Controllers\DayServiceController::class, 'downloadReport'])->name('reception.day-services.reports.download');
        Route::post('/day-services', [\App\Http\Controllers\DayServiceController::class, 'store'])->name('reception.day-services.store');
        Route::get('/day-services/{dayService}', [\App\Http\Controllers\DayServiceController::class, 'show'])->name('reception.day-services.show');
        Route::post('/day-services/{dayService}/payment', [\App\Http\Controllers\DayServiceController::class, 'processPayment'])->name('reception.day-services.payment');
        Route::get('/day-services/{dayService}/receipt', [\App\Http\Controllers\DayServiceController::class, 'downloadReceipt'])->name('reception.day-services.receipt');
        Route::get('/day-services/{dayService}/docket', [\App\Http\Controllers\DayServiceController::class, 'docket'])->name('reception.day-services.docket');
        Route::post('/day-services/{dayService}/add-items', [\App\Http\Controllers\DayServiceController::class, 'addItems'])->name('reception.day-services.add-items');
        Route::put('/day-services/{dayService}/update-items', [\App\Http\Controllers\DayServiceController::class, 'updateItems'])->name('reception.day-services.update-items');

        // Service Catalog Routes (View only for reception)
        Route::get('/service-catalog', [\App\Http\Controllers\ServiceCatalogController::class, 'index'])->name('reception.service-catalog.index');
        Route::get('/service-catalog/active/list', [\App\Http\Controllers\ServiceCatalogController::class, 'getActiveServices'])->name('reception.service-catalog.active');

        // Issue Reports
        Route::get('/issues', [\App\Http\Controllers\IssueReportController::class, 'receptionIndex'])->name('reception.issues.index');
        Route::get('/issues/{issue}', [\App\Http\Controllers\IssueReportController::class, 'receptionShow'])->name('reception.issues.show');
        Route::put('/issues/{issue}', [\App\Http\Controllers\IssueReportController::class, 'receptionUpdate'])->name('reception.issues.update');

        // Extension Requests
        Route::get('/extension-requests', [\App\Http\Controllers\ReceptionController::class, 'extensionRequests'])->name('reception.extension-requests');

        // Rooms Cleaning
        Route::get('/rooms/cleaning', [\App\Http\Controllers\ReceptionController::class, 'roomsNeedsCleaning'])->name('reception.rooms.cleaning');
        Route::post('/rooms/{room}/mark-cleaned', [\App\Http\Controllers\ReceptionController::class, 'markRoomCleaned'])->name('reception.rooms.mark-cleaned');

        // Purchase Request Routes
        Route::get('/purchase-requests/create', [\App\Http\Controllers\PurchaseRequestController::class, 'create'])->name('reception.purchase-requests.create');
        Route::post('/purchase-requests', [\App\Http\Controllers\PurchaseRequestController::class, 'store'])->name('reception.purchase-requests.store');
        Route::get('/purchase-requests/my-requests', [\App\Http\Controllers\PurchaseRequestController::class, 'myRequests'])->name('reception.purchase-requests.my');
        Route::get('/purchase-requests/history', [\App\Http\Controllers\PurchaseRequestController::class, 'history'])->name('reception.purchase-requests.history');
        Route::post('/purchase-requests/receive-items', [\App\Http\Controllers\PurchaseRequestController::class, 'receiveItems'])->name('reception.purchase-requests.receive');

        // Purchase Request Templates Routes
        Route::get('/purchase-requests/templates', [\App\Http\Controllers\PurchaseRequestController::class, 'templates'])->name('reception.purchase-requests.templates');
        Route::post('/purchase-requests/templates', [\App\Http\Controllers\PurchaseRequestController::class, 'storeTemplate'])->name('reception.purchase-requests.templates.store');
        Route::get('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'getTemplate'])->name('reception.purchase-requests.templates.get');
        Route::put('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'updateTemplate'])->name('reception.purchase-requests.templates.update');
        Route::delete('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'deleteTemplate'])->name('reception.purchase-requests.templates.delete');

        // Checkout Payment
        Route::get('/checkout-payment/{booking}', [\App\Http\Controllers\ReceptionController::class, 'checkoutPayment'])->name('reception.checkout-payment');
        Route::post('/checkout-payment/{booking}/process', [\App\Http\Controllers\ReceptionController::class, 'processCheckoutPayment'])->name('reception.checkout-payment.process');
        Route::post('/checkout-payment/{booking}/cash', [\App\Http\Controllers\ReceptionController::class, 'processCashPayment'])->name('reception.checkout-payment.cash');

        // Corporate Group Checkout
        Route::post('/bookings/checkout-company-group/{company}', [\App\Http\Controllers\ReceptionController::class, 'checkoutCompanyGroup'])->name('reception.bookings.checkout-company-group');
        Route::post('/bookings/checkout-company-payment/{company}', [\App\Http\Controllers\ReceptionController::class, 'processCompanyPayment'])->name('reception.bookings.checkout-company-payment');

        // Shift Handovers
        Route::get('/shift-handovers', [\App\Http\Controllers\ReceptionController::class, 'shiftHandovers'])->name('reception.shift-handovers');
        Route::get('/shift-handovers/{shiftClosure}/sales', [\App\Http\Controllers\ReceptionController::class, 'viewShiftSales'])->name('reception.shift-handovers.sales');
        Route::post('/shift-handovers/{shiftClosure}/acknowledge', [\App\Http\Controllers\ReceptionController::class, 'acknowledgeShiftClosure'])->name('reception.shift-handovers.acknowledge');
    });

    // Checkout Bill (Reception Operations)
    Route::get('/bookings/{booking}/checkout-bill', [\App\Http\Controllers\ServiceRequestController::class, 'generateCheckoutBill'])->name('reception.bookings.checkout-bill');
    Route::get('/companies/{company}/group-bill', [\App\Http\Controllers\ReceptionController::class, 'companyGroupBill'])->name('reception.companies.group-bill');
});

// Housekeeper Routes
Route::prefix('housekeeper')->group(function () {
    // Login routes redirect to unified login
    Route::get('/login', function () {
        return redirect()->route('login');
    })->name('housekeeper.login');
    Route::post('/login', function () {
        return redirect()->route('login');
    })->name('housekeeper.login.post');

    // Protected routes (require authentication)
    Route::middleware(['check.auth', 'role:housekeeper,manager,reception'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\HousekeeperController::class, 'index'])->name('housekeeper.dashboard');

        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('housekeeper.profile');
        Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('housekeeper.profile.update');
        Route::post('/profile/update-photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('housekeeper.profile.update-photo');
        Route::post('/profile/update-password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('housekeeper.profile.update-password');
        Route::post('/profile/update-notifications', [\App\Http\Controllers\ProfileController::class, 'updateNotificationPreferences'])->name('housekeeper.profile.update-notifications');

        Route::post('/logout', [AuthController::class, 'logout'])->name('housekeeper.logout');

        // Room Cleaning Routes
        Route::get('/rooms/cleaning', [\App\Http\Controllers\HousekeeperController::class, 'roomsNeedingCleaning'])->name('housekeeper.rooms.cleaning');
        Route::post('/rooms/{room}/mark-cleaned', [\App\Http\Controllers\HousekeeperController::class, 'markRoomCleaned'])->name('housekeeper.rooms.mark-cleaned');
        Route::get('/rooms/status', [\App\Http\Controllers\HousekeeperController::class, 'roomStatus'])->name('housekeeper.rooms.status');

        // Inventory Management Routes
        Route::get('/inventory', [\App\Http\Controllers\HousekeeperController::class, 'inventory'])->name('housekeeper.inventory');
        Route::post('/inventory/{item}/update-stock', [\App\Http\Controllers\HousekeeperController::class, 'updateInventoryStock'])->name('housekeeper.inventory.update-stock');
        Route::post('/inventory/{item}/update-minimum-stock', [\App\Http\Controllers\HousekeeperController::class, 'updateMinimumStock'])->name('housekeeper.inventory.update-minimum-stock');
        Route::get('/inventory/{item}/usage-track', [\App\Http\Controllers\HousekeeperController::class, 'getItemUsageTrack'])->name('housekeeper.inventory.usage-track');

        // Reports Route
        Route::get('/reports', [\App\Http\Controllers\HousekeeperController::class, 'reports'])->name('housekeeper.reports');

        // Room Issues Routes
        Route::get('/room-issues', [\App\Http\Controllers\HousekeeperController::class, 'roomIssues'])->name('housekeeper.room-issues');
        Route::post('/room-issues/report', [\App\Http\Controllers\HousekeeperController::class, 'reportRoomIssue'])->name('housekeeper.room-issues.report');

        // Purchase Request Routes (available to all staff)
        Route::get('/purchase-requests/create', [\App\Http\Controllers\PurchaseRequestController::class, 'create'])->name('housekeeper.purchase-requests.create');
        Route::post('/purchase-requests', [\App\Http\Controllers\PurchaseRequestController::class, 'store'])->name('housekeeper.purchase-requests.store');
        Route::get('/purchase-requests/my-requests', [\App\Http\Controllers\PurchaseRequestController::class, 'myRequests'])->name('housekeeper.purchase-requests.my');
        Route::get('/purchase-requests/history', [\App\Http\Controllers\PurchaseRequestController::class, 'history'])->name('housekeeper.purchase-requests.history');
        Route::post('/purchase-requests/receive-items', [\App\Http\Controllers\PurchaseRequestController::class, 'receiveItems'])->name('housekeeper.purchase-requests.receive');

        // Purchase Request Templates Routes
        Route::get('/purchase-requests/templates', [\App\Http\Controllers\PurchaseRequestController::class, 'templates'])->name('housekeeper.purchase-requests.templates');
        Route::post('/purchase-requests/templates', [\App\Http\Controllers\PurchaseRequestController::class, 'storeTemplate'])->name('housekeeper.purchase-requests.templates.store');
        Route::get('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'getTemplate'])->name('housekeeper.purchase-requests.templates.get');
        Route::put('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'updateTemplate'])->name('housekeeper.purchase-requests.templates.update');
        Route::delete('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'deleteTemplate'])->name('housekeeper.purchase-requests.templates.delete');

        // Stock Requests (Housekeeper requesting items from store)
        Route::get('/stock-requests', [\App\Http\Controllers\StockRequestController::class, 'index'])->name('housekeeper.stock-requests.index');
        Route::get('/stock-requests/create', [\App\Http\Controllers\StockRequestController::class, 'create'])->name('housekeeper.stock-requests.create');
        Route::post('/stock-requests', [\App\Http\Controllers\StockRequestController::class, 'store'])->name('housekeeper.stock-requests.store');

        // Stock Returns (Housekeeper returning items to store)
        Route::get('/stock-returns', [\App\Http\Controllers\StockReturnController::class, 'housekeeperIndex'])->name('housekeeper.stock-returns.index');
        Route::post('/stock-returns', [\App\Http\Controllers\StockReturnController::class, 'store'])->name('stock-returns.store');
    });
});

// Bar Keeper Routes
Route::prefix('bar-keeper')->group(function () {
    // Login routes redirect to unified login
    Route::get('/login', function () {
        return redirect()->route('login');
    })->name('bar-keeper.login');
    Route::post('/login', function () {
        return redirect()->route('login');
    })->name('bar-keeper.login.post');

    // Protected routes (require authentication)
    Route::middleware(['check.auth', 'role:bar_keeper,manager'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\BarKeeperController::class, 'dashboard'])->name('bar-keeper.dashboard');

        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('bar-keeper.profile');
        Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('bar-keeper.profile.update');
        Route::post('/profile/update-photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('bar-keeper.profile.update-photo');
        Route::post('/profile/update-password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('bar-keeper.profile.update-password');
        Route::post('/profile/update-notifications', [\App\Http\Controllers\ProfileController::class, 'updateNotificationPreferences'])->name('bar-keeper.profile.update-notifications');

        Route::post('/logout', [AuthController::class, 'logout'])->name('bar-keeper.logout');

        // Shift Closure Routes
        Route::get('/shift-summary', [\App\Http\Controllers\BarKeeperController::class, 'getShiftSummary'])->name('bar-keeper.shift-summary');
        Route::post('/open-shift', [\App\Http\Controllers\BarKeeperController::class, 'openShift'])->name('bar-keeper.open-shift');
        Route::post('/close-shift', [\App\Http\Controllers\BarKeeperController::class, 'closeShift'])->name('bar-keeper.close-shift');

        // Complete a Guest Order (Service Request)
        Route::put('/orders/{serviceRequest}/complete', [\App\Http\Controllers\BarKeeperController::class, 'completeOrder'])->name('bar-keeper.orders.complete');

        Route::post('/logout', [AuthController::class, 'logout'])->name('bar-keeper.logout');

        // Stock Transfers
        Route::get('/transfers', [\App\Http\Controllers\BarKeeperController::class, 'transfers'])->name('bar-keeper.transfers.index');
        Route::put('/transfers/{stockTransfer}/receive', [\App\Http\Controllers\BarKeeperController::class, 'receiveTransfer'])->name('bar-keeper.transfers.receive');

        // Stock Overview
        Route::get('/my-stock', [\App\Http\Controllers\BarKeeperController::class, 'stock'])->name('bar-keeper.stock.index');
        Route::post('/stock/update-minimum/{variant}', [\App\Http\Controllers\BarKeeperController::class, 'updateMinimumStock'])->name('bar-keeper.stock.update-minimum');
        Route::post('/stock/update-prices/{variant}', [\App\Http\Controllers\BarKeeperController::class, 'updatePrices'])->name('bar-keeper.stock.update-prices');
        Route::get('/stock/{variant}/usage-track', [\App\Http\Controllers\BarKeeperController::class, 'getBarItemUsageTrack'])->name('bar-keeper.stock.usage-track');

        // Guest Orders
        Route::get('/orders', [\App\Http\Controllers\BarKeeperController::class, 'completedOrders'])->name('bar-keeper.orders.index');
        Route::post('/orders/{serviceRequest}/complete', [\App\Http\Controllers\BarKeeperController::class, 'completeOrder'])->name('bar-keeper.orders.complete');
        Route::post('/orders/{serviceRequest}/serve', [\App\Http\Controllers\BarKeeperController::class, 'serveOrder'])->name('bar-keeper.orders.serve');
        Route::post('/orders/{serviceRequest}/cancel', [\App\Http\Controllers\BarKeeperController::class, 'cancelOrder'])->name('bar-keeper.orders.cancel');
        Route::get('/orders/{serviceRequest}/print-docket', [\App\Http\Controllers\BarKeeperController::class, 'printDocket'])->name('bar-keeper.orders.print-docket');
        Route::get('/orders/print-group', [\App\Http\Controllers\BarKeeperController::class, 'printGroupDocket'])->name('bar-keeper.orders.print-group');

        // Reports
        Route::get('/reports', [\App\Http\Controllers\BarKeeperController::class, 'reports'])->name('bar-keeper.reports');

        // Product Management (Read Only for Bar Keeper)
        Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('bar-keeper.products.index');
        Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('bar-keeper.products.show');

        Route::get('/recorded-items', [\App\Http\Controllers\BarKeeperController::class, 'recordedItems'])->name('bar-keeper.recorded-items');

        // Purchase Request Routes
        Route::get('/purchase-requests/create', [\App\Http\Controllers\PurchaseRequestController::class, 'create'])->name('bar-keeper.purchase-requests.create');
        Route::post('/purchase-requests', [\App\Http\Controllers\PurchaseRequestController::class, 'store'])->name('bar-keeper.purchase-requests.store');
        Route::get('/purchase-requests/my-requests', [\App\Http\Controllers\PurchaseRequestController::class, 'myRequests'])->name('bar-keeper.purchase-requests.my');
        Route::get('/purchase-requests/history', [\App\Http\Controllers\PurchaseRequestController::class, 'history'])->name('bar-keeper.purchase-requests.history');
        Route::post('/purchase-requests/receive-items', [\App\Http\Controllers\PurchaseRequestController::class, 'receiveItems'])->name('bar-keeper.purchase-requests.receive');

        // Purchase Request Templates Routes
        Route::get('/purchase-requests/templates', [\App\Http\Controllers\PurchaseRequestController::class, 'templates'])->name('bar-keeper.purchase-requests.templates');
        Route::post('/purchase-requests/templates', [\App\Http\Controllers\PurchaseRequestController::class, 'storeTemplate'])->name('bar-keeper.purchase-requests.templates.store');
        Route::get('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'getTemplate'])->name('bar-keeper.purchase-requests.templates.get');
        Route::put('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'updateTemplate'])->name('bar-keeper.purchase-requests.templates.update');
        Route::delete('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'deleteTemplate'])->name('bar-keeper.purchase-requests.templates.delete');

        // Day Services Routes (Certemonies/Events)
        Route::get('/day-services/{dayService}', [\App\Http\Controllers\DayServiceController::class, 'show'])->name('bar-keeper.day-services.show');
        Route::get('/day-services/{dayService}/docket', [\App\Http\Controllers\DayServiceController::class, 'docket'])->name('bar-keeper.day-services.docket');
        Route::post('/day-services/{dayService}/payment', [\App\Http\Controllers\DayServiceController::class, 'processPayment'])->name('bar-keeper.day-services.payment');
        Route::get('/day-services/{dayService}/receipt', [\App\Http\Controllers\DayServiceController::class, 'downloadReceipt'])->name('bar-keeper.day-services.receipt');
    });
});

// Customer Dashboard Routes
Route::prefix('customer')->group(function () {
    // Login routes redirect to unified login
    Route::get('/login', function () {
        return redirect()->route('login');
    })->name('customer.login');
    Route::post('/login', function () {
        return redirect()->route('login');
    })->name('customer.login.post');

    // Logout route - accessible to all authenticated users (no role restriction needed)
    Route::middleware(['check.auth'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('customer.logout');
    });

    // Protected routes (require authentication and customer role)
    // Use 'check.auth' instead of 'auth' to support custom guards (staff/guest)
    Route::middleware(['check.auth', 'role:customer'])->group(function () {
        Route::get('/dashboard', [BookingController::class, 'customerDashboard'])->name('customer.dashboard');
        Route::get('/restaurant', [BookingController::class, 'restaurantService'])->name('customer.restaurant');

        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('customer.profile');
        Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('customer.profile.update');
        Route::post('/profile/update-photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('customer.profile.update-photo');
        Route::post('/profile/update-password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('customer.profile.update-password');
        Route::post('/profile/update-preferences', [\App\Http\Controllers\ProfileController::class, 'updatePreferences'])->name('profile.update-preferences');
        Route::post('/profile/update-notifications', [\App\Http\Controllers\ProfileController::class, 'updateNotificationPreferences'])->name('customer.profile.update-notifications');
    });

    // Public/Shared Service Request Routes (accessible by guests and staff)
    Route::middleware(['check.auth', 'role:customer,waiter,bar_keeper,manager,receptionist,head_chef'])->group(function () {
        Route::get('/services/available', [\App\Http\Controllers\ServiceRequestController::class, 'getAvailableServices'])->name('customer.services.available');
        Route::post('/services/request', [\App\Http\Controllers\ServiceRequestController::class, 'requestService'])->name('customer.services.request');
        Route::post('/ceremonies/settle-usage', [\App\Http\Controllers\ServiceRequestController::class, 'settleCeremonyUsage'])->name('ceremonies.settle-usage');
        Route::post('/pos/settle-payment/{serviceRequest}', [\App\Http\Controllers\ServiceRequestController::class, 'settlePayment'])->name('pos.settle-payment');
        Route::get('/bookings/{booking}/checkout-bill', [\App\Http\Controllers\ServiceRequestController::class, 'generateCheckoutBill'])->name('customer.bookings.checkout-bill');
    });

    Route::middleware(['check.auth', 'role:customer'])->group(function () {
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('customer.bookings.show');
        Route::get('/bookings/{booking}/services', [\App\Http\Controllers\ServiceRequestController::class, 'getBookingServices'])->name('customer.bookings.services');
        // Route::get('/bookings/{booking}/checkout-bill', ...); // Moved up to shared group
        Route::get('/bookings/{booking}/checkout-payment', [\App\Http\Controllers\BookingController::class, 'customerCheckoutPayment'])->name('customer.bookings.checkout-payment');
        Route::get('/bookings/{booking}/identity-card', [\App\Http\Controllers\BookingController::class, 'downloadIdentityCard'])->name('customer.bookings.identity-card');

        // My Bookings, Booking History, My Extensions, and My Payments Routes
        Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('customer.my-bookings');
        Route::get('/booking-history', [BookingController::class, 'bookingHistory'])->name('customer.booking-history');
        Route::get('/extensions', [BookingController::class, 'myExtensions'])->name('customer.extensions');
        Route::get('/payments', [BookingController::class, 'myPayments'])->name('customer.payments');

        // Payment Receipt Download
        Route::get('/payment/receipt/{booking}/download', [PaymentController::class, 'downloadReceipt'])->name('customer.payment.receipt.download');

        // New Feature Pages
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('customer.notifications');
        Route::get('/calendar', [BookingController::class, 'bookingCalendar'])->name('customer.calendar');
        Route::get('/room-information', [BookingController::class, 'roomInformation'])->name('customer.room-information');
        Route::get('/feedback', [\App\Http\Controllers\FeedbackController::class, 'index'])->name('customer.feedback');
        Route::post('/feedback/submit', [\App\Http\Controllers\FeedbackController::class, 'submit'])->name('customer.feedback.submit');
        Route::get('/local-info', [\App\Http\Controllers\LocalInfoController::class, 'index'])->name('customer.local-info');
        Route::get('/support', [BookingController::class, 'customerSupport'])->name('customer.support');

        // Issue Reports
        Route::post('/issues', [\App\Http\Controllers\IssueReportController::class, 'store'])->name('customer.issues.store');
        Route::get('/issues', [\App\Http\Controllers\IssueReportController::class, 'customerIndex'])->name('customer.issues.index');
        Route::get('/issues/{issue}', [\App\Http\Controllers\IssueReportController::class, 'customerShow'])->name('customer.issues.show');

        // Booking Extensions
        Route::post('/bookings/{booking}/extend', [BookingController::class, 'requestExtension'])->name('customer.bookings.extend');
        Route::post('/bookings/{booking}/decrease', [BookingController::class, 'requestDecrease'])->name('customer.bookings.decrease');
    });
});

// Chef Master Routes
Route::prefix('chef-master')->group(function () {
    // Login routes redirect to unified login
    Route::get('/login', function () {
        return redirect()->route('login');
    })->name('chef-master.login');

    // Protected routes (require authentication)
    Route::middleware(['check.auth', 'role:head_chef,manager,super_admin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\KitchenController::class, 'dashboard'])->name('chef-master.dashboard');

        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('chef-master.profile');
        Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('chef-master.profile.update');
        Route::post('/profile/update-photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('chef-master.profile.update-photo');
        Route::post('/profile/update-password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('chef-master.profile.update-password');

        Route::post('/logout', [AuthController::class, 'logout'])->name('chef-master.logout');

        // Inventory Management
        Route::get('/inventory', [\App\Http\Controllers\KitchenController::class, 'inventory'])->name('chef-master.inventory');
        Route::post('/inventory/{item}/update-stock', [\App\Http\Controllers\KitchenController::class, 'updateInventoryStock'])->name('chef-master.inventory.update-stock');
        Route::post('/inventory/{item}/update-minimum-stock', [\App\Http\Controllers\KitchenController::class, 'updateMinimumStock'])->name('chef-master.inventory.update-minimum-stock');
        Route::get('/inventory/{item}/usage-track', [\App\Http\Controllers\KitchenController::class, 'getItemUsageTrack'])->name('chef-master.inventory.usage-track');
        Route::post('/inventory/release', [\App\Http\Controllers\KitchenController::class, 'releaseStock'])->name('chef-master.inventory.release');

        // Purchase Request Routes
        Route::get('/purchase-requests/create', [\App\Http\Controllers\PurchaseRequestController::class, 'create'])->name('chef-master.purchase-requests.create');
        Route::post('/purchase-requests', [\App\Http\Controllers\PurchaseRequestController::class, 'store'])->name('chef-master.purchase-requests.store');
        Route::get('/purchase-requests/my-requests', [\App\Http\Controllers\PurchaseRequestController::class, 'myRequests'])->name('chef-master.purchase-requests.my');
        Route::get('/purchase-requests/history', [\App\Http\Controllers\PurchaseRequestController::class, 'history'])->name('chef-master.purchase-requests.history');
        Route::post('/purchase-requests/receive-items', [\App\Http\Controllers\PurchaseRequestController::class, 'receiveItems'])->name('chef-master.purchase-requests.receive');

        // Purchase Request Templates
        Route::get('/purchase-requests/templates', [\App\Http\Controllers\PurchaseRequestController::class, 'templates'])->name('chef-master.purchase-requests.templates');
        Route::post('/purchase-requests/templates', [\App\Http\Controllers\PurchaseRequestController::class, 'storeTemplate'])->name('chef-master.purchase-requests.templates.store');
        Route::get('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'getTemplate'])->name('chef-master.purchase-requests.templates.get');
        Route::put('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'updateTemplate'])->name('chef-master.purchase-requests.templates.update');
        Route::delete('/purchase-requests/templates/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'deleteTemplate'])->name('chef-master.purchase-requests.templates.delete');

        // Daily Stock Sheet Report
        Route::get('/reports', [\App\Http\Controllers\KitchenController::class, 'reports'])->name('chef-master.reports');

        // Day Services (Docket Print)
        Route::get('/day-services/{dayService}/docket', [\App\Http\Controllers\DayServiceController::class, 'docket'])->name('chef-master.day-services.docket');
    });
});

// Storekeeper Routes
Route::prefix('storekeeper')->group(function () {
    // Protected routes (require authentication)
    Route::middleware(['check.auth', 'role:storekeeper,manager,super_admin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\StorekeeperController::class, 'dashboard'])->name('storekeeper.dashboard');
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('storekeeper.profile');

        // Inventory Management
        Route::get('/inventory', [\App\Http\Controllers\StorekeeperController::class, 'inventory'])->name('storekeeper.inventory');

        // Purchase Request Management
        Route::get('/purchase-requests', [\App\Http\Controllers\StorekeeperController::class, 'purchaseRequests'])->name('storekeeper.purchase-requests');
        Route::get('/purchase-requests/create', [\App\Http\Controllers\PurchaseRequestController::class, 'create'])->name('storekeeper.purchase-requests.create');
        Route::post('/purchase-requests', [\App\Http\Controllers\PurchaseRequestController::class, 'store'])->name('storekeeper.purchase-requests.store');
        Route::get('/purchase-requests/emergency', [\App\Http\Controllers\PurchaseRequestController::class, 'createEmergency'])->name('storekeeper.purchase-requests.emergency');
        Route::post('/purchase-requests/emergency', [\App\Http\Controllers\PurchaseRequestController::class, 'storeEmergency'])->name('storekeeper.purchase-requests.emergency.store');
        Route::get('/purchase-requests/my-requests', [\App\Http\Controllers\PurchaseRequestController::class, 'myRequests'])->name('storekeeper.purchase-requests.my');

        // Stock Receipts (Receiving items)
        Route::get('/stock-receipts', [\App\Http\Controllers\StockReceiptController::class, 'index'])->name('storekeeper.stock-receipts.index');
        Route::get('/stock-receipts/create', [\App\Http\Controllers\StockReceiptController::class, 'create'])->name('storekeeper.stock-receipts.create');
        Route::post('/stock-receipts', [\App\Http\Controllers\StockReceiptController::class, 'store'])->name('storekeeper.stock-receipts.store');
        Route::get('/stock-receipts/get-variants/{product}', [\App\Http\Controllers\StockReceiptController::class, 'getProductVariants'])->name('storekeeper.stock-receipts.get-variants');
        Route::get('/stock-receipts/{stockReceipt}/download', [\App\Http\Controllers\StockReceiptController::class, 'download'])->name('storekeeper.stock-receipts.download');

        // Stock Transfers (Distributing items)
        Route::get('/transfers', [\App\Http\Controllers\StockTransferController::class, 'index'])->name('storekeeper.transfers.index');
        Route::get('/transfers/create', [\App\Http\Controllers\StockTransferController::class, 'create'])->name('storekeeper.transfers.create');
        Route::post('/transfers', [\App\Http\Controllers\StockTransferController::class, 'store'])->name('storekeeper.transfers.store');

        // Product Management (Registration & Departments)
        Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('storekeeper.products.index');
        Route::get('/products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('storekeeper.products.create');
        Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('storekeeper.products.store');
        Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('storekeeper.products.edit');
        Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('storekeeper.products.update');
        Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('storekeeper.products.destroy');
        Route::delete('/products/variant/{id}', [\App\Http\Controllers\ProductController::class, 'destroyVariant'])->name('storekeeper.products.variant.destroy');

        // Shopping List Management
        Route::get('/shopping-lists', [\App\Http\Controllers\KitchenController::class, 'index'])->name('storekeeper.shopping-list.index');
        Route::get('/shopping-lists/create', [\App\Http\Controllers\KitchenController::class, 'create'])->name('storekeeper.shopping-list.create');
        Route::post('/shopping-lists', [\App\Http\Controllers\KitchenController::class, 'store'])->name('storekeeper.shopping-list.store');
        Route::get('/shopping-lists/{shoppingList}', [\App\Http\Controllers\KitchenController::class, 'show'])->name('storekeeper.shopping-list.show');
        Route::get('/shopping-lists/{shoppingList}/edit', [\App\Http\Controllers\KitchenController::class, 'edit'])->name('storekeeper.shopping-list.edit');
        // Stock Returns (From housekeepers)
        Route::get('/stock-returns', [\App\Http\Controllers\StockReturnController::class, 'storekeeperIndex'])->name('storekeeper.stock-returns.index');
        Route::post('/stock-returns/{stockReturn}/receive', [\App\Http\Controllers\StockReturnController::class, 'receive'])->name('storekeeper.stock-returns.receive');
        Route::post('/stock-returns/{stockReturn}/reject', [\App\Http\Controllers\StockReturnController::class, 'reject'])->name('storekeeper.stock-returns.reject');

        // Store Announcements (Marquee)
        Route::get('/announcements', [\App\Http\Controllers\StoreAnnouncementController::class, 'index'])->name('storekeeper.announcements.index');
        Route::post('/announcements', [\App\Http\Controllers\StoreAnnouncementController::class, 'store'])->name('storekeeper.announcements.store');
        Route::post('/announcements/{announcement}/toggle', [\App\Http\Controllers\StoreAnnouncementController::class, 'toggleStatus'])->name('storekeeper.announcements.toggle');
        Route::delete('/announcements/{announcement}', [\App\Http\Controllers\StoreAnnouncementController::class, 'destroy'])->name('storekeeper.announcements.destroy');

        Route::post('/logout', [AuthController::class, 'logout'])->name('storekeeper.logout');
    });
});

// Accountant Role Routes
Route::middleware(['auth:staff', 'role:accountant,manager,super_admin'])->prefix('accountant')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AccountantController::class, 'dashboard'])->name('accountant.dashboard');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('accountant.profile');

    // Shopping List Approvals
    Route::get('/shopping-lists', [\App\Http\Controllers\AccountantController::class, 'shoppingLists'])->name('accountant.shopping-lists');
    Route::get('/shopping-lists/{shoppingList}', [\App\Http\Controllers\AccountantController::class, 'showShoppingList'])->name('accountant.shopping-list.show');
    Route::post('/shopping-lists/{shoppingList}/approve', [\App\Http\Controllers\AccountantController::class, 'approveShoppingList'])->name('accountant.shopping-list.approve');
    Route::post('/shopping-lists/{shoppingList}/disburse', [\App\Http\Controllers\AccountantController::class, 'disburseFunds'])->name('accountant.shopping-list.disburse');
    Route::post('/shopping-lists/{shoppingList}/reject', [\App\Http\Controllers\AccountantController::class, 'rejectShoppingList'])->name('accountant.shopping-list.reject');
    Route::post('/shopping-lists/{shoppingList}/claim', [\App\Http\Controllers\AccountantController::class, 'claimPurchase'])->name('accountant.shopping-list.claim');
    Route::get('/shopping-lists/{shoppingList}/record-purchase', [\App\Http\Controllers\AccountantController::class, 'recordPurchaseView'])->name('accountant.shopping-list.record-purchase');
    Route::put('/shopping-lists/{shoppingList}/record-purchase', [\App\Http\Controllers\AccountantController::class, 'updatePurchase'])->name('accountant.shopping-list.update-purchase');

    // Payment Verification
    Route::get('/payments', [\App\Http\Controllers\AccountantController::class, 'paymentVerification'])->name('accountant.payments');
    Route::post('/payments/{shoppingList}/verify', [\App\Http\Controllers\AccountantController::class, 'verifyPayment'])->name('accountant.payment.verify');

    // Day Services Revenue Verification
    Route::get('/day-services/revenue', [\App\Http\Controllers\AccountantController::class, 'dayServicesRevenue'])->name('accountant.day-services.revenue');
    Route::post('/day-services/verify-day', [\App\Http\Controllers\AccountantController::class, 'verifyDayServicesRevenue'])->name('accountant.day-services.verify-day');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\AccountantController::class, 'reports'])->name('accountant.reports');

    Route::post('/logout', [AuthController::class, 'logout'])->name('accountant.logout');
});

/**
 * WAITER ROUTES
 */
Route::prefix('waiter')->group(function () {
    // Waiter Login 
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('waiter.login');

    Route::middleware(['auth:staff', 'role:waiter'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\WaiterController::class, 'dashboard'])->name('waiter.dashboard');
        Route::get('/sales-summary', [\App\Http\Controllers\WaiterController::class, 'salesSummary'])->name('waiter.sales-summary');
        Route::get('/active-bookings', [\App\Http\Controllers\WaiterController::class, 'getActiveBookings'])->name('waiter.active-bookings');
        Route::post('/order/store', [\App\Http\Controllers\WaiterController::class, 'storeOrder'])->name('waiter.order.store');
        Route::get('/orders', [\App\Http\Controllers\WaiterController::class, 'orders'])->name('waiter.orders');
        Route::get('/orders/{serviceRequest}/print-docket', [\App\Http\Controllers\WaiterController::class, 'printDocket'])->name('waiter.orders.print-docket');
        Route::get('/orders/print-group', [\App\Http\Controllers\WaiterController::class, 'printGroupDocket'])->name('waiter.orders.print-group');
        Route::post('/orders/{serviceRequest}/cancel', [\App\Http\Controllers\WaiterController::class, 'cancelOrder'])->name('waiter.orders.cancel');

        // Profile & Logout
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('waiter.profile');
        Route::post('/logout', [AuthController::class, 'logout'])->name('waiter.logout');
    });
});
Route::middleware(['check.auth'])->prefix('stock-requests')->group(function () {
    Route::get('/', [\App\Http\Controllers\StockRequestController::class, 'index'])->name('stock-requests.index');
    Route::get('/create', [\App\Http\Controllers\StockRequestController::class, 'create'])->name('stock-requests.create');
    Route::get('/row-template', [\App\Http\Controllers\StockRequestController::class, 'rowTemplate'])->name('stock-requests.row-template');
    Route::post('/', [\App\Http\Controllers\StockRequestController::class, 'store'])->name('stock-requests.store');

    // Batch distribution and printing
    Route::get('/batch/{batchId}/distribute', [\App\Http\Controllers\StockRequestController::class, 'batchDistribute'])->name('stock-requests.batch-distribute');
    Route::post('/batch/{batchId}/distribute', [\App\Http\Controllers\StockRequestController::class, 'batchDistribute'])->name('stock-requests.batch-distribute.submit');
    Route::get('/batch/{batchId}/print', [\App\Http\Controllers\StockRequestController::class, 'batchPrint'])->name('stock-requests.batch-print');
    Route::post('/{stockRequest}/pass-to-manager', [\App\Http\Controllers\StockRequestController::class, 'passToManager'])->name('stock-requests.pass-to-manager');
    Route::post('/{stockRequest}/approve', [\App\Http\Controllers\StockRequestController::class, 'approve'])->name('stock-requests.approve');
    Route::post('/{stockRequest}/reject', [\App\Http\Controllers\StockRequestController::class, 'reject'])->name('stock-requests.reject');
    Route::post('/{stockRequest}/distribute', [\App\Http\Controllers\StockRequestController::class, 'distribute'])->name('stock-requests.distribute');
    Route::get('/{stockRequest}/print', [\App\Http\Controllers\StockRequestController::class, 'printNote'])->name('stock-requests.print');
    Route::get('/pending-counts', [\App\Http\Controllers\StockRequestController::class, 'pendingCounts'])->name('stock-requests.pending-counts');
});

// Success page test
Route::get('/login-success-test', function () {
    return "<h1>LOGIN SUCCESSFUL!</h1><p>If you see this, authentication worked. Session ID: " . session()->getId() . "</p>";
})->name('login-success-test')->middleware('check.auth');

// Emergency Direct Login (for recovery)
Route::get('/emergency-direct-login/{token}', [App\Http\Controllers\EmergencyAuthController::class, 'emergencyDirectLogin'])->name('emergency-login');
