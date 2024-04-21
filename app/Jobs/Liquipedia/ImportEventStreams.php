<?php

namespace App\Jobs\Liquipedia;

use App\Models\Event;
use App\Models\Streamer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ImportEventStreams implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Event $event, protected Collection $streamsData) { }

    public function handle()
    {
        $this->streamsData->each(fn (array $streamData) => $this->importStream($streamData));
    }

    protected function importStream(array $streamData)
    {
        /** @var $streamer Streamer */
        $streamer = Streamer::query()->firstOrCreate([
            'source' => $streamData['source'],
            'source_id' => $streamData['source_id'],
            'url' => $streamData['url'],
            'language_id' => $streamData['language_id']
        ]);

        $streamer->events()->syncWithoutDetaching($this->event);
    }
}
