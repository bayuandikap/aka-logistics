<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Operational Alerts</x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-danger-300 dark:border-danger-700 p-5">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-6 w-6 text-danger-500" />
                    <span class="font-semibold text-danger-600">On Hold Shipments</span>
                </div>
                <p class="text-3xl font-bold mt-3">{{ $onHoldCount }}</p>
                <p class="text-sm text-gray-500 mt-1">Shipments requiring attention</p>
                @if ($onHoldShipments->isNotEmpty())
                    <ul class="mt-3 space-y-1 text-sm">
                        @foreach ($onHoldShipments as $shipment)
                            <li>{{ $shipment->tracking_number }} — {{ $shipment->sender_name }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="rounded-xl border border-warning-300 dark:border-warning-700 p-5">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-clock" class="h-6 w-6 text-warning-500" />
                    <span class="font-semibold text-warning-600">Overdue Shipments</span>
                </div>
                <p class="text-3xl font-bold mt-3">{{ $overdueCount }}</p>
                <p class="text-sm text-gray-500 mt-1">Past estimated arrival</p>
                @if ($overdueShipments->isNotEmpty())
                    <ul class="mt-3 space-y-1 text-sm">
                        @foreach ($overdueShipments as $shipment)
                            <li>{{ $shipment->tracking_number }} — {{ $shipment->estimated_arrival?->format('d M Y') }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
