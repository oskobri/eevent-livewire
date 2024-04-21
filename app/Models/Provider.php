<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function videoGames(): BelongsToMany
    {
        return $this->belongsToMany(VideoGame::class)
            ->withPivot([
                'provider_video_game_id'
            ]);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
