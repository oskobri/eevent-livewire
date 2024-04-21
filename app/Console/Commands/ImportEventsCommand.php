<?php

namespace App\Console\Commands;

use App\Jobs\StartGG\ImportEvents as StartGGImportEvents;
use App\Jobs\Liquipedia\ImportEvents as LiquipediaImportEvents;
use Illuminate\Console\Command;

class ImportEventsCommand extends Command
{
    protected $signature = 'import:events';

    protected $description = 'Import events from different providers.';

    public function handle()
    {
        //StartGGImportEvents::dispatch();
        LiquipediaImportEvents::dispatch();
    }
}
