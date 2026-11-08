<?php

namespace App\\Filament\\Widgets;

use App\\Models\\Shipment;
use Filament\\Widgets\\ChartWidget;

class ShipmentActivityChart extends ChartWidget
{
    protected static ?string $heading = 'Shipment Activity';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = collect(range(5, 0))
            ->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        $labels = [];
        $data = [];

        foreach ($months as $month) {
            $labels[] = $month->format('M Y');
            $data[] = Shipment::query()
                ->whereBetween('created_at', [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth(),
                ])
                ->count();
        }

        return [
            'datasets' => [[
                'label' => 'Shipments',
                'data' => $data,
                'borderColor' => '#3B82F6',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'fill' => true,
                'tension' => 0.3,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
