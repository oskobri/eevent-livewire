<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoGameResource\Pages;
use App\Models\Player;
use App\Models\Team;
use App\Models\VideoGame;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Relations\Relation;

class VideoGameResource extends Resource
{
    protected static ?string $model = VideoGame::class;

    protected static ?string $navigationIcon = 'heroicon-o-play';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        $opponentTypes = [app(Team::class)->getMorphClass(), app(Player::class)->getMorphClass()];

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('opponent_type')
                    ->required()
                    ->options(array_combine($opponentTypes, $opponentTypes)),
            ]);
    }

    /**
     * @param Table $table
     * @return Table
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListVideoGames::route('/'),
            'create' => Pages\CreateVideoGame::route('/create'),
            'edit' => Pages\EditVideoGame::route('/{record}/edit'),
        ];
    }
}
