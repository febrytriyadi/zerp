<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxReportResource\Pages\CreateTaxReport;
use App\Filament\Resources\TaxReportResource\Pages\EditTaxReport;
use App\Filament\Resources\TaxReportResource\Pages\ListTaxReports;
use App\Models\Finance\TaxReport;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TaxReportResource extends Resource
{
    protected static ?string $model = TaxReport::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('report_type')
                    ->options([
                        'ppn_1111' => 'SPT Masa PPN 1111',
                        'pph_23' => 'SPT Masa PPh 23',
                        'pph_42' => 'SPT Masa PPh 4(2)',
                        'pph_21' => 'SPT Masa PPh 21',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('period_code')
                    ->required()
                    ->maxLength(10),
                Forms\Components\DatePicker::make('period_start')
                    ->required(),
                Forms\Components\DatePicker::make('period_end')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('report_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('period_code'),
                Tables\Columns\TextColumn::make('total_dpp')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('total_tax')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
            'index' => ListTaxReports::route('/'),
            'create' => CreateTaxReport::route('/create'),
            'edit' => EditTaxReport::route('/{record}/edit'),
        ];
    }
}
