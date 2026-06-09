<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxInvoiceResource\Pages\CreateTaxInvoice;
use App\Filament\Resources\TaxInvoiceResource\Pages\EditTaxInvoice;
use App\Filament\Resources\TaxInvoiceResource\Pages\ListTaxInvoices;
use App\Models\Finance\TaxInvoice;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TaxInvoiceResource extends Resource
{
    protected static ?string $model = TaxInvoice::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('tax_invoice_number')
                    ->required()
                    ->maxLength(100),
                Forms\Components\DatePicker::make('tax_invoice_date')
                    ->required(),
                Forms\Components\Select::make('transaction_type')
                    ->options([
                        'sales' => 'Penjualan',
                        'purchase' => 'Pembelian',
                        'sales_return' => 'Retur Penjualan',
                        'purchase_return' => 'Retur Pembelian',
                    ])
                    ->required(),
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->nullable()
                    ->label('Pelanggan'),
                Forms\Components\TextInput::make('taxpayer_name')
                    ->required(),
                Forms\Components\TextInput::make('taxpayer_npwp')
                    ->required()
                    ->label('NPWP'),
                Forms\Components\Textarea::make('taxpayer_address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('dpp')
                    ->required()
                    ->numeric()
                    ->label('DPP'),
                Forms\Components\TextInput::make('ppn_amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ppnbm_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tax_invoice_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_invoice_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('taxpayer_name')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('dpp')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('ppn_amount')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_type')
                    ->options([
                        'sales' => 'Penjualan',
                        'purchase' => 'Pembelian',
                        'sales_return' => 'Retur Penjualan',
                        'purchase_return' => 'Retur Pembelian',
                    ]),
            ])
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
            'index' => ListTaxInvoices::route('/'),
            'create' => CreateTaxInvoice::route('/create'),
            'edit' => EditTaxInvoice::route('/{record}/edit'),
        ];
    }
}
