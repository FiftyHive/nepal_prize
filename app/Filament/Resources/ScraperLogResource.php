<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScraperLogResource\Pages;
use App\Models\ScraperLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ScraperLogResource extends Resource
{
    protected static ?string $model = ScraperLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    protected static ?string $navigationLabel = 'Scraper Logs';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([]); // Read-only — no create/edit
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Date / Time')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'success',
                        'danger'  => 'failed',
                        'warning' => 'partial',
                        'info'    => 'running',
                    ]),

                Tables\Columns\TextColumn::make('triggered_by')
                    ->label('Triggered By')
                    ->badge(),

                Tables\Columns\TextColumn::make('periods_processed')->label('Periods'),
                Tables\Columns\TextColumn::make('coupons_found')->label('Found'),
                Tables\Columns\TextColumn::make('new_coupons')->label('New'),
                Tables\Columns\TextColumn::make('existing_coupons')->label('Existing'),
                Tables\Columns\TextColumn::make('errors')->label('Errors')
                    ->color(fn ($state) => $state > 0 ? 'danger' : null),

                Tables\Columns\TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(60)
                    ->placeholder('—')
                    ->tooltip(fn ($state) => $state),
            ])
            ->defaultSort('started_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'failed'  => 'Failed',
                        'partial' => 'Partial',
                        'running' => 'Running',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->poll('30s'); // Auto-refresh every 30s
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canCreate(): bool
    {
        return false; // No manual creation of logs
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScraperLogs::route('/'),
        ];
    }
}
