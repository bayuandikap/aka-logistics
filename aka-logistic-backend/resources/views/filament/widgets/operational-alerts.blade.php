<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Peringatan Operasional</x-slot>
        <x-slot name="description">Pengiriman yang memerlukan perhatian dan tindak lanjut.</x-slot>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            {{-- On-hold shipments --}}
            <section class="rounded-xl border border-danger-200 bg-danger-50/60 p-3 dark:border-danger-800 dark:bg-danger-950/20">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex min-w-0 items-center gap-2">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-5 w-5 shrink-0 text-danger-600 dark:text-danger-400" />
                        <h3 class="truncate text-sm font-semibold text-danger-800 dark:text-danger-300">
                            Pengiriman Tertahan
                        </h3>
                    </div>
                    <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-danger-100 px-2 py-0.5 text-xs font-semibold text-danger-700 dark:bg-danger-900/60 dark:text-danger-300">
                        {{ $onHoldCount }}
                    </span>
                </div>

                <p class="mt-1 text-xs text-danger-700/80 dark:text-danger-300/80">
                    Pengiriman yang memerlukan penanganan
                </p>

                @if ($onHoldShipments->isNotEmpty())
                    <ul class="mt-2 divide-y divide-danger-200 dark:divide-danger-800">
                        @foreach ($onHoldShipments as $shipment)
                            <li class="py-2 first:pt-0 last:pb-0">
                                <a
                                    href="{{ route('filament.admin.resources.shipments.edit', ['record' => $shipment]) }}"
                                    class="group flex items-center justify-between gap-2 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-danger-500"
                                >
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-semibold text-gray-950 group-hover:text-danger-700 dark:text-white dark:group-hover:text-danger-300">
                                            {{ $shipment->tracking_number }}
                                        </span>
                                        <span class="block truncate text-xs text-gray-600 dark:text-gray-400">
                                            {{ $shipment->sender_name ?: 'Pengirim belum diisi' }}
                                        </span>
                                    </span>
                                    <x-filament::icon icon="heroicon-m-chevron-right" class="h-4 w-4 shrink-0 text-danger-500" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="mt-2 flex items-center gap-2 rounded-lg bg-white/70 px-3 py-2 text-xs text-success-700 dark:bg-gray-900/40 dark:text-success-400">
                        <x-filament::icon icon="heroicon-o-check-circle" class="h-4 w-4 shrink-0" />
                        Tidak ada pengiriman yang sedang tertahan.
                    </div>
                @endif
            </section>

            {{-- Terlambat shipments --}}
            <section class="rounded-xl border border-warning-200 bg-warning-50/60 p-3 dark:border-warning-800 dark:bg-warning-950/20">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex min-w-0 items-center gap-2">
                        <x-filament::icon icon="heroicon-o-clock" class="h-5 w-5 shrink-0 text-warning-600 dark:text-warning-400" />
                        <h3 class="truncate text-sm font-semibold text-warning-800 dark:text-warning-300">
                            Pengiriman Terlambat
                        </h3>
                    </div>
                    <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-warning-100 px-2 py-0.5 text-xs font-semibold text-warning-800 dark:bg-warning-900/60 dark:text-warning-300">
                        {{ $overdueCount }}
                    </span>
                </div>

                <p class="mt-1 text-xs text-warning-800/80 dark:text-warning-300/80">
                    Melewati estimasi waktu tiba
                </p>

                @if ($overdueShipments->isNotEmpty())
                    <ul class="mt-2 divide-y divide-warning-200 dark:divide-warning-800">
                        @foreach ($overdueShipments as $shipment)
                            <li class="py-2 first:pt-0 last:pb-0">
                                <a
                                    href="{{ route('filament.admin.resources.shipments.edit', ['record' => $shipment]) }}"
                                    class="group flex items-center justify-between gap-2 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-warning-500"
                                >
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-semibold text-gray-950 group-hover:text-warning-800 dark:text-white dark:group-hover:text-warning-300">
                                            {{ $shipment->tracking_number }}
                                        </span>
                                        <span class="block text-xs text-gray-600 dark:text-gray-400">
                                            Estimasi tiba {{ $shipment->estimated_arrival?->format('d M Y') ?? 'belum ditentukan' }}
                                        </span>
                                    </span>
                                    <span class="shrink-0 rounded-md bg-warning-100 px-2 py-1 text-[11px] font-semibold text-warning-800 dark:bg-warning-900/60 dark:text-warning-300">
                                        {{ $shipment->estimated_arrival ? (int) $shipment->estimated_arrival->diffInDays(now()) . 'hari terlambat' : 'Overdue' }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="mt-2 flex items-center gap-2 rounded-lg bg-white/70 px-3 py-2 text-xs text-success-700 dark:bg-gray-900/40 dark:text-success-400">
                        <x-filament::icon icon="heroicon-o-check-circle" class="h-4 w-4 shrink-0" />
                        Tidak ada pengiriman yang terlambat.
                    </div>
                @endif
            </section>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
