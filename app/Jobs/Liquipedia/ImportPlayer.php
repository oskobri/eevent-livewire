<?php

namespace App\Jobs\Liquipedia;

use App\Models\Player;
use App\Models\Team;
use App\Models\VideoGame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPlayer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct( protected VideoGame $videoGame, protected Team $team, protected array $player) { }

    public function handle()
    {
        Player::query()->firstOrCreate([
            'nickname' => $this->player['nickname'],
            'team_id' => $this->team->id,
            'video_game_id' => $this->videoGame->id
        ], [
            'provider_url' => $this->player['provider_url'],
            'country_id' => $this->player['country_id'],
        ]);
    }
}
