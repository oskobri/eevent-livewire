<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
