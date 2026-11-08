<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentShipments extends BaseWidget
{
    protected static ?string $heading = 'Recent Shipments';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Shipment::query()
                    ->latest('created_at')
            )
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('Tracking Number')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('sender_name')
                    ->label('Sender')
                    ->searchable(),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label('Recipient')
                    ->searchable(),

                Tables\Columns\TextColumn::make('originBranch.name')
                    ->label('Origin'),

                Tables\Columns\TextColumn::make('destinationBranch.name')
                    ->label('Destination'),

                Tables\Columns\TextColumn::make('current_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'REGISTERED' => 'gray',
                        'IN_TRANSIT' => 'info',
                        'IN_MARITIME_TRANSIT' => 'primary',
                        'ARRIVED_AT_DESTINATION' => 'warning',
                        'DELIVERED' => 'success',
                        'ON_HOLD' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
