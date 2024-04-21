<?php

namespace App\Jobs\StartGG;

use App\Enums\ProviderEnum;
use App\Enums\Tier;
use App\Models\Event;
use App\Models\Provider;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ImportEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        protected Provider $provider,
        protected array    $tournamentData,
        protected array    $eventData
    )
    {
    }

    public function handle()
    {
        $event = $this->importEvent();

        if ($event) {
            ImportEventStreams::dispatch($event, collect($this->tournamentData['streams']));
        }
    }

    protected function importEvent(): null|Event|Model
    {
        $videoGame = $this->provider
            ->videoGames()
            ->firstWhere('provider_video_game_id', $this->eventData['videogame']['id']);

        // We don't want to retrieve video game that are not in eevent
        if (!$videoGame) {
            return null;
        }

        $eventData = [
            'name' => $eventName = ($this->tournamentData['name'] . ' ' . $this->eventData['name']),
            'start_at' => $startAt = ($this->eventData['startAt'] ? Carbon::createFromTimestamp($this->eventData['startAt'], $this->tournamentData['timezone']) : null),
            'tier' => $tier = Tier::match(ProviderEnum::StartGG, $this->eventData['competitionTier']),
            'is_online' => $this->eventData['isOnline'],
        ];

        $providerEventData = [
            'provider_event_name' => $eventName,
            'provider_event_start_at' => $startAt,
            'provider_event_tier' => $tier,
            'provider_event_is_online' => $this->eventData['isOnline'],
            'provider_event_url' => config('services.start_gg.host_website') . $this->eventData['slug']
        ];

        $event = $this->provider
            ->events()
            ->withTrashed()
            ->firstOrCreate([
                'provider_event_id' => $this->eventData['id'],
                'video_game_id' => $videoGame->id
            ], array_merge($eventData, $providerEventData));

        // If event was already created, only update provider fields
        if (!$event->wasRecentlyCreated) {
            $event->update($providerEventData);
            // TODO Listener event updated, send notification if event is already published and there is changes on one of these fields
        }

        return $event;
    }
}
