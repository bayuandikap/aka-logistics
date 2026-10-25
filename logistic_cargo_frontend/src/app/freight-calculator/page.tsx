"use client";

import { FormEvent, useEffect, useState } from "react";

type Branch = {
  id: number;
  name: string;
};

type Quote = {
  actual_weight_kg: number;
  volume_weight_kg: number;
  chargeable_weight_kg: number;
  base_fee: number;
  rate_per_kg: number;
  weight_fee: number;
  total_amount: number;
  currency: string;
};

const API_URL =
  process.env.NEXT_PUBLIC_API_URL ?? "http://127.0.0.1:8000";

const money = (value: number) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(value);

export default function FreightCalculatorPage() {
  const [branches, setBranches] = useState<Branch[]>([]);
  const [origin, setOrigin] = useState("");
  const [destination, setDestination] = useState("");
  const [service, setService] = useState("REGULAR_SEA");
  const [weight, setWeight] = useState("12");
  const [length, setLength] = useState("40");
  const [width, setWidth] = useState("30");
  const [height, setHeight] = useState("20");
  const [quote, setQuote] = useState<Quote | null>(null);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    fetch(`${API_URL}/api/v1/branches`)
      .then(async (response) => {
        if (!response.ok) {
          throw new Error("Daftar cabang belum dapat dimuat.");
        }
        return response.json();
      })
      .then((result) => {
        const list: Branch[] = Array.isArray(result)
          ? result
          : result.data ?? [];

        setBranches(list);
      })
      .catch(() => {
        setError(
          "Daftar cabang gagal dimuat. Endpoint daftar cabang perlu disiapkan di Laravel."
        );
      });
  }, []);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setError("");
    setQuote(null);

    if (!origin || !destination || origin === destination) {
      setError("Pilih cabang asal dan tujuan yang berbeda.");
      return;
    }

    setLoading(true);

    try {
      const response = await fetch(`${API_URL}/api/v1/freight/quote`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          origin_branch_id: Number(origin),
          destination_branch_id: Number(destination),
          service_type: service,
          actual_weight_kg: Number(weight),
          length_cm: Number(length),
          width_cm: Number(width),
          height_cm: Number(height),
        }),
      });

      const result = await response.json();

      if (!response.ok) {
        const messages = result.errors
          ? Object.values(result.errors).flat().join(" ")
          : result.message;

        throw new Error(messages || "Kalkulasi ongkir gagal.");
      }

      setQuote(result.data);
    } catch (err) {
      setError(
        err instanceof Error
          ? err.message
          : "Tidak dapat terhubung ke server."
      );
    } finally {
      setLoading(false);
    }
  }

  return (
    <main className="min-h-screen bg-slate-50 px-4 py-10 text-slate-900">
      <div className="mx-auto max-w-5xl">
        <div className="mb-8">
          <p className="text-sm font-semibold uppercase tracking-[0.18em] text-orange-600">
            Layanan pengiriman
          </p>

          <h1 className="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
            Kalkulator Ongkos Kirim
          </h1>

          <p className="mt-3 max-w-2xl text-base leading-7 text-slate-600">
            Hitung estimasi biaya pengiriman berdasarkan rute, jenis layanan,
            berat aktual, dan dimensi paket Anda.
          </p>
        </div>

        <div className="grid gap-6 md:grid-cols-[1.2fr_0.8fr]">
          <form
            onSubmit={handleSubmit}
            className="space-y-6 rounded-2xl border bg-white p-6 shadow-sm"
          >
            <h2 className="text-xl font-semibold">Detail pengiriman</h2>

            <div className="grid gap-4 sm:grid-cols-2">
              <label className="text-sm font-medium">
                Cabang asal
                <select
                  required
                  value={origin}
                  onChange={(e) => setOrigin(e.target.value)}
                  className="mt-2 w-full rounded-lg border p-3"
                >
                  <option value="">Pilih cabang</option>
                  {branches.map((branch) => (
                    <option key={branch.id} value={branch.id}>
                      {branch.name}
                    </option>
                  ))}
                </select>
              </label>

              <label className="text-sm font-medium">
                Cabang tujuan
                <select
                  required
                  value={destination}
                  onChange={(e) => setDestination(e.target.value)}
                  className="mt-2 w-full rounded-lg border p-3"
                >
                  <option value="">Pilih cabang</option>
                  {branches.map((branch) => (
                    <option
                      key={branch.id}
                      value={branch.id}
                      disabled={String(branch.id) === origin}
                    >
                      {branch.name}
                    </option>
                  ))}
                </select>
              </label>
            </div>

            <label className="block text-sm font-medium">
              Jenis layanan
              <select
                value={service}
                onChange={(e) => setService(e.target.value)}
                className="mt-2 w-full rounded-lg border p-3"
              >
                <option value="REGULAR_SEA">Kargo Laut Reguler</option>
                <option value="EXPRESS_AIR">Kargo Udara Ekspres</option>
                <option value="OVERLAND">Darat</option>
              </select>
            </label>

            <div>
              <h3 className="mb-3 font-semibold">Berat dan dimensi paket</h3>
              <label className="block text-sm font-medium">
                Berat aktual (kg)
                <input
                  required
                  type="number"
                  min="0.01"
                  step="0.01"
                  value={weight}
                  onChange={(e) => setWeight(e.target.value)}
                  className="mt-2 w-full rounded-lg border p-3"
                />
              </label>

              <div className="mt-4 grid grid-cols-3 gap-3">
                {[
                  ["Panjang (cm)", length, setLength],
                  ["Lebar (cm)", width, setWidth],
                  ["Tinggi (cm)", height, setHeight],
                ].map(([label, value, setter]) => (
                  <label key={label as string} className="text-sm font-medium">
                    {label as string}
                    <input
                      required
                      type="number"
                      min="0.01"
                      step="0.01"
                      value={value as string}
                      onChange={(e) =>
                        (setter as (value: string) => void)(e.target.value)
                      }
                      className="mt-2 w-full min-w-0 rounded-lg border p-3"
                    />
                  </label>
                ))}
              </div>
            </div>

            {error && (
              <div
                role="alert"
                className="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"
              >
                {error}
              </div>
            )}

            <button
              disabled={loading || branches.length === 0}
              className="w-full rounded-lg bg-orange-600 px-5 py-3 font-bold text-white hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
              {loading ? "Menghitung..." : "Hitung Ongkos Kirim"}
            </button>
          </form>

          <aside className="h-fit rounded-2xl bg-slate-900 p-6 text-white">
            <p className="text-sm font-medium text-orange-400">
              ESTIMASI BIAYA
            </p>

            {quote ? (
              <>
                <p className="mt-3 text-3xl font-bold">
                  {money(Number(quote.total_amount))}
                </p>

                <div className="mt-6 space-y-4 border-t border-slate-700 pt-5 text-sm">
                  <div className="flex justify-between gap-4">
                    <span className="text-slate-300">Berat aktual</span>
                    <span>{Number(quote.actual_weight_kg).toFixed(2)} kg</span>
                  </div>
                  <div className="flex justify-between gap-4">
                    <span className="text-slate-300">Berat volumetrik</span>
                    <span>{Number(quote.volume_weight_kg).toFixed(2)} kg</span>
                  </div>
                  <div className="flex justify-between gap-4">
                    <span className="text-slate-300">Berat ditagihkan</span>
                    <span>{Number(quote.chargeable_weight_kg).toFixed(2)} kg</span>
                  </div>
                  <div className="flex justify-between gap-4">
                    <span className="text-slate-300">Biaya dasar</span>
                    <span>{money(Number(quote.base_fee))}</span>
                  </div>
                  <div className="flex justify-between gap-4">
                    <span className="text-slate-300">Biaya berat</span>
                    <span>{money(Number(quote.weight_fee))}</span>
                  </div>
                </div>

                <p className="mt-6 text-xs leading-5 text-slate-400">
                  Ini merupakan estimasi berdasarkan tarif aktif yang
                  dikonfigurasi. Biaya akhir dapat mengikuti ketentuan
                  operasional AKA Cargo.
                </p>
              </>
            ) : (
              <p className="mt-4 text-sm leading-6 text-slate-300">
                Lengkapi detail pengiriman, lalu pilih Hitung Ongkos Kirim
                untuk melihat estimasi biaya berdasarkan tarif aktif.
              </p>
            )}
          </aside>
        </div>
      </div>
    </main>
  );
}
