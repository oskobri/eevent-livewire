<?php

namespace App\Services\Crawler\Liquipedia;

use App\Enums\LiquipediaUpcomingMatchesPage;
use App\Http\Integrations\Liquipedia\Requests\GetPageRequest;
use App\Models\VideoGame;
use Illuminate\Support\Collection;

class UpcomingMatchesScraper
{
    public function __construct(public string $providerVideoGameId, public VideoGame $videoGame) { }

    public function getMatches(): Collection
    {
        $request = new GetPageRequest(
            $this->providerVideoGameId,
            LiquipediaUpcomingMatchesPage::getPage($this->videoGame->id)->value
        );

        if(!($html = $request->getPageHTML())) {
            return collect();
        }

        return (new ParseUpcomingMatches($html, $this->videoGame))();
    }
}
