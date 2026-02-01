# 🏨 BezTower and Residences - Hotel Management System

A comprehensive hotel management system built with Laravel 11/12, featuring role-based access control, room management, booking system, payment verification, housekeeping management, and detailed reporting.

## 📋 Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [User Roles & Permissions](#user-roles--permissions)
- [Features Overview](#features-overview)
- [Database Structure](#database-structure)
- [Usage Guide](#usage-guide)
- [API Documentation](#api-documentation)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### Admin Dashboard
- 📊 Real-time statistics and analytics
- 📈 Revenue tracking with Chart.js visualizations
- 📉 Occupancy rate trends (12-month historical data)
- 📅 Daily arrivals and departures tracking
- 🔔 Pending actions notifications

### Room Management
- ➕ Create, edit, and delete rooms
- 🏷️ Room categorization by type (Standard, Deluxe, Suite, etc.)
- 🛏️ Amenities management (WiFi, AC, TV, etc.)
- 📸 Room photo uploads
- 🎨 Status badges (Available, Occupied, Maintenance)
- 🔍 Advanced filtering and search

### Booking System
- 📝 Complete booking lifecycle management
- ✅ Status tracking (Pending, Confirmed, Checked In, Checked Out, Cancelled)
- 👤 Guest profile integration
- 💰 Automated pricing calculations
- 📧 Email notifications
- 🔄 Booking reference generation

### Payment Verification
- 💳 Multiple payment methods support (GCash, Bank Transfer, Credit Card, Cash)
- 📤 Proof of payment upload
- ✔️ Admin payment verification system
- ❌ Payment rejection with reason
- 📧 Automated email notifications (approved/rejected)
- 💵 Revenue tracking and reporting

### Guest Management
- 👥 Comprehensive guest profiles
- 📜 Booking history tracking
- 💰 Total spending analytics
- 📊 Payment status overview
- ✏️ Guest information updates
- 🔍 Guest search and filtering

### Housekeeping Management
- 🧹 Room cleaning status tracking
- ⏱️ In-progress monitoring
- 📝 Housekeeping notes
- 🔄 Status updates (Clean, Dirty, In Progress)
- 📋 Daily housekeeping overview

### Reports & Analytics
- 📊 Revenue reports (daily, monthly, yearly)
- 📈 Occupancy reports
- 💹 Booking trends analysis
- 📥 CSV export functionality
- 📅 Date range filtering
- 📊 Visual charts and graphs

### User Management
- 👤 User account creation and management
- 🔐 Role assignment (Admin, Manager, Receptionist)
- ✏️ User profile updates
- 🔒 Password management
- 👁️ User activity tracking

## 🛠️ Technologies Used

- **Backend Framework:** Laravel 11
- **Frontend:** Blade Templates
- **Database:** MySQL
- **Authentication:** Laravel Breeze
- **Styling:** Inline CSS with Custom Design System
- **Charts:** Chart.js
- **Package Manager:** Composer, NPM
- **Testing:** Pest PHP
- **Email:** Laravel Mail

## 💻 System Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- NPM or Yarn
- MySQL >= 8.0
- Apache/Nginx Web Server

## 📥 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/beztower-hotel-management.git
cd beztower-hotel-management
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=beztower_hotel
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations and Seeders

```bash
# Run database migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### 6. Create Storage Symlink

```bash
php artisan storage:link
```

### 7. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## ⚙️ Configuration

### Mail Configuration

Configure your email settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@beztower.com
MAIL_FROM_NAME="BezTower and Residences"
```

### File Uploads

Maximum file size is set to 5MB for payment proofs and room photos. Adjust in `php.ini` if needed:

```ini
upload_max_filesize = 5M
post_max_size = 5M
```

## 👥 User Roles & Permissions

### Admin
- Full system access
- User management
- Room creation/deletion
- Payment verification
- Report generation
- System settings

### Manager
- Booking management
- Guest management
- Housekeeping oversight
- Payment verification
- Report viewing
- Room status updates

### Receptionist
- Booking creation
- Guest check-in/check-out
- Payment recording
- Housekeeping requests
- Guest inquiries

### Default Login Credentials

After seeding, use these credentials:

```
Admin:
Email: admin@beztower.com
Password: password

Manager:
Email: manager@beztower.com
Password: password

Receptionist:
Email: receptionist@beztower.com
Password: password
```

⚠️ **Change these passwords immediately in production!**

## 🎯 Features Overview

### Dashboard
- **Statistics Cards:** Revenue, Occupancy Rate, Arrivals, Pending Actions
- **Revenue Chart:** 12-month revenue visualization
- **Booking Trends:** Monthly booking counts (bar chart)
- **Occupancy Trends:** Historical occupancy rate (line chart)
- **Recent Bookings:** Last 5 bookings with quick actions
- **Today's Arrivals:** Check-ins scheduled for today
- **Housekeeping Status:** Real-time room cleaning status

### Booking Workflow
1. Customer selects room and dates
2. Customer submits booking with guest information
3. Customer uploads payment proof
4. Admin/Manager verifies payment
5. Booking status updated to "Confirmed"
6. Email notification sent to guest
7. Guest checks in → Status: "Checked In"
8. Guest checks out → Status: "Checked Out"

### Payment Verification Process
1. Customer uploads payment proof (image)
2. Payment shows in admin panel with "Pending" status
3. Admin reviews payment proof
4. Admin approves or rejects:
   - **Approve:** Email sent with confirmation
   - **Reject:** Email sent with reason
5. Payment status updated in system

## 🗄️ Database Structure

### Main Tables

- `users` - System users (Admin, Manager, Receptionist)
- `guests` - Hotel guests (first_name, last_name, email, phone)
- `room_types` - Room categories with pricing
- `rooms` - Individual room inventory
- `amenities` - Room amenities (WiFi, AC, etc.)
- `bookings` - Reservation records
- `payments` - Payment transactions with proof
- `housekeeping` - Room cleaning status
- `room_photos` - Room image gallery

### Key Relationships

- Guest → Bookings (One-to-Many)
- Booking → Payments (One-to-Many)
- Room → Room Type (Many-to-One)
- Room → Amenities (Many-to-Many)
- Room → Housekeeping (One-to-One)

## 📖 Usage Guide

### Creating a Room

1. Navigate to **Rooms** → Click floating **+** button
2. Fill in room details (number, type, floor, status)
3. Select amenities
4. Add description (optional)
5. Click **Create Room**

### Managing Bookings

1. Go to **Bookings** section
2. Use filters (status, dates) to find bookings
3. Click booking to view details
4. Update status as needed
5. Export to CSV for reporting

### Verifying Payments

1. Navigate to **Payments**
2. View pending payments with proof images
3. Click on payment to enlarge proof
4. Click **✓ Verify Payment** to approve
5. Or click **✗ Reject** and provide reason
6. System sends automatic email to guest

### Generating Reports

1. Go to **Reports** section
2. Select report type (Revenue/Occupancy)
3. Choose date range
4. Click **Export CSV** to download
5. View charts for visual analysis

## 🔌 API Documentation

While this system primarily uses Blade views, key endpoints include:

### Admin Routes
- `GET /admin/dashboard` - Dashboard view
- `GET /admin/bookings` - Booking list
- `GET /admin/rooms` - Room management
- `POST /admin/payments/{payment}/verify` - Verify payment
- `POST /admin/payments/{payment}/reject` - Reject payment
- `GET /admin/reports/export/{type}` - Export reports

### Authentication
All admin routes require authentication and appropriate role permissions via middleware.

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 coding standards
- Write descriptive commit messages
- Add comments for complex logic
- Test your changes before submitting

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 📧 Contact & Support

For questions, issues, or feature requests:

- Create an issue on GitHub
- Email: support@beztower.com
- Documentation: [Wiki](https://github.com/yourusername/beztower-hotel-management/wiki)

## 🙏 Acknowledgments

- Laravel Framework
- Chart.js for beautiful charts
- Pest PHP for testing
- All contributors and supporters

---

**Built with ❤️ for BezTower and Residences**

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
