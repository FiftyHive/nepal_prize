<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WinnerCouponResource\Pages;
use App\Models\PrizePeriod;
use App\Models\WinnerCoupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WinnerCouponResource extends Resource
{
    protected static ?string $model = WinnerCoupon::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Winner Coupons';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('period_id')
                ->label('Prize Period')
                ->relationship('period', 'display_label')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('coupon_code')
                ->label('Coupon Number')
                ->required()
                ->maxLength(20)
                ->placeholder('e.g. 123456789'),

            Forms\Components\TextInput::make('prize')
                ->label('Prize Description')
                ->nullable()
                ->maxLength(100),

            Forms\Components\Select::make('source')
                ->label('Source')
                ->options(['scraper' => 'Scraper', 'manual' => 'Manual', 'import' => 'Import'])
                ->default('manual')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('coupon_code')
                    ->label('Coupon Number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('period.display_label')
                    ->label('Prize Period')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('prize')
                    ->label('Prize')
                    ->placeholder('—'),

                Tables\Columns\BadgeColumn::make('source')
                    ->colors([
                        'primary' => 'scraper',
                        'warning' => 'manual',
                        'success' => 'import',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('period_id')
                    ->label('Prize Period')
                    ->options(PrizePeriod::orderByDesc('start_date')->pluck('display_label', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('source')
                    ->options(['scraper' => 'Scraper', 'manual' => 'Manual', 'import' => 'Import']),
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
            'index'  => Pages\ListWinnerCoupons::route('/'),
            'create' => Pages\CreateWinnerCoupon::route('/create'),
            'edit'   => Pages\EditWinnerCoupon::route('/{record}/edit'),
        ];
    }
}
