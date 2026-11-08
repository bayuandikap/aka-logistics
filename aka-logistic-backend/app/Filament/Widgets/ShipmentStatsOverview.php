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
            Stat::make('Total Shipments', $totalShipments)
                ->description('All registered shipments')->color('primary')->icon('heroicon-o-cube'),
            Stat::make('Delivered', $deliveredShipments)
                ->description('Successfully delivered')->color('success')->icon('heroicon-o-check-circle'),
            Stat::make('In Transit', $inTransitShipments)
                ->description('Currently in transportation')->color('warning')->icon('heroicon-o-truck'),
            Stat::make('On Hold', $onHoldShipments)
                ->description('Require operational attention')->color('danger')->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
