<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatcheResource\Pages;
use App\Filament\Resources\MatcheResource\RelationManagers;
use App\Models\MatchModel;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class MatchResource extends Resource
{
    protected static ?string $model = MatchModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Match';

    protected static ?string $pluralModelLabel = 'Matches';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('video_game_id')->relationship('videoGame', 'name')->nullable(),
                Forms\Components\Select::make('event_id')->relationship('event', 'name')->nullable(),
                Forms\Components\Select::make('left_opponent_id')->relationship('leftOpponent', 'name')->nullable(),
                Forms\Components\Select::make('right_opponent_id')->relationship('rightOpponent', 'name')->nullable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('videoGame.name'),
                Tables\Columns\TextColumn::make('event.name'),
                Tables\Columns\TextColumn::make('left_opponent_type'),
                Tables\Columns\TextColumn::make('leftOpponent.name'),
                Tables\Columns\TextColumn::make('rightOpponent.name'),
                Tables\Columns\TextColumn::make('score'),
                Tables\Columns\TextColumn::make('time')->dateTime(),

            ])
            ->filters([
                Filter::make('time')
                    ->indicateUsing(fn (array $data) => $data['date'] ? Carbon::parse($data['date'])->toFormattedDateString() : null)
                    ->form([
                        DatePicker::make('date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('time', '=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('video_game_id')
                    ->label('Video game')
                    ->relationship('videoGame', 'name')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatches::route('/'),
            'create' => Pages\CreateMatch::route('/create'),
            'edit' => Pages\EditMatch::route('/{record}/edit'),
        ];
    }
}
