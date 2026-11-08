# AKA Logistics — Cargo & Freight Management System

A full-stack logistics management application for managing shipment operations, calculating freight charges, and giving customers shipment-tracking visibility.

The project is organized as two applications:

- **Laravel 11 + Filament 3** — backend, REST API, and internal operations dashboard.
- **Next.js 14** — customer-facing tracking portal and freight calculator.

> This is a portfolio project. Use test data only; do not enter real customer or shipment information.

## Features

### Operations dashboard

- Shipment overview statistics.
- Shipment activity and status charts.
- Recent shipment list.
- Operational alerts for shipments requiring attention.

### Shipment management

- Create and manage shipment records.
- Store sender and recipient details, origin and destination branches, and service type.
- Calculate freight charges using actual and volumetric weight.
- Preserve applied tariff values on the shipment record for historical pricing consistency.
- Print an A4 Air Waybill (AWB) using the shipment's tracking number.

### Tracking and customer tools

- Public shipment tracking by tracking number.
- Shipment milestone timeline based on tracking checkpoints.
- Freight quote calculator.
- Branch list endpoint for the client application.

### Access control

- Role- and permission-based access to operational resources.
- Protected Filament administration panel.
- Rate-limited public tracking and freight quote endpoints.

## Technology stack

| Area | Technology |
|---|---|
| Backend | PHP 8.2+, Laravel 11 |
| Admin panel | Filament 3 |
| Authorization | Spatie Laravel Permission |
| API authentication package | Laravel Sanctum |
| Frontend | Next.js 14, React 18, TypeScript |
| Styling | Tailwind CSS |
| Database | SQLite by default; MySQL configuration is also available |

## Repository structure

```text
aka-logistics/
├── aka-logistic-backend/   # Laravel application, API, Filament admin
├── aka-logistics-client/   # Next.js customer portal
└── README.md
```

## Architecture

```text
Customer browser
      |
      v
Next.js client
  |-- Shipment tracking page
  |-- Freight quote calculator
      |
      | HTTP requests (/api/v1)
      v
Laravel 11 backend
  |-- Tracking API
  |-- Freight calculator API
  |-- Filament operations panel
  |-- Shipment and checkpoint domain logic
      |
      v
Relational database
  |-- Branches and tariffs
  |-- Shipments
  |-- Tracking checkpoints
```

The backend exposes these public client endpoints:

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/api/v1/tracking/{tracking_number}` | Retrieve tracking information |
| POST | `/api/v1/freight/quote` | Calculate a freight quote |
| GET | `/api/v1/branches` | Retrieve available branches |

The tracking, freight quote, and branch endpoints are rate-limited. The authenticated `/api/user` endpoint is separate from the public client endpoints.

## Local development

### Requirements

- PHP 8.2 or newer and Composer
- Node.js compatible with Next.js 14 and npm
- A database supported by Laravel

### 1. Set up the Laravel backend

```bash
cd aka-logistic-backend
composer install
cp .env.example .env
php artisan key:generate
```

The example environment uses SQLite. Create the database file before running migrations:

**Windows PowerShell**
```powershell
New-Item -ItemType File -Path database/database.sqlite -Force
```

Set `DB_DATABASE` in `.env` to the absolute path of that file if your environment does not resolve the relative path correctly. Keep the database connection consistent with the database you intend to use.

Run migrations and seeders:

```bash
php artisan migrate --seed
```

Install frontend assets used by the Laravel application and start the backend:

```bash
npm install
npm run build
php artisan serve
```

The Laravel application is available at `http://127.0.0.1:8000`. The Filament panel is available at `/admin`, subject to the configured user access and permissions.

### 2. Set up the Next.js client

Open a second terminal:

```bash
cd aka-logistics-client
npm install
```

Create `.env.local` in the client directory:

```env
NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api/v1
```

Start the client:

```bash
npm run dev
```

Open `http://localhost:3000`.

For production, configure `NEXT_PUBLIC_API_URL` to point to the deployed Laravel API before building the Next.js application.

## Important implementation notes

- Freight calculation is handled by the backend service; the client should treat the backend quote as the source of truth.
- Shipment records retain applied tariff values so later tariff changes do not rewrite historical shipment pricing.
- Tracking status is derived from the latest checkpoint event.
- The AWB uses the existing shipment tracking number; it does not introduce a separate AWB numbering system.
- Never commit real `.env` files, credentials, database files, or personal customer data.

## Project status

The `development` branch contains the dashboard and printable AWB work. Additional features may be developed on separate feature branches and merged after review and testing.

## License

This repository is a portfolio project. Add or update a license file if you intend to grant reuse rights.
