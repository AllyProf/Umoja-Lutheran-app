# PrimeLand Hotel Management System - Module Documentation

## System Overview
PrimeLand Hotel Management System is a comprehensive hotel management solution built with Laravel, designed to manage all aspects of hotel operations from guest bookings to staff management, inventory control, and financial reporting.

---

## 1. AUTHENTICATION & USER MANAGEMENT MODULE

### Features:
- **Multi-Role Authentication System**
  - Super Admin
  - Admin/Manager
  - Receptionist
  - Bar Keeper
  - Head Chef
  - Housekeeper
  
- **Security Features**
  - Two-Factor Authentication (2FA) with OTP
  - Failed login attempt tracking and account lockout
  - Password reset functionality
  - Session management
  - Activity logging

### Key Components:
- **Controllers**: `AuthController.php`, `PasswordResetController.php`
- **Models**: `User.php`, `Staff.php`, `Role.php`, `Permission.php`, `LoginOtp.php`, `FailedLoginAttempt.php`
- **Features**:
  - Email-based OTP verification
  - Role-based access control (RBAC)
  - Profile management
  - Password encryption

---

## 2. BOOKING & RESERVATION MODULE

### Features:
- **Online Booking System**
  - Real-time room availability checking
  - Multi-currency support (USD/TZS)
  - Dynamic pricing
  - Guest information management
  - Booking confirmation emails
  
- **Booking Management**
  - Check-in/Check-out processing
  - Booking modifications
  - Cancellations and refunds
  - Early check-in/late check-out
  - Booking history tracking

### Key Components:
- **Controllers**: `BookingController.php`, `ReceptionController.php`
- **Models**: `Booking.php`, `Guest.php`, `BookingGuest.php`
- **Payment Integration**:
  - Stripe payment gateway
  - M-Pesa integration
  - Cash payments
  - Partial payments support
  - Payment tracking and receipts

---

## 3. ROOM MANAGEMENT MODULE

### Features:
- **Room Inventory**
  - Room types and categories
  - Room status tracking (Available, Occupied, Maintenance, Cleaning)
  - Room pricing management
  - Room amenities tracking
  
- **Room Operations**
  - Room assignment
  - Room transfers
  - Maintenance scheduling
  - Cleaning status updates

### Key Components:
- **Controllers**: `RoomController.php`
- **Models**: `Room.php`, `RoomCleaningLog.php`, `RoomIssue.php`
- **Room Types**:
  - Single rooms
  - Double rooms
  - Suites
  - Family rooms

---

## 4. HOUSEKEEPING MODULE

### Features:
- **Cleaning Management**
  - Daily cleaning schedules
  - Room cleaning logs
  - Cleaning status tracking
  - Priority cleaning assignments
  
- **Inventory Management**
  - Housekeeping supplies tracking
  - Stock level monitoring
  - Usage recording
  - Reorder alerts

### Key Components:
- **Controllers**: `HousekeeperController.php`
- **Models**: `RoomCleaningLog.php`, `HousekeepingInventoryItem.php`, `InventoryStockMovement.php`
- **Features**:
  - Task assignment
  - Completion tracking
  - Performance monitoring
  - Issue reporting

---

## 5. BAR & RESTAURANT MODULE

### Features:
- **Bar Operations**
  - Drink inventory management
  - Sales recording (walk-in and ceremony-linked)
  - Stock tracking
  - Price management
  
- **Restaurant Operations**
  - Food menu management
  - Order processing
  - Kitchen integration
  - Service tracking

### Key Components:
- **Controllers**: `BarKeeperController.php`, `KitchenController.php`
- **Models**: `Product.php`, `ProductVariant.php`, `ServiceRequest.php`
- **Features**:
  - Point of Sale (POS) functionality
  - Ceremony consumption tracking
  - Walk-in sales
  - Revenue reporting
  - Stock depletion alerts

---

## 6. KITCHEN & FOOD MANAGEMENT MODULE

### Features:
- **Kitchen Operations**
  - Food inventory management
  - Recipe management
  - Ingredient tracking
  - Food preparation monitoring
  
- **Menu Management**
  - Menu item creation
  - Pricing
  - Recipe ingredients
  - Portion control

### Key Components:
- **Controllers**: `KitchenController.php`, `KitchenOrderController.php`, `RecipeController.php`
- **Models**: `KitchenInventoryItem.php`, `Recipe.php`, `RecipeIngredient.php`, `KitchenStockMovement.php`
- **Features**:
  - Walk-in food orders
  - Ceremony food usage
  - Stock consumption tracking
  - Recipe costing

---

## 7. INVENTORY & STOCK MANAGEMENT MODULE

### Features:
- **Stock Control**
  - Multi-department inventory (Bar, Kitchen, Housekeeping)
  - Stock receipt recording
  - Stock transfers between departments
  - Stock movement tracking
  
- **Purchase Management**
  - Purchase requests
  - Purchase deadlines
  - Supplier management
  - Stock receipt verification

### Key Components:
- **Controllers**: `StockReceiptController.php`, `StockTransferController.php`, `PurchaseRequestController.php`
- **Models**: `StockReceipt.php`, `StockTransfer.php`, `PurchaseRequest.php`, `Supplier.php`
- **Features**:
  - Automated reorder points
  - Stock valuation
  - Expiry tracking
  - Wastage recording

---

## 8. CEREMONY & EVENT MANAGEMENT MODULE

### Features:
- **Event Booking**
  - Ceremony reservations
  - Venue management
  - Service package selection
  - Guest capacity planning
  
- **Service Tracking**
  - Food consumption recording
  - Bar consumption recording
  - Service delivery monitoring
  - Event billing

### Key Components:
- **Controllers**: `DayServiceController.php`, `ServiceCatalogController.php`
- **Models**: `DayService.php`, `Service.php`, `ServiceCatalog.php`
- **Features**:
  - Package pricing
  - Consumption tracking
  - Payment management
  - Receipt generation

---

## 9. FINANCIAL MANAGEMENT MODULE

### Features:
- **Payment Processing**
  - Multiple payment methods
  - Payment tracking
  - Receipt generation
  - Refund processing
  
- **Revenue Management**
  - Room revenue
  - Bar revenue
  - Restaurant revenue
  - Ceremony revenue
  - Total revenue reporting

### Key Components:
- **Controllers**: `PaymentController.php`, `ReportController.php`
- **Models**: `Booking.php`, `DayService.php`, `ProductVariant.php`
- **Features**:
  - Multi-currency support
  - Exchange rate management
  - Payment status tracking
  - Financial reports

---

## 10. REPORTING & ANALYTICS MODULE

### Features:
- **Operational Reports**
  - Daily sales reports
  - Occupancy reports
  - Revenue reports
  - Stock reports
  
- **Financial Reports**
  - Income statements
  - Payment summaries
  - Outstanding balances
  - Tax reports
  
- **Performance Reports**
  - Staff performance
  - Room utilization
  - Service efficiency
  - Customer satisfaction

### Key Components:
- **Controllers**: `ReportController.php`, `PurchaseReportController.php`
- **Features**:
  - Date range filtering
  - Export to PDF/Excel
  - Graphical visualizations
  - Customizable reports

---

## 11. CUSTOMER RELATIONSHIP MODULE

### Features:
- **Guest Management**
  - Guest profiles
  - Booking history
  - Preferences tracking
  - Contact information
  
- **Communication**
  - Email notifications
  - Booking confirmations
  - Payment receipts
  - Newsletter subscriptions

### Key Components:
- **Controllers**: `FeedbackController.php`, `NewsletterController.php`
- **Models**: `Guest.php`, `Feedback.php`, `NewsletterSubscription.php`
- **Features**:
  - Feedback collection
  - Newsletter management
  - Guest communication history

---

## 12. MAINTENANCE & ISSUE TRACKING MODULE

### Features:
- **Issue Reporting**
  - Room issues
  - Equipment problems
  - Maintenance requests
  - Priority assignment
  
- **Issue Management**
  - Issue tracking
  - Assignment to staff
  - Resolution monitoring
  - History logging

### Key Components:
- **Controllers**: `IssueReportController.php`
- **Models**: `IssueReport.php`, `RoomIssue.php`
- **Features**:
  - Photo attachments
  - Status updates
  - Resolution tracking
  - Preventive maintenance scheduling

---

## 13. NOTIFICATION SYSTEM MODULE

### Features:
- **Real-time Notifications**
  - Booking notifications
  - Payment alerts
  - Stock alerts
  - Task assignments
  
- **Notification Types**
  - In-app notifications
  - Email notifications
  - SMS notifications (configurable)

### Key Components:
- **Controllers**: `NotificationController.php`
- **Models**: `Notification.php`
- **Features**:
  - Read/unread status
  - Notification history
  - User preferences
  - Automated triggers

---

## 14. ADMIN & SUPER ADMIN MODULE

### Features:
- **System Administration**
  - User management
  - Role and permission management
  - System settings
  - Hotel configuration
  
- **Super Admin Features**
  - Multi-hotel management
  - System-wide settings
  - Advanced reporting
  - Database management

### Key Components:
- **Controllers**: `AdminController.php`, `SuperAdminController.php`
- **Models**: `HotelSetting.php`, `Company.php`
- **Features**:
  - Staff management
  - Access control
  - System logs
  - Backup and restore

---

## 15. LANDING PAGE & PUBLIC WEBSITE MODULE

### Features:
- **Public Website**
  - Home page
  - About us
  - Services showcase
  - Room gallery
  - Contact information
  
- **Interactive Features**
  - Online booking form
  - Newsletter subscription
  - Feedback form
  - Live chat integration (Tawk.to)
  - WhatsApp integration
  - Google Maps integration

### Key Components:
- **Views**: `landing_page_views/*`
- **Features**:
  - Responsive design
  - SEO optimization
  - Social media integration
  - Image galleries
  - Video tours

---

## 16. ACTIVITY LOGGING & AUDIT MODULE

### Features:
- **Activity Tracking**
  - User actions logging
  - System events
  - Data modifications
  - Login history
  
- **Audit Trail**
  - Change tracking
  - User accountability
  - Security monitoring
  - Compliance reporting

### Key Components:
- **Models**: `ActivityLog.php`, `SystemLog.php`
- **Features**:
  - Comprehensive logging
  - Search and filter
  - Export capabilities
  - Retention policies

---

## 17. EXCHANGE RATE MANAGEMENT MODULE

### Features:
- **Currency Management**
  - USD/TZS exchange rates
  - Automatic rate updates
  - Manual rate override
  - Historical rate tracking

### Key Components:
- **Controllers**: `ExchangeRateController.php`
- **Models**: `HotelSetting.php`
- **Features**:
  - API integration for live rates
  - Fallback rates
  - Rate change notifications

---

## 18. SHOPPING LIST & PROCUREMENT MODULE

### Features:
- **Shopping Lists**
  - Automated list generation
  - Manual list creation
  - Item categorization
  - Supplier assignment
  
- **Procurement**
  - Purchase order creation
  - Supplier management
  - Delivery tracking
  - Invoice matching

### Key Components:
- **Controllers**: `PurchaseRequestController.php`, `SupplierController.php`
- **Models**: `ShoppingList.php`, `ShoppingListItem.php`, `PurchaseDeadline.php`
- **Features**:
  - Deadline management
  - Budget tracking
  - Approval workflows

---

## TECHNICAL SPECIFICATIONS

### Technology Stack:
- **Backend**: Laravel 10.x (PHP 8.1+)
- **Frontend**: Blade Templates, Bootstrap, jQuery
- **Database**: MySQL
- **Payment Gateways**: Stripe, M-Pesa
- **Email**: SMTP (Gmail, SendGrid)
- **File Storage**: Local filesystem
- **Authentication**: Laravel Sanctum/Session

### Security Features:
- CSRF protection
- XSS prevention
- SQL injection protection
- Password hashing (bcrypt)
- Role-based access control
- Two-factor authentication
- Session management
- Failed login protection

### Performance Features:
- Database query optimization
- Caching (Redis/File)
- Lazy loading
- Image optimization
- Minification of assets

---

## DATABASE STRUCTURE

### Core Tables:
1. **users** - System users and authentication
2. **staff** - Staff member details
3. **roles** - User roles
4. **permissions** - System permissions
5. **bookings** - Room reservations
6. **guests** - Guest information
7. **rooms** - Room inventory
8. **products** - Bar/restaurant items
9. **product_variants** - Item variations and pricing
10. **day_services** - Ceremony bookings
11. **services** - Service catalog
12. **kitchen_inventory_items** - Kitchen stock
13. **housekeeping_inventory_items** - Housekeeping supplies
14. **stock_receipts** - Stock receiving records
15. **stock_transfers** - Inter-department transfers
16. **purchase_requests** - Purchase requisitions
17. **notifications** - User notifications
18. **activity_logs** - System activity tracking
19. **feedback** - Customer feedback
20. **newsletter_subscriptions** - Newsletter subscribers

---

## API ENDPOINTS

### Public APIs:
- `/api/check-availability` - Room availability check
- `/api/exchange-rate` - Current exchange rate
- `/api/newsletter/subscribe` - Newsletter subscription

### Authenticated APIs:
- `/api/bookings` - Booking management
- `/api/rooms` - Room operations
- `/api/inventory` - Stock management
- `/api/reports` - Report generation
- `/api/notifications` - Notification management

---

## DEPLOYMENT REQUIREMENTS

### Server Requirements:
- PHP 8.1 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer
- Node.js and NPM (for asset compilation)

### PHP Extensions:
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD or Imagick

### Recommended:
- SSL certificate
- Redis for caching
- Supervisor for queue workers
- Backup solution
- Monitoring tools

---

## FUTURE ENHANCEMENTS

### Planned Features:
1. Mobile application (iOS/Android)
2. Advanced analytics dashboard
3. AI-powered pricing optimization
4. Integration with OTAs (Booking.com, Airbnb)
5. Loyalty program management
6. Advanced CRM features
7. Multi-property management
8. Channel manager integration
9. Revenue management system
10. Guest self-service portal

---

## SUPPORT & MAINTENANCE

### Documentation:
- User manuals for each role
- API documentation
- Database schema documentation
- Deployment guides

### Training:
- Staff training materials
- Video tutorials
- Quick reference guides

### Maintenance:
- Regular security updates
- Bug fixes
- Feature enhancements
- Performance optimization

---

**Document Version**: 1.0  
**Last Updated**: February 4, 2026  
**System Version**: PrimeLand Hotel Management System v1.0
