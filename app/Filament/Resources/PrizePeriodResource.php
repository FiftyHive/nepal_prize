<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrizePeriodResource\Pages;
use App\Models\PrizePeriod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PrizePeriodResource extends Resource
{
    protected static ?string $model = PrizePeriod::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Prize Periods';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Period Details')->schema([

                Forms\Components\TextInput::make('year')
                    ->label('Nepali Year')
                    ->placeholder('e.g. 2083')
                    ->numeric()
                    ->minValue(2070)
                    ->maxValue(2100)
                    ->required(),

                Forms\Components\TextInput::make('month')
                    ->label('Nepali Month')
                    ->placeholder('e.g. Shrawan')
                    ->required()
                    ->maxLength(20),

                Forms\Components\TextInput::make('start_day')
                    ->label('Start Day')
                    ->numeric()->minValue(1)->maxValue(32)->required(),

                Forms\Components\TextInput::make('end_day')
                    ->label('End Day')
                    ->numeric()->minValue(1)->maxValue(32)->required(),

            ])->columns(2),

            Forms\Components\Section::make('Gregorian Dates (for scraper matching)')->schema([

                Forms\Components\DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),

                Forms\Components\DatePicker::make('end_date')
                    ->label('End Date')
                    ->required()
                    ->afterOrEqual('start_date'),

                Forms\Components\DatePicker::make('draw_date')
                    ->label('Draw Date')
                    ->nullable(),

            ])->columns(3),

            Forms\Components\Section::make('Display & Status')->schema([

                Forms\Components\TextInput::make('display_label')
                    ->label('Display Label')
                    ->placeholder('e.g. 2083 Shrawan 1 - 15')
                    ->required()
                    ->maxLength(100)
                    ->columnSpan(2),

                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),

            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_label')
                    ->label('Period')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('draw_date')
                    ->label('Draw Date')
                    ->date('d M Y')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('winner_coupons_count')
                    ->label('Winners')
                    ->counts('winnerCoupons')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'active', 'danger' => 'inactive']),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListPrizePeriods::route('/'),
            'create' => Pages\CreatePrizePeriod::route('/create'),
            'edit'   => Pages\EditPrizePeriod::route('/{record}/edit'),
        ];
    }
}
