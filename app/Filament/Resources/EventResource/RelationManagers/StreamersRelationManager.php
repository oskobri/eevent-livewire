<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Enums\StreamSource;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class StreamersRelationManager extends RelationManager
{
    protected static string $relationship = 'streamers';

    protected static ?string $recordTitleAttribute = 'source_id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('source_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    /**
     * @param Table $table
     * @return Table
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('source'),
                Tables\Columns\TextColumn::make('source_id'),
                Tables\Columns\TextColumn::make('language.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
