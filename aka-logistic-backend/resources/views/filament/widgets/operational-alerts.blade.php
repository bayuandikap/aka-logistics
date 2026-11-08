<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Operational Alerts</x-slot>
        <x-slot name="description">Shipments that may require operational follow-up.</x-slot>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            {{-- On-hold shipments --}}
            <section class="rounded-xl border border-danger-200 bg-danger-50/60 p-4 dark:border-danger-800 dark:bg-danger-950/20">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <x-filament::icon
                            icon="heroicon-o-exclamation-triangle"
                            class="h-5 w-5 text-danger-600 dark:text-danger-400"
                        />
                        <h3 class="font-semibold text-danger-800 dark:text-danger-300">
                            On Hold Shipments
                        </h3>
                    </div>
                    <span class="inline-flex min-w-8 items-center justify-center rounded-full bg-danger-100 px-2.5 py-1 text-sm font-semibold text-danger-700 dark:bg-danger-900/60 dark:text-danger-300">
                        {{ $onHoldCount }}
                    </span>
                </div>

                <p class="mt-2 text-sm text-danger-700/80 dark:text-danger-300/80">
                    Shipments requiring attention
                </p>

                @if ($onHoldShipments->isNotEmpty())
                    <ul class="mt-4 divide-y divide-danger-200 dark:divide-danger-800">
                        @foreach ($onHoldShipments as $shipment)
                            <li class="py-3 first:pt-0 last:pb-0">
                                <a
                                    href="{{ route('filament.admin.resources.shipments.edit', ['record' => $shipment]) }}"
                                    class="group flex items-center justify-between gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-danger-500"
                                >
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-semibold text-gray-950 group-hover:text-danger-700 dark:text-white dark:group-hover:text-danger-300">
                                            {{ $shipment->tracking_number }}
                                        </span>
                                        <span class="mt-0.5 block truncate text-xs text-gray-600 dark:text-gray-400">
                                            {{ $shipment->sender_name ?: 'Sender not specified' }}
                                        </span>
                                    </span>
                                    <x-filament::icon
                                        icon="heroicon-m-chevron-right"
                                        class="h-4 w-4 shrink-0 text-danger-500"
                                    />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="mt-4 flex items-center gap-2 rounded-lg bg-white/70 px-3 py-4 text-sm text-success-700 dark:bg-gray-900/40 dark:text-success-400">
                        <x-filament::icon icon="heroicon-o-check-circle" class="h-5 w-5 shrink-0" />
                        No shipments are currently on hold.
                    </div>
                @endif
            </section>

            {{-- Overdue shipments --}}
            <section class="rounded-xl border border-warning-200 bg-warning-50/60 p-4 dark:border-warning-800 dark:bg-warning-950/20">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <x-filament::icon
                            icon="heroicon-o-clock"
                            class="h-5 w-5 text-warning-600 dark:text-warning-400"
                        />
                        <h3 class="font-semibold text-warning-800 dark:text-warning-300">
                            Overdue Shipments
                        </h3>
                    </div>
                    <span class="inline-flex min-w-8 items-center justify-center rounded-full bg-warning-100 px-2.5 py-1 text-sm font-semibold text-warning-800 dark:bg-warning-900/60 dark:text-warning-300">
                        {{ $overdueCount }}
                    </span>
                </div>

                <p class="mt-2 text-sm text-warning-800/80 dark:text-warning-300/80">
                    Past estimated arrival
                </p>

                @if ($overdueShipments->isNotEmpty())
                    <ul class="mt-4 divide-y divide-warning-200 dark:divide-warning-800">
                        @foreach ($overdueShipments as $shipment)
                            <li class="py-3 first:pt-0 last:pb-0">
                                <a
                                    href="{{ route('filament.admin.resources.shipments.edit', ['record' => $shipment]) }}"
                                    class="group flex items-center justify-between gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-warning-500"
                                >
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-semibold text-gray-950 group-hover:text-warning-800 dark:text-white dark:group-hover:text-warning-300">
                                            {{ $shipment->tracking_number }}
                                        </span>
                                        <span class="mt-0.5 block text-xs text-gray-600 dark:text-gray-400">
                                            Estimated {{ $shipment->estimated_arrival?->format('d M Y') ?? 'not specified' }}
                                        </span>
                                    </span>
                                    <span class="shrink-0 rounded-md bg-warning-100 px-2 py-1 text-xs font-semibold text-warning-800 dark:bg-warning-900/60 dark:text-warning-300">
                                        {{ $shipment->estimated_arrival ? (int) $shipment->estimated_arrival->diffInDays(now()) . 'd overdue' : 'Overdue' }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="mt-4 flex items-center gap-2 rounded-lg bg-white/70 px-3 py-4 text-sm text-success-700 dark:bg-gray-900/40 dark:text-success-400">
                        <x-filament::icon icon="heroicon-o-check-circle" class="h-5 w-5 shrink-0" />
                        No overdue shipments.
                    </div>
                @endif
            </section>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
