<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FixedAssetResource\Pages\CreateFixedAsset;
use App\Filament\Resources\FixedAssetResource\Pages\EditFixedAsset;
use App\Filament\Resources\FixedAssetResource\Pages\ListFixedAssets;
use App\Models\Finance\FixedAsset;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FixedAssetResource extends Resource
{
    protected static ?string $model = FixedAsset::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-calculator';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('asset_name')
                    ->required()
                    ->maxLength(200)
                    ->label('Nama Aset'),
                Forms\Components\Select::make('asset_category')
                    ->options([
                        'land' => 'Tanah',
                        'building' => 'Bangunan',
                        'machinery' => 'Mesin',
                        'vehicle' => 'Kendaraan',
                        'furniture' => 'Furniture',
                        'computer' => 'Komputer',
                        'other' => 'Lainnya',
                    ])
                    ->required()
                    ->label('Kategori'),
                Forms\Components\DatePicker::make('purchase_date')
                    ->required()
                    ->label('Tanggal Perolehan'),
                Forms\Components\TextInput::make('purchase_cost')
                    ->required()
                    ->numeric()
                    ->label('Harga Perolehan'),
                Forms\Components\TextInput::make('salvage_value')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Nilai Residu'),
                Forms\Components\TextInput::make('useful_life_years')
                    ->required()
                    ->integer()
                    ->minValue(1)
                    ->label('Masa Manfaat (Tahun)'),
                Forms\Components\Select::make('depreciation_method')
                    ->options([
                        'straight_line' => 'Garis Lurus',
                        'declining_balance' => 'Saldo Menurun Ganda',
                    ])
                    ->required()
                    ->label('Metode Penyusutan'),
                Forms\Components\TextInput::make('location')
                    ->maxLength(200)
                    ->label('Lokasi'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull()
                    ->label('Keterangan'),
                Forms\Components\Select::make('chart_of_account_id')
                    ->relationship('chartOfAccount', 'name')
                    ->searchable()
                    ->label('Akun Aset'),
                Forms\Components\Select::make('accumulated_depr_account_id')
                    ->relationship('accumulatedDeprAccount', 'name')
                    ->searchable()
                    ->label('Akun Akumulasi Penyusutan'),
                Forms\Components\Select::make('depreciation_expense_account_id')
                    ->relationship('depreciationExpenseAccount', 'name')
                    ->searchable()
                    ->label('Akun Beban Penyusutan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_number')
                    ->searchable()
                    ->sortable()
                    ->label('Asset #'),
                Tables\Columns\TextColumn::make('asset_name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                Tables\Columns\TextColumn::make('asset_category')
                    ->badge()
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('purchase_cost')
                    ->money('IDR')
                    ->sortable()
                    ->label('Harga Perolehan'),
                Tables\Columns\TextColumn::make('accumulated_depreciation')
                    ->money('IDR')
                    ->sortable()
                    ->label('Akum. Penyusutan'),
                Tables\Columns\TextColumn::make('book_value')
                    ->money('IDR')
                    ->sortable()
                    ->label('Nilai Buku'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'info',
                        'depreciating' => 'warning',
                        'fully_depreciated' => 'success',
                        'sold' => 'purple',
                        'retired' => 'danger',
                        default => 'gray',
                    })
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'depreciating' => 'Disusutkan',
                        'fully_depreciated' => 'Tersusutkan',
                        'sold' => 'Terjual',
                        'retired' => 'Retire',
                    ]),
                Tables\Filters\SelectFilter::make('asset_category')
                    ->options([
                        'land' => 'Tanah',
                        'building' => 'Bangunan',
                        'machinery' => 'Mesin',
                        'vehicle' => 'Kendaraan',
                        'furniture' => 'Furniture',
                        'computer' => 'Komputer',
                        'other' => 'Lainnya',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFixedAssets::route('/'),
            'create' => CreateFixedAsset::route('/create'),
            'edit' => EditFixedAsset::route('/{record}/edit'),
        ];
    }
}
