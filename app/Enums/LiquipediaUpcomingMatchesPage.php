<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LiquipediaUpcomingMatchesPage: string implements HasLabel
{
    use Enumerable;

    case Upcoming = 'Liquipedia:Matches';
    case UpcomingAndOngoing = 'Liquipedia:Upcoming_and_ongoing_matches';

    public function getLabel(): ?string
    {
        return $this->name;
    }


    /**
     * Returns upcoming matches page to fetch by video game
     *
     * @param int $VideoGameId
     * @return LiquipediaUpcomingMatchesPage|null
     */
    public static function getPage(int $VideoGameId): ?LiquipediaUpcomingMatchesPage
    {
        return match ($VideoGameId) {
            5, 7, 8, 11 => LiquipediaUpcomingMatchesPage::Upcoming,
            6, 9, 10, 12, 13, 14 => LiquipediaUpcomingMatchesPage::UpcomingAndOngoing,
            default => null
        };
    }
}
