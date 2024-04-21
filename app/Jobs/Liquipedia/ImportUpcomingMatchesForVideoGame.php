<?php

namespace App\Jobs\Liquipedia;

use App\Models\Provider;
use App\Models\Team;
use App\Models\VideoGame;
use App\Services\Crawler\Liquipedia\TeamScraper;
use App\Services\Crawler\Liquipedia\UpcomingMatchesScraper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ImportUpcomingMatchesForVideoGame implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @param Provider $provider
     * @param VideoGame $videoGame
     * @param bool $deepScraping if true, will scrap team page information + players
     */
    public function __construct(private readonly Provider $provider, private readonly VideoGame $videoGame, private readonly bool $deepScraping = true) { }

    public function handle()
    {
        (new UpcomingMatchesScraper($this->videoGame->pivot->provider_video_game_id, $this->videoGame))
            ->getMatches()
            ->each(function ($match) {
                if ($this->deepScraping) {
                    // Complete teams information by scraping team page
                    if ($match['left']['opponent_type'] === 'team') {
                        $match['left']['additional_information'] = $this->scrapTeamData($match['left']);
                    }
                    if ($match['right']['opponent_type'] === 'team') {
                        $match['right']['additional_information'] = $this->scrapTeamData($match['right']);
                    }
                }

                ImportMatch::dispatch($this->provider, $this->videoGame, $match);
            });
    }

    private function scrapTeamData($matchTeam): ?array
    {
        // Can be TBD so not defined
        if (!isset($matchTeam['url'])) {
            return null;
        }

        return (new TeamScraper(
            $this->videoGame->pivot->provider_video_game_id,
            formatPageName($this->videoGame, $matchTeam['url'])
        ))->getTeamData();
    }

}
