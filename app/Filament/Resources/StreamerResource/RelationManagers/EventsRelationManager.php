<?php

namespace App\Filament\Resources\StreamerResource\RelationManagers;

use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
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
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('videoGame.name'),
                Tables\Columns\TextColumn::make('provider.name'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('start_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->date(),
                Tables\Columns\TextColumn::make('tier')->sortable(),
                Tables\Columns\IconColumn::make('is_online')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),            ])
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
