<?php

namespace App\\Filament\\Widgets;

use App\\Models\\Shipment;
use Filament\\Widgets\\ChartWidget;

class ShipmentStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Pengiriman';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $statuses = [
            'REGISTERED',
            'IN_TRANSIT',
            'IN_MARITIME_TRANSIT',
            'ARRIVED_AT_DESTINATION',
            'DELIVERED',
            'ON_HOLD',
        ];

        $counts = Shipment::query()
            ->selectRaw('current_status, COUNT(*) as total')
            ->whereIn('current_status', $statuses)
            ->groupBy('current_status')
            ->pluck('total', 'current_status');

        return [
            'datasets' => [[
                'label' => 'Pengiriman',
                'data' => collect($statuses)
                    ->map(fn ($status) => (int) ($counts[$status] ?? 0))
                    ->values()
                    ->all(),
                'backgroundColor' => [
                    '#64748b', '#3b82f6', '#8b5cf6',
                    '#06b6d4', '#22c55e', '#ef4444',
                ],
            ]],
            'labels' => [
                'Terdaftar',
                'Dalam Perjalanan',
                'Dalam Pelayaran Laut',
                'Tiba di Cabang Tujuan',
                'Terkirim',
                'Tertahan',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
