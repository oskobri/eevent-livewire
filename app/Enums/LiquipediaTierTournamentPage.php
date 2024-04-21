<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Collection;

enum LiquipediaTierTournamentPage: string implements HasLabel
{
    use Enumerable;

    case STier = 'S-Tier_Tournaments';
    case ATier = 'A-Tier_Tournaments';
    case BTier = 'B-Tier_Tournaments';
    case Tier1 = 'Tier_1_Tournaments';
    case Premier = 'Premier_Tournaments';
    case Major = 'Major_Tournaments';

    public function getLabel(): ?string
    {
        return $this->name;
    }


    /**
     * Returns pages to fetch by video game
     *
     * @param int $VideoGameId
     * @return Collection<LiquipediaTierTournamentPage>
     */
    public static function getPages(int $VideoGameId): Collection
    {
        return collect(match ($VideoGameId) {
            5, 6, 7, 8, 11, 12, 13, 14 => [
                LiquipediaTierTournamentPage::STier,
                LiquipediaTierTournamentPage::ATier,
                LiquipediaTierTournamentPage::BTier,
            ],
            9 => [
                LiquipediaTierTournamentPage::Tier1
            ],
            10 => [
                LiquipediaTierTournamentPage::Premier,
                LiquipediaTierTournamentPage::Major
            ],
            default => []
        });
    }
}
