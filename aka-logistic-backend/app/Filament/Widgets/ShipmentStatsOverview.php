<?php

namespace App\\Filament\\Widgets;

use App\\Models\\Shipment;
use Filament\\Widgets\\StatsOverviewWidget as BaseWidget;
use Filament\\Widgets\\StatsOverviewWidget\\Stat;

class ShipmentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalShipments = Shipment::count();

        // The summary intentionally groups land and sea transit into one KPI.
        $inTransitShipments = Shipment::whereIn('current_status', [
            'IN_TRANSIT',
            'IN_MARITIME_TRANSIT',
        ])->count();

        $deliveredShipments = Shipment::where('current_status', 'DELIVERED')->count();
        $onHoldShipments = Shipment::where('current_status', 'ON_HOLD')->count();

        return [
            Stat::make('Total Pengiriman', $totalShipments)
                ->description('Semua pengiriman yang tercatat')
                ->color('primary')
                ->icon('heroicon-o-cube'),

            Stat::make('Dalam Perjalanan', $inTransitShipments)
                ->description('Gabungan pengiriman darat dan laut')
                ->color('info')
                ->icon('heroicon-o-truck'),

            Stat::make('Terkirim', $deliveredShipments)
                ->description('Sudah diterima oleh penerima')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Tertahan', $onHoldShipments)
                ->description('Perlu pemeriksaan atau tindak lanjut')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
