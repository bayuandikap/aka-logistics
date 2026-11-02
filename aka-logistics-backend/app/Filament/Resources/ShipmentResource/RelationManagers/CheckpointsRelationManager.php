<?php

namespace App\Filament\Resources\ShipmentResource\RelationManagers;

use App\Models\TrackingCheckpoint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class CheckpointsRelationManager extends RelationManager
{
    protected static string $relationship = 'checkpoints';

    protected static ?string $title = 'Riwayat Pelacakan';

    // -------------------------------------------------------------------------
    // Status options (single source of truth for this manager)
    // -------------------------------------------------------------------------

    private static function statusOptions(): array
    {
        return [
            'REGISTERED'             => 'Barang Diterima',
            'IN_TRANSIT'             => 'Dalam Perjalanan',
            'IN_MARITIME_TRANSIT'    => 'Dalam Pelayaran Laut',
            'ARRIVED_AT_DESTINATION' => 'Tiba di Cabang Tujuan',
            'DELIVERED'              => 'Terkirim',
            'ON_HOLD'                => 'Ditahan',
        ];
    }

    private static function statusColor(string $state): string
    {
        return match ($state) {
            'REGISTERED'             => 'gray',
            'IN_TRANSIT',
            'IN_MARITIME_TRANSIT'    => 'info',
            'ARRIVED_AT_DESTINATION' => 'warning',
            'DELIVERED'              => 'success',
            'ON_HOLD'                => 'danger',
            default                  => 'gray',
        };
    }

    // -------------------------------------------------------------------------
    // Sync the shipment's current_status to the latest checkpoint
    // -------------------------------------------------------------------------

    private function syncShipmentStatus(): void
    {
        $shipment = $this->getOwnerRecord();

        DB::transaction(function () use ($shipment) {
            $shipment->refresh()->lockForUpdate();

            $latest = $shipment->checkpoints()
                ->orderByDesc('event_timestamp')
                ->orderByDesc('id')
                ->first();

            $shipment->current_status = $latest?->status ?? 'REGISTERED';
            $shipment->save();
        });
    }

    // -------------------------------------------------------------------------
    // Form
    // -------------------------------------------------------------------------

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(self::statusOptions())
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('branch_id')
                            ->label('Cabang / Lokasi')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('— Pilih cabang (opsional) —'),

                        Forms\Components\TextInput::make('location_label')
                            ->label('Label Lokasi')
                            ->placeholder('Contoh: Pelabuhan Tanjung Perak, Surabaya')
                            ->required()
                            ->maxLength(150),

                        Forms\Components\DateTimePicker::make('event_timestamp')
                            ->label('Waktu Kejadian')
                            ->timezone('Asia/Jakarta')
                            ->default(fn() => now('Asia/Jakarta'))
                            ->required(),
                    ]),

                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->placeholder('Contoh: Kargo telah dimuat ke kapal KM Nusantara Ekspres')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    // -------------------------------------------------------------------------
    // Table
    // -------------------------------------------------------------------------

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('location_label')
            ->defaultSort('event_timestamp', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('event_timestamp')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i', 'Asia/Jakarta')
                    ->sortable()
                    ->width('170px'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => self::statusColor($state))
                    ->formatStateUsing(fn(string $state): string => self::statusOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('location_label')
                    ->label('Lokasi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(60)
                    ->tooltip(fn(TrackingCheckpoint $record): string => $record->description),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dicatat oleh')
                    ->placeholder('—'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Checkpoint')
                    ->icon('heroicon-m-map-pin')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = auth()->id();
                        return $data;
                    })
                    ->after(function (): void {
                        $this->syncShipmentStatus();
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Checkpoint ditambahkan')
                            ->body('Status pengiriman telah diperbarui.')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Ubah')
                    ->after(function (): void {
                        $this->syncShipmentStatus();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->after(function (): void {
                        $this->syncShipmentStatus();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->after(function (): void {
                            $this->syncShipmentStatus();
                        }),
                ]),
            ]);
    }
}
