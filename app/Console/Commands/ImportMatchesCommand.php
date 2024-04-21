<?php

namespace App\Console\Commands;

use App\Jobs\Liquipedia\ImportUpcomingMatches as LiquipediaImportUpcomingMatches;
use Illuminate\Console\Command;

class ImportMatchesCommand extends Command
{
    protected $signature = 'import:matches {--onlyMatches}';

    protected $description = 'Import upcoming matches';

    public function handle()
    {
        LiquipediaImportUpcomingMatches::dispatch(null, !$this->option('onlyMatches'));
    }
}
