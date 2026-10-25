# 🌐 AKA Logistics & Freight Management System — Client Presentation Portal

> High-performance client-facing portal and tracking interface built with Next.js 14 App Router, featuring Server-Side Rendering (SSR) for instant tracking milestones and dynamic tariff calculations.

---

## 🏗️ Frontend Architecture & Tech Stack

- **Framework:** Next.js 14 (App Router, Server Components & Client Hooks)
- **Styling:** Tailwind CSS, Lucide Icons
- **Data Fetching:** Native `fetch` with incremental static revalidation and cache control
- **Integration:** Communicates directly with the Laravel 11 Headless REST API core

---

## 📂 Project Directory Structure

```text
aka-logistics-client/
├── app/
│   ├── tracking/
│   │   └── [awb]/                # Dynamic SSR route for tracking consignment milestones
│   ├── calculator/               # Interactive dynamic freight tariff calculation page
│   ├── layout.tsx                # Root layout with responsive navigation & styling
│   └── page.tsx                  # Landing page & fast AWB tracking search bar
├── public/                       # Static brand assets and icons
└── tailwind.config.js            # Tailwind CSS styling configurations
✨ Key Client Features
Fast AWB Tracking (SSR): Allows users to look up shipment statuses instantly by entering their tracking number (AKA-YYYYMM-XXXXX), rendering the chronological progress timeline directly on the server for optimal SEO and performance.

Interactive Tariff Calculator: Computes estimated shipping costs by taking origin and destination branch matrices along with actual and volumetric weights (P x L x T / 4000).

Responsive Mobile-First UI: Built with Tailwind CSS to ensure seamless viewing across mobile devices, tablets, and desktop displays.

🚀 Local Setup & Installation Guide
Navigate to the client directory (or your frontend folder):

Bash
cd aka-logistics-client
Install Node.js dependencies:

Bash
npm install
Configure Environment Variables:
Create a .env.local file in the root of this frontend folder:

Code snippet
NEXT_PUBLIC_API_URL=[http://127.0.0.1:8000/api/v1](http://127.0.0.1:8000/api/v1)
(For production deployment on Vercel, point this to your live backend domain, e.g., https://aka-backend-production.up.railway.app/api/v1)

Run the Development Server:

Bash
npm run dev
Access the portal in your browser at: http://localhost:3000