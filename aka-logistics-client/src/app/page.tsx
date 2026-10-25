
"use client";

import { FormEvent, useState } from "react";
import Link from "next/link";

type TrackingEvent = {
  status: string;
  title: string;
  location: string;
  description: string;
  occurred_at: string;
};

type TrackingData = {
  tracking_number: string;
  service_type: string;
  status_summary: {
    code: string;
    display_text: string;
    updated_at: string | null;
  };
  routing: {
    origin: { name?: string; city?: string } | null;
    destination: { name?: string; city?: string } | null;
    estimated_delivery: string | null;
  };
  shipment_metrics: {
    weight_kg: number;
  };
  timeline: TrackingEvent[];
};

type ApiResponse = {
  data?: TrackingData;
  error?: { message: string };
};

const API_URL =
  process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000";

function formatDate(value: string | null) {
  if (!value) return "Belum tersedia";

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;

  return new Intl.DateTimeFormat("id-ID", {
    dateStyle: "medium",
    timeStyle: "short",
  }).format(date);
}

function locationName(
  location: { name?: string; city?: string } | null
) {
  return location?.name || location?.city || "Belum tersedia";
}

function statusLabel(status: string) {
  const labels: Record<string, string> = {
    REGISTERED: "Barang diterima",
    IN_TRANSIT: "Dalam perjalanan",
    IN_MARITIME_TRANSIT: "Dalam pelayaran laut",
    ARRIVED_AT_DESTINATION: "Tiba di cabang tujuan",
    DELIVERED: "Terkirim",
    ON_HOLD: "Ditahan",
  };

  return labels[status] || status.replaceAll("_", " ");
}

export default function Home() {
  const [awb, setAwb] = useState("");
  const [shipment, setShipment] = useState<TrackingData | null>(null);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  async function handleTrack(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();

    const trackingNumber = awb.trim().toUpperCase();
    if (!trackingNumber) return;

    setLoading(true);
    setError("");
    setShipment(null);

    try {
      const response = await fetch(
        `${API_URL}/api/v1/tracking/${encodeURIComponent(trackingNumber)}`,
        { headers: { Accept: "application/json" } }
      );

      const result: ApiResponse = await response.json();

      if (!response.ok || !result.data) {
        throw new Error(
          result.error?.message || "Nomor resi tidak ditemukan."
        );
      }

      setShipment(result.data);
    } catch (err) {
      setError(
        err instanceof TypeError
          ? "Tidak dapat terhubung ke server. Pastikan Laravel berjalan."
          : err instanceof Error
            ? err.message
            : "Terjadi kesalahan. Silakan coba lagi."
      );
    } finally {
      setLoading(false);
    }
  }

  return (
    <main className="min-h-screen bg-slate-50 text-slate-900">

      <section className="bg-slate-900 px-5 py-16 text-white sm:py-20">
        <div className="mx-auto max-w-4xl">
          <p className="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">
            Sistem pelacakan pengiriman
          </p>

          <h1 className="mt-4 text-3xl font-bold sm:text-5xl">
            Lacak kargo Anda.
            <br />
            <span className="text-orange-400">Ketahui setiap perjalanan.</span>
          </h1>

          <p className="mt-5 max-w-2xl text-slate-300">
            Masukkan nomor resi untuk melihat status terbaru, lokasi, dan
            riwayat perjalanan kiriman Anda.
          </p>

          <form
            onSubmit={handleTrack}
            className="mt-8 flex flex-col gap-3 rounded-2xl bg-white p-3 sm:flex-row"
          >
            <label htmlFor="awb" className="sr-only">
              Nomor resi
            </label>

            <input
              id="awb"
              value={awb}
              onChange={(event) => setAwb(event.target.value)}
              placeholder="Masukkan nomor resi, mis. AKA-202610-LSY1"
              autoComplete="off"
              required
              className="min-w-0 flex-1 rounded-xl px-4 py-3 text-slate-900 outline-none ring-orange-500 focus:ring-2"
            />

            <button
              type="submit"
              disabled={loading}
              className="rounded-xl bg-orange-600 px-7 py-3 font-semibold text-white transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
              {loading ? "Mencari..." : "Lacak Sekarang"}
            </button>
          </form>

          {error && (
            <div
              role="alert"
              className="mt-5 rounded-xl border border-red-300 bg-red-950/50 p-4 text-red-100"
            >
              {error}
            </div>
          )}
        </div>
      </section>

      <section className="mx-auto max-w-5xl px-5 py-10 sm:py-14">
        {!shipment && !error && !loading && (
          <div className="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
            <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-orange-50 text-2xl text-orange-600">
              ↗
            </div>
            <h2 className="mt-4 text-lg font-bold">
              Informasi pengiriman Anda
            </h2>
            <p className="mt-2 text-sm text-slate-500">
              Hasil pelacakan akan tampil di sini setelah Anda memasukkan nomor resi.
            </p>
          </div>
        )}

        {loading && (
          <div className="rounded-2xl border bg-white p-10 text-center">
            <p className="font-medium">Sedang mencari pengiriman...</p>
            <p className="mt-2 text-sm text-slate-500">
              Mohon tunggu sebentar.
            </p>
          </div>
        )}

        {shipment && (
          <div className="space-y-6">
            <div className="rounded-2xl border bg-white p-6 shadow-sm sm:p-8">
              <div className="flex flex-col justify-between gap-5 sm:flex-row sm:items-start">
                <div>
                  <p className="text-sm text-slate-500">Nomor resi</p>
                  <h2 className="mt-1 break-all text-2xl font-bold">
                    {shipment.tracking_number}
                  </h2>
                  <p className="mt-2 text-sm text-slate-500">
                    Layanan: {shipment.service_type.replaceAll("_", " ")}
                  </p>
                </div>

                <span className="inline-flex w-fit rounded-full bg-orange-50 px-4 py-2 text-sm font-semibold text-orange-800">
                  {statusLabel(shipment.status_summary.code)}
                </span>
              </div>

              <div className="mt-7 grid gap-4 border-t pt-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                  <p className="text-sm text-slate-500">Asal</p>
                  <p className="mt-1 font-semibold">
                    {locationName(shipment.routing.origin)}
                  </p>
                </div>
                <div>
                  <p className="text-sm text-slate-500">Tujuan</p>
                  <p className="mt-1 font-semibold">
                    {locationName(shipment.routing.destination)}
                  </p>
                </div>
                <div>
                  <p className="text-sm text-slate-500">Berat</p>
                  <p className="mt-1 font-semibold">
                    {shipment.shipment_metrics.weight_kg} kg
                  </p>
                </div>
                <div>
                  <p className="text-sm text-slate-500">Estimasi tiba</p>
                  <p className="mt-1 font-semibold">
                    {shipment.routing.estimated_delivery
                      ? formatDate(shipment.routing.estimated_delivery)
                      : "Belum tersedia"}
                  </p>
                </div>
              </div>
            </div>

            <div className="rounded-2xl border bg-white p-6 shadow-sm sm:p-8">
              <div className="flex items-center justify-between gap-4">
                <h2 className="text-xl font-bold">Riwayat pengiriman</h2>
                <span className="text-sm text-slate-500">
                  {shipment.timeline.length} pembaruan
                </span>
              </div>

              {shipment.timeline.length === 0 ? (
                <div className="mt-6 rounded-xl bg-slate-50 p-5 text-sm text-slate-600">
                  Belum ada riwayat perjalanan untuk kiriman ini. Silakan
                  periksa kembali nanti.
                </div>
              ) : (
                <ol className="mt-7 space-y-7">
                  {shipment.timeline.map((item, index) => (
                    <li
                      key={`${item.occurred_at}-${index}`}
                      className="relative flex gap-4"
                    >
                      <div className="flex flex-col items-center">
                        <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-orange-100 text-sm font-bold text-orange-700">
                          {shipment.timeline.length - index}
                        </span>
                        {index < shipment.timeline.length - 1 && (
                          <span className="mt-2 min-h-8 w-px flex-1 bg-slate-200" />
                        )}
                      </div>

                      <div className="min-w-0 flex-1 pb-1">
                        <p className="font-semibold">
                          {item.title || statusLabel(item.status)}
                        </p>
                        <p className="mt-1 text-sm font-medium text-orange-700">
                          {item.location}
                        </p>
                        <p className="mt-2 text-sm leading-6 text-slate-600">
                          {item.description}
                        </p>
                        <time className="mt-2 block text-xs text-slate-400">
                          {formatDate(item.occurred_at)}
                        </time>
                      </div>
                    </li>
                  ))}
                </ol>
              )}
            </div>
          </div>
        )}
      </section>

      <footer className="border-t bg-white px-5 py-7">
        <div className="mx-auto flex max-w-6xl flex-col justify-between gap-2 text-sm text-slate-500 sm:flex-row">
          <p>© {new Date().getFullYear()} AKA Cargo. Hak cipta dilindungi.</p>
          <p>Sistem Pelacakan Logistik dan Kargo</p>
        </div>
      </footer>
    </main>
  );
}
