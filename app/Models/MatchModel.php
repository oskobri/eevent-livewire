<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Unfortunately Match is a reserved keyword, so here is MatchModel :(
 */
class MatchModel extends Model
{
    protected $table = 'matches';

    protected $guarded = [];

    protected $casts = [
        'time' => 'datetime'
    ];

    public function videoGame(): BelongsTo
    {
        return $this->belongsTo(VideoGame::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function leftOpponent(): MorphTo
    {
        return $this->morphTo();
    }

    public function rightOpponent(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query
            ->where('time', '>=', now()->startOfDay())
            ->where('time', '<', now()->endOfDay());
    }

    /**
     * @param Builder $query
     * @param int $countryId
     * @param bool $throughPlayers // If true, will get matches if players of a team has countryId even if team has not
     * @return Builder
     */
    public function scopeFromCountry(Builder $query, int $countryId, bool $throughPlayers = false): Builder
    {
        return $query
            ->where(function ($query) use ($countryId, $throughPlayers) {
                $query
                    ->whereRelation('leftOpponent', 'country_id', $countryId)
                    ->orWhereRelation('rightOpponent', 'country_id', $countryId);

                if (!$throughPlayers) {
                    return $query;
                }

                return $query
                    ->orWhereHasMorph('leftOpponent', [Team::class], fn(Builder $query) => $query->whereRelation('players', 'country_id', $countryId))
                    ->orWhereHasMorph('rightOpponent', [Team::class], fn(Builder $query) => $query->whereRelation('players', 'country_id', $countryId));
            });
    }
}
