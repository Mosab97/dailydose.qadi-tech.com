# eRestro - Food Delivery & Restaurant Management System

## Overview

**eRestro** is a comprehensive food delivery and restaurant management platform developed by WRTeam Developers. It's built on the CodeIgniter PHP framework and provides a complete solution for managing online food ordering, delivery operations, and restaurant partnerships.

## System Architecture

### Framework & Technology Stack
- **Backend Framework**: CodeIgniter 3.x (PHP)
- **Architecture**: MVC (Model-View-Controller)
- **Authentication**: Ion Auth library with JWT support
- **Database**: MySQL/MariaDB (configured via CodeIgniter's database config)

### Core Components
1. **Admin Panel** - Complete backend management system
2. **Rider Application** - Delivery management for riders
3. **Customer Mobile App API** - RESTful API for customer applications
4. **Partner Portal** - Restaurant/partner management interface

## Key Features

### 1. User Management
- **Customer Management**: User registration, profiles, referral system, wallet management
- **Partner/Restaurant Management**: Multiple branch support, restaurant profiles, operating hours
- **Rider Management**: Delivery personnel registration, commission tracking, cash collection
- **Role-Based Access Control**: Granular permissions for different user types

### 2. Product & Catalog Management
- Product catalog with categories
- Variable products with attributes and add-ons
- Product ratings and reviews with image uploads
- Favorites/wishlist functionality
- Vegetarian/Non-vegetarian filtering
- Product tags and highlights
- Bulk upload/update via CSV

### 3. Order Management
- Complete order lifecycle: Pending → Confirmed → Preparing → Out for Delivery → Delivered
- Self-pickup option
- Order tracking with live GPS tracking
- OTP verification for delivery
- Order cancellation with reasons
- Thermal printer invoice support
- Order notes and delivery tips

### 4. Delivery & Logistics
- City-based delivery coverage
- Dynamic delivery charge calculation
- Rider assignment and management
- Live tracking for riders and customers
- Distance-based delivery pricing
- Multiple address management for customers

### 5. Payment Integration
Supports multiple payment gateways:
- PayPal
- Razorpay
- Stripe
- Paystack
- Flutterwave
- Paytm
- Midtrans
- PhonePe
- Cash on Delivery (COD)
- Wallet payments

### 6. Marketing & Promotions
- Promo codes with validation
- Referral system with rewards
- Home slider images
- Offer banners
- Featured sections
- Discount management

### 7. Financial Management
- Wallet system for customers
- Fund transfers for riders
- Commission tracking
- Cash collection management
- Withdrawal requests
- Transaction history
- Tax calculation

### 8. Communication & Support
- Push notifications (FCM integration)
- SMS gateway integration
- Support ticket system with attachments
- Ticket types and status management
- In-app messaging

### 9. Ratings & Reviews
- Product ratings with images
- Rider ratings
- Partner/restaurant ratings
- Review management

### 10. Multi-Language Support
Supports 40+ languages including:
- English, Spanish, French, German, Italian, Portuguese
- Arabic, Hindi, Bengali, Gujarati, Tamil, Telugu
- Chinese (Simplified & Traditional), Japanese, Korean
- And many more regional languages

### 11. Multi-Currency Support
- 150+ currency support with locale mapping
- Automatic currency detection based on locale

## API Documentation

### Customer App API
**Base URL**: `https://erestro.me/app/v1/api/{METHOD_NAME}`

**Key Endpoints** (62 total):
- Authentication: login, register, verify_user, reset_password
- User Management: update_user, update_fcm, delete_my_account
- Products: get_products, get_categories, get_sections, search
- Orders: place_order, get_orders, update_order_status, delete_order
- Cart: manage_cart, get_user_cart, remove_from_cart
- Address: add_address, update_address, get_address, delete_address
- Payments: make_payments, add_transaction, validate_promo_code
- Favorites: add_to_favorites, remove_from_favorites, get_favorites
- Ratings: set_product_rating, get_product_rating, set_rider_rating
- Support: add_ticket, get_tickets, send_message, get_messages
- Tracking: get_live_tracking_details
- Wallet: send_withdrawal_request, get_withdrawal_request

### Rider App API
**Base URL**: `https://erestro.me/rider/app/v1/api/{METHOD_NAME}`

**Key Endpoints** (17 total):
- Authentication: login, verify_user, reset_password
- Rider Management: get_rider_details, update_user, update_fcm
- Orders: get_orders, get_pending_orders, update_order_status, update_order_request
- Finances: get_fund_transfers, get_rider_cash_collection
- Tracking: manage_live_tracking, delete_live_tracking
- Withdrawals: send_withdrawal_request, get_withdrawal_request

## System Modules

The admin panel includes comprehensive management modules:
- Orders Management
- Categories Management
- Products Management
- Media Library
- Tax Management
- Attributes Management
- Sliders & Offers
- Promo Codes
- Featured Sections
- Customer Management
- Rider Management
- Fund Transfers
- Notifications
- Client API Keys
- City Management
- FAQ Management
- Support Tickets
- Branch Management
- Tags Management
- Payment Requests
- System Settings

## Database Migrations

The system includes migrations for:
1. Custom notifications
2. SMS gateway integration
3. Multiple products of same variant
4. Free delivery charge
5. Multiple branch login
6. Rider registration
7. Rider max commission
8. Slider slug addition

## File Structure

```
erstro/
├── application/          # CodeIgniter application folder
│   ├── controllers/     # Admin, App, Auth, Rider controllers
│   ├── models/          # Database models
│   ├── views/           # Admin, Auth, Rider views
│   ├── libraries/       # Payment gateway libraries
│   ├── helpers/         # Custom helper functions
│   ├── config/          # Configuration files
│   └── language/        # Multi-language support files
├── assets/              # Frontend assets
│   ├── admin/          # Admin panel assets
│   └── [images, CSS, JS files]
├── system/              # CodeIgniter core files
├── uploads/             # User uploaded content
│   ├── media/          # Product images
│   ├── user_image/     # User profile images
│   ├── review_image/   # Review images
│   ├── tickets/        # Support ticket attachments
│   └── [CSV templates]
└── index.php            # Front controller

```

## Security Features

- JWT token-based authentication
- Ion Auth integration for user management
- CSRF protection
- XSS filtering
- SQL injection prevention (via CodeIgniter's Query Builder)
- Password hashing
- OTP verification for sensitive operations
- Webhook verification for payment gateways

## Configuration

Key configuration files:
- `application/config/database.php` - Database settings
- `application/config/erestro.php` - System modules and settings
- `application/config/config.php` - CodeIgniter core config
- `application/config/ion_auth.php` - Authentication settings
- `application/config/email.php` - Email configuration
- `application/config/paypal.php` - PayPal settings

## Third-Party Integrations

### Payment Gateways
- PayPal (via Paypal_lib)
- Stripe
- Razorpay
- Paystack
- Flutterwave
- Paytm
- Midtrans
- PhonePe

### Other Services
- Firebase Cloud Messaging (FCM) for push notifications
- Google Maps API for location services
- SMS Gateway for OTP and notifications

## Installation

The system includes an installer in the `/install` directory for easy setup.

## Developer Information

**Developed by**: WRTeam Developers  
**System Name**: eRestro  
**Framework**: CodeIgniter 3.x  
**Environment**: Configurable (Development/Testing/Production)

## File Upload Support

The system supports various file types:
- **Images**: jpg, jpeg, png, gif, bmp, eps
- **Videos**: mp4, 3gp, avchd, avi, flv, mkv, mov, webm, wmv, mpg, mpeg, ogg
- **Documents**: doc, docx, txt, pdf, ppt, pptx
- **Spreadsheets**: xls, xlsx
- **Archives**: zip, 7z, bz2, gz, gzip, rar, tar

## Bulk Operations

Supports bulk operations via CSV:
- Category bulk upload/update
- Simple product bulk upload/update
- Variable product bulk upload/update
- Partner bulk upload/update
- Partner product bulk upload/update

## Use Cases

This system is ideal for:
1. **Food Delivery Startups**: Complete solution to launch a food delivery service
2. **Restaurant Chains**: Manage multiple branches and delivery operations
3. **Cloud Kitchens**: Online-only restaurant management
4. **Multi-Vendor Marketplaces**: Platform for multiple restaurant partners
5. **Catering Services**: Order management for catering businesses

## Scalability Features

- Multi-branch support for partners
- City-based operations management
- Multiple language and currency support
- API-first architecture for mobile apps
- Modular permission system
- Configurable payment methods
- Extensible via CodeIgniter's library system

---

*This system provides a complete end-to-end solution for food delivery operations with robust features for customers, restaurants, delivery riders, and administrators.*

