<?php

namespace App\Console\Commands\Liquipedia;

use App\Enums\ProviderEnum;
use App\Jobs\Liquipedia\ImportEvent;
use App\Models\Provider;
use Illuminate\Console\Command;

class ImportEventCommand extends Command
{
    protected $signature = 'import:liquipedia-page-events {videogame} {page}';

    protected $description = 'Import events from a liquipedia specific page';

    public function handle()
    {
        $provider = Provider::query()->firstWhere('key', ProviderEnum::Liquipedia);
        $videoGame = $provider->videoGames()->wherePivot('video_game_id', $this->argument('videogame'))->first();

        ImportEvent::dispatch(
            $provider,
            $videoGame,
            $this->argument('page')
        );
    }
}
