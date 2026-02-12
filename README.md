# PrimeLand Hotel Management System

A comprehensive hotel management system built with Laravel, featuring booking management, guest services, room management, and administrative tools.

## Features

- **Booking Management**: Complete booking system with online reservations
- **Guest Dashboard**: Customer portal for managing bookings and services
- **Staff Management**: Role-based access for Super Admin, Manager, and Reception staff
- **Room Management**: Room status tracking, cleaning schedules, and inventory
- **Payment Processing**: Integrated payment system with multiple payment methods
- **Service Requests**: Guest service request management
- **Issue Reports**: Issue tracking and resolution system
- **Email Notifications**: Automated email notifications for bookings, check-ins, and more
- **Feedback System**: Customer feedback collection and management
- **Exchange Rates**: Real-time currency exchange rate management

## Technology Stack

- **Backend**: Laravel 11.x
- **Frontend**: Blade Templates, Bootstrap, jQuery
- **Database**: MySQL
- **Email**: Laravel Mail

## Requirements

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM (for assets)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/AllyProf/PrimeLand_Hotel.git
cd PrimeLand_Hotel
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations:
```bash
php artisan migrate
```

7. Seed database (optional):
```bash
php artisan db:seed
```

8. Create storage link:
```bash
php artisan storage:link
```

9. Start development server:
```bash
php artisan serve
```

## Production Setup

For production deployment, ensure the following:

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Set `SESSION_SECURE_COOKIE=true` for HTTPS sites
3. Configure `APP_URL` correctly
4. Set proper file permissions:
```bash
chmod -R 755 storage bootstrap/cache
```

## License

Proprietary - All rights reserved

## Contact

PrimeLand Hotel
Website: https://primelandhotel.co.tz

---

Powered by EmCa Techonologies
