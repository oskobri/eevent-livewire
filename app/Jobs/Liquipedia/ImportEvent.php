<?php

namespace App\Jobs\Liquipedia;

use App\Enums\ProviderEnum;
use App\Enums\Tier;
use App\Models\Event;
use App\Models\Provider;
use App\Models\VideoGame;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ImportEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(protected Provider $provider, protected VideoGame $videoGame, protected array $tournamentData) { }

    public function handle()
    {
        if (empty($this->tournamentData)) {
            return;
        }

        $event = $this->importEvent($this->tournamentData['tournament']);

        if ($this->tournamentData['streams']->isEmpty()) {
            return;
        }

        ImportEventStreams::dispatch($event, $this->tournamentData['streams']);
    }

    public function importEvent(array $tournamentData): Event
    {
        $eventData = [
            'name' => $tournamentData['name'],
            'start_at' => $startAt = ($tournamentData['start_at'] ? Carbon::createFromFormat('Y-m-d', $tournamentData['start_at']) : null),
            'end_at' => $endAt = ($tournamentData['end_at'] ? Carbon::createFromFormat('Y-m-d', $tournamentData['end_at']) : null),
            'tier' => $tier = Tier::match(ProviderEnum::Liquipedia, $tournamentData['tier']),
            'is_online' => $isOnline = ($tournamentData['is_online'] ?? false),
        ];

        $providerEventData = [
            'provider_event_name' => $tournamentData['name'],
            'provider_event_start_at' => $startAt,
            'provider_event_end_at' => $endAt,
            'provider_event_tier' => $tier,
            'provider_event_is_online' => $isOnline,
            'provider_event_url' => config('services.liquipedia.host_website') . '/' . $this->videoGame->pivot->provider_video_game_id . '/' . $tournamentData['page_name']
        ];

        $event = $this->provider
            ->events()
            ->withTrashed()
            ->firstOrCreate([
                'provider_event_id' => $tournamentData['provider_event_id'],
                'video_game_id' => $this->videoGame->id
            ], array_merge($eventData, $providerEventData));


        // If event was already created, only update provider fields
        if (!$event->wasRecentlyCreated) {
            $event->update($providerEventData);
            // TODO Listener event updated, send notification if event is already published and there is changes on one of these fields
        }

        /** @var Event */
        return $event;
    }
}
