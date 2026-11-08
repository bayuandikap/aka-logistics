<?php

namespace App\Filament\Resources\ShipmentResource\RelationManagers;

use App\Models\Shipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class CheckpointsRelationManager extends RelationManager
{
    protected static string $relationship = 'checkpoints';

    protected static ?string $title = 'Riwayat Pelacakan';

    protected static ?string $recordTitleAttribute = 'location_label';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('branch_id')
                ->label('Cabang / Lokasi')
                ->relationship('branch', 'name')
                ->searchable()
                ->preload(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'REGISTERED' => 'Barang Diterima',
                    'IN_TRANSIT' => 'Dalam Perjalanan',
                    'IN_MARITIME_TRANSIT' => 'Dalam Pelayaran Laut',
                    'ARRIVED_AT_DESTINATION' => 'Tiba di Cabang Tujuan',
                    'DELIVERED' => 'Terkirim',
                    'ON_HOLD' => 'Ditahan',
                ])
                ->required(),

            Forms\Components\TextInput::make('location_label')
                ->label('Lokasi')
                ->required()
                ->maxLength(150),

            Forms\Components\Textarea::make('description')
                ->label('Keterangan')
                ->required()
                ->columnSpanFull(),

            Forms\Components\DateTimePicker::make('event_timestamp')
                ->label('Waktu Kejadian')
                ->timezone('Asia/Jakarta')
                ->default(fn () => now('Asia/Jakarta'))
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'REGISTERED' => 'gray',
                        'IN_TRANSIT', 'IN_MARITIME_TRANSIT' => 'info',
                        'ARRIVED_AT_DESTINATION' => 'warning',
                        'DELIVERED' => 'success',
                        'ON_HOLD' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('location_label')
                    ->label('Lokasi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(60)
                    ->wrap(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('event_timestamp')
                    ->label('Waktu Kejadian')
                    ->dateTime('d M Y, H:i', 'Asia/Jakarta')
                    ->sortable(),
            ])
            ->defaultSort('event_timestamp', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Riwayat')
                    ->visible(fn (): bool => auth()->user()?->can('create tracking checkpoints') ?? false)
                    ->after(fn () => $this->syncShipmentStatus()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Ubah')
                    ->visible(fn (): bool => auth()->user()?->can('edit tracking checkpoints') ?? false)
                    ->after(fn () => $this->syncShipmentStatus()),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->visible(fn (): bool => auth()->user()?->can('delete tracking checkpoints') ?? false)
                    ->after(fn () => $this->syncShipmentStatus()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus Terpilih')
                    ->visible(fn (): bool => auth()->user()?->can('delete tracking checkpoints') ?? false)
                    ->after(fn () => $this->syncShipmentStatus()),
            ]);
    }

    protected function syncShipmentStatus(): void
    {
        $shipmentId = $this->getOwnerRecord()->getKey();

        DB::transaction(function () use ($shipmentId): void {
            $shipment = Shipment::query()
                ->lockForUpdate()
                ->find($shipmentId);

            if (! $shipment) {
                return;
            }

            $latestCheckpoint = $shipment->checkpoints()
                ->orderByDesc('event_timestamp')
                ->orderByDesc('id')
                ->first();

            $shipment->current_status = $latestCheckpoint
                ? $latestCheckpoint->status
                : 'REGISTERED';

            $shipment->save();
        });
    }
}
