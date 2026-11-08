<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWB {{ $shipment->tracking_number }} | AKA Logistics</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; color: #172033; background: #eef1f5; font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.45; }
        .toolbar { max-width: 210mm; margin: 0 auto 16px; display: flex; justify-content: flex-end; }
        .toolbar button { border: 0; border-radius: 6px; padding: 10px 16px; color: #fff; background: #b7791f; cursor: pointer; font-weight: 700; }
        .sheet { width: 210mm; min-height: 148mm; margin: 0 auto; padding: 14mm; background: #fff; border: 1px solid #d9dee7; }
        .header, .row, .section-heading, .footer { display: flex; justify-content: space-between; gap: 16px; }
        .header { align-items: flex-start; border-bottom: 3px solid #b7791f; padding-bottom: 14px; }
        .brand { font-size: 22px; font-weight: 800; letter-spacing: .5px; }
        .muted { color: #687386; }
        .awb-label { text-align: right; }
        .awb-number { margin-top: 4px; font-size: 21px; font-weight: 800; letter-spacing: 1px; }
        .section { margin-top: 18px; }
        .section-heading { padding-bottom: 6px; margin-bottom: 10px; border-bottom: 1px solid #d9dee7; color: #8a5a13; font-size: 11px; font-weight: 800; letter-spacing: .7px; text-transform: uppercase; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .field { margin-bottom: 9px; }
        .field-label { display: block; margin-bottom: 2px; color: #687386; font-size: 10px; text-transform: uppercase; }
        .field-value { font-weight: 600; overflow-wrap: anywhere; }
        .route { display: grid; grid-template-columns: 1fr 32px 1fr; align-items: center; gap: 10px; padding: 12px; background: #f7f8fa; border-radius: 6px; }
        .route-arrow { text-align: center; font-size: 20px; color: #b7791f; }
        .route-name { font-size: 15px; font-weight: 800; }
        .details { width: 100%; border-collapse: collapse; }
        .details th, .details td { padding: 8px; border: 1px solid #d9dee7; text-align: left; }
        .details th { width: 25%; background: #f7f8fa; color: #687386; font-weight: 600; }
        .total { font-size: 16px; font-weight: 800; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px dashed #aab2bf; color: #687386; font-size: 10px; }
        .status { display: inline-block; padding: 3px 8px; border: 1px solid #aab2bf; border-radius: 20px; font-size: 10px; font-weight: 700; }
        @page { size: A4; margin: 10mm; }
        @media print { body { padding: 0; background: #fff; } .toolbar { display: none; } .sheet { width: auto; min-height: 0; margin: 0; padding: 4mm; border: 0; } * { print-color-adjust: exact; -webkit-print-color-adjust: exact; } }
        @media screen and (max-width: 800px) { body { padding: 8px; } .sheet { width: 100%; min-height: 0; padding: 18px; } }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Cetak / Simpan sebagai PDF</button></div>
    <main class="sheet">
        <header class="header">
            <div><div class="brand">AKA LOGISTICS</div><div class="muted">Surat Muatan / Bukti Pengiriman</div></div>
            <div class="awb-label">
                <div class="muted">NOMOR RESI / AWB</div>
                <div class="awb-number">{{ $shipment->tracking_number }}</div>
                <div class="muted">Tanggal: {{ $shipment->created_at?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }} WIB</div>
            </div>
        </header>
        <section class="section">
            <div class="section-heading">Informasi rute pengiriman</div>
            <div class="route">
                <div><div class="field-label">Asal</div><div class="route-name">{{ $shipment->originBranch?->name ?? '-' }}</div><div class="muted">{{ $shipment->originBranch?->city ?? '' }}</div></div>
                <div class="route-arrow">→</div>
                <div><div class="field-label">Tujuan</div><div class="route-name">{{ $shipment->destinationBranch?->name ?? '-' }}</div><div class="muted">{{ $shipment->destinationBranch?->city ?? '' }}</div></div>
            </div>
            <div class="row" style="margin-top:10px">
                <div><span class="field-label">Layanan</span><strong>{{ str_replace('_', ' ', $shipment->service_type) }}</strong></div>
                <div><span class="field-label">Estimasi tiba</span><strong>{{ $shipment->estimated_arrival?->format('d M Y') ?? '-' }}</strong></div>
                <div><span class="field-label">Status</span><span class="status">{{ match ($shipment->current_status) { 'REGISTERED' => 'Terdaftar', 'IN_TRANSIT' => 'Dalam Perjalanan', 'IN_MARITIME_TRANSIT' => 'Dalam Pelayaran Laut', 'ARRIVED_AT_DESTINATION' => 'Tiba di Cabang Tujuan', 'DELIVERED' => 'Terkirim', 'ON_HOLD' => 'Tertahan', default => str_replace('_', ' ', $shipment->current_status) } }}</span></div>
            </div>
        </section>
        <section class="section grid">
            <div>
                <div class="section-heading">Data pengirim</div>
                <div class="field"><span class="field-label">Nama</span><div class="field-value">{{ $shipment->sender_name }}</div></div>
                <div class="field"><span class="field-label">Telepon</span><div class="field-value">{{ $shipment->sender_phone }}</div></div>
                <div class="field"><span class="field-label">Alamat</span><div class="field-value">{{ $shipment->sender_address }}</div></div>
            </div>
            <div>
                <div class="section-heading">Data penerima</div>
                <div class="field"><span class="field-label">Nama</span><div class="field-value">{{ $shipment->recipient_name }}</div></div>
                <div class="field"><span class="field-label">Telepon</span><div class="field-value">{{ $shipment->recipient_phone }}</div></div>
                <div class="field"><span class="field-label">Alamat</span><div class="field-value">{{ $shipment->recipient_address }}</div></div>
            </div>
        </section>
        <section class="section">
            <div class="section-heading">Detail barang dan biaya</div>
            <table class="details"><tbody>
                <tr><th>Berat aktual</th><td>{{ $shipment->actual_weight_kg }} kg</td><th>Berat volume</th><td>{{ $shipment->volume_weight }} kg</td></tr>
                <tr><th>Dimensi (P × L × T)</th><td>{{ $shipment->length_cm }} × {{ $shipment->width_cm }} × {{ $shipment->height_cm }} cm</td><th>Berat ditagihkan</th><td>{{ $shipment->chargeable_weight }} kg</td></tr>
                <tr><th>Biaya dasar (snapshot)</th><td>Rp {{ number_format((float) $shipment->base_fee_applied, 0, ',', '.') }}</td><th>Biaya berat (snapshot)</th><td>Rp {{ number_format((float) $shipment->weight_fee, 0, ',', '.') }}</td></tr>
                <tr><th>Pembayaran</th><td>{{ match ($shipment->payment_status) { 'PAID' => 'Lunas', 'UNPAID' => 'Belum Dibayar', 'PENDING' => 'Menunggu Pembayaran', default => str_replace('_', ' ', $shipment->payment_status) } }}</td><th>Total biaya</th><td class="total">Rp {{ number_format((float) $shipment->total_amount, 0, ',', '.') }}</td></tr>
            </tbody></table>
        </section>
        <footer class="footer"><div>Dokumen ini dibuat oleh sistem AKA Logistics.</div><div>Nomor resi: {{ $shipment->tracking_number }}</div></footer>
    </main>
</body>
</html>
