```markdown
# 📦 AKA Logistics & Freight Management System — Backend Core

> The robust enterprise core, RESTful API service, and administrative back-office for the AKA Logistics platform, built on Laravel 11 and Filament v3.

---

## 🏗️ Backend Architecture & Tech Stack

- **Framework:** Laravel 11 (PHP 8.2+)
- **Admin Panel:** Filament v3 (Livewire / Reactive Forms)
- **Database Persistence:** MySQL 8.0 / PostgreSQL 16 (Indexed Relational Schema)
- **API Security:** Laravel Sanctum, Rate Limiting (60 req/min/IP), CORS Origin Control
- **Key Design Patterns:** Service Layer Pattern, ACID Database Transactions, Eloquent API Resources

---

## 📂 Project Directory Structure

```text
aka-logistics-backend/
├── app/
│   ├── Filament/Resources/       # Filament v3 Admin CRUD panels (Shipments, Branches, Tariffs)
│   ├── Http/
│   │   ├── Controllers/Api/      # Public RESTful API endpoints (Tracking, Tariff Calculator)
│   │   └── Middleware/           # CORS, Rate Limiting, and Custom Request Filters
│   ├── Models/                   # Eloquent models (Shipment, Branch, TrackingCheckpoint, Tariff)
│   └── Services/                 # Business logic engines (FreightCalculatorService)
├── database/
│   ├── migrations/               # Normalized relational schema DDL migrations
│   └── seeders/                  # Indonesian hub seeders & test datasets
└── routes/
    ├── api.php                   # Public API endpoints (/api/v1/tracking, /api/v1/tariffs)
    └── web.php                   # Filament admin panel routing

```

---

## 📊 Relational Database Schema

The core database uses strict referential integrity and B-Tree indexes for sub-millisecond public tracking queries:

1. **`branches`**: Master locations for origin/destination ports and hubs (e.g., Surabaya `SUB01`, Bau-Bau `BAU01`).
2. **`shipments`**: Main consignment ledger storing sender/recipient data, service types, and calculated chargeable weights.
3. **`tracking_checkpoints`**: Chronological event logs recording transit milestones with operator stamps and location details.
4. **`tariffs`**: Route-based pricing matrix handling base fees and extra weight charges per kilogram.

---

## 🔌 API Endpoints Documentation

### 1. Track Consignment Status

* **Endpoint:** `GET /api/v1/tracking/{tracking_number}`
* **Description:** Retrieves complete shipment details, current routing status, and chronological checkpoint timeline.
* **Success Response (`200 OK`):**
```json
{
  "meta": {
    "status": "success",
    "api_version": "v1.2"
  },
  "data": {
    "tracking_number": "AKA-202610-0091",
    "service_name": "Maritime Cargo Reguler",
    "current_status": "IN_MARITIME_TRANSIT",
    "routing": {
      "origin": { "code": "SUB", "city": "Surabaya" },
      "destination": { "code": "BAU", "city": "Bau-Bau" }
    },
    "timeline": [
      {
        "status": "IN_MARITIME_TRANSIT",
        "title": "Kargo Berangkat Berlayar",
        "location": "Pelabuhan Tanjung Perak, Surabaya",
        "occurred_at": "2026-10-15 08:30:00"
      }
    ]
  }
}

```



---

## 🚀 Local Setup & Installation Guide

1. **Clone the repository (or navigate inside the umbrella repo):**
```bash
cd aka-logistics-backend

```


2. **Install PHP dependencies via Composer:**
```bash
composer install

```


3. **Configure Environment Variables:**
```bash
cp .env.example .env
php artisan key:generate

```


*Update your database credentials inside `.env`:*
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aka_logistics
DB_USERNAME=root
DB_PASSWORD=

```


4. **Run Migrations & Seeders:**
```bash
php artisan migrate --seed

```


5. **Create Filament Administrator Account:**
```bash
php artisan filament:user

```


6. **Run Local Development Server:**
```bash
php artisan serve

```


* Access the Admin Panel at: `http://127.0.0.1:8000/admin`
* Access the API at: `http://127.0.0.1:8000/api/v1/tracking/{resi}`



```
