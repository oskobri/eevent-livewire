<?php

namespace App\Filament\Resources\VideoGameResource\Pages;

use App\Filament\Resources\VideoGameResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVideoGame extends CreateRecord
{
    protected static string $resource = VideoGameResource::class;
}
