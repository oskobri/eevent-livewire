<?php

namespace App\Models;

use App\Enums\Tier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
        'tier' => Tier::class,
        'is_online' => 'bool',
        'provider_event_start_at' => 'date',
        'provider_event_end_at' => 'date',
        'provider_event_tier' => Tier::class,
        'provider_event_is_online' => 'bool',
        'is_published' => 'bool',
    ];

    public function videoGame(): BelongsTo
    {
        return $this->belongsTo(VideoGame::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function streamers(): BelongsToMany
    {
        return $this->belongsToMany(Streamer::class, 'streams');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchModel::class);
    }

    public function scopePriority(Builder $query): Builder
    {
        return $query
            ->whereIn('tier', [Tier::S, Tier::A])
            ->where(fn(Builder $query) => $query
                ->whereNull('start_at')
                ->orWhere('start_at', '<', now()->addMonth())
            )
            ->whereHas('streamers')
            ->where('is_published', false);
    }

    public static function getListWithMatches($filter = [])
    {
        return static::query()
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
            ->when($filter['video_game_id'], fn($query) => $query->where('video_game_id', $filter['video_game_id']))
            /*->where('is_published', true)*/
            ->with([
                'streamers.language',
                'videoGame',
                'matches',
                'matches.videoGame',
                'matches.leftOpponent.country',
                'matches.rightOpponent.country',
                'matches.leftOpponent' => fn(MorphTo $query) => $query
                    ->morphWith([
                        Team::class => 'players.country'
                    ]),
                'matches.rightOpponent' => fn(MorphTo $query) => $query
                    ->morphWith([
                        Team::class => 'players.country'
                    ]),
                'matches.event:id,name',
                'matches.event.streamers',
            ])
            ->orderBy('video_game_id')
            ->get();
    }
}
