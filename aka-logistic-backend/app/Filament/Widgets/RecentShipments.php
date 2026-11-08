<?php

namespace App\\Filament\\Widgets;

use App\\Models\\Shipment;
use Filament\\Tables;
use Filament\\Tables\\Table;
use Filament\\Widgets\\TableWidget as BaseWidget;

class RecentShipments extends BaseWidget
{
    protected static ?string $heading = 'Pengiriman Terbaru';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Shipment::query()->with(['originBranch', 'destinationBranch'])->latest('created_at'))
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\\Columns\\TextColumn::make('tracking_number')
                    ->label('Nomor Resi')->searchable()->copyable()->weight('bold'),
                Tables\\Columns\\TextColumn::make('sender_name')->label('Pengirim')->searchable(),
                Tables\\Columns\\TextColumn::make('recipient_name')->label('Penerima')->searchable(),
                Tables\\Columns\\TextColumn::make('originBranch.name')->label('Asal'),
                Tables\\Columns\\TextColumn::make('destinationBranch.name')->label('Tujuan'),
                Tables\\Columns\\TextColumn::make('current_status')
                    ->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'REGISTERED' => 'gray',
                        'IN_TRANSIT' => 'info',
                        'IN_MARITIME_TRANSIT' => 'primary',
                        'ARRIVED_AT_DESTINATION' => 'warning',
                        'DELIVERED' => 'success',
                        'ON_HOLD' => 'danger',
                        default => 'gray',
                    }),
                Tables\\Columns\\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')->dateTime('d M Y, H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
