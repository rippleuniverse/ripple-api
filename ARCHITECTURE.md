# Ripple API - System Documentation

## Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture & Structure](#architecture--structure)
3. [Technology Stack](#technology-stack)
4. [Database Design](#database-design)
5. [Authentication & Authorization](#authentication--authorization)
6. [API Endpoints](#api-endpoints)
7. [Core Modules](#core-modules)
8. [Payment Processing](#payment-processing)
9. [File Management](#file-management)

10. [Email & Notifications](#email--notifications)
11. [Key Traits & Utilities](#key-traits--utilities)
12. [Development & Deployment](#development--deployment)

---

## Project Overview

**Ripple API** is a comprehensive Laravel-based REST API backend for a multi-featured platform that manages educational, commercial, and content distribution services. The system is designed to handle:

- **Educational Content**: Programs, courses, and learning materials
- **Event Management**: Event organization, ticketing, and management
- **Product Sales**: E-commerce functionality with shopping cart and checkout
- **Content Publishing**: Blogs and podcasts
- **Job Portal**: Job listings and application management
- **Payment Processing**: Multi-currency transactions with Stripe and Paystack integration
- **User Management**: Role-based access control and user administration
- **Newsletter Management**: Email subscription management

The API is built with a focus on scalability, security, and maintainability using Laravel 12 with modern PHP 8.2+ features.

---

## Architecture & Structure

### Project Directory Layout

```
ripple-api/
├── app/
│   ├── Console/              # Artisan commands
│   ├── Enums/               # Application enums (StatusCode, etc.)
│   ├── Helpers/             # Global helper functions
│   ├── Http/
│   │   ├── Controllers/     # API controllers organized by module
│   │   │   ├── Auth/        # Authentication endpoints
│   │   │   ├── Event/       # Event management
│   │   │   ├── Product/     # Product management
│   │   │   ├── Program/     # Program/course management
│   │   │   ├── Podcast/     # Podcast management
│   │   │   ├── Blog/        # Blog management
│   │   │   ├── Invoices/    # Payment and checkout
│   │   │   ├── OpenRoles/   # Job listings and applications
│   │   │   ├── Dashboard/   # Admin dashboard
│   │   │   ├── Coupon/      # Discount management
│   │   │   ├── Newsletter/  # Newsletter subscriptions
│   │   │   ├── Webhook/     # Payment webhooks
│   │   │   ├── Settings/    # Platform settings
│   │   │   └── User/        # User management
│   │   ├── Middleware/      # Custom middleware
│   │   │   ├── VerifiedMiddleware.php
│   │   │   └── RoleAuthorizeMiddleware.php
│   │   └── Resources/       # API resource classes for data transformation
│   ├── Mail/                # Email templates and mailing logic
│   ├── Models/              # Eloquent models (20+ models)
│   ├── Notifications/       # Notification classes
│   ├── Policies/            # Authorization policies
│   ├── Providers/           # Service providers
│   └── Traits/              # Reusable functionality
│       ├── HttpResponses.php
│       ├── Stripe.php
│       ├── Files.php
│       ├── OtpTrait.php
│       └── Pagination.php
├── bootstrap/               # Application bootstrapping
├── config/                  # Configuration files
├── database/
│   ├── factories/           # Model factories for testing
│   ├── migrations/          # Database migrations (50+)
│   └── seeders/             # Database seeders
├── public/                  # Public assets and entry point
├── resources/               # Frontend resources (Tailwind CSS, Vite)
├── routes/
│   ├── api.php              # API routes
│   ├── web.php              # Web routes
│   └── console.php          # Console commands
├── storage/                 # File storage (logs, uploads, cache)
├── tests/                   # Unit and feature tests
├── composer.json            # PHP dependencies
├── package.json             # Node.js dependencies
└── vite.config.js           # Vite bundler configuration
```

### Architectural Principles

1. **MVC Pattern**: Models handle data, Controllers handle business logic, Views/Resources transform data
2. **RESTful Design**: Standard HTTP methods for CRUD operations
3. **Resource-Oriented**: API endpoints focus on resources rather than actions
4. **Middleware Pipeline**: Request authentication and authorization through middleware
5. **Service Layer Pattern**: Business logic separation using traits and service classes
6. **Repository Pattern**: Data access abstraction through Eloquent models
7. **API Resource Transformation**: Consistent data formatting through Laravel Resource classes

---

## Technology Stack

### Backend
- **Framework**: Laravel 12.0
- **Language**: PHP 8.2+
- **Database**: SQLite (development), MySQL/MariaDB/PostgreSQL (production)
- **ORM**: Eloquent
- **Authentication**: Laravel Sanctum (API tokens)
- **Queue System**: Redis-backed job processing
- **Caching**: Redis/File-based caching

### Frontend Build Tools
- **Build Tool**: Vite 7
- **CSS Framework**: Tailwind CSS 4
- **JavaScript Library**: Axios
- **Package Manager**: npm

### Payment Gateway Integration
- **Stripe**: Credit/debit card payments with webhook support
- **Paystack**: African payment processing with webhook support

### Email & Notifications
- **Mail Drivers**: SMTP, Mailgun, Postmark, SES
- **Queue**: Database/Redis for background email sending
- **Templates**: Blade templating engine

### Development Dependencies
- **Testing**: PHPUnit, Mockery
- **Code Quality**: Laravel Pint (PHP linter/formatter)
- **Monitoring**: Laravel Pail (log monitoring)
- **Database**: Laravel Sail (Docker environment)
- **Development Server**: Concurrently running server, queue, logs, and Vite

---

## Database Design

### Core Models & Relationships

#### 1. **User Model**
```
- id (Primary Key)
- full_name
- email (Unique)
- password (hashed)
- avatar (nullable)
- role (admin, user)
- email_verified_at (nullable)
- status (active/inactive)
- timestamps

Relationships:
- HasMany: otps, invoices, coupons
- HasOne: billingInformation, coupon
```

#### 2. **Program Model**
```
- id
- name
- author
- description
- price (JSON: multi-currency)
- featured_image
- experience_level
- program_category_id (FK)
- skills (array)
- file (downloadable course material)
- timestamps

Relationships:
- BelongsTo: category (ProgramCategory)
- HasMany: ratings (Rating)
```

#### 3. **Event Model**
```
- id
- title
- description
- featured_image
- date
- type (online, offline, hybrid)
- access (free, paid)
- status (draft, published, cancelled)
- event_category_id (FK)
- what_to_expect (array)
- who_to_expect (array)
- facilitators (array)
- agendas (array)
- images (array)
- timestamps

Relationships:
- HasMany: tickets (EventTicket)
- BelongsTo: category (EventCategory)
```

#### 4. **EventTicket Model**
```
- id
- event_id (FK)
- name
- price (JSON: multi-currency)
- quantity_available
- quantity_sold
- timestamps

Relationships:
- BelongsTo: event
- HasMany: purchasedTickets
```

#### 5. **Product Model**
```
- id
- title
- description
- price
- featured_image
- type
- product_category_id (FK)
- about (array)
- benefits (array)
- target_users (array)
- how_to_use (array)
- access_delivery (array)
- available_quantity
- timestamps

Relationships:
- BelongsTo: category (ProductCategory)
```

#### 6. **Invoice Model**
```
- id
- user_id (FK)
- trx_id (unique transaction ID)
- amount
- discount
- shipping_fee
- currency (USD, NGN)
- status (pending, paid, in_transit, delivered, cancelled)
- payment_method (stripe, paystack)
- payment_url
- billing_information (JSON)
- metadata (JSON)
- coupon_id (FK)
- timestamps

Relationships:
- BelongsTo: user, coupon
- HasMany: items (InvoiceItem)
```

#### 7. **InvoiceItem Model**
```
- id
- invoice_id (FK)
- product_id
- product_type (product, event, program)
- quantity
- unit_price
- currency

Relationships:
- BelongsTo: invoice
```

#### 8. **Coupon Model**
```
- id
- code (unique discount code)
- type (percentage, fixed)
- percentage_value (for percentage coupons)
- fixed_value (JSON: multi-currency for fixed coupons)
- is_active
- is_created_by_admin
- user_id (FK: nullable, user-specific coupons)
- timestamps

Methods:
- calculateDeduction(): Calculate discount amount
- calculateValue(): Calculate final price after discount
```

#### 9. **Blog Model**
```
- id
- title
- slug (URL-friendly)
- content (rich text)
- description
- featured_image
- author
- blog_category_id (FK)
- timestamps

Relationships:
- BelongsTo: category (BlogCategory)

Accessors:
- getReadTimeAttribute(): Auto-calculated reading time
```

#### 10. **Podcast Model**
```
- id
- title
- description
- featured_image
- audio (file path)
- duration_in_minutes
- podcast_category_id (FK)
- timestamps

Relationships:
- BelongsTo: category (PodcastCategory)
```

#### 11. **OpenRole Model (Job Listing)**
```
- id
- name (job title)
- company_name
- company_location
- type (full-time, part-time, contract, freelance)
- experience_level (junior, mid, senior)
- style (remote, onsite, hybrid)
- salary (JSON)
- description
- about_company
- responsibilities (array)
- requirements (array)
- benefits (array)
- timestamps
```

#### 12. **JobApplication Model**
```
- id
- open_role_id (FK)
- applicant_name
- applicant_email
- applicant_phone
- resume (file path)
- cover_letter
- status (applied, reviewed, rejected, accepted)
- timestamps
```

#### 13. **OneTimePassword (OTP) Model**
```
- id
- user_id (FK)
- type (email_verification, password_reset)
- otp_code (generated code)
- expires_at
- timestamps

Purpose: Email verification and password reset OTP management
```

#### 14. **BillingInformation Model**
```
- id
- user_id (FK)
- first_name
- last_name
- email
- phone
- apartment
- city
- country
- postal_code
- timestamps
```

#### 15. **NewsletterSubscription Model**
```
- id
- email
- timestamps

Purpose: Track newsletter subscribers
```

#### 16. **Rating Model**
```
- id
- program_id (FK)
- user_id (FK)
- rating (1-5)
- review (text)
- timestamps
```

#### 17. **ShippingFee Model**
```
- id
- country
- shipping_fee (decimal)
- timestamps
```

#### 18. **PurchasedTicket Model**
```
- id
- user_id (FK)
- event_ticket_id (FK)
- quantity
- qr_code (for entry verification)
- timestamps
```

#### 19. **Settings Model**
```
- id
- key
- value (JSON)
- timestamps

Purpose: Store platform-wide settings
```

#### 20. **BlogAsset Model**
```
- id
- path (file path)
- created_at

Purpose: Manage blog editor assets
```

### Category Models
- **ProgramCategory**: Categories for programs/courses
- **EventCategory**: Categories for events
- **PodcastCategory**: Categories for podcasts
- **ProductCategory**: Categories for products
- **BlogCategory**: Categories for blog posts

### Migration Strategy
- Timestamp-based naming convention for migrations
- Forward and backward migration support
- Foreign key constraints enabled
- Automatic timestamp columns (created_at, updated_at)

---

## Authentication & Authorization

### Authentication Flow

#### 1. **Sign-Up Process**
```
POST /auth/sign-up
Request:
{
  "full_name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePassword123",
  "password_confirmation": "SecurePassword123",
  "can_subscribe_newsletter": true
}

Response:
{
  "data": {
    "token": "Bearer token...",
    "user": { user data }
  },
  "message": "Signed up successfully."
}

Process:
1. Validate input (name, unique email, password confirmation)
2. Create user with hashed password
3. Generate unique coupon code (10% discount)
4. Send email verification OTP
5. Create welcome email notification
6. Subscribe to newsletter if requested
7. Issue Sanctum API token
8. Return token and user profile
```

#### 2. **Sign-In Process**
```
POST /auth/sign-in
Request:
{
  "email": "john@example.com",
  "password": "SecurePassword123"
}

Process:
1. Attempt authentication with email/password
2. If successful, create API token
3. Return token and user profile
4. If failed, return 401 Unauthorized
```

#### 3. **Email Verification**
```
POST /auth/verify-email
Request:
{
  "otp_code": "12345"
}

Process:
1. Find valid OTP for user
2. Check expiration time
3. Mark email as verified
4. Delete OTP record
5. User gains access to verified-only endpoints
```

#### 4. **Password Reset**
```
POST /auth/send-password-reset
Request: { "email": "john@example.com" }

POST /auth/reset-password
Request: {
  "token": "reset_token",
  "password": "NewPassword123",
  "password_confirmation": "NewPassword123"
}
```

### Authorization Model

#### Role-Based Access Control (RBAC)
```
Roles:
- admin: Full platform access, content management
- user: Limited access, can purchase content, submit applications

Middleware:
- RoleAuthorizeMiddleware: Checks user role for protected endpoints
- VerifiedMiddleware: Ensures email is verified

Typical Protected Route:
Route::middleware([
  'auth:sanctum',
  VerifiedMiddleware::class,
  RoleAuthorizeMiddleware::class . ':admin'
])->post('/programs', 'ProgramsController@store');
```

### Token Management

**Laravel Sanctum** provides:
- Stateless API authentication
- Per-request token validation
- Token revocation on logout
- Multiple tokens per user for different applications

```
Token Generation:
$user->createToken('auth_token')->plainTextToken;

Token Usage in Requests:
Authorization: Bearer <token>

Logout:
POST /auth/logout
- Revokes current token
- User must re-authenticate
```

---

## API Endpoints

### Authentication Endpoints

```
POST   /api/auth/sign-up                      Register new user
POST   /api/auth/sign-in                      Login user
GET    /api/auth/user                         Get authenticated user (protected)
POST   /api/auth/logout                       Logout user (protected)
POST   /api/auth/verify-email                 Verify email with OTP (protected)
POST   /api/auth/resend-email-verification    Resend verification OTP (protected)
POST   /api/auth/update-profile               Update user profile (protected, verified)
DELETE /api/auth/remove-avatar                Delete user avatar (protected, verified)
POST   /api/auth/send-password-reset          Send password reset email
POST   /api/auth/reset-password               Reset password with token
```

### Programs Endpoints

```
GET    /api/programs                          List all programs (paginated)
GET    /api/programs/categories               Get program categories
GET    /api/programs/{program}                Get program details
GET    /api/programs/{program}/related        Get related programs
GET    /api/programs/{program}/reviews        Get program reviews/ratings
POST   /api/programs                          Create program (admin only)
POST   /api/programs/{program}                Update program (admin only)
DELETE /api/programs/{program}                Delete program (admin only)
```

### Events Endpoints

```
GET    /api/events                            List all events
GET    /api/events/categories                 Get event categories
GET    /api/events/overview                   Get event overview/stats
GET    /api/events/{event}                    Get event details
POST   /api/events                            Create event (admin only)
POST   /api/events/{event}                    Update event (admin only)
DELETE /api/events/{event}                    Delete event (admin only)
POST   /api/events/{event}/images             Add event images (admin only)
DELETE /api/events/{event}/images             Remove event image (admin only)
DELETE /api/events/tickets/{ticket}           Delete event ticket (admin only)
```

### Products Endpoints

```
GET    /api/products                          List all products
GET    /api/products/overview                 Get products overview
GET    /api/products/categories               Get product categories
GET    /api/products/categories/all           Get all categories without pagination
GET    /api/products/categories/{category}    Get products in category
GET    /api/products/{product}                Get product details
POST   /api/products                          Create product (admin only)
POST   /api/products/categories               Create product category (admin only)
PATCH  /api/products/categories/{category}    Update category (admin only)
DELETE /api/products/categories/{category}    Delete category (admin only)
POST   /api/products/{product}                Update product (admin only)
DELETE /api/products/{product}                Delete product (admin only)
```

### Podcasts Endpoints

```
GET    /api/podcasts                          List all podcasts
GET    /api/podcasts/categories               Get podcast categories
GET    /api/podcasts/overview                 Get podcasts overview
GET    /api/podcasts/{podcast}                Get podcast details
GET    /api/podcasts/{podcast}/related        Get related podcasts
POST   /api/podcasts                          Create podcast (admin only)
POST   /api/podcasts/{podcast}                Update podcast (admin only)
DELETE /api/podcasts/{podcast}                Delete podcast (admin only)
```

### Blogs Endpoints

```
GET    /api/blogs                             List all blogs
GET    /api/blogs/categories                  Get blog categories
GET    /api/blogs/overview                    Get blogs overview
GET    /api/blogs/{slug}                      Get blog by slug
GET    /api/blogs/{slug}/related              Get related blogs
POST   /api/blogs                             Create blog (admin only)
POST   /api/blogs/{blog}                      Update blog (admin only)
DELETE /api/blogs/{blog}                      Delete blog (admin only)
POST   /api/blogs/assets                      Upload blog asset (admin only)
GET    /api/blogs/assets                      List blog assets (admin only)
DELETE /api/blogs/assets/{asset}              Delete blog asset (admin only)
```

### Job Roles Endpoints

```
GET    /api/roles                             List all open roles
GET    /api/roles/overview                    Get roles overview/stats
GET    /api/roles/overview/home               Get roles home overview
GET    /api/roles/{role}                      Get role details
POST   /api/roles                             Create open role (admin only)
PATCH  /api/roles/{role}                      Update open role (admin only)
DELETE /api/roles/{role}                      Delete open role (admin only)
GET    /api/roles/all                         Get all roles unpaginated (admin only)
```

### Job Applications Endpoints

```
POST   /api/job-applications                  Submit application
GET    /api/job-applications                  List applications (admin only)
GET    /api/job-applications/{application}    Get application details (admin only)
DELETE /api/job-applications/{application}    Delete application (admin only)
```

### Coupons Endpoints

```
GET    /api/coupons/user                      Get user's personal coupon (protected)
GET    /api/coupons                           List all coupons (admin only)
GET    /api/coupons/{coupon}                  Get coupon details (admin only)
POST   /api/coupons                           Create coupon (admin only)
PATCH  /api/coupons/{coupon}                  Update coupon (admin only)
DELETE /api/coupons/{coupon}                  Delete coupon (admin only)
```

### Checkout Endpoints

```
POST   /api/checkout/shop                     Checkout for products (protected, verified)
POST   /api/checkout/event                    Checkout for event tickets (protected, verified)
POST   /api/checkout/program                  Checkout for programs (protected, verified)
```

### Invoice Endpoints

```
GET    /api/invoice/purchases                 Get user's purchases (protected, verified)
GET    /api/invoice/purchases/all             Get all purchases (admin only)
GET    /api/invoice/purchases/{item}          Get purchase details (protected, verified)
GET    /api/invoice/purchases/{item}/download-program    Download program file
```

### Billing Information Endpoints

```
GET    /api/billing-info                      Get user's billing info (protected, verified)
PUT    /api/billing-info                      Save billing info (protected, verified)
DELETE /api/billing-info                      Delete billing info (protected, verified)
```

### Newsletter Endpoints

```
POST   /api/newsletter/subscribe              Subscribe to newsletter
```

### Admin Endpoints

```
GET    /api/users                             List all users (admin only)
GET    /api/users/{user}                      Get user details (admin only)
GET    /api/users/staffs                      List staff members (admin only)
GET    /api/users/staffs/{user}               Get staff details (admin only)
POST   /api/users/create-admin                Create admin user (admin only)
PATCH  /api/users/{user}/change-status        Change user status (admin only)
GET    /api/dashboard/admin/overview          Get admin dashboard (admin only)
```

### Settings Endpoints

```
POST   /api/settings/site-login               Admin site login
GET    /api/settings/shipping-fees            Get shipping fees
GET    /api/settings/check-site-lock-status   Check if site is locked
```

### Health & Webhook Endpoints

```
GET    /api                                   Health check
POST   /api/webhook/stripe                    Stripe payment webhook
POST   /api/webhook/paystack                  Paystack payment webhook
```

---

## Core Modules

### 1. Authentication Module

**Files**: `app/Http/Controllers/Auth/AuthController.php`

**Features**:
- User registration with email verification
- Email/password authentication
- JWT token generation via Sanctum
- Profile management with avatar uploads
- Email change with re-verification
- Password reset via email OTP
- Automatic coupon generation for new users (10% discount)
- Newsletter subscription during signup

**Key Methods**:
```php
signUp()        - Register new user
signIn()        - Authenticate user
user()          - Get authenticated user profile
updateProfile() - Update user information
verifyEmail()   - Verify email with OTP
logout()        - Revoke authentication token
```

### 2. Programs Module

**Files**: `app/Http/Controllers/Program/ProgramsController.php`

**Features**:
- Manage educational programs/courses
- Multi-currency pricing
- Program categorization
- Program ratings and reviews
- Downloadable program files
- Search and filtering capabilities
- Related programs recommendations

**Data Model**:
- Program: Course/educational content
- ProgramCategory: Program classification
- Rating: User ratings and reviews

**Admin Operations**:
```
POST   - Create new program
PUT    - Update program
DELETE - Remove program
GET    - View program details and related content
```

### 3. Events Module

**Files**: `app/Http/Controllers/Event/EventsController.php`

**Features**:
- Event creation and management
- Multiple event types (online, offline, hybrid)
- Event ticketing system
- Multi-currency pricing
- Event details (facilitators, agenda, expectations)
- Event image galleries
- Event categorization
- Search by type, category, access level

**Ticket System**:
- Multiple ticket types per event
- Quantity management
- Price per currency
- Ticket reservations and purchases

**Admin Operations**:
- Create, update, delete events
- Manage event tickets
- Add/remove event images
- Publish/draft status management

### 4. Products Module

**Files**: `app/Http/Controllers/Product/ProductsController.php`

**Features**:
- E-commerce product management
- Product categorization
- Product attributes (benefits, how_to_use, target_users)
- Inventory management
- Search and filtering
- Product overview/statistics

**Product Metadata**:
- Benefits (array)
- Target users (array)
- How to use (array)
- Access delivery info (array)

### 5. Invoicing & Checkout Module

**Files**: `app/Http/Controllers/Invoices/CheckoutController.php`

**Features**:
- Multi-currency checkout (USD, NGN)
- Stripe and Paystack integration
- Coupon/discount application
- Billing information management
- Invoice generation and tracking
- Order history

**Checkout Flow**:
```
1. Select items (products, events, programs)
2. Apply coupon (optional)
3. Calculate total with discounts
4. Collect billing information (optional save)
5. Generate payment link via payment gateway
6. Send invoice email
7. Track payment status
```

**Payment Status Tracking**:
- Pending: Awaiting payment
- Paid: Payment successful
- In Transit: For physical products
- Delivered: Order completed
- Cancelled: Transaction cancelled

### 6. Coupon Module

**Files**: `app/Http/Controllers/Coupon/CouponController.php`

**Features**:
- Admin-created coupons
- User-generated referral coupons (10% discount for new signups)
- Two types: Percentage-based, Fixed amount
- Multi-currency support for fixed coupons
- Coupon activation/deactivation
- User-specific coupon codes

**Coupon Logic**:
```php
Type: Percentage
- Deduction = (percentage / 100) * amount

Type: Fixed
- Deduction = fixed_amount[currency]

Final Price = Original Amount - Deduction
```

### 7. Blogging Module

**Files**: `app/Http/Controllers/Blog/BlogsController.php`

**Features**:
- Blog post creation and management
- Blog categorization
- Rich content editing with asset uploads
- Auto-calculated read time
- Blog search and filtering
- Related blogs recommendations
- URL slug generation

**Asset Management**:
- Upload assets while editing blogs
- List uploaded assets
- Delete unused assets

### 8. Podcasting Module

**Files**: `app/Http/Controllers/Podcast/PodcastsController.php`

**Features**:
- Podcast management
- Podcast categorization
- Audio file management
- Duration tracking
- Search and filtering
- Related podcasts

### 9. Job Portal Module

**Files**: `app/Http/Controllers/OpenRoles/` directory

**Features**:
- Job listing management (OpenRole)
- Application management (JobApplication)
- Job filtering by type, level, style
- Application tracking
- Resume uploads
- Cover letter submission

**Job Attributes**:
- Type: Full-time, Part-time, Contract, Freelance
- Experience Level: Junior, Mid, Senior
- Style: Remote, Onsite, Hybrid
- Responsibilities, Requirements, Benefits (arrays)

### 10. User Management Module

**Files**: `app/Http/Controllers/User/UsersController.php`

**Features**:
- User listing and management (admin)
- Staff management
- User status management (active/inactive)
- Admin user creation
- User profile viewing

### 11. Newsletter Module

**Files**: `app/Http/Controllers/Newsletter/SubscriptionsController.php`

**Features**:
- Email subscription management
- Welcome email on subscription
- Newsletter subscriber tracking
- Optional newsletter signup on user registration

### 12. Dashboard Module

**Files**: `app/Http/Controllers/Dashboard/AdminController.php`

**Features**:
- Admin overview dashboard
- Statistics and metrics
- System health information

---

## Payment Processing

### Integration Points

#### Stripe Integration

**Configuration**: `config/services.php`
```php
'stripe' => [
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
]
```

**Payment Flow**:
```
1. User selects items and applies coupon
2. Calculate final amount
3. Create Stripe payment session
4. Generate payment URL
5. Redirect user to payment page
6. Webhook receives payment confirmation
7. Update invoice status to 'paid'
8. Send confirmation email
```

**Webhook Handling**: `app/Http/Controllers/Webhook/WebhookController@stripe`

#### Paystack Integration

**Configuration**: `config/services.php`
```php
'paystack' => [
    'url' => env('PAYSTACK_API_URL'),
    'secret_key' => env('PAYSTACK_SECRET_KEY'),
    'callback_url' => env('PAYSTACK_CALLBACK_URL'),
]
```

**Webhook Handling**: `app/Http/Controllers/Webhook/WebhookController@paystack`

### Multi-Currency Support

Supported currencies:
- USD (US Dollar)
- NGN (Nigerian Naira)

**Price Storage**:
Prices stored as JSON with currency information:
```json
[
  { "currency": "USD", "amount": 99.99 },
  { "currency": "NGN", "amount": 15000 }
]
```

**Checkout Process**:
```php
$price = collect(sanitizedJsonDecode($product->price));
$amountInSelectedCurrency = $price->where('currency', $selectedCurrency)->first()->amount;
```

### Invoice Lifecycle

```
CREATE:
- Generate unique transaction ID (UUID)
- Calculate subtotal, discounts, shipping
- Create invoice record
- Create invoice items for each product

PAYMENT:
- Receive payment via Stripe/Paystack webhook
- Update invoice status to 'paid'
- Send confirmation email
- Create purchase records

FULFILLMENT:
- For digital goods: Immediate delivery
- For physical goods: In Transit → Delivered
- Generate download link for programs/courses
```

---

## File Management

### File Upload Trait

**File**: `app/Traits/Files.php`

**Methods**:

```php
uploadFile($file, $folder, $storage = 'public')
- Upload single file
- Returns relative path
- $folder: Target directory
- $storage: Storage disk (public/private)

uploadFiles($documentFiles, $folder, $max = 4, $storage = 'public')
- Upload multiple files
- Enforces max file limit
- Returns array of paths

getFilePath($path)
- Convert relative path to full URL
- Returns asset URL for access

getFilePaths($paths)
- Convert multiple paths to URLs

deleteFile($path)
- Delete file from storage
- Handles file not found gracefully
```

### Upload Directory Structure

```
storage/app/public/
├── users/               # User avatars
├── programs/            # Program files
├── blogs/               # Blog assets and featured images
├── podcasts/            # Podcast audio files
├── events/              # Event images
├── products/            # Product images
└── job-applications/    # Resume uploads
```

### File Validation

```php
// Avatar validation
'avatar' => ['nullable', 'mimes:jpg,jpeg,png', 'max:5120'] // 5MB

// Document validation
'resume' => ['required', 'mimes:pdf,doc,docx', 'max:10240'] // 10MB
```

---

## Email & Notifications

### Mail Classes

**Email Templates Location**: `app/Mail/`

#### Authentication Emails
- `WelcomeMail.php`: Welcome email for new users
- `Auth/OtpMail.php`: OTP code for verification/password reset

#### Invoice Emails
- `InvoiceMail.php`: Order confirmation with invoice details

#### Newsletter Emails
- `Newsletter/SubscribedMail.php`: Welcome to newsletter

#### Event Emails
- `Event/EventMail.php`: Event confirmation and details

#### Job-Related Emails
- `OpenRoles/ApplicationMail.php`: Application confirmation

#### Other
- `Auth/PasswordResetMail.php`: Password reset instructions

### Email Configuration

**Config File**: `config/mail.php`

Supported drivers:
- SMTP (primary)
- Mailgun
- Postmark
- AWS SES

**Environment Variables**:
```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=username
MAIL_PASSWORD=password
MAIL_FROM_ADDRESS=noreply@ripple.app
MAIL_FROM_NAME="Ripple"
```

### Queue Integration

**Background Email Sending**:
```php
Mail::to($user->email)->send(new InvoiceMail($invoice));
```

Emails are typically queued in production for:
- Non-blocking user experience
- Retry on failure
- Rate limiting
- Batch processing

**Queue Configuration**:
```
QUEUE_CONNECTION=database|redis
```

### OTP Management

**File**: `app/Traits/OtpTrait.php`

**Process**:
```php
1. Generate OTP record in database
2. Set expiration time (typically 15 minutes)
3. Send OTP via email using OtpMail
4. User submits OTP
5. Validate: Check existence and expiration
6. If valid, delete and proceed
7. If expired, delete and request new OTP
```

---

## Key Traits & Utilities

### HttpResponses Trait

**File**: `app/Traits/HttpResponses.php`

**Purpose**: Standardize API response format across all endpoints

**Methods**:

```php
success($data, $message = "Okay", $code = 200)
- Returns success response
- Includes data and message
- HTTP status code

failed($data, $code, $message = "Failed")
- Returns error response
- Includes data and message
- HTTP status code
```

**Response Format**:
```json
{
  "data": { /* response data */ },
  "message": "Success message"
}
```

**HTTP Status Codes** (from StatusCode enum):
- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Unprocessable Entity
- 500: Internal Server Error

### Stripe Trait

**File**: `app/Traits/Stripe.php`

**Purpose**: Provide Stripe client instance to controllers

**Methods**:
```php
stripe()
- Returns StripeClient instance
- Uses config('services.stripe.secret_key')
- Can be used in checkout and payment processing
```

### Pagination Trait

**File**: `app/Traits/Pagination.php`

**Purpose**: Standardize pagination across list endpoints

**Features**:
- Configurable per-page limit
- Cursor or offset pagination
- Sortable results

### OTP Trait

**File**: `app/Traits/OtpTrait.php`

**Methods**:
```php
generateSendOtp(User $user, string $type)
- Generate OTP for user
- Send via email
- Types: email_verification, password_reset

validateOtp(?OneTimePassword $otp)
- Check OTP existence
- Check expiration time
- Delete if expired
- Return validity
```

---

## Development & Deployment

### Development Environment

**Setup Commands**:
```bash
# Clone repository
git clone <repo-url>
cd ripple-api

# Run setup script
composer run-script setup
# This runs:
# - composer install
# - Copy .env.example to .env
# - Generate application key
# - Run migrations
# - npm install
# - npm run build
```

**Development Server**:
```bash
composer run dev
# Runs concurrently:
# - PHP development server (port 8000)
# - Queue listener for background jobs
# - Laravel Pail for log monitoring
# - Vite dev server for frontend assets
```

### Database Migrations

**Running Migrations**:
```bash
php artisan migrate              # Run all pending migrations
php artisan migrate:fresh        # Reset and migrate
php artisan migrate:refresh      # Rollback and migrate
php artisan migrate:reset        # Rollback all migrations
```

**Creating Migrations**:
```bash
php artisan make:migration create_table_name
php artisan make:migration add_column_to_table
```

### Testing

**Running Tests**:
```bash
composer run test               # Run all tests
php artisan test tests/Feature  # Run feature tests
php artisan test tests/Unit     # Run unit tests
```

**Testing Configuration**: `phpunit.xml`

### Code Quality

**Linting and Formatting**:
```bash
php artisan pint              # Fix code style issues
php artisan pint --test       # Check code style without fixing
```

### Environment Configuration

**Key Environment Variables**:
```env
APP_NAME=Ripple
APP_ENV=production/local
APP_DEBUG=false/true
APP_URL=https://api.ripple.app

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ripple
DB_USERNAME=root
DB_PASSWORD=

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=
MAIL_PASSWORD=

STRIPE_SECRET_KEY=
STRIPE_WEBHOOK_SECRET=

PAYSTACK_API_URL=
PAYSTACK_SECRET_KEY=

SANCTUM_STATEFUL_DOMAINS=localhost:3000,*.example.com

QUEUE_CONNECTION=database/redis

CACHE_DRIVER=redis/file

SESSION_DRIVER=database/redis
```

### Production Deployment

**Pre-Deployment**:
1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Generate application key
4. Configure database for production
5. Set up SSL certificates
6. Configure email service
7. Set up Stripe/Paystack credentials
8. Configure Redis for caching/queues

**Deployment Steps**:
```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear application cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start queue workers
php artisan queue:work --daemon

# Restart web server
sudo systemctl restart nginx/apache
```

**Performance Optimization**:
- Enable query caching
- Use Redis for sessions/cache
- Configure queue workers for background jobs
- Set up CDN for file serving
- Enable gzip compression
- Implement rate limiting

### Security Best Practices

1. **Environment Variables**: Never commit `.env` file
2. **Database**: Use strong credentials, encrypted connections
3. **API Tokens**: Implement token rotation, expiration
4. **HTTPS**: Enforce SSL/TLS in production
5. **CORS**: Configure appropriate CORS headers
6. **Input Validation**: Validate all user inputs
7. **SQL Injection**: Use prepared statements (Eloquent ORM)
8. **XSS Protection**: Sanitize outputs, use CSP headers
9. **CSRF Protection**: Validate tokens for state-changing requests
10. **Rate Limiting**: Implement rate limiting on sensitive endpoints

---

## System Flow Examples

### Complete Purchase Flow

```
1. USER BROWSING
   - Browse products/events/programs
   - View details and prices

2. CHECKOUT INITIATION
   POST /checkout/shop (or event/program)
   - Select items
   - Choose currency
   - Apply coupon code (optional)

3. PAYMENT PROCESSING
   - System calculates:
     * Item subtotal
     * Coupon discount (if applicable)
     * Shipping fee (if applicable)
     * Total amount
   - Generate Stripe/Paystack payment link
   - Send invoice email

4. USER PAYMENT
   - User redirected to payment gateway
   - User completes payment
   - Payment gateway processes transaction

5. WEBHOOK NOTIFICATION
   - Payment gateway sends webhook
   - System updates invoice status to 'paid'
   - Creates purchase records

6. POST-PURCHASE
   - Send confirmation email
   - If program: Generate download link
   - If event: Create ticket, send ticket details
   - If product: Mark for shipping
   - Update user purchase history

7. FULFILLMENT
   - Digital goods: Immediate access
   - Physical goods: In Transit → Delivered
```

### User Registration & Verification Flow

```
1. SIGNUP REQUEST
   POST /auth/sign-up
   - Email, password, name

2. USER CREATION
   - Hash password
   - Create user record
   - Generate unique coupon code

3. EMAIL VERIFICATION
   - Generate OTP
   - Send OTP via email
   - User receives email

4. OTP VERIFICATION
   POST /auth/verify-email
   - User submits OTP
   - Validate: Exists, not expired
   - Mark email_verified_at

5. ACCESS GRANTED
   - User can access verified-only endpoints
   - Can make purchases
   - Can access admin features (if admin)
```

### Admin Content Management Flow

```
1. LOGIN
   - Admin sign-in with credentials
   - Receive API token

2. CREATE CONTENT
   POST /api/programs (or events/products/etc)
   - Submit content details
   - Upload featured image
   - Set pricing (multi-currency)
   - Categorize content

3. FILE MANAGEMENT
   - Attach downloadable files (programs)
   - Upload event images
   - Manage blog assets

4. PUBLISH
   - Set status to published
   - Content visible to users

5. ANALYTICS
   GET /dashboard/admin/overview
   - View statistics
   - Monitor sales
   - Track user activity
```

---

## API Response Examples

### Success Response

```json
{
  "data": {
    "id": 1,
    "email": "user@example.com",
    "full_name": "John Doe",
    "role": "user",
    "email_verified_at": "2026-05-25T10:30:00Z",
    "avatar": "https://api.ripple.app/storage/users/avatar.jpg"
  },
  "message": "User profile retrieved successfully"
}
```

### Error Response

```json
{
  "data": null,
  "message": "Email not verified. Please verify your email before accessing this resource."
}
```

### Paginated Response

```json
{
  "data": [
    { /* item 1 */ },
    { /* item 2 */ }
  ],
  "message": "Items retrieved successfully",
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

---

## Conclusion

The Ripple API is a comprehensive, well-structured Laravel application designed to handle multiple business domains:

- **E-Learning**: Programs with ratings and reviews
- **Events**: Ticketing and event management
- **E-Commerce**: Product catalog and shopping
- **Content**: Blogs and podcasts
- **Recruitment**: Job listings and applications
- **Payments**: Multi-currency, multi-gateway processing
- **User Management**: Role-based access control

The architecture emphasizes:
- **Security**: Multi-layer authentication and authorization
- **Scalability**: Modular design, queue-based processing
- **Maintainability**: Clear separation of concerns, reusable traits
- **User Experience**: Real-time feedback, email notifications
- **Extensibility**: Easy to add new modules and features

All endpoints follow RESTful conventions, use consistent response formatting, and implement proper error handling and validation.
