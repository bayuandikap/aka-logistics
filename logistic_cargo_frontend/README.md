### High-Level Architecture Flow

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
```
