<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VideoGame extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(Provider::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchModel::class);
    }

    public static function getListWithMatches($videoGameId = null, array $filter = []): Collection
    {
        return static::query()
            ->with([
                'events' => fn($query) => $query
                    ->withWhereHas('matches', fn($query) => $query
                        ->whereDate('time', $filter['date'])
                        ->when($filter['opponentCountry'], fn($query, $countryId) => $query
                            ->fromCountry($filter['opponentCountry'], $filter['withPlayersCountry'])
                        )
                        ->when($filter['opponent'], fn($query) => $query
                            ->where(fn($query) => $query
                                ->whereHas('leftOpponent', fn($query) => $query->whereName($filter['opponent']))
                                ->orWhereHas('rightOpponent', fn($query) => $query->whereName($filter['opponent']))
                            )
                        )
                        ->orderBy('time')
                    )
                    ->when($filter['tier'], fn($query) => $query->where('tier', $filter['tier']))
                /*->where('is_published', true)*/,
                'events.streamers.language',
                'events.matches',
                'events.matches.videoGame',
                'events.matches.leftOpponent.country',
                'events.matches.rightOpponent.country',
                'events.matches.leftOpponent' => fn(MorphTo $query) => $query
                    ->morphWith([
                        Team::class => 'players.country'
                    ]),
                'events.matches.rightOpponent' => fn(MorphTo $query) => $query
                    ->morphWith([
                        Team::class => 'players.country'
                    ]),
                'events.matches.event:id,name',
                'events.matches.event.streamers',
            ])
            ->when($videoGameId, fn($query) => $query->where('id', $videoGameId))
            ->get();
    }
}
