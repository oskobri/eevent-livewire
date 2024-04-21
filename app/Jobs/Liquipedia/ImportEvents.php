<?php

namespace App\Jobs\Liquipedia;

use App\Enums\LiquipediaTierTournamentPage;
use App\Enums\ProviderEnum;
use App\Http\Integrations\Liquipedia\Requests\GetPageRequest;
use App\Models\Provider;
use App\Models\VideoGame;
use App\Services\Crawler\Liquipedia\ParseTournaments;
use App\Services\Crawler\Liquipedia\TournamentScraper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ImportEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?Provider $provider;

    public function __construct()
    {
        $this->provider = Provider::query()->firstWhere('key', ProviderEnum::Liquipedia);
    }

    public function handle()
    {
        $this->provider->videoGames()->each(function (VideoGame $videoGame) {
            // S-Tier, A-Tier, ....
            LiquipediaTierTournamentPage::getPages($videoGame->id)
                ->each(function (LiquipediaTierTournamentPage $tierTournamentPageName) use ($videoGame) {
                    $this->importTournaments($videoGame, $tierTournamentPageName);
                });
        });
    }

    protected function importTournaments(VideoGame $videoGame, $tierTournamentPageName)
    {
        $this
            ->getTournamentsPageNames($videoGame, $tierTournamentPageName)
            ->each(function ($tournamentPageName) use ($videoGame) {
                $tournamentData = (new TournamentScraper($videoGame->pivot->provider_video_game_id, $tournamentPageName))->getTournamentData();

                ImportEvent::dispatch($this->provider, $videoGame, $tournamentData);
            });
    }

    /**
     * Get all current or upcoming tournament page names to be fetched and imported after
     * @param VideoGame $videoGame
     * @param LiquipediaTierTournamentPage $page
     * @return Collection
     */
    protected function getTournamentsPageNames(VideoGame $videoGame, LiquipediaTierTournamentPage $page): Collection
    {
        $request = new GetPageRequest($videoGame->pivot->provider_video_game_id, $page->value);

        if (!($html = $request->getPageHTML())) {
            return collect();
        }

        return (new ParseTournaments($html))();
    }
}
