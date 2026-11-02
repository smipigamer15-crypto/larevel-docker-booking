# 📅 Booking System

## 📸 Screenshots

Here is a quick look at the application:

<table>
  <tr>
    <td align="center" width="50%">
      <b>Home Page</b><br>
      <img src="https://github.com/user-attachments/assets/e1c8a895-3028-4556-83c6-29bcec7a6623" width="100%" style="border-radius: 8px;" alt="Home Page">
    </td>
    <td align="center" width="50%">
      <b>Services Catalog</b><br>
      <img src="https://github.com/user-attachments/assets/817908cd-65e2-435b-8338-cb5d49fc0fc5" width="100%" style="border-radius: 8px;" alt="News Article">
    </td>
  </tr>
  <tr>
    <td align="center" width="50%">
      <b>Booking Calendar</b><br>
      <img src="https://github.com/user-attachments/assets/723d5cec-1961-423f-b365-9d33c6cca602" width="100%" style="border-radius: 8px;" alt="Admin Dashboard">
    </td>
    <td align="center" width="50%">
      <b>Mobile Version</b><br>
      <img src="https://github.com/user-attachments/assets/3c132187-867e-4be6-9a0b-d3bc5a573ee7" width="100%" style="border-radius: 8px;" alt="Mobile Version">
    </td>
  </tr>
</table>

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

---

## 🛠️ Admin Panel Overview

Administrators can open the **Admin** menu after signing in with an admin account.

- **📊 Dashboard** Booking and registration statistics, date ranges, charts, and exports 
- **🗓️ Bookings**  Browse reservations, inspect details, and update statuses 
- **📆 Calendar**  Block and unblock whole dates 
- **🛎️ Services**  Create and edit services, upload images, and control visibility 
- **🏷️ Categories**  Organize services and manage categories 
- **👥 Users**  Search customers, create accounts, and manage roles 
- **💳 Subscriptions**  Inspect booking plans and manage subscription access 
- **🛡️ Security**  Manage security package inquiries and access 
- **📈 Monitoring**  Inspect application activity and operational events 
- **🔥 Firewall**  Manage rules, blocked IPs, and logs 
- **🔎 Vulnerabilities**  Run and review vulnerability scans 
- **💾 Backups**  Create, list, download, and clean backups 
- **🔑 API access**  Manage API tokens and custom access rules 
- **💬 Support**  Read tickets, reply to customers, and resolve requests 

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

---

## 🏃 How to Run Locally

### Prerequisites
- Docker & Docker Compose
- Git

### Installation

Clone the repository and navigate to the project folder:
```bash
git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name
```

Configure Environment Variables
```bash
cp .env.docker .env
```

Start Docker Containers
```bash
docker compose up -d --build
```

Install PHP Dependencies
```bash
docker compose exec app composer install
```

Generate Application Key
```bash
docker compose exec app php artisan key:generate
```

Run Database Migrations
```bash
docker compose exec app php artisan migrate --seed
```

Install Frontend Dependencies
```bash
npm install
npm run build
------------
npm run dev
```

Open the Website
```bash
http://localhost
```

### 🐳 Useful Docker Commands

| Command |	Description |
| ------- | ----------- |
| ``` docker compose up -d ``` | Start containers | 
| ``` docker compose down ``` | Stop containers | 
| ``` docker compose ps ```	| Check container status |
| ``` docker compose logs -f ``` | View application logs |
| ``` docker compose restart ``` | Restart containers |
| ``` docker compose up -d --build ``` | Rebuild containers |


