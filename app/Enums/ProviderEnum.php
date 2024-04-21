<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProviderEnum: string implements HasLabel
{
    use Enumerable;

    case StartGG = 'start_gg';
    case Liquipedia = 'liquipedia';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
