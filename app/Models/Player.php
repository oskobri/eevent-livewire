<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Player extends Model
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

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function leftOpponent(): MorphOne
    {
        return $this->morphOne(MatchModel::class, 'left_opponent');
    }

    public function rightOpponent(): MorphOne
    {
        return $this->morphOne(MatchModel::class, 'right_opponent');
    }
}
