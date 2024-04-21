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

    public static function getList(array $filter): Collection
    {
        return static::query()
            ->with([
                'events' => fn($query) => $query
                    ->whereHas('matches', fn($query) => $query
                        ->today()
                        ->when($filter['opponentCountry'], fn($query, $countryId) => $query
                            ->fromCountry($filter['opponentCountry'], $filter['withPlayersCountry'])
                        )
                    )
                    ->withCount(['matches' => fn($query) => $query
                        ->today()
                        ->when($filter['opponentCountry'], fn($query, $countryId) => $query
                            ->fromCountry($filter['opponentCountry'], $filter['withPlayersCountry'])
                        )
                    ])
                /*->where('is_published', true)*/,
                'events.streamers.language',
                'events.matches' => fn($query) => $query
                    ->today()
                    ->when($filter['opponentCountry'], fn($query, $countryId) => $query
                        ->fromCountry($filter['opponentCountry'], $filter['withPlayersCountry'])
                    )
                    ->orderBy('matches.time'),
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
            ->withCount(['matches' => fn($query) => $query
                ->today()
                ->when($filter['opponentCountry'], fn($query, $countryId) => $query
                    ->fromCountry($filter['opponentCountry'], $filter['withPlayersCountry'])
                )
            ])
            ->whereHas('matches', fn($query) => $query
                ->today()
                ->when($filter['opponentCountry'], fn($query, $countryId) => $query
                    ->fromCountry($filter['opponentCountry'], $filter['withPlayersCountry'])
                )
            )
            ->get();
    }
}
