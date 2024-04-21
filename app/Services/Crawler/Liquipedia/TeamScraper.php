<?php

namespace App\Services\Crawler\Liquipedia;

use App\Http\Integrations\Liquipedia\Requests\GetPageRequest;

class TeamScraper
{
    public function __construct(public string $providerVideoGameId, public string $teamPageName) { }

    public function getTeamData(): ?array
    {
        $request = new GetPageRequest($this->providerVideoGameId, $this->teamPageName);

        if(!($page = $request->getPage())) {
            return null;
        }

        $teamData = (new ParseInfoBox($page['html']))();

        $playersData = (new ParseTeamPlayers($page['html']))();

        return [
            'team' => $teamData,
            'players' => $playersData
        ];
    }
}
