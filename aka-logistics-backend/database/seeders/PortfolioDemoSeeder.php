<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Tariff;
use App\Models\Shipment;
use App\Models\TrackingCheckpoint;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PortfolioDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            /*
             * 1. Branches
             */
            $branchData = [
                'JKT' => [
                    'name' => 'Jakarta Main Hub',
                    'city' => 'Jakarta',
                    'address' => 'Jl. Raya Cakung, Jakarta Timur',
                    'phone' => '0215550101',
                ],
                'BDG' => [
                    'name' => 'Bandung Branch',
                    'city' => 'Bandung',
                    'address' => 'Jl. Soekarno Hatta, Bandung',
                    'phone' => '0225550102',
                ],
                'SMG' => [
                    'name' => 'Semarang Branch',
                    'city' => 'Semarang',
                    'address' => 'Jl. Kaligawe, Semarang',
                    'phone' => '0245550103',
                ],
                'SBY' => [
                    'name' => 'Surabaya Branch',
                    'city' => 'Surabaya',
                    'address' => 'Jl. Perak Barat, Surabaya',
                    'phone' => '0315550104',
                ],
                'MKS' => [
                    'name' => 'Makassar Branch',
                    'city' => 'Makassar',
                    'address' => 'Jl. Ir. Sutami, Makassar',
                    'phone' => '04115550105',
                ],
            ];

            $branches = [];

            foreach ($branchData as $code => $data) {
                $branches[$code] = Branch::updateOrCreate(
                    ['code' => $code],
                    [...$data, 'code' => $code, 'is_active' => true],
                );
            }

            /*
             * 2. Demo tariffs
             */
            $tariffData = [
                ['JKT', 'BDG', 'OVERLAND', 35000, 4500, 4000],
                ['JKT', 'SMG', 'OVERLAND', 50000, 5000, 4000],
                ['JKT', 'SBY', 'REGULAR_SEA', 75000, 3500, 6000],
                ['JKT', 'MKS', 'REGULAR_SEA', 90000, 5000, 6000],
                ['JKT', 'SBY', 'EXPRESS_AIR', 120000, 12000, 6000],
                ['SBY', 'MKS', 'EXPRESS_AIR', 110000, 11000, 6000],
            ];

            $tariffs = [];

            foreach (
                $tariffData as [
                    $origin,
                    $destination,
                    $service,
                    $baseFee,
                    $ratePerKg,
                    $divisor
                ]
            ) {
                $tariffs[$origin . '-' . $destination . '-' . $service] =
                    Tariff::updateOrCreate(
                        [
                            'origin_branch_id' => $branches[$origin]->id,
                            'destination_branch_id' => $branches[$destination]->id,
                            'service_type' => $service,
                        ],
                        [
                            'base_fee' => $baseFee,
                            'rate_per_kg' => $ratePerKg,
                            'volumetric_divisor' => $divisor,
                            'is_active' => true,
                        ],
                    );
            }

            /*
             * 3. Demo users
             *
             * RoleSeeder must run before this seeder.
             */
            $users = [
                'superadmin@aka-demo.test' => [
                    'name' => 'AKA Demo Super Admin',
                    'role' => 'super_admin',
                ],
                'operasional@aka-demo.test' => [
                    'name' => 'AKA Demo Operational Admin',
                    'role' => 'admin_operasional',
                ],
                'petugas@aka-demo.test' => [
                    'name' => 'AKA Demo Officer',
                    'role' => 'petugas',
                ],
            ];

            $demoUsers = [];

            foreach ($users as $email => $data) {
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $data['name'],
                        'password' => Hash::make('DemoAKA2026!'),
                    ],
                );

                $user->syncRoles([$data['role']]);

                $demoUsers[$data['role']] = $user;
            }

            /*
             * 4. Shipments and tracking histories
             *
             * IMPORTANT:
             * Replace the example freight calculation below with
             * your application's actual calculation service.
             */
            $scenarios = [
                [
                    'number' => 'AKA-DEMO-0001',
                    'origin' => 'JKT',
                    'destination' => 'BDG',
                    'service' => 'OVERLAND',
                    'sender' => 'Andi Pratama',
                    'sender_phone' => '081234567801',
                    'sender_address' => 'Jl. Sudirman No. 10',
                    'recipient_address' => 'Jl. Dago No. 25',
                    'recipient_phone' => '081234567802',
                    'recipient' => 'Siti Rahma',
                    'weight' => 4.5,
                    'dimensions' => [30, 20, 15],
                    'payment' => 'PAID',
                    'events' => [
                        [
                            'REGISTERED',
                            'Jakarta Main Hub',
                            'Shipment registered and awaiting dispatch',
                            'JKT',
                            0
                        ],
                    ],
                ],
                [
                    'number' => 'AKA-DEMO-0002',
                    'origin' => 'JKT',
                    'destination' => 'SMG',
                    'service' => 'OVERLAND',
                    'sender' => 'CV Sumber Makmur',
                    'sender_phone' => '081234567801',
                    'sender_address' => 'Jl. Sudirman No. 10',
                    'recipient_address' => 'Jl. Dago No. 25',
                    'recipient_phone' => '081234567802',
                    'recipient' => 'Budi Santoso',
                    'weight' => 12,
                    'dimensions' => [40, 30, 25],
                    'payment' => 'PAID',
                    'events' => [
                        [
                            'REGISTERED',
                            'Jakarta Main Hub',
                            'Shipment received at origin hub',
                            'JKT',
                            72
                        ],
                        [
                            'IN_TRANSIT',
                            'Jakarta - Semarang Route',
                            'Shipment dispatched by land transport',
                            'JKT',
                            48
                        ],
                    ],
                ],
                [
                    'number' => 'AKA-DEMO-0003',
                    'origin' => 'JKT',
                    'destination' => 'SBY',
                    'service' => 'REGULAR_SEA',
                    'sender' => 'PT Nusantara Elektronik',
                    'sender_phone' => '081234567801',
                    'sender_address' => 'Jl. Sudirman No. 10',
                    'recipient_address' => 'Jl. Dago No. 25',
                    'recipient_phone' => '081234567802',
                    'recipient' => 'Rina Kusuma',
                    'weight' => 35,
                    'dimensions' => [60, 45, 40],
                    'payment' => 'PAID',
                    'events' => [
                        [
                            'REGISTERED',
                            'Jakarta Main Hub',
                            'Shipment registered',
                            'JKT',
                            120
                        ],
                        [
                            'IN_TRANSIT',
                            'Jakarta Port',
                            'Shipment transferred to port facility',
                            'JKT',
                            96
                        ],
                        [
                            'IN_MARITIME_TRANSIT',
                            'Java Sea',
                            'Shipment departed by sea freight',
                            'JKT',
                            72
                        ],
                    ],
                ],
                [
                    'number' => 'AKA-DEMO-0004',
                    'origin' => 'JKT',
                    'destination' => 'SBY',
                    'service' => 'REGULAR_SEA',
                    'sender' => 'Toko Berkah Jaya',
                    'sender_phone' => '081234567801',
                    'sender_address' => 'Jl. Sudirman No. 10',
                    'recipient_address' => 'Jl. Dago No. 25',
                    'recipient_phone' => '081234567802',
                    'recipient' => 'Dewi Anggraini',
                    'weight' => 8,
                    'dimensions' => [35, 25, 20],
                    'payment' => 'UNPAID',
                    'events' => [
                        [
                            'REGISTERED',
                            'Jakarta Main Hub',
                            'Shipment registered',
                            'JKT',
                            96
                        ],
                        [
                            'IN_TRANSIT',
                            'Jakarta Port',
                            'Shipment dispatched to destination',
                            'JKT',
                            72
                        ],
                        [
                            'ARRIVED_AT_DESTINATION',
                            'Surabaya Branch',
                            'Shipment arrived at destination branch',
                            'SBY',
                            24
                        ],
                    ],
                ],
                [
                    'number' => 'AKA-DEMO-0005',
                    'origin' => 'JKT',
                    'destination' => 'MKS',
                    'service' => 'REGULAR_SEA',
                    'sender' => 'PT Mitra Dagang Indonesia',
                    'sender_phone' => '081234567801',
                    'sender_address' => 'Jl. Sudirman No. 10',
                    'recipient_address' => 'Jl. Dago No. 25',
                    'recipient_phone' => '081234567802',
                    'recipient' => 'Fajar Ramadhan',
                    'weight' => 22,
                    'dimensions' => [50, 40, 35],
                    'payment' => 'PAID',
                    'events' => [
                        [
                            'REGISTERED',
                            'Jakarta Main Hub',
                            'Shipment registered',
                            'JKT',
                            168
                        ],
                        [
                            'IN_TRANSIT',
                            'Jakarta Port',
                            'Shipment dispatched to port',
                            'JKT',
                            144
                        ],
                        [
                            'IN_MARITIME_TRANSIT',
                            'Makassar Sea Route',
                            'Shipment transported by sea',
                            'JKT',
                            96
                        ],
                        [
                            'ARRIVED_AT_DESTINATION',
                            'Makassar Branch',
                            'Shipment arrived at destination branch',
                            'MKS',
                            48
                        ],
                        [
                            'DELIVERED',
                            'Makassar',
                            'Shipment delivered to recipient',
                            'MKS',
                            24
                        ],
                    ],
                ],
                [
                    'number' => 'AKA-DEMO-0006',
                    'origin' => 'JKT',
                    'destination' => 'BDG',
                    'service' => 'OVERLAND',
                    'sender' => 'Maya Collection',
                    'sender_phone' => '081234567801',
                    'sender_address' => 'Jl. Sudirman No. 10',
                    'recipient_address' => 'Jl. Dago No. 25',
                    'recipient_phone' => '081234567802',
                    'recipient' => 'Nadia Putri',
                    'weight' => 6,
                    'dimensions' => [25, 20, 15],
                    'payment' => 'UNPAID',
                    'events' => [
                        [
                            'REGISTERED',
                            'Jakarta Main Hub',
                            'Shipment registered',
                            'JKT',
                            48
                        ],
                        [
                            'IN_TRANSIT',
                            'Jakarta - Bandung Route',
                            'Shipment temporarily held for address verification',
                            'JKT',
                            24
                        ],
                        [
                            'ON_HOLD',
                            'Jakarta Main Hub',
                            'Delivery paused pending recipient address confirmation',
                            'JKT',
                            2
                        ],
                    ],
                ],
            ];

            foreach ($scenarios as $scenario) {
                $origin = $branches[$scenario['origin']];
                $destination = $branches[$scenario['destination']];

                $tariffKey = $scenario['origin'] . '-'
                    . $scenario['destination'] . '-' . $scenario['service'];

                $tariff = $tariffs[$tariffKey];

                [$length, $width, $height] = $scenario['dimensions'];

                // Placeholder only: match this to the real calculation service.
                $volumeWeight = ($length * $width * $height)
                    / $tariff->volumetric_divisor;

                $chargeableWeight = max(
                    $scenario['weight'],
                    $volumeWeight,
                );

                $weightFee = $chargeableWeight * $tariff->rate_per_kg;
                $totalAmount = $tariff->base_fee + $weightFee;

                $events = $scenario['events'];
                $latestEvent = $events[array_key_last($events)];

                $shipment = Shipment::updateOrCreate(
                    ['tracking_number' => $scenario['number']],
                    [
                        'sender_name' => $scenario['sender'],
                        'sender_phone' => '081234567801',
                        'sender_address' => $scenario['sender_address'],
                        'recipient_address' => $scenario['recipient_address'],
                        'recipient_phone' => '081234567802',
                        'recipient_name' => $scenario['recipient'],
                        'origin_branch_id' => $origin->id,
                        'destination_branch_id' => $destination->id,
                        'service_type' => $scenario['service'],
                        'actual_weight_kg' => $scenario['weight'],
                        'length_cm' => $length,
                        'width_cm' => $width,
                        'height_cm' => $height,
                        'volume_weight' => $volumeWeight,
                        'chargeable_weight' => $chargeableWeight,
                        'tariff_id' => $tariff->id,
                        'base_fee_applied' => $tariff->base_fee,
                        'rate_per_kg_applied' => $tariff->rate_per_kg,
                        'volumetric_divisor_applied' =>
                        $tariff->volumetric_divisor,
                        'weight_fee' => $weightFee,
                        'total_amount' => $totalAmount,
                        'payment_status' => $scenario['payment'],
                        'current_status' => $latestEvent[0],
                        'estimated_arrival' => now()->addDays(3),
                    ],
                );

                foreach (
                    $events as [
                        $status,
                        $location,
                        $description,
                        $branchCode,
                        $hoursAgo
                    ]
                ) {
                    $eventTime = now('UTC')->subHours($hoursAgo);

                    TrackingCheckpoint::updateOrCreate(
                        [
                            'shipment_id' => $shipment->id,
                            'status' => $status,
                            'event_timestamp' => $eventTime,
                        ],
                        [
                            'branch_id' => $branches[$branchCode]->id,
                            'location_label' => $location,
                            'description' => $description,
                            'created_by' => $demoUsers['petugas']->id,
                        ],
                    );
                }
            }
        });
    }
}
