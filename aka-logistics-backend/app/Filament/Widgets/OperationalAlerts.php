<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\Widget;

class OperationalAlerts extends Widget
{
    protected static string $view = 'filament.widgets.operational-alerts';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $onHoldQuery = Shipment::query()
            ->where('current_status', 'ON_HOLD');

        $overdueQuery = Shipment::query()
            ->whereNotNull('estimated_arrival')
            ->where('estimated_arrival', '<', now())
            ->where('current_status', '!=', 'DELIVERED');

        return [
            'onHoldCount' => (clone $onHoldQuery)->count(),
            'overdueCount' => (clone $overdueQuery)->count(),

            'onHoldShipments' => (clone $onHoldQuery)
                ->latest('created_at')
                ->limit(5)
                ->get(),

            'overdueShipments' => (clone $overdueQuery)
                ->oldest('estimated_arrival')
                ->limit(5)
                ->get(),
        ];
    }
}
