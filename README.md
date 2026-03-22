# HOBMS — Hotel Online Booking Management System

A full-stack hotel management web application built with **Laravel 13**, **Livewire 4**, **Flux UI**, and **Tailwind CSS v4**. HOBMS handles the complete guest lifecycle — from online room browsing and self-service booking to front-desk management, payments, complaints, and reporting.

---

## Features

### Public Portal (Guest-Facing)
- **Room Browsing** — View available room categories with pricing and capacity, all on one page
- **Online Booking** — Guests can book a room directly from the public website
- **Booking Tracker** — Guests look up their reservation status using their booking reference number
- **Booking Confirmation** — Shareable confirmation page with booking details via a secure token

### Staff Dashboard (Authenticated)
- **Dashboard** — Live stats: today's check-ins, check-outs, available rooms, and revenue
- **Bookings** — View, filter, and manage all reservations; update booking status (Pending → Confirmed → Checked In → Checked Out)
- **Walk-in Bookings** — Staff can create manual bookings for walk-in guests
- **Payments** — Record and track payments per booking; supports Cash, Card, and Bank Transfer
- **Complaints** — Log and resolve guest complaints with status tracking

### Admin-Only Management
- **Room Categories** — Create and manage room types with pricing, capacity, and descriptions
- **Rooms** — Manage individual rooms with status tracking (Available, Occupied, Under Maintenance, Out of Order)
- **Reports** — Revenue summaries and occupancy analytics
- **Staff Management** — Create and manage staff accounts with role assignment

### System Features
- **Role-Based Access Control** — Admin and Receptionist roles with middleware enforcement
- **Light / Dark Mode** — Persistent theme preference stored in `localStorage`
- **Responsive Design** — Mobile-first layout with hamburger navigation
- **Scroll to Top** — Floating button on all pages
- **Two-Factor Authentication** — Optional TOTP 2FA via Laravel Fortify

---

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.4 |
| Framework | Laravel 13 |
| Authentication | Laravel Fortify v1 |
| Frontend | Livewire 4 + Alpine.js |
| UI Components | Flux UI v2 (Free) |
| Styling | Tailwind CSS v4 |
| Build Tool | Vite 8 |
| Testing | Pest v4 / PHPUnit v12 |
| Code Style | Laravel Pint |

---

## User Roles

| Role | Access |
|---|---|
| **Admin** | Full access — all staff features + room categories, room management, complaints, reports, staff management |
| **Receptionist** | Bookings, walk-in bookings, payments |
| **Guest** | Public website, online booking, booking tracker |

---

## Booking Statuses

| Status | Description |
|---|---|
| `Pending` | Booking submitted, awaiting confirmation |
| `Confirmed` | Booking confirmed by staff |
| `Checked In` | Guest has arrived and checked in |
| `Checked Out` | Guest has departed |
| `Cancelled` | Booking was cancelled |
| `No Show` | Guest did not arrive |

---

## Payment Statuses

| Status | Description |
|---|---|
| `Unpaid` | No payment recorded yet |
| `Partially Paid` | Partial payment received |
| `Paid` | Payment settled in full |

---

## Getting Started

### Requirements
- PHP 8.4+
- Composer
- Node.js 20+
- A database (MySQL, PostgreSQL, or SQLite)

### Installation

```bash
# 1. Clone the repository
git clone <repo-url> HOBMS
cd HOBMS

# 2. Run the setup script (installs dependencies, sets up env, runs migrations)
composer run setup

# 3. Seed the database with sample data
php artisan db:seed
```

### Development Server

```bash
composer run dev
```

This starts the Laravel server, Vite dev server, queue worker, and log watcher concurrently.

### Running Tests

```bash
php artisan test
```

Or with linting:

```bash
composer run test
```

---

## Database

| Table | Description |
|---|---|
| `users` | Staff accounts with roles (Admin / Receptionist) |
| `room_categories` | Room types with pricing, capacity, and descriptions |
| `rooms` | Individual rooms assigned to a category with status tracking |
| `bookings` | Guest reservations with check-in/out dates and booking status |
| `payments` | Payment records per booking |
| `complaints` | Guest complaints with resolution tracking |

---

## Project Structure

```
app/
├── Enums/          # BookingStatus, RoomStatus, PaymentStatus, UserRole, etc.
├── Models/         # Eloquent models
├── Http/
│   ├── Controllers/
│   └── Middleware/
resources/
├── views/
│   ├── welcome.blade.php       # Public landing page
│   ├── layouts/                # App, Auth, Public layouts
│   └── pages/                  # Livewire page components (SFC)
│       ├── booking/            # Online booking flow
│       ├── bookings/           # Staff booking management
│       ├── complaints/
│       ├── payments/
│       ├── portal/             # Guest booking tracker
│       ├── reports/
│       ├── room-categories/
│       ├── rooms/              # Public room browsing
│       ├── rooms-manage/       # Staff room management
│       ├── settings/
│       └── staff/
database/
├── migrations/
├── factories/
└── seeders/
tests/
├── Feature/
└── Unit/
```

---

## Key Scripts

| Command | Description |
|---|---|
| `composer run setup` | Full setup: install deps, migrate, build assets |
| `composer run dev` | Start all dev servers (Laravel + Vite + Queue + Logs) |
| `composer run test` | Lint check + run all tests |
| `composer run lint` | Auto-fix code style with Pint |
| `npm run build` | Build production assets |

---

## Developed by

**CodeHub.Site** — Hotel Online Booking Management System © 2026
