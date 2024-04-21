<?php

namespace App\Console\Commands\Liquipedia;

use App\Jobs\Liquipedia\ImportUpcomingMatches as LiquipediaImportUpcomingMatches;
use App\Models\VideoGame;
use Illuminate\Console\Command;

class ImportMatchesForVideoGameCommand extends Command
{
    protected $signature = 'import:liquipedia-matches {video_game?} {--onlyMatches}';

    protected $description = 'Import upcoming matches';

    public function handle()
    {
        $videoGame = VideoGame::find($this->argument('video_game'));

        LiquipediaImportUpcomingMatches::dispatch($videoGame, !$this->option('onlyMatches'));
    }
}
