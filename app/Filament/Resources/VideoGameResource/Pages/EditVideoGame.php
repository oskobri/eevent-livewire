<?php

namespace App\Filament\Resources\VideoGameResource\Pages;

use App\Filament\Resources\VideoGameResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVideoGame extends EditRecord
{
    protected static string $resource = VideoGameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
