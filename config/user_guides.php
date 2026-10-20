<?php

/**
 * Contextual user manuals for dashboard menus.
 * Keys are route names; path_prefixes catch nested URLs.
 */
return [

    'path_prefixes' => [
        'bar-keeper/order-summary' => [
            'title' => 'Order Summary',
            'breadcrumb' => ['Counter Operations', 'Order Summary'],
            'overview' => 'Review live counter shifts, unpaid guests, period sales history, and cancelled orders for restaurant and bar sales.',
            'steps' => [
                'Use Daily / Weekly / Monthly filters (and the date picker) to choose the period you want to review.',
                'Open the Live Now tab to see counters with an active shift, or Sales History for closed-shift totals.',
                'Check Unpaid for guests who still owe, then download PDF reports when you need a printout.',
            ],
            'tip' => 'Restaurant managers see all counters. Counter staff only see their own sales.',
        ],
        'bar-keeper/dashboard' => [
            'title' => 'Counter POS',
            'breadcrumb' => ['Counter Operations', 'Counter POS'],
            'overview' => 'Sell drinks and food from the counter: open a shift (or pick a counter if you are a restaurant manager), take orders, settle payment, and close the shift.',
            'steps' => [
                'Open your shift (counter) or select an open counter shift (restaurant manager) before selling.',
                'Search an in-house guest or create a walk-in, add items, then complete payment or room charge.',
                'When finished, close the shift and hand over cash to the cashier.',
            ],
            'tip' => 'Orders placed by a restaurant manager show “Placed by” on the counter’s shift.',
        ],
        'bar-keeper/debts' => [
            'title' => 'Debt Management',
            'breadcrumb' => ['Counter Operations', 'Debt Management'],
            'overview' => 'Track unpaid counter guests and outstanding restaurant/bar balances.',
            'steps' => [
                'Search by guest name or item to find a debt.',
                'Review the amount due and which counter served the guest.',
            ],
            'tip' => 'Clear debts promptly so shift and reception totals stay accurate.',
        ],
        'reception/shift-management' => [
            'title' => 'Shift Management',
            'breadcrumb' => ['Finance', 'Shift Management'],
            'overview' => 'Open and close your reception shift, see today’s revenue breakdown, and hand cash to the cashier.',
            'steps' => [
                'Click Open New Shift if no shift is active.',
                'During the day, bookings and paid services are tracked on your open shift.',
                'Enter cash to submit, add notes if needed, then Close & Send to Cashier.',
            ],
            'tip' => 'You cannot track today’s cash correctly without an open shift.',
        ],
        'cashier' => [
            'title' => 'Cashier Desk',
            'breadcrumb' => ['Cashier', 'Dashboard'],
            'overview' => 'Receive cash from reception and counters, send collections to the accountant, and review closed shift reports.',
            'steps' => [
                'Open Receive & Send to accept handovers from reception or counters.',
                'Use Closed Shift Reports and Order Summary to verify totals.',
            ],
            'tip' => 'Always match physical cash to the expected cash figure before confirming a handover.',
        ],
    ],

    'pages' => [

        // ── Counter / Restaurant Manager ──────────────────────────
        'bar-keeper.dashboard' => [
            'title' => 'Counter POS',
            'breadcrumb' => ['Counter Operations', 'Counter POS'],
            'overview' => 'Point-of-sale for restaurant and bar. Serve in-house guests and walk-ins, take payment, and manage your shift.',
            'steps' => [
                'Open a shift (or select a counter with an open shift if you are a restaurant manager).',
                'Find a guest or start a walk-in order, add menu items, then complete or charge to room.',
                'Review shift sales at the bottom; close the shift and hand over cash when done.',
            ],
            'tip' => 'Never sell without an open shift — totals will not be tracked.',
            'markers' => [
                ['n' => 1, 'top' => '10%', 'left' => '82%'],
                ['n' => 2, 'top' => '28%', 'left' => '18%'],
                ['n' => 3, 'top' => '72%', 'left' => '45%'],
            ],
        ],
        'bar-keeper.order-summary' => [
            'title' => 'Order Summary',
            'breadcrumb' => ['Counter Operations', 'Order Summary'],
            'overview' => 'See sold items, unpaid guests, live/closed shifts, and cancellations for the selected period.',
            'steps' => [
                'Choose Daily, Weekly, or Monthly and pick a date if needed.',
                'Switch tabs: Live Now, Unpaid, Sales History, Cancelled.',
                'Download unpaid or period PDF reports from the action buttons.',
            ],
            'tip' => 'Live Now only shows open counter shifts; closed sales appear under Sales History.',
            'markers' => [
                ['n' => 1, 'top' => '14%', 'left' => '70%'],
                ['n' => 2, 'top' => '32%', 'left' => '25%'],
                ['n' => 3, 'top' => '32%', 'left' => '88%'],
            ],
        ],
        'bar-keeper.debts' => [
            'title' => 'Debt Management',
            'breadcrumb' => ['Counter Operations', 'Debt Management'],
            'overview' => 'List of unpaid counter orders and guests who still owe.',
            'steps' => [
                'Use search to filter by guest or item.',
                'Open a row to see who served the guest and how much is due.',
            ],
            'tip' => 'Managers and cashiers can see debts across all counters.',
        ],
        'bar-keeper.orders.index' => [
            'title' => 'Orders History',
            'breadcrumb' => ['Counter Operations', 'Orders History'],
            'overview' => 'Browse past counter orders and their payment status.',
            'steps' => [
                'Filter or scroll the list to find an order.',
                'Open an order to review items, guest, and payment details.',
            ],
            'tip' => 'Use Order Summary for period totals; use this page for individual order lookup.',
        ],
        'bar-keeper.stock.index' => [
            'title' => 'Counter Inventory',
            'breadcrumb' => ['Inventory', 'Counter Inventory'],
            'overview' => 'See stock available at the counter after transfers and sales.',
            'steps' => [
                'Scan the list for low stock items.',
                'Request or receive transfers when stock is low.',
            ],
            'tip' => 'Sales reduce counter stock automatically when orders are completed.',
        ],
        'bar-keeper.transfers.index' => [
            'title' => 'Stock Transfers',
            'breadcrumb' => ['Inventory', 'Stock Transfers'],
            'overview' => 'Receive stock sent to the counter from the store.',
            'steps' => [
                'Open pending transfers and confirm quantities received.',
                'Mark the transfer complete so inventory updates.',
            ],
            'tip' => 'Do not sell items you have not confirmed as received.',
        ],
        'bar-keeper.reports' => [
            'title' => 'Drinks Stock Sheet',
            'breadcrumb' => ['Reports', 'Drinks Stock Sheet'],
            'overview' => 'Printed-style stock and sales sheet for drinks and counter products.',
            'steps' => [
                'Select the period you need.',
                'Review opening, sales, and closing figures, then print if required.',
            ],
            'tip' => 'Compare this sheet with physical count at shift close.',
        ],
        'bar-keeper.products.index' => [
            'title' => 'Products',
            'breadcrumb' => ['Inventory', 'Products'],
            'overview' => 'Counter product list used when selling.',
            'steps' => [
                'Find a product and check price or availability.',
                'Ask a manager if a product price or recipe needs changing.',
            ],
            'tip' => 'Menu recipes control what appears for sale.',
        ],
        'bar-keeper.recorded-items' => [
            'title' => 'Usage Records',
            'breadcrumb' => ['Inventory', 'Usage Records'],
            'overview' => 'Track recorded usage of counter items outside normal sales.',
            'steps' => [
                'Review recorded usage entries for the period.',
                'Add a record only when stock was used without a POS sale.',
            ],
            'tip' => 'Keep usage notes clear for stock reconciliation.',
        ],
        'bar-keeper.profile' => [
            'title' => 'My Profile',
            'breadcrumb' => ['Account', 'My Profile'],
            'overview' => 'Update your name, photo, password, and notification preferences.',
            'steps' => [
                'Edit profile fields and save.',
                'Change password regularly and keep it private.',
            ],
            'tip' => 'Never share your login. Each sale is linked to your account.',
        ],

        // ── Kitchen / Restaurant ──────────────────────────────────
        'admin.recipes.index' => [
            'title' => 'Menu Recipes',
            'breadcrumb' => ['Kitchen & Restaurant', 'Menu Recipes'],
            'overview' => 'Manage recipes and menu items sold at the restaurant and counter.',
            'steps' => [
                'Open a recipe to edit ingredients, price, or availability.',
                'Add a new recipe when a new menu item is introduced.',
            ],
            'tip' => 'Inactive recipes will not appear on the counter POS.',
        ],
        'chef-master.inventory' => [
            'title' => 'Kitchen Inventory',
            'breadcrumb' => ['Kitchen & Restaurant', 'Kitchen Inventory'],
            'overview' => 'Monitor kitchen stock levels for cooking and prep.',
            'steps' => [
                'Check low-stock items first.',
                'Raise a shopping list or purchase request when stock is short.',
            ],
            'tip' => 'Keep kitchen and store counts aligned after deliveries.',
        ],
        'admin.restaurants.kitchen.orders' => [
            'title' => 'Live Kitchen Orders',
            'breadcrumb' => ['Kitchen & Restaurant', 'Live Kitchen Orders'],
            'overview' => 'See orders that need cooking or plating right now.',
            'steps' => [
                'Watch new tickets as they arrive.',
                'Update status when food is ready for service.',
            ],
            'tip' => 'Clear ready tickets quickly so waiters and counter staff can serve guests.',
        ],
        'admin.restaurants.shopping-list.index' => [
            'title' => 'Shopping Lists',
            'breadcrumb' => ['Kitchen & Restaurant', 'Shopping Lists'],
            'overview' => 'Build and track shopping lists for restaurant supplies.',
            'steps' => [
                'Create or open a shopping list.',
                'Mark items as purchased when stock arrives.',
            ],
            'tip' => 'Link lists to purchase requests for accountant approval when required.',
        ],
        'admin.restaurants.reports' => [
            'title' => 'Restaurant Sales',
            'breadcrumb' => ['Kitchen & Restaurant', 'Restaurant Sales'],
            'overview' => 'Sales reports for restaurant performance.',
            'steps' => [
                'Pick a date range.',
                'Review totals and export or print if needed.',
            ],
            'tip' => 'Compare restaurant sales with counter Order Summary for the same period.',
        ],
        'chef-master.reports' => [
            'title' => 'Kitchen Report',
            'breadcrumb' => ['Reports', 'Kitchen Report'],
            'overview' => 'Kitchen activity and consumption reports.',
            'steps' => [
                'Select the report period.',
                'Review usage and request restocking where needed.',
            ],
            'tip' => 'Share large variances with the restaurant manager.',
        ],
        'chef-master.dashboard' => [
            'title' => 'Kitchen Dashboard',
            'breadcrumb' => ['Kitchen', 'Dashboard'],
            'overview' => 'Overview of kitchen tasks, stock, and orders.',
            'steps' => [
                'Check pending kitchen orders and low stock alerts.',
                'Open Inventory or Live Orders from the sidebar for details.',
            ],
            'tip' => 'Start each shift by clearing overdue kitchen tickets.',
        ],

        // ── Reception ─────────────────────────────────────────────
        'reception.dashboard' => [
            'title' => 'Reception Dashboard',
            'breadcrumb' => ['Reception', 'Dashboard'],
            'overview' => 'Front-desk home: arrivals, departures, and quick links for bookings and services.',
            'steps' => [
                'Open your shift before processing paid activities.',
                'Use bookings, check-in/out, and payments from the sidebar.',
            ],
            'tip' => 'Keep the shift open for the whole desk period so cash is tracked.',
        ],
        'reception.shift-management' => [
            'title' => 'Shift Management',
            'breadcrumb' => ['Finance', 'Shift Management'],
            'overview' => 'Open/close reception shifts and hand cash to the cashier with a source breakdown.',
            'steps' => [
                'Open a new shift if none is active.',
                'Review Room Bookings vs Restaurant / Bar vs Day Services totals.',
                'Enter cash to submit and Close & Send to Cashier.',
            ],
            'tip' => 'Expected cash must match what you physically hand over.',
            'markers' => [
                ['n' => 1, 'top' => '22%', 'left' => '50%'],
                ['n' => 2, 'top' => '45%', 'left' => '70%'],
                ['n' => 3, 'top' => '35%', 'left' => '20%'],
            ],
        ],
        'reception.bookings' => [
            'title' => 'All Bookings',
            'breadcrumb' => ['Bookings & Front Desk', 'All Bookings'],
            'overview' => 'Search and manage hotel bookings.',
            'steps' => [
                'Filter by date or guest name.',
                'Open a booking to check in, extend, or update payment.',
            ],
            'tip' => 'Confirm room status before assigning a room at check-in.',
        ],
        'reception.bookings.manual.create' => [
            'title' => 'Manual Booking',
            'breadcrumb' => ['Bookings & Front Desk', 'Manual Booking'],
            'overview' => 'Create a booking for a walk-in or phone guest.',
            'steps' => [
                'Enter guest details and stay dates.',
                'Select a room and record payment as required.',
            ],
            'tip' => 'Double-check dates to avoid double-booking.',
        ],
        'reception.reservations.check-in' => [
            'title' => 'Check In',
            'breadcrumb' => ['Bookings & Front Desk', 'Check In'],
            'overview' => 'Check arriving guests into their rooms.',
            'steps' => [
                'Find today’s arrival.',
                'Confirm ID and payment, then complete check-in.',
            ],
            'tip' => 'Ensure the room is clean before handing over keys.',
        ],
        'reception.reservations.check-out' => [
            'title' => 'Check Out',
            'breadcrumb' => ['Bookings & Front Desk', 'Check Out'],
            'overview' => 'Settle bills and check guests out.',
            'steps' => [
                'Open the departing booking.',
                'Clear outstanding room and restaurant charges, then check out.',
            ],
            'tip' => 'Ask the counter about unpaid restaurant bills before closing the folio.',
        ],
        'reception.guests' => [
            'title' => 'Guest Directory',
            'breadcrumb' => ['Reception', 'Guests'],
            'overview' => 'Search registered guests and their stay history.',
            'steps' => [
                'Search by name, phone, or email.',
                'Open a guest profile to view bookings.',
            ],
            'tip' => 'Keep contact details up to date for invoicing.',
        ],
        'reception.payments' => [
            'title' => 'All Payments',
            'breadcrumb' => ['Finance', 'All Payments'],
            'overview' => 'View payments collected at reception.',
            'steps' => [
                'Filter by date or method (cash, M-Pesa, other).',
                'Match payments to bookings or services when reconciling.',
            ],
            'tip' => 'Cash payments should appear on your open shift totals.',
        ],
        'reception.revenue-handovers' => [
            'title' => 'Handovers History',
            'breadcrumb' => ['Finance', 'Handovers History'],
            'overview' => 'History of cash handovers sent to the cashier.',
            'steps' => [
                'Find a past handover by date.',
                'Open it to see amounts and status.',
            ],
            'tip' => 'Keep handover notes when cash differs from expected.',
        ],
        'reception.day-services.index' => [
            'title' => 'Day Services',
            'breadcrumb' => ['Day Services', 'All'],
            'overview' => 'Manage non-room day services (parking, garden, conference, etc.).',
            'steps' => [
                'Create or open a day service booking.',
                'Record payment so it counts on your shift.',
            ],
            'tip' => 'Use the correct service type so reports stay accurate.',
        ],
        'reception.service-requests' => [
            'title' => 'Service Requests',
            'breadcrumb' => ['Reception', 'Service Requests'],
            'overview' => 'Guest service requests from rooms or reception.',
            'steps' => [
                'Open pending requests and assign or complete them.',
                'Update status when the guest is served.',
            ],
            'tip' => 'Urgent requests should be handled before routine ones.',
        ],
        'reception.reports' => [
            'title' => 'Operational Reports',
            'breadcrumb' => ['Reports', 'Operational'],
            'overview' => 'Operational reports for front desk activity.',
            'steps' => [
                'Select report type and date range.',
                'Export or print for management review.',
            ],
            'tip' => 'Run end-of-day reports after closing your shift.',
        ],
        'reception.reports.financial' => [
            'title' => 'Financial Reports',
            'breadcrumb' => ['Reports', 'Financial'],
            'overview' => 'Financial summaries for reception revenue.',
            'steps' => [
                'Choose the period.',
                'Review cash vs digital payments.',
            ],
            'tip' => 'Reconcile with cashier collections for the same dates.',
        ],
        'reception.profile' => [
            'title' => 'My Profile',
            'breadcrumb' => ['Account', 'My Profile'],
            'overview' => 'Manage your reception staff profile and password.',
            'steps' => [
                'Update details and save.',
                'Change your password when required.',
            ],
            'tip' => 'Log out when leaving the desk.',
        ],

        // ── Cashier ───────────────────────────────────────────────
        'cashier.dashboard' => [
            'title' => 'Cashier Dashboard',
            'breadcrumb' => ['Cashier', 'Dashboard'],
            'overview' => 'Home for cash collections from reception and counters.',
            'steps' => [
                'Check pending handovers waiting for your confirmation.',
                'Open Receive & Send to process cash movement.',
            ],
            'tip' => 'Count cash before accepting any handover.',
        ],
        'cashier.reception.collections' => [
            'title' => 'Reception Collections',
            'breadcrumb' => ['Receive & Send', 'Reception'],
            'overview' => 'Receive cash handovers from reception shifts.',
            'steps' => [
                'Open a pending reception handover.',
                'Verify amount and confirm receipt.',
            ],
            'tip' => 'Note any shortage or surplus before confirming.',
        ],
        'cashier.accountant.handovers' => [
            'title' => 'Accountant Handovers',
            'breadcrumb' => ['Receive & Send', 'Accountant'],
            'overview' => 'Send collected cash onward to the accountant.',
            'steps' => [
                'Create or open a handover to accountant.',
                'Enter amounts and submit.',
            ],
            'tip' => 'Keep bank deposit slips with the handover record.',
        ],
        'cashier.shift-reports' => [
            'title' => 'Closed Shift Reports',
            'breadcrumb' => ['Cashier', 'Closed Shift Reports'],
            'overview' => 'Review closed counter and reception shift reports.',
            'steps' => [
                'Filter by date or staff.',
                'Open a report to verify sales and cash.',
            ],
            'tip' => 'Investigate large variances before accepting cash.',
        ],
        'cashier.order-summary' => [
            'title' => 'Order Summary',
            'breadcrumb' => ['Cashier', 'Order Summary'],
            'overview' => 'Counter sales summary across shifts for cashier verification.',
            'steps' => [
                'Select the period.',
                'Review live and history tabs, then unpaid guests if needed.',
            ],
            'tip' => 'Use this together with closed shift reports when reconciling.',
        ],
        'cashier.debts' => [
            'title' => 'Debt Management',
            'breadcrumb' => ['Cashier', 'Debt Management'],
            'overview' => 'Outstanding unpaid counter guests.',
            'steps' => [
                'Search for a guest debt.',
                'Follow up with reception or counter to collect payment.',
            ],
            'tip' => 'Do not close the day with unexplained high unpaid totals.',
        ],
        'cashier.profile' => [
            'title' => 'My Profile',
            'breadcrumb' => ['Account', 'My Profile'],
            'overview' => 'Your cashier account settings.',
            'steps' => [
                'Update profile information.',
                'Change password regularly.',
            ],
            'tip' => 'Always log out on shared computers.',
        ],

        // ── Storekeeper ───────────────────────────────────────────
        'storekeeper.dashboard' => [
            'title' => 'Store Dashboard',
            'breadcrumb' => ['Store', 'Dashboard'],
            'overview' => 'Overview of store stock, requests, and supplier activity.',
            'steps' => [
                'Check low stock and pending stock requests.',
                'Process transfers and supplier receipts from the sidebar.',
            ],
            'tip' => 'Confirm deliveries before updating stock quantities.',
        ],
        'storekeeper.inventory' => [
            'title' => 'Store Inventory',
            'breadcrumb' => ['Store', 'Inventory'],
            'overview' => 'Main store stock levels.',
            'steps' => [
                'Search a product.',
                'Adjust or investigate variances with a supervisor.',
            ],
            'tip' => 'Physical counts should match the system after receiving supplier orders.',
        ],
        'storekeeper.products.index' => [
            'title' => 'Products',
            'breadcrumb' => ['Store', 'Products'],
            'overview' => 'Master product list for the store.',
            'steps' => [
                'Add or edit products and package ratios.',
                'Keep units consistent (crate, bottle, kg).',
            ],
            'tip' => 'Wrong package ratios cause wrong stock after receiving.',
        ],
        'supplier-orders.index' => [
            'title' => 'Supplier Orders',
            'breadcrumb' => ['Procurement', 'Supplier Orders'],
            'overview' => 'Orders placed with suppliers and goods receiving.',
            'steps' => [
                'Open an order and receive items into stock.',
                'Select the registered product for each line before confirming.',
            ],
            'tip' => 'Receiving updates store stock automatically using package ratios.',
        ],
        'lpo.index' => [
            'title' => 'LPOs',
            'breadcrumb' => ['Procurement', 'LPOs'],
            'overview' => 'Local purchase orders for approved buying.',
            'steps' => [
                'Open an LPO to review status.',
                'Follow approval flow before committing purchases.',
            ],
            'tip' => 'Do not buy outside an approved LPO when policy requires one.',
        ],
        'admin.suppliers.index' => [
            'title' => 'Suppliers',
            'breadcrumb' => ['Procurement', 'Suppliers'],
            'overview' => 'Supplier directory and contacts.',
            'steps' => [
                'Add a new supplier or edit existing details.',
                'Keep phone and account info current.',
            ],
            'tip' => 'Inactive suppliers should not be used on new orders.',
        ],
        'stock-requests.index' => [
            'title' => 'Stock Requests',
            'breadcrumb' => ['Inventory', 'Stock Requests'],
            'overview' => 'Requests from counters or departments for stock.',
            'steps' => [
                'Open a pending request.',
                'Approve and transfer stock when available.',
            ],
            'tip' => 'Partial issues should be noted on the request.',
        ],
        'storekeeper.announcements.index' => [
            'title' => 'Announcements',
            'breadcrumb' => ['Store', 'Announcements'],
            'overview' => 'Notices shown on staff dashboards (for example low stock).',
            'steps' => [
                'Create a clear short message.',
                'Set active dates so the ticker shows when needed.',
            ],
            'tip' => 'Remove outdated announcements so staff trust the ticker.',
        ],
        'storekeeper.profile' => [
            'title' => 'My Profile',
            'breadcrumb' => ['Account', 'My Profile'],
            'overview' => 'Storekeeper account settings.',
            'steps' => [
                'Update your profile.',
                'Change password when required.',
            ],
            'tip' => 'Log out after finishing stock counts.',
        ],

        // ── Accountant ────────────────────────────────────────────
        'accountant.dashboard' => [
            'title' => 'Accountant Dashboard',
            'breadcrumb' => ['Accounts', 'Dashboard'],
            'overview' => 'Finance overview: shopping lists, payments, and cashier collections.',
            'steps' => [
                'Review pending approvals and collections.',
                'Open reports for period reconciliation.',
            ],
            'tip' => 'Match cashier handovers to bank deposits daily.',
        ],
        'accountant.shopping-lists' => [
            'title' => 'Shopping Lists',
            'breadcrumb' => ['Accounts', 'Shopping Lists'],
            'overview' => 'Approve or review shopping lists for purchasing.',
            'steps' => [
                'Open a list and check quantities and costs.',
                'Approve or return with comments.',
            ],
            'tip' => 'High-value items may need manager confirmation.',
        ],
        'accountant.payments' => [
            'title' => 'Payments',
            'breadcrumb' => ['Accounts', 'Payments'],
            'overview' => 'Track supplier and operational payments.',
            'steps' => [
                'Filter by date or payee.',
                'Record or confirm payment status.',
            ],
            'tip' => 'Attach reference numbers for audit trails.',
        ],
        'accountant.cashier-collections' => [
            'title' => 'Cashier Collections',
            'breadcrumb' => ['Accounts', 'Cashier Collections'],
            'overview' => 'Cash received from the cashier.',
            'steps' => [
                'Open a pending collection.',
                'Confirm amount and mark received.',
            ],
            'tip' => 'Investigate shortages before confirming.',
        ],
        'accountant.reports' => [
            'title' => 'Accountant Reports',
            'breadcrumb' => ['Accounts', 'Reports'],
            'overview' => 'Financial reports for management.',
            'steps' => [
                'Select report and period.',
                'Export for filing or sharing.',
            ],
            'tip' => 'Month-end reports should be locked after review.',
        ],
        'accountant.profile' => [
            'title' => 'My Profile',
            'breadcrumb' => ['Account', 'My Profile'],
            'overview' => 'Your accountant profile settings.',
            'steps' => [
                'Update details and save.',
                'Change password regularly.',
            ],
            'tip' => 'Use a strong unique password.',
        ],

        // ── Housekeeper ───────────────────────────────────────────
        'housekeeper.dashboard' => [
            'title' => 'Housekeeping Dashboard',
            'breadcrumb' => ['Housekeeping', 'Dashboard'],
            'overview' => 'Rooms to clean, issues, and HK stock at a glance.',
            'steps' => [
                'Open rooms that need cleaning first.',
                'Report room issues when you find them.',
            ],
            'tip' => 'Mark rooms clean only after inspection.',
        ],
        'housekeeper.rooms.cleaning' => [
            'title' => 'Needs Cleaning',
            'breadcrumb' => ['Rooms', 'Needs Cleaning'],
            'overview' => 'Rooms waiting to be cleaned.',
            'steps' => [
                'Select a room and start cleaning.',
                'Mark complete when ready for guests.',
            ],
            'tip' => 'Priority rooms are usually check-ins for today.',
        ],
        'housekeeper.rooms.status' => [
            'title' => 'Room Status',
            'breadcrumb' => ['Rooms', 'Room Status'],
            'overview' => 'Current cleanliness and occupancy status of rooms.',
            'steps' => [
                'Filter by status.',
                'Update a room when its state changes.',
            ],
            'tip' => 'Reception relies on this status for check-in.',
        ],
        'housekeeper.room-issues' => [
            'title' => 'Room Issues',
            'breadcrumb' => ['Housekeeping', 'Room Issues'],
            'overview' => 'Maintenance or damage issues found in rooms.',
            'steps' => [
                'Log a new issue with clear details.',
                'Update when the issue is fixed.',
            ],
            'tip' => 'Photos and room numbers help maintenance respond faster.',
        ],
        'housekeeper.inventory' => [
            'title' => 'HK Inventory',
            'breadcrumb' => ['Housekeeping', 'Inventory'],
            'overview' => 'Housekeeping supplies on hand.',
            'steps' => [
                'Check low linen and amenity stock.',
                'Raise a stock or purchase request when needed.',
            ],
            'tip' => 'Do not wait until stock is zero before requesting.',
        ],
        'housekeeper.reports' => [
            'title' => 'HK Reports',
            'breadcrumb' => ['Housekeeping', 'Reports'],
            'overview' => 'Housekeeping performance and room turnaround reports.',
            'steps' => [
                'Select dates.',
                'Review cleaned rooms and outstanding issues.',
            ],
            'tip' => 'Share daily summaries with reception.',
        ],
        'housekeeper.profile' => [
            'title' => 'My Profile',
            'breadcrumb' => ['Account', 'My Profile'],
            'overview' => 'Housekeeper account settings.',
            'steps' => [
                'Update your profile.',
                'Change password when asked.',
            ],
            'tip' => 'Log out on shared tablets.',
        ],

        // ── Waiter ────────────────────────────────────────────────
        'waiter.dashboard' => [
            'title' => 'Waiter Dashboard',
            'breadcrumb' => ['Waiter', 'Dashboard'],
            'overview' => 'Take and follow up table or guest orders.',
            'steps' => [
                'Create or open an order for a guest.',
                'Send items to kitchen/counter and track status.',
            ],
            'tip' => 'Confirm special requests with the kitchen before promising guests.',
        ],
        'waiter.sales-summary' => [
            'title' => 'Sales Summary',
            'breadcrumb' => ['Waiter', 'Sales Summary'],
            'overview' => 'Your sales performance summary.',
            'steps' => [
                'Select the period.',
                'Review totals and closed tickets.',
            ],
            'tip' => 'Ask a supervisor if a closed ticket looks incorrect.',
        ],
        'waiter.orders' => [
            'title' => 'Orders',
            'breadcrumb' => ['Waiter', 'Orders'],
            'overview' => 'List of orders you are handling.',
            'steps' => [
                'Open an active order to add items or serve.',
                'Close when the guest is finished and paid.',
            ],
            'tip' => 'Never leave unpaid tickets open overnight without notice.',
        ],
        'waiter.profile' => [
            'title' => 'My Profile',
            'breadcrumb' => ['Account', 'My Profile'],
            'overview' => 'Waiter profile and password.',
            'steps' => [
                'Update your details.',
                'Save changes.',
            ],
            'tip' => 'Keep your PIN/password private.',
        ],

        // ── Admin / Super Admin hubs ──────────────────────────────
        'admin.dashboard' => [
            'title' => 'Manager Dashboard',
            'breadcrumb' => ['Management', 'Dashboard'],
            'overview' => 'Hotel and restaurant management overview.',
            'steps' => [
                'Review KPIs and alerts on the dashboard.',
                'Open modules from the sidebar (rooms, staff, reports).',
            ],
            'tip' => 'Use Order Summary and financial reports for daily control.',
        ],
        'super_admin.dashboard' => [
            'title' => 'Super Admin Dashboard',
            'breadcrumb' => ['System', 'Dashboard'],
            'overview' => 'Full system administration: users, roles, and configuration.',
            'steps' => [
                'Manage users and roles carefully.',
                'Review system-wide reports and settings.',
            ],
            'tip' => 'Prefer least privilege when creating staff accounts.',
        ],
        'stock-requests.index' => [
            'title' => 'Stock Requests',
            'breadcrumb' => ['Inventory', 'Stock Requests'],
            'overview' => 'Department stock requests and fulfilment.',
            'steps' => [
                'Create or review a request.',
                'Fulfil from store when approved.',
            ],
            'tip' => 'Always record what was actually issued.',
        ],
    ],
];
