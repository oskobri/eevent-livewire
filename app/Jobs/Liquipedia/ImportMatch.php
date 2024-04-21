<?php

namespace App\Jobs\Liquipedia;

use App\Models\MatchModel;
use App\Models\Player;
use App\Models\Provider;
use App\Models\Team;
use App\Models\VideoGame;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportMatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Provider $provider, protected VideoGame $videoGame, protected array $match) { }

    public function handle()
    {
        $leftOpponent = $this->getOrCreateOpponent('left');
        $rightOpponent = $this->getOrCreateOpponent('right');

        MatchModel::query()->firstOrCreate([
            'event_id' => $this->match['event_id'],
            'video_game_id' => $this->videoGame->id,
            'left_opponent_type' => $leftOpponent->getMorphClass(),
            'left_opponent_id' => $leftOpponent->id,
            'right_opponent_type' => $rightOpponent->getMorphClass(),
            'right_opponent_id' => $rightOpponent->id,
            'time' => $this->match['date']
        ]);
    }

    protected function getOrCreateOpponent(string $side): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {
        if ($this->match[$side]['opponent_type'] === 'player') {
            return Player::query()->firstOrCreate([
                'nickname' => $this->match[$side]['name'],
                'video_game_id' => $this->videoGame->id
            ], [
                'provider_url' => $this->match[$side]['url'],
                'country_id' => $this->match[$side]['country_id'],
            ]);
        }

        $team = Team::query()->firstOrCreate([
            'name' => $this->match[$side]['name'],
            'video_game_id' => $this->videoGame->id
        ], [
            'short_name' => $this->match[$side]['short'],
            'provider_url' => $this->match[$side]['url'],
            'icon' => $this->match[$side]['icon'],
            'country_id' => $this->match[$side]['additional_information']['team']['country_id'] ?? null
        ]);

        if (isset($this->match[$side]['additional_information']) && $this->match[$side]['additional_information']) {
            $this->match[$side]['additional_information']['players']->each(function (array $playerData) use ($team) {
                ImportPlayer::dispatch($this->videoGame, $team, $playerData);
            });
        }

        return $team;
    }
}
