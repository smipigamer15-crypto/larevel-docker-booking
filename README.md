# 📅 Booking System

A multilingual service booking application built with **Laravel 13**, **Blade**, **Alpine.js**, **Bootstrap 5**, and **Docker**. Customers can discover services, reserve available time slots, manage bookings, and subscribe to paid plans. Administrators manage the catalog, customers, subscriptions, and availability from a separate dashboard.

> The application is under active development. Local automated checks pass, but production deployment and live payment verification still need to be completed.

---

## 🖥️ Application Screens

| Screen | Local URL | Access |
| :--- | :--- | :--- |
| Home page | `http://localhost` | Public |
| Services catalog | `http://localhost/services` | Public |
| Plans & pricing | `http://localhost/pricing` | Public |
| New booking | `http://localhost/bookings/create` | Verified customer |
| My bookings | `http://localhost/bookings` | Verified customer |
| Profile | `http://localhost/profile` | Signed-in customer |
| Admin dashboard | `http://localhost/admin/dashboard` | Admin |

Screenshots of the current interface have not been added yet.

---

## ✨ Features

### 🔐 Authentication & Accounts
- Registration, login, email verification, password reset, and email-based two-factor authentication.
- Separate customer and administrator access.
- Profile details, avatar uploads, password management, and account deletion.
- API tokens with **Laravel Sanctum**, scoped abilities, and rate limits.

### 🗓️ Services & Bookings
- Service catalog with categories, search, price filters, duration filters, and sorting.
- Guided booking form with available time slots and a monthly calendar.
- Checks for service duration, opening hours, blocked dates, and overlapping reservations.
- Protection against booking overlapping services for the same customer.
- Upcoming, completed, and cancelled booking views.
- Booking details, cancellation, confirmation emails, and reviews for completed bookings.

### 💳 Subscriptions & Billing
- Free booking allowance and paid booking plans.
- Security package checkout and subscription management.
- **Stripe Checkout** with server-side payment verification.
- Cancellation at the end of the billing period and subscription resumption.
- Signed webhooks for activation, renewal, failed payment, and cancellation.
- Subscription management pages and downloadable PDF reports.

### 🌍 Languages & Responsive Design
- **English, Ukrainian, Russian, Polish, and German** on the public interface.
- Language selection persists between visits; administration stays in English.
- Responsive navigation, service cards, booking forms, and account pages.
- Section reveal animations that respect reduced-motion preferences, excluding admin pages.

### 🧰 Workspace & Integrations
- Calendar feed links and calendar export.
- Support tickets with private conversations and administrator replies.
- Booking webhooks, including supported Zapier hooks.
- Branding settings and shareable booking pages.
- Campaign URL tools, configurable emails, and voice-assisted booking drafts.
- Installable web app metadata and a generic offline screen.

External payment, email, broadcasting, and SMS services require their own configuration. Voice input depends on browser support.

---

## 🛠️ Admin Panel Overview

Administrators can open the **Admin** menu after signing in with an admin account.

| Section | Capabilities |
| :--- | :--- |
| **📊 Dashboard** | Booking and registration statistics, date ranges, charts, and exports |
| **🗓️ Bookings** | Browse reservations, inspect details, and update statuses |
| **📆 Calendar** | Block and unblock whole dates |
| **🛎️ Services** | Create and edit services, upload images, and control visibility |
| **🏷️ Categories** | Organize services and manage categories |
| **👥 Users** | Search customers, create accounts, and manage roles |
| **💳 Subscriptions** | Inspect booking plans and manage subscription access |
| **🛡️ Security** | Manage security package inquiries and access |
| **📈 Monitoring** | Inspect application activity and operational events |
| **🔥 Firewall** | Manage rules, blocked IPs, and logs |
| **🔎 Vulnerabilities** | Run and review vulnerability scans |
| **💾 Backups** | Create, list, download, and clean backups |
| **🔑 API access** | Manage API tokens and custom access rules |
| **💬 Support** | Read tickets, reply to customers, and resolve requests |

---

## 🛠️ Tech Stack

| Category | Technologies |
| :--- | :--- |
| **Backend** | PHP 8.4.1+, Laravel 13 |
| **Authentication & API** | Laravel authentication, Laravel Sanctum |
| **Database** | MySQL 8; SQLite in-memory for tests |
| **Frontend** | Blade, Alpine.js, JavaScript, Bootstrap 5, Vite 7 |
| **Payments** | Stripe Checkout, subscriptions, signed webhooks |
| **Reports & Backups** | Dompdf, Spatie Laravel Backup |
| **Infrastructure** | Docker Compose, PHP-FPM, Nginx, cron |
| **Local Tools** | phpMyAdmin, Mailhog, Composer, npm |
| **Testing** | PHPUnit, Node.js test runner, Laravel Pint |

Use **`src/composer.json`** and **`src/package.json`** for application dependencies. The root Composer manifest is a separate development artifact.

---

## 🏃 How to Run Locally

### Prerequisites
- Git and Docker with Docker Compose v2.
- For host tools: PHP 8.4.1+ and Node.js 22.12+ with npm.

### 1. Clone the project

```bash
git clone https://github.com/smipigamer15-crypto/larevel-docker-booking.git
cd larevel-docker-booking
```

### 2. Configure environment variables

Run from the repository root:

```bash
cp .env.example .env
cp src/.env.example src/.env
```

On PowerShell, `Copy-Item` can be used instead of `cp`.

| File | Purpose |
| :--- | :--- |
| `.env` | Docker Compose database credentials |
| `src/.env` | Laravel, mail, Stripe, and optional demo accounts |

Replace the database password placeholders in the root `.env`. Docker passes these credentials to MySQL and Laravel. Keep `src/.env` consistent if you also run PHP tools on the host. Both files are ignored by Git.

For an existing installation, preserve its database credentials and `APP_KEY`. Changing environment variables does not reset passwords in an existing MySQL data directory.

### 3. Start containers and install dependencies

```bash
docker compose up -d --build
docker compose ps
docker compose exec app composer install
docker compose exec app npm ci
```

Wait for MySQL to become healthy before running migrations. The image installs production PHP dependencies; `composer install` in the mounted application also installs development tools.

### 4. Initialize a new installation

Generate a key **only when creating a new installation with an empty `APP_KEY`**:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan storage:link
docker compose exec app npm run build
```

Optional sample categories and services for a **fresh database**:

```bash
docker compose exec app php artisan db:seed --class=ServicesSeeder
```

The services seeder is intended for a fresh database; do not repeatedly seed an existing installation.

### 5. Optional local demo accounts

There are **no default login credentials**. Set `DEMO_ADMIN_EMAIL` / `DEMO_ADMIN_PASSWORD` and/or `DEMO_USER_EMAIL` / `DEMO_USER_PASSWORD` in `src/.env`, then run:

```bash
docker compose exec app php artisan db:seed --class=UserSeeder
```

Passwords require at least 12 characters. Demo account creation is disabled outside `local` and `testing` and does not overwrite existing accounts. After signing in, request email verification and open the message in Mailhog to access verified customer pages.

### 6. Open the application

| Service | Address |
| :--- | :--- |
| **Website** | `http://localhost` |
| **phpMyAdmin** | `http://localhost:8080` |
| **Mailhog** | `http://localhost:8025` |
| **MySQL from host tools** | `127.0.0.1:3307` |
| **MySQL inside Docker** | `db:3306` |

For frontend development, run `npm run dev` from `src/` on the host. If using the container, run `docker compose exec app npm run dev -- --host 0.0.0.0` and expose port 5173 in your local Compose configuration.

On Windows environments that block Vite child processes with `spawn EPERM`, use the alternative build:

```powershell
./src/scripts/build-assets.ps1
```

It uses the installed native esbuild compiler and generates the Laravel asset manifest. The regular `npm run build` remains the default build command.

---

## 💳 Stripe Setup

Set your own Stripe test-mode configuration in **`src/.env`**:

```dotenv
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
STRIPE_PRICE_BOOKING_PRO=
STRIPE_PRICE_BOOKING_ENTERPRISE=
STRIPE_PRICE_PRO=
STRIPE_PRICE_ENTERPRISE=
```

Booking Business/Enterprise checkout uses `STRIPE_PRICE_BOOKING_ENTERPRISE`. Security packages use `STRIPE_PRICE_PRO` and `STRIPE_PRICE_ENTERPRISE`.

The webhook endpoint is **`POST /stripe/webhook`**. Configure these events:

- `checkout.session.completed`
- `customer.subscription.updated`
- `customer.subscription.deleted`
- `invoice.payment_succeeded`
- `invoice.payment_failed`

For local development with the Stripe CLI:

```bash
stripe listen --forward-to localhost/stripe/webhook
```

Put the signing secret reported by the CLI into `STRIPE_WEBHOOK_SECRET`. Verify a full test Checkout payment and subscription lifecycle before switching to live credentials. Never commit API keys or signing secrets.

---

## 🧪 Tests & Code Quality

```bash
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
docker compose exec app node --test tests/Frontend/*.test.mjs
docker compose exec app npm run build
```

**`src/phpunit.dist.xml`** uses an isolated SQLite in-memory database. PHPUnit and Pint require development Composer dependencies. The current Dockerfile installs `pdo_mysql`; the default test suite additionally needs `pdo_sqlite`. Use a PHP runtime with SQLite enabled or add the extension to your development image.

On Windows where Node.js test subprocesses are restricted, run from `src/`:

```bash
node --test --experimental-test-isolation=none tests/Frontend/*.test.mjs
```

Coverage includes booking availability, ownership, API permissions, authentication, profiles, languages, admin pages, catalog management, migrations, and subscription events. Payment tests simulate provider responses and do not charge cards.

---

## 🐳 Useful Docker Commands

| Command | Description |
| :--- | :--- |
| `docker compose up -d` | Start containers |
| `docker compose down` | Stop containers without deleting database files |
| `docker compose ps` | Show container status |
| `docker compose logs --tail=100 app` | Show recent application container logs |
| `docker compose logs -f` | Follow container logs |
| `docker compose restart` | Restart containers |
| `docker compose up -d --build` | Rebuild and start containers |
| `docker compose exec app php artisan route:list` | List application routes |
| `docker compose exec app php artisan queue:work` | Run a queue worker |
| `docker compose exec app php artisan schedule:list` | Inspect scheduled tasks |


