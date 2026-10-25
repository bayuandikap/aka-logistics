"use client";

import { useParams } from "next/navigation";

export default function TrackingPage() {
  const params = useParams();

  return (
    <main style={{ padding: 32 }}>
      <h1>Tracking route diagnostic</h1>
      <pre>{JSON.stringify(params, null, 2)}</pre>
    </main>
  );
}