<?php

namespace App\Jobs\Liquipedia;

use App\Enums\ProviderEnum;
use App\Models\Provider;
use App\Models\VideoGame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportUpcomingMatches implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?Provider $provider;

    /**
     * @param $videoGame
     * @param bool $deepScraping if true, will scrap team information + players
     */
    public function __construct(private $videoGame = null, private readonly bool $deepScraping = true)
    {
        $this->provider = Provider::query()->firstWhere('key', ProviderEnum::Liquipedia);
    }

    public function handle()
    {
        if($this->videoGame) {
            $videoGame = $this->provider->videoGames()->find($this->videoGame->id);
            ImportUpcomingMatchesForVideoGame::dispatch($this->provider, $videoGame, $this->deepScraping);
            return;
        }

        $this->provider->videoGames()->each(function (VideoGame $videoGame) {
            ImportUpcomingMatchesForVideoGame::dispatch($this->provider, $videoGame, $this->deepScraping);
        });
    }
}
