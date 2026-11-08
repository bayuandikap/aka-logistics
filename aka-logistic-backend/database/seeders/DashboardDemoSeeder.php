<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Shipment;
use App\Models\Tariff;
use Illuminate\Database\Seeder;

class DashboardDemoSeeder extends Seeder
{
    /**
     * Create a repeatable, self-contained dataset for dashboard demonstrations.
     *
     * Demo records use the AKA-DEMO- prefix so they can be identified easily.
     * Existing non-demo business data is never deleted or modified.
     */
    public function run(): void
    {
        $branches = collect([
            ['code' => 'JKT', 'name' => 'Jakarta Hub', 'city' => 'Jakarta', 'address' => 'Jl. Gatot Subroto No. 21, Jakarta', 'phone' => '+62 21 5550 1001'],
            ['code' => 'BDG', 'name' => 'Bandung Branch', 'city' => 'Bandung', 'address' => 'Jl. Asia Afrika No. 88, Bandung', 'phone' => '+62 22 5550 1002'],
            ['code' => 'SBY', 'name' => 'Surabaya Hub', 'city' => 'Surabaya', 'address' => 'Jl. Ahmad Yani No. 145, Surabaya', 'phone' => '+62 31 5550 1003'],
            ['code' => 'SMG', 'name' => 'Semarang Branch', 'city' => 'Semarang', 'address' => 'Jl. Pandanaran No. 17, Semarang', 'phone' => '+62 24 5550 1004'],
            ['code' => 'YGY', 'name' => 'Yogyakarta Branch', 'city' => 'Yogyakarta', 'address' => 'Jl. Malioboro No. 42, Yogyakarta', 'phone' => '+62 274 5550 1005'],
            ['code' => 'MKS', 'name' => 'Makassar Hub', 'city' => 'Makassar', 'address' => 'Jl. Perintis Kemerdekaan No. 9, Makassar', 'phone' => '+62 411 5550 1006'],
        ])->mapWithKeys(function (array $data) {
            $branch = Branch::firstOrCreate(
                ['code' => $data['code']],
                $data + ['is_active' => true],
            );

            return [$data['code'] => $branch];
        });

        $services = [
            'REGULAR_SEA' => ['base_fee' => 12000, 'rate_per_kg' => 4500, 'volumetric_divisor' => 5000],
            'EXPRESS_AIR' => ['base_fee' => 20000, 'rate_per_kg' => 6500, 'volumetric_divisor' => 5000],
            'OVERLAND' => ['base_fee' => 30000, 'rate_per_kg' => 3500, 'volumetric_divisor' => 4000],
        ];

        $tariffs = [];
        foreach ($branches as $originCode => $origin) {
            foreach ($branches as $destinationCode => $destination) {
                if ($originCode === $destinationCode) {
                    continue;
                }

                foreach ($services as $service => $rates) {
                    $tariffs[$originCode . '-' . $destinationCode . '-' . $service] = Tariff::firstOrCreate(
                        [
                            'origin_branch_id' => $origin->id,
                            'destination_branch_id' => $destination->id,
                            'service_type' => $service,
                        ],
                        $rates + ['is_active' => true],
                    );
                }
            }
        }

        $demoShipments = [
            ['0001', 'Maya Collection', 'Nadia Putri', 'JKT', 'BDG', 'REGULAR_SEA', 'DELIVERED', 1, 2.4, 30, 25, 18, -26, -23],
            ['0002', 'Ruang Rasa Coffee', 'Dimas Pratama', 'BDG', 'JKT', 'EXPRESS_AIR', 'DELIVERED', 2, 5.0, 40, 30, 25, -24, -21],
            ['0003', 'Batik Arunika', 'Sari Wulandari', 'YGY', 'SBY', 'REGULAR_SEA', 'DELIVERED', 3, 3.2, 35, 25, 20, -20, -17],
            ['0004', 'Nusa Gadget Store', 'Rizky Mahendra', 'JKT', 'SMG', 'EXPRESS_AIR', 'DELIVERED', 4, 1.8, 22, 18, 12, -18, -15],
            ['0005', 'Kopi Pagi Roastery', 'Aulia Rahman', 'SBY', 'MKS', 'OVERLAND', 'DELIVERED', 5, 18.0, 60, 45, 40, -16, -13],
            ['0006', 'Maya Collection', 'Maya Lestari', 'SMG', 'JKT', 'REGULAR_SEA', 'ON_HOLD', 6, 4.5, 35, 25, 20, -8, -5],
            ['0007', 'Sentra Elektronik', 'Fajar Nugroho', 'JKT', 'SBY', 'OVERLAND', 'IN_TRANSIT', 7, 22.0, 70, 50, 45, -4, 3],
            ['0008', 'Dapur Rempah', 'Intan Permata', 'BDG', 'YGY', 'REGULAR_SEA', 'IN_TRANSIT', 8, 6.0, 40, 30, 20, -3, 4],
            ['0009', 'Karya Kulit Studio', 'Bima Saputra', 'SBY', 'SMG', 'EXPRESS_AIR', 'IN_TRANSIT', 9, 2.2, 28, 20, 16, -2, 5],
            ['0010', 'Urban Living ID', 'Citra Amelia', 'JKT', 'MKS', 'OVERLAND', 'IN_MARITIME_TRANSIT', 10, 45.0, 90, 60, 55, -1, 8],
            ['0011', 'Pustaka Kita', 'Dewi Anggraini', 'YGY', 'JKT', 'REGULAR_SEA', 'IN_TRANSIT', 11, 7.5, 42, 32, 24, 0, 6],
            ['0012', 'Lensa Nusantara', 'Arif Setiawan', 'SMG', 'SBY', 'EXPRESS_AIR', 'ARRIVED_AT_DESTINATION', 12, 1.4, 20, 16, 12, -1, 1],
            ['0013', 'Green Home Supply', 'Tania Kusuma', 'BDG', 'MKS', 'OVERLAND', 'IN_MARITIME_TRANSIT', 13, 32.0, 80, 55, 45, -2, 9],
            ['0014', 'Kebun Organik', 'Yoga Firmansyah', 'SBY', 'JKT', 'REGULAR_SEA', 'REGISTERED', 14, 9.0, 45, 35, 25, 0, 5],
            ['0015', 'Atelier Senja', 'Larasati', 'JKT', 'YGY', 'EXPRESS_AIR', 'IN_TRANSIT', 15, 3.6, 32, 24, 18, -1, 4],
            ['0016', 'Prima Office', 'Hendra Wijaya', 'MKS', 'SBY', 'OVERLAND', 'ON_HOLD', 16, 28.0, 75, 50, 45, -5, -2],
            ['0017', 'Daily Essentials', 'Nabila Zahra', 'SMG', 'BDG', 'REGULAR_SEA', 'DELIVERED', 17, 2.0, 25, 20, 15, -45, -42],
            ['0018', 'Monokrom Apparel', 'Kevin Tan', 'JKT', 'SBY', 'EXPRESS_AIR', 'DELIVERED', 18, 6.5, 38, 28, 22, -75, -72],
            ['0019', 'Sumber Makmur', 'Wahyu Hidayat', 'BDG', 'SMG', 'REGULAR_SEA', 'DELIVERED', 19, 12.0, 50, 40, 30, -105, -102],
            ['0020', 'Pixel Works', 'Andini Safitri', 'YGY', 'MKS', 'OVERLAND', 'DELIVERED', 20, 16.0, 65, 45, 35, -135, -132],
            ['0021', 'Rumah Anyam', 'Galih Prakoso', 'SBY', 'BDG', 'REGULAR_SEA', 'IN_TRANSIT', 21, 5.5, 36, 26, 18, -6, 1],
            ['0022', 'Savana Outdoor', 'Rani Oktavia', 'JKT', 'SMG', 'EXPRESS_AIR', 'ARRIVED_AT_DESTINATION', 22, 8.0, 48, 35, 28, -1, 1],
            ['0023', 'Pangan Sehat', 'Bagas Ramadhan', 'MKS', 'YGY', 'OVERLAND', 'REGISTERED', 23, 14.0, 58, 42, 36, 0, 7],
            ['0024', 'Studio Kembang', 'Putri Ananda', 'SMG', 'JKT', 'REGULAR_SEA', 'IN_TRANSIT', 24, 1.2, 20, 15, 10, -3, 4],
        ];

        foreach ($demoShipments as $row) {
            [
                $number, $sender, $recipient, $originCode, $destinationCode,
                $service, $status, $createdDaysAgo, $weight, $length, $width,
                $height, $etaOffset, $eventOffset,
            ] = $row;

            $origin = $branches[$originCode];
            $destination = $branches[$destinationCode];
            $tariff = $tariffs[$originCode . '-' . $destinationCode . '-' . $service];

            $volumeWeight = round(($length * $width * $height) / $tariff->volumetric_divisor, 2);
            $chargeableWeight = max($weight, $volumeWeight);
            $weightFee = round($chargeableWeight * (float) $tariff->rate_per_kg, 2);
            $createdAt = now()->subDays($createdDaysAgo)->setTime(9 + ($createdDaysAgo % 8), 15);
            $eta = now()->addDays($etaOffset)->toDateString();
            $eventAt = now()->subDays(min($createdDaysAgo, abs($eventOffset)))->setTime(10 + ($createdDaysAgo % 7), 30);

            $shipment = Shipment::updateOrCreate(
                ['tracking_number' => 'AKA-DEMO-' . $number],
                [
                    'sender_name' => $sender,
                    'sender_phone' => '+62 812 1000 ' . str_pad($number, 4, '0', STR_PAD_LEFT),
                    'sender_address' => 'Jl. Melati No. ' . (10 + (int) $number) . ', ' . $origin->city,
                    'recipient_name' => $recipient,
                    'recipient_phone' => '+62 813 2000 ' . str_pad($number, 4, '0', STR_PAD_LEFT),
                    'recipient_address' => 'Jl. Kenanga No. ' . (20 + (int) $number) . ', ' . $destination->city,
                    'origin_branch_id' => $origin->id,
                    'destination_branch_id' => $destination->id,
                    'service_type' => $service,
                    'actual_weight_kg' => $weight,
                    'length_cm' => $length,
                    'width_cm' => $width,
                    'height_cm' => $height,
                    'chargeable_weight' => $chargeableWeight,
                    'volume_weight' => $volumeWeight,
                    'total_amount' => (float) $tariff->base_fee + $weightFee,
                    'tariff_id' => $tariff->id,
                    'base_fee_applied' => $tariff->base_fee,
                    'rate_per_kg_applied' => $tariff->rate_per_kg,
                    'weight_fee' => $weightFee,
                    'volumetric_divisor_applied' => $tariff->volumetric_divisor,
                    'payment_status' => in_array($status, ['DELIVERED', 'ARRIVED_AT_DESTINATION'], true) ? 'PAID' : 'UNPAID',
                    'current_status' => $status,
                    'estimated_arrival' => $eta,
                    'created_at' => $createdAt,
                    'updated_at' => $eventAt,
                ],
            );

            $shipment->created_at = $createdAt;
            $shipment->updated_at = $eventAt;
            $shipment->saveQuietly();

            $location = in_array($status, ['DELIVERED', 'ARRIVED_AT_DESTINATION'], true)
                ? $destination
                : $origin;

            $descriptions = [
                'REGISTERED' => 'Shipment registered and awaiting dispatch.',
                'IN_TRANSIT' => 'Shipment is moving through the land transportation network.',
                'IN_MARITIME_TRANSIT' => 'Shipment is currently in maritime transportation.',
                'ARRIVED_AT_DESTINATION' => 'Shipment has arrived at the destination branch.',
                'DELIVERED' => 'Shipment successfully delivered to the recipient.',
                'ON_HOLD' => 'Shipment temporarily held for operational verification.',
            ];

            $shipment->checkpoints()->updateOrCreate(
                [
                    'status' => $status,
                    'location_label' => $location->city,
                ],
                [
                    'branch_id' => $location->id,
                    'description' => $descriptions[$status],
                    'event_timestamp' => $eventAt,
                ],
            );
        }
    }
}
