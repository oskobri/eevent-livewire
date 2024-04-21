<?php

namespace App\Filament\Resources\LanguageResource\Pages;

use App\Filament\Resources\LanguageResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLanguages extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
