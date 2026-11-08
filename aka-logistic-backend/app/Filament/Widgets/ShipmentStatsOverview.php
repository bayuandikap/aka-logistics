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
        $deliveredShipments = Shipment::where('current_status', 'DELIVERED')->count();
        $inTransitShipments = Shipment::whereIn('current_status', [
            'IN_TRANSIT',
            'IN_MARITIME_TRANSIT',
        ])->count();
        $onHoldShipments = Shipment::where('current_status', 'ON_HOLD')->count();

        return [
            Stat::make('Total Pengiriman', $totalShipments)
                ->description('Seluruh pengiriman terdaftar')->color('primary')->icon('heroicon-o-cube'),
            Stat::make('Terkirim', $deliveredShipments)
                ->description('Berhasil diantarkan')->color('success')->icon('heroicon-o-check-circle'),
            Stat::make('Dalam Perjalanan', $inTransitShipments)
                ->description('Sedang dalam proses pengiriman')->color('warning')->icon('heroicon-o-truck'),
            Stat::make('Tertahan', $onHoldShipments)
                ->description('Memerlukan tindak lanjut operasional')->color('danger')->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
