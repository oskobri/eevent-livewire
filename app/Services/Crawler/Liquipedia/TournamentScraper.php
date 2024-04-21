<?php

namespace App\Services\Crawler\Liquipedia;

use App\Http\Integrations\Liquipedia\Requests\GetPageRequest;

class TournamentScraper
{
    public function __construct(public string $providerVideoGameId, public string $tournamentPageName) { }

    public function getTournamentData(): array
    {
        $request = new GetPageRequest($this->providerVideoGameId, $this->tournamentPageName);

        if (!($page = $request->getPage())) {
            return [];
        }

        $tournamentData = (new ParseInfoBox($page['html']))();
        if (!$tournamentData || !isset($tournamentData['start_at'])) {
            return [];
        }

        $tournamentData['provider_event_id'] = $page['id'];
        $tournamentData['page_name'] = $this->tournamentPageName;

        $streams = (new ParseStreams($page['html']))();

        return [
            'tournament' => $tournamentData,
            'streams' => $streams
        ];
    }
}
