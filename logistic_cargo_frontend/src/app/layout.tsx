
import type { Metadata } from "next";
import localFont from "next/font/local";
import Link from "next/link";
import "./globals.css";

const geistSans = localFont({
  src: "./fonts/GeistVF.woff",
  variable: "--font-geist-sans",
  weight: "100 900",
});

const geistMono = localFont({
  src: "./fonts/GeistMonoVF.woff",
  variable: "--font-geist-mono",
  weight: "100 900",
});

export const metadata: Metadata = {
  title: {
    default: "AKA Cargo | Logistik & Pelacakan Pengiriman",
    template: "%s | AKA Cargo",
  },
  description:
    "Lacak pengiriman dan hitung estimasi ongkos kirim AKA Cargo dengan mudah.",
};

function SiteHeader() {
  return (
    <header className="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur">
      <div className="mx-auto flex min-h-[72px] max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
        <Link href="/" aria-label="AKA Cargo - Beranda" className="shrink-0">
          <span className="text-xl font-black tracking-tight text-slate-900 sm:text-2xl">
            AKA <span className="text-orange-600">CARGO</span>
          </span>
          <span className="hidden text-[10px] font-medium uppercase tracking-[0.19em] text-slate-500 sm:block">
            Logistik terpercaya
          </span>
        </Link>

        <nav
          aria-label="Navigasi utama"
          className="flex items-center gap-1 sm:gap-2"
        >
          <Link
            href="/"
            className="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950"
          >
            <span className="hidden sm:inline">Lacak Resi</span>
            <span className="sm:hidden">Lacak</span>
          </Link>

          <Link
            href="/freight-calculator"
            className="rounded-lg bg-orange-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 sm:px-4"
          >
            <span className="hidden sm:inline">Kalkulator Ongkir</span>
            <span className="sm:hidden">Ongkir</span>
          </Link>
        </nav>
      </div>
    </header>
  );
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="id">
      <body
        className={`${geistSans.variable} ${geistMono.variable} min-h-screen bg-slate-50 font-sans text-slate-900 antialiased`}
      >
        <SiteHeader />
        {children}
      </body>
    </html>
  );
}
