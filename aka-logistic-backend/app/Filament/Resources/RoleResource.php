<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Manajemen Akses';

    protected static ?string $modelLabel = 'Role';

    protected static ?string $pluralModelLabel = 'Role';

    public static function canViewAny(): bool
    {
        return static::isSuperAdmin();
    }

    public static function canCreate(): bool
    {
        return static::isSuperAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return static::isSuperAdmin()
            && $record->guard_name === 'web'
            && $record->name !== 'super_admin';
    }

    public static function canDelete(Model $record): bool
    {
        return static::canEdit($record);
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Role')
                ->required()
                ->maxLength(125)
                ->unique(ignoreRecord: true),

            Forms\Components\CheckboxList::make('permissions')
                ->label('Permissions')
                ->relationship(
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query): Builder => $query
                        ->where('guard_name', 'web')
                        ->orderBy('name'),
                )
                ->columns(2)
                ->searchable()
                ->bulkToggleable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Role')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Jumlah Permissions'),

                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Jumlah Pengguna'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Ubah'),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    private static function isSuperAdmin(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }
}
