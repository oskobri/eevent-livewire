<?php

namespace App\Filament\Resources\VideoGameResource\Pages;

use App\Filament\Resources\VideoGameResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVideoGames extends ListRecords
{
    protected static string $resource = VideoGameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
