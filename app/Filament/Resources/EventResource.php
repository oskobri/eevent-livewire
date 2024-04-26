<?php

namespace App\Filament\Resources;

use App\Enums\Tier;
use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers\StreamersRelationManager;
use App\Models\Event;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('video_game_id')->relationship('videoGame', 'name'),
                Forms\Components\Select::make('provider_id')->relationship('provider', 'name'),
                Forms\Components\TextInput::make('slug')
                    ->maxLength(255),

                Forms\Components\Toggle::make('is_published')
                    ->required(),

                Tabs::make('Heading')
                    ->tabs([
                        Tabs\Tab::make('Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('start_at'),
                                Forms\Components\DatePicker::make('end_at'),
                                Forms\Components\Select::make('tier')
                                    ->options(Tier::collection()),
                                Forms\Components\Toggle::make('is_online')
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Provider Information')
                            ->schema([
                                Forms\Components\TextInput::make('provider_event_name')->disabled(),
                                Forms\Components\DatePicker::make('provider_event_start_at')->disabled(),
                                Forms\Components\DatePicker::make('provider_event_end_at')->disabled(),
                                Forms\Components\Select::make('provider_event_tier')
                                    ->options(Tier::collection())
                                    ->disabled(),
                                Forms\Components\Toggle::make('provider_event_is_online')->disabled(),
                                Forms\Components\TextInput::make('provider_event_id')->disabled(),
                            ]),
                    ]),
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
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('provider')
                    ->relationship('provider', 'name'),
                Tables\Filters\SelectFilter::make('tier')
                    ->options(Tier::collection()),
                Tables\Filters\Filter::make('priority')
                    ->toggle()
                    ->query(fn(Builder $query) => $query->priority())
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ])
            ->defaultSort('tier');
    }

    public static function getRelations(): array
    {
        return [
            StreamersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
