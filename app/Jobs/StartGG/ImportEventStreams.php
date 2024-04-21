<?php

namespace App\Jobs\StartGG;

use App\Enums\ProviderEnum;
use App\Enums\StreamSource;
use App\Models\Event;
use App\Models\Streamer;
use App\Models\VideoGame;
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
        $streamer = $this->importStreamer($streamData);

        if (!$this->isStreamGameSameAsEventGame($streamData)) {
            return;
        }

        $streamer->events()->attach($this->event);
    }

    protected function importStreamer(array $streamData): Streamer
    {
        /** @var Streamer */
        return Streamer::query()->firstOrCreate([
            'source' => $source = StreamSource::match(ProviderEnum::StartGG, $streamData['streamSource']),
            'source_id' => $streamData['streamName'],
            'url' => $source->getStreamerUrl($streamData['streamName']),
            'followers_count' => $streamData['followerCount'] // Surely wrong
        ]);
    }

    /**
     * Start gg stream are linked to tournament, not event
     * So it's possible that the stream's video game is not the same as event's video game, in this case we don't add it
     * Sometimes we don't have stream's video game, so we suppose it's the same
     * @param array $streamData
     * @return bool
     */
    protected function isStreamGameSameAsEventGame(array $streamData): bool
    {
        if ($streamData['streamGame'] === null) {
            return true;
        }

        // We only have stream game name, not id, so we compare event's video game name with stream's video game name
        return VideoGame::query()
            ->where('id', $this->event->video_game_id)
            ->where('name', $streamData['streamGame'])
            ->exists();
    }
}
