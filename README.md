# 📦 AKA Logistics & Freight Management System (AKA-CMS)

> An enterprise-grade cargo and freight management platform featuring a high-performance Next.js public tracking portal and a reactive Filament v3 back-office administration panel.

---

## 📂 Repository Structure
- **[`aka-logistics-backend/`](./aka-logistics-backend)**: Laravel 11 REST API core, relational database schema, and Filament v3 operational back-office.
- **[`aka-logistics-client/`](./aka-logistics-client)**: Next.js 14 App Router public tracking portal and dynamic tariff calculator.

---

## 🏗️ System Architecture Flow

```text
[ Next.js 14 Public Client ]           [ Filament v3 Back-Office Panel ]
   (Customer Self-Service)                  (Warehouse Dispatchers)
              │                                        │
  (HTTPS JSON / Rate-Limited)               (Session Auth + CSRF)
              ▼                                        ▼
   ┌───────────────────────────────────────────────────────────────┐
   │                  LARAVEL 11 CORE BACKEND                      │
   │  • Freight Calculation Engine (Actual vs Volumetric Weight)   │
   │  • Multi-Hub Checkpoint Transit Logger (Event Timeline)       │
   │  • Strict DB Transactions (ACID) & Barcode Generation         │
   └──────────────────────────────┬────────────────────────────────┘
                                  │
                                  ▼
   ┌───────────────────────────────────────────────────────────────┐
   │               RELATIONAL DATABASE (MySQL/PostgreSQL)          │
   │   • Indexing: tracking_number, branch_id, event_timestamp     │
   │   • Referential Integrity (Foreign Keys, Cascade Rules)       │
   └───────────────────────────────────────────────────────────────┘