<?php

namespace App\Filament\Resources;

use App\Enums\StreamSource;
use App\Filament\Resources\StreamerResource\Pages;
use App\Filament\Resources\StreamerResource\RelationManagers;
use App\Models\Streamer;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class StreamerResource extends Resource
{
    protected static ?string $model = Streamer::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('source')
                    ->options(StreamSource::collection())
                    ->required(),
                Forms\Components\TextInput::make('source_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('followers_count'),
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
                Tables\Columns\TextColumn::make('source'),
                Tables\Columns\TextColumn::make('source_id'),
                Tables\Columns\TextColumn::make('url'),
                Tables\Columns\TextColumn::make('language.name'),
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
            RelationManagers\EventsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStreamers::route('/'),
            'create' => Pages\CreateStreamer::route('/create'),
            'edit' => Pages\EditStreamer::route('/{record}/edit'),
        ];
    }
}
