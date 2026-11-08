<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Shipment;
use App\Models\Tariff;
use App\Models\TrackingCheckpoint;
use App\Models\User;
use App\Services\FreightCalculator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PortfolioDemoSeeder extends Seeder
{
    /**
     * Create a repeatable Indonesian logistics dataset for local portfolio demos.
     * Production databases are deliberately excluded.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('Data demo AKA Logistics dilewati pada environment production.');

            return;
        }

        DB::transaction(function (): void {
            $branches = $this->seedBranches();
            $operator = User::firstOrCreate(
                ['email' => 'operator.demo@aka-logistics.test'],
                ['name' => 'Rina Puspitasari', 'password' => 'password']
            );

            $operator->assignRole('petugas');
            $this->seedShipments($branches, $operator);
        });
    }

    private function seedBranches(): array
    {
        $records = [
            ['code' => 'JKT01', 'name' => 'Cabang Jakarta Pusat', 'city' => 'Jakarta', 'address' => 'Jl. Cikini Raya No. 18, Menteng, Jakarta Pusat', 'phone' => '021-3190-1101'],
            ['code' => 'BDG01', 'name' => 'Cabang Bandung', 'city' => 'Bandung', 'address' => 'Jl. Soekarno Hatta No. 245, Buahbatu, Bandung', 'phone' => '022-8734-2201'],
            ['code' => 'SBY01', 'name' => 'Cabang Surabaya', 'city' => 'Surabaya', 'address' => 'Jl. Perak Timur No. 45, Pabean Cantian, Surabaya', 'phone' => '031-9901-3301'],
            ['code' => 'SMG01', 'name' => 'Cabang Semarang', 'city' => 'Semarang', 'address' => 'Jl. Kaligawe Raya No. 72, Genuk, Semarang', 'phone' => '024-8602-4401'],
            ['code' => 'DPS01', 'name' => 'Cabang Denpasar', 'city' => 'Denpasar', 'address' => 'Jl. Gatot Subroto Timur No. 56, Denpasar', 'phone' => '0361-8805-5501'],
            ['code' => 'MKS01', 'name' => 'Cabang Makassar', 'city' => 'Makassar', 'address' => 'Jl. Perintis Kemerdekaan No. 108, Makassar', 'phone' => '0411-8706-6601'],
        ];

        $branches = [];

        foreach ($records as $record) {
            $branches[$record['code']] = Branch::updateOrCreate(
                ['code' => $record['code']],
                [...$record, 'is_active' => true]
            );
        }

        return $branches;
    }

    private function seedShipments(array $branches, User $operator): void
    {
        $customers = [
            ['sender' => 'CV Sumber Makmur', 'recipient' => 'Andi Pratama', 'sender_address' => 'Jl. Industri Raya No. 12, Jakarta', 'recipient_address' => 'Jl. Dago Atas No. 88, Bandung'],
            ['sender' => 'PT Nusantara Elektronik', 'recipient' => 'Dewi Anggraini', 'sender_address' => 'Kawasan Industri Pulogadung Blok C2, Jakarta', 'recipient_address' => 'Jl. Rungkut Industri No. 21, Surabaya'],
            ['sender' => 'Toko Batik Laras', 'recipient' => 'Budi Santoso', 'sender_address' => 'Pasar Klewer Blok A No. 14, Surakarta', 'recipient_address' => 'Jl. Pandanaran No. 32, Semarang'],
            ['sender' => 'UD Berkah Pangan', 'recipient' => 'Siti Rahmawati', 'sender_address' => 'Jl. Raya Darmo No. 45, Surabaya', 'recipient_address' => 'Jl. Teuku Umar No. 19, Denpasar'],
            ['sender' => 'PT Karya Furnitur', 'recipient' => 'Made Wirawan', 'sender_address' => 'Jl. Terusan Buahbatu No. 17, Bandung', 'recipient_address' => 'Jl. Gatot Subroto No. 61, Denpasar'],
            ['sender' => 'CV Teknologi Mandiri', 'recipient' => 'Nurul Hidayah', 'sender_address' => 'Ruko Mutiara Niaga Blok B7, Semarang', 'recipient_address' => 'Jl. Perintis Kemerdekaan No. 90, Makassar'],
            ['sender' => 'Dapur Sejahtera', 'recipient' => 'Rizky Firmansyah', 'sender_address' => 'Jl. Ahmad Yani No. 10, Bandung', 'recipient_address' => 'Jl. Pettarani No. 28, Makassar'],
            ['sender' => 'PT Mitra Kesehatan', 'recipient' => 'Ni Luh Putu', 'sender_address' => 'Jl. Kertajaya No. 77, Surabaya', 'recipient_address' => 'Jl. Diponegoro No. 16, Denpasar'],
            ['sender' => 'Toko Perkakas Jaya', 'recipient' => 'Agus Setiawan', 'sender_address' => 'Jl. Gajah Mada No. 51, Jakarta', 'recipient_address' => 'Jl. Pemuda No. 73, Semarang'],
            ['sender' => 'CV Kreatif Indonesia', 'recipient' => 'Maya Lestari', 'sender_address' => 'Jl. Suryakencana No. 29, Jakarta', 'recipient_address' => 'Jl. Basuki Rahmat No. 40, Surabaya'],
            ['sender' => 'PT Agro Pangan Raya', 'recipient' => 'Fajar Maulana', 'sender_address' => 'Jl. Urip Sumoharjo No. 38, Makassar', 'recipient_address' => 'Jl. Asia Afrika No. 12, Bandung'],
            ['sender' => 'Bengkel Presisi Teknik', 'recipient' => 'Putri Amelia', 'sender_address' => 'Jl. Kaligawe No. 110, Semarang', 'recipient_address' => 'Jl. Sudirman No. 25, Jakarta'],
        ];

        $routes = [
            ['JKT01', 'BDG01', 'REGULAR_SEA'],
            ['JKT01', 'SBY01', 'EXPRESS_AIR'],
            ['BDG01', 'DPS01', 'OVERLAND'],
            ['SBY01', 'DPS01', 'REGULAR_SEA'],
            ['SMG01', 'MKS01', 'EXPRESS_AIR'],
            ['DPS01', 'JKT01', 'REGULAR_SEA'],
            ['MKS01', 'BDG01', 'OVERLAND'],
            ['JKT01', 'SMG01', 'OVERLAND'],
            ['BDG01', 'SBY01', 'REGULAR_SEA'],
            ['SBY01', 'MKS01', 'EXPRESS_AIR'],
            ['SMG01', 'JKT01', 'REGULAR_SEA'],
            ['DPS01', 'SMG01', 'OVERLAND'],
        ];

        // Deliberately varied so all dashboard status groups have useful data.
        $statuses = [
            'DELIVERED', 'IN_TRANSIT', 'DELIVERED', 'ON_HOLD', 'IN_MARITIME_TRANSIT', 'DELIVERED',
            'ARRIVED_AT_DESTINATION', 'DELIVERED', 'IN_TRANSIT', 'DELIVERED', 'REGISTERED', 'IN_MARITIME_TRANSIT',
            'DELIVERED', 'ON_HOLD', 'IN_TRANSIT', 'DELIVERED', 'ARRIVED_AT_DESTINATION', 'IN_TRANSIT',
            'DELIVERED', 'IN_MARITIME_TRANSIT', 'REGISTERED', 'DELIVERED', 'IN_TRANSIT', 'ARRIVED_AT_DESTINATION',
            'DELIVERED', 'IN_MARITIME_TRANSIT', 'IN_TRANSIT', 'DELIVERED', 'REGISTERED', 'IN_TRANSIT',
            'DELIVERED', 'ARRIVED_AT_DESTINATION', 'IN_MARITIME_TRANSIT', 'DELIVERED', 'IN_TRANSIT', 'DELIVERED',
        ];

        $calculator = app(FreightCalculator::class);
        $sequence = 1;

        // Six shipments per month across the last six calendar months.
        for ($monthOffset = 5; $monthOffset >= 0; $monthOffset--) {
            for ($slot = 0; $slot < 6; $slot++) {
                $index = $sequence - 1;
                $customer = $customers[$index % count($customers)];
                $route = $routes[$index % count($routes)];
                [$originCode, $destinationCode, $service] = $route;
                if ($status === 'IN_MARITIME_TRANSIT') {
                    $service = 'REGULAR_SEA';
                }
                $origin = $branches[$originCode];
                $destination = $branches[$destinationCode];
                $status = $statuses[$index];

                $monthStart = now()->startOfMonth()->subMonths($monthOffset);
                $day = $monthOffset === 0 ? min(1 + ($slot * 1), (int) now()->day) : 2 + (($slot * 4) % 24);
                $createdAt = $monthStart->copy()->addDays($day - 1)->setTime(8 + ($slot % 8), ($slot * 7) % 60);

                $actualWeight = [1.2, 2.5, 4.0, 7.5, 12.0, 18.5][$index % 6];
                $dimensions = [
                    [20, 15, 10], [30, 20, 15], [35, 25, 20],
                    [45, 30, 25], [50, 40, 35], [60, 45, 40],
                ][$index % 6];

                $tariff = Tariff::updateOrCreate(
                    [
                        'origin_branch_id' => $origin->id,
                        'destination_branch_id' => $destination->id,
                        'service_type' => $service,
                    ],
                    [
                        'base_fee' => $service === 'EXPRESS_AIR' ? 18000 : ($service === 'OVERLAND' ? 12000 : 10000),
                        'rate_per_kg' => $service === 'EXPRESS_AIR' ? 14500 : ($service === 'OVERLAND' ? 7500 : 6000),
                        'volumetric_divisor' => $service === 'EXPRESS_AIR' ? 6000 : 5000,
                        'is_active' => true,
                    ]
                );

                $calculation = $calculator->calculate(
                    $origin->id,
                    $destination->id,
                    $service,
                    $actualWeight,
                    $dimensions[0],
                    $dimensions[1],
                    $dimensions[2],
                );

                $estimatedDays = $service === 'EXPRESS_AIR' ? 2 : ($service === 'OVERLAND' ? 4 : 6);
                $estimatedArrival = $createdAt->copy()->addDays($estimatedDays);
                $deliveredAt = $estimatedArrival->copy()->addDays(($index % 4) - 1);

                if ($status === 'DELIVERED') {
                    $paymentStatus = $index % 5 === 0 ? 'UNPAID' : 'PAID';
                } else {
                    $paymentStatus = $index % 4 === 0 ? 'PAID' : 'UNPAID';
                }

                $trackingNumber = sprintf('AKA-DEMO-2026-%03d', $sequence);

                $shipment = Shipment::updateOrCreate(
                    ['tracking_number' => $trackingNumber],
                    [
                        'sender_name' => $customer['sender'],
                        'sender_phone' => '08' . str_pad((string) (120000000 + ($index * 137)), 10, '0', STR_PAD_LEFT),
                        'sender_address' => $customer['sender_address'],
                        'recipient_name' => $customer['recipient'],
                        'recipient_phone' => '08' . str_pad((string) (130000000 + ($index * 173)), 10, '0', STR_PAD_LEFT),
                        'recipient_address' => $customer['recipient_address'],
                        'origin_branch_id' => $origin->id,
                        'destination_branch_id' => $destination->id,
                        'service_type' => $service,
                        'actual_weight_kg' => $actualWeight,
                        'length_cm' => $dimensions[0],
                        'width_cm' => $dimensions[1],
                        'height_cm' => $dimensions[2],
                        'chargeable_weight' => $calculation['chargeable_weight_kg'],
                        'volume_weight' => $calculation['volume_weight_kg'],
                        'total_amount' => $calculation['total_amount'],
                        'tariff_id' => $tariff->id,
                        'base_fee_applied' => $calculation['base_fee'],
                        'rate_per_kg_applied' => $calculation['rate_per_kg'],
                        'weight_fee' => $calculation['weight_fee'],
                        'volumetric_divisor_applied' => $calculation['volumetric_divisor'],
                        'payment_status' => $paymentStatus,
                        'current_status' => $status,
                        'estimated_arrival' => $estimatedArrival->toDateString(),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt->copy()->addHours(1),
                    ]
                );

                $shipment->forceFill([
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addHours(1),
                ])->save();

                $this->seedCheckpoints($shipment, $origin, $destination, $service, $status, $createdAt, $deliveredAt, $operator);

                $sequence++;
            }
        }
    }

    private function seedCheckpoints(
        Shipment $shipment,
        Branch $origin,
        Branch $destination,
        string $service,
        string $status,
        Carbon $createdAt,
        Carbon $deliveredAt,
        User $operator
    ): void {
        $shipment->checkpoints()->delete();

        $events = [
            [
                'status' => 'REGISTERED',
                'branch' => $origin,
                'location' => $origin->name,
                'description' => 'Kiriman diterima dan data pengiriman berhasil dicatat di sistem.',
                'at' => $createdAt,
            ],
        ];

        if ($status !== 'REGISTERED') {
            $events[] = [
                'status' => 'IN_TRANSIT',
                'branch' => $origin,
                'location' => 'Pusat sortir ' . $origin->city,
                'description' => 'Paket selesai diproses dan diberangkatkan menuju cabang tujuan.',
                'at' => $createdAt->copy()->addHours(8),
            ];
        }

        if ($service === 'REGULAR_SEA' && in_array($status, ['IN_MARITIME_TRANSIT', 'ARRIVED_AT_DESTINATION', 'DELIVERED'], true)) {
            $events[] = [
                'status' => 'IN_MARITIME_TRANSIT',
                'branch' => null,
                'location' => 'Dalam perjalanan antarpulau',
                'description' => 'Kiriman sedang dalam perjalanan melalui jalur laut menuju wilayah tujuan.',
                'at' => $createdAt->copy()->addDays(1),
            ];
        }

        if (in_array($status, ['ARRIVED_AT_DESTINATION', 'DELIVERED'], true)) {
            $events[] = [
                'status' => 'ARRIVED_AT_DESTINATION',
                'branch' => $destination,
                'location' => $destination->name,
                'description' => 'Kiriman telah tiba dan diproses di cabang tujuan.',
                'at' => $createdAt->copy()->addDays(2),
            ];
        }

        if ($status === 'DELIVERED') {
            $events[] = [
                'status' => 'DELIVERED',
                'branch' => $destination,
                'location' => 'Alamat penerima, ' . $destination->city,
                'description' => 'Paket telah diterima oleh penerima. Pengiriman selesai.',
                'at' => $deliveredAt,
            ];
        }

        if ($status === 'ON_HOLD') {
            $events[] = [
                'status' => 'ON_HOLD',
                'branch' => $origin,
                'location' => $origin->name,
                'description' => 'Pengiriman ditahan sementara untuk verifikasi alamat dan konfirmasi penerima.',
                'at' => $createdAt->copy()->addDay(),
            ];
        }

        foreach ($events as $event) {
            TrackingCheckpoint::create([
                'shipment_id' => $shipment->id,
                'branch_id' => $event['branch']?->id,
                'status' => $event['status'],
                'location_label' => $event['location'],
                'description' => $event['description'],
                'event_timestamp' => $event['at'],
                'created_by' => $operator->id,
                'created_at' => $event['at'],
                'updated_at' => $event['at'],
            ]);
        }

        // Keep the shipment summary consistent with its latest tracking event.
        $latestStatus = $shipment->checkpoints()
            ->orderByDesc('event_timestamp')
            ->orderByDesc('id')
            ->value('status');

        $shipment->update(['current_status' => $latestStatus ?? 'REGISTERED']);
    }
}
