<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;use Illuminate\Database\Eloquent\Relations\HasMany;use Illuminate\Database\Eloquent\Relations\MorphOne;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function videoGame(): BelongsTo
    {
        return $this->belongsTo(VideoGame::class);
    }

    public function leftOpponent(): MorphOne
    {
        return $this->morphOne(MatchModel::class, 'left_opponent');
    }

    public function rightOpponent(): MorphOne
    {
        return $this->morphOne(MatchModel::class, 'right_opponent');
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function scopeWhereName(Builder $query, $name): Builder
    {
        return $query->where('name', 'like', "%$name%");
    }
}
