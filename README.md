# Bookstore Management System

A Laravel 11 bookstore application covering catalog management, inventory, customer orders, promotions, notifications, and sandbox payment integrations. It combines a customer-facing Blade storefront with a role-protected administration area.

## Project Status

Portfolio project demonstrating Laravel backend development, relational database design, authentication, authorization, transaction-safe order processing, payment integration, and automated testing. The application is intended for learning and demonstration; production payment credentials are not included.

## Key Features

- Product catalog with categories, publishers, authors, search, and product details
- Session-based registration and authentication with admin/staff authorization
- Persistent cart, favorites, checkout, promotions, and order tracking
- Inventory receiving, order administration, revenue reporting, and customer management
- VNPay sandbox flow and token-authenticated bank payment webhook
- Transaction-safe inventory updates and idempotent bank webhook processing
- Customer notifications, articles, and optional Gemini-powered support chat

## Tech Stack

- Backend: PHP 8.2, Laravel 11, Eloquent ORM
- Frontend: Blade, JavaScript, CSS, Vite 5, Axios
- Database: SQLite for quick local setup; MySQL/MariaDB supported
- Testing: PHPUnit 10 with Laravel feature tests
- Deployment: Docker multi-stage build with Apache and PHP 8.2

## Architecture

```text
Browser / Payment Provider
           |
     Routes + Middleware
           |
       Controllers
           |
   Services / Eloquent Models
           |
     SQLite or MySQL
```

The project is a Laravel monolith. Controllers coordinate HTTP workflows, Eloquent models represent the domain and relationships, middleware protects administration routes, and `GeminiService` isolates the external AI integration. Checkout and payment flows use database transactions where multiple records must change together.

## Main Data Model

The core tables are `sanpham` (products), `danhmuc`, `tacgia`, `nhaxuatban`, `khachhang`, `taikhoan`, `giohang`, `donhang`, and their detail/junction tables. Foreign keys protect most order, cart, inventory, and catalog relationships; composite primary keys prevent duplicate line items.

## Project Structure

```text
app/Http/Controllers/   HTTP and business workflow coordination
app/Models/             Eloquent models and relationships
app/Services/           External service integration
database/migrations/    Versioned database schema
database/seeders/       Demonstration data
resources/views/        Blade storefront and admin views
routes/                 Web and API endpoints
tests/Feature/          End-to-end HTTP and security tests
docs/                   Audit and interview notes
```

## Getting Started

### Prerequisites

- PHP 8.2+ with PDO SQLite (or PDO MySQL)
- Composer 2
- Node.js 20+ and npm

### Installation

```bash
git clone <repository-url>
cd BookStoreManagement
composer install
npm install
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, use `Copy-Item .env.example .env` instead of `cp`.

### Database Setup

SQLite is the default. Create `database/database.sqlite`, then run:

```bash
php artisan migrate --seed
```

For MySQL, set `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` before migrating.

### Environment Variables

Required core settings are documented in `.env.example`. Optional integrations use:

- `GEMINI_API_KEY` for chatbot responses
- `VNP_TMN_CODE`, `VNP_HASH_SECRET`, `VNP_URL`, `VNP_RETURN_URL` for VNPay sandbox
- `PAYMENT_WEBHOOK_TOKEN` for the bank webhook (`Authorization: Bearer` or `Secure-Token`)

Never commit real credentials. The VNPay credential formerly present in Git history must be rotated before enabling payments.

### Run

```bash
php artisan serve
npm run dev
```

Open `http://127.0.0.1:8000`. Seed data creates local demonstration accounts; their credentials are defined in `database/seeders/InitialDataSeeder.php` and must not be used outside a local demo.

## Important Endpoints

| Method | Endpoint | Purpose | Access |
|---|---|---|---|
| GET | `/san-pham` | Browse products | Public |
| POST | `/register`, `/login` | Customer authentication | Public |
| GET/POST | `/cart`, `/checkout` | Shopping and checkout | Authenticated |
| GET | `/profile` | Orders and account profile | Authenticated |
| `/admin/*` | Catalog, orders, inventory, reports | Admin/staff |
| POST | `/api/payment/webhook` | Bank payment confirmation | Webhook token |
| GET/POST | `/vnpay-ipn` | VNPay callback | VNPay signature |

Use `php artisan route:list --except-vendor` for the complete route inventory.

## Testing

Tests use an in-memory SQLite database and do not modify the development database.

```bash
php artisan test
npm run build
```

## Docker

```bash
docker build -t bookstore-management .
docker run --rm -p 8080:8080 --env-file .env bookstore-management
```

Set `SEED_DATABASE=true` only for the first disposable demo deployment. Production startup fails when migrations fail instead of silently continuing.

## Security and Reliability

- Password hashing, session regeneration, CSRF protection, and role middleware
- Customer ownership checks for order and notification resources
- Environment-backed payment credentials and authenticated callbacks
- Row-level stock locking during checkout to prevent overselling
- Unique provider transaction references to make bank webhooks idempotent

## Screenshots

Screenshot slots are available under `docs/screenshots/`. Before publishing, capture these screens using non-sensitive demo data:

- Storefront
- Product details
- Cart and checkout
- Admin dashboard
- Product management
- Revenue reporting

## Future Improvements

- Extract checkout and payment state transitions into dedicated services if those workflows continue to grow
- Add provider-specific webhook signature adapters when connecting a real bank service
- Add API documentation if the application evolves beyond Blade clients
- Add screenshots and a hosted demo

## Author

Originally developed by Hoàng Duy An and Vũ Đình Hoàn as a student project. Add the preferred GitHub profile links before publishing.
