<?php

namespace App\Jobs\StartGG;

use App\Enums\Tier;
use App\Http\Integrations\StartGG\Requests\GetTournamentsRequest;
use App\Http\Integrations\StartGG\Responses\GetTournamentsResponse;
use App\Models\Event;
use App\Enums\ProviderEnum;
use App\Models\Provider;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?Provider $provider;

    private int $page;

    public function __construct()
    {
        $this->provider = Provider::query()->firstWhere('key', ProviderEnum::StartGG);
        $this->page = 1;
    }

    public function handle()
    {

        $this->provider->videoGames()
            ->chunk(10, function (Collection $videoGamesChunk) {
                $this->importTournaments($videoGamesChunk);

                // Reset page after each chunk
                $this->page = 1;
            });
    }

    protected function importTournaments(Collection $videoGamesChunk): void
    {
        do {
            $response = $this->getTournamentsResponse($videoGamesChunk->pluck('pivot.provider_video_game_id'));

            if ($response->failed()) {
                Log::error('Start.gg: Import events (getTournaments) failed', ['error' => $response->body()]);
                return;
            }

            $response->items()->each(fn ($tournamentData) => $this->importEvents($tournamentData));
        } while ($response->hasNextPage());
    }

    protected function importEvents($tournamentData): void
    {
        collect($tournamentData['events'])->each(function ($eventData) use ($tournamentData) {
            ImportEvent::dispatch($this->provider, $tournamentData, $eventData);
        });
    }


    protected function getTournamentsResponse($videoGameIds): GetTournamentsResponse
    {
        /** @var GetTournamentsResponse */
        return (new GetTournamentsRequest($videoGameIds))
            ->setPage($this->page++)
            ->setPerPage(10)
            ->sendAndRetry(3, 100, null, false);
    }
}
