<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerResource\Pages;
use App\Filament\Resources\PlayerResource\RelationManagers;
use App\Models\Player;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nickname')
                    ->required(),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name->en')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nickname'),
                Tables\Columns\TextColumn::make('team.name'),
                Tables\Columns\TextColumn::make('videogame.name'),
                Tables\Columns\TextColumn::make('country.name'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('video_game_id')
                    ->label('Video game')
                    ->relationship('videoGame', 'name'),
                Tables\Filters\SelectFilter::make('team_id')
                    ->label('Team')
                    ->searchable()
                    ->preload()
                    ->relationship('team', 'name'),
                Tables\Filters\SelectFilter::make('country_id')
                    ->label('Country')
                    ->searchable()
                    ->preload()
                    ->relationship('country', 'name->en'),
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
            'index' => Pages\ListPlayers::route('/'),
            'create' => Pages\CreatePlayer::route('/create'),
            'edit' => Pages\EditPlayer::route('/{record}/edit'),
        ];
    }
}
