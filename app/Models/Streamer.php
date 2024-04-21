<?php

namespace App\Models;

use App\Enums\StreamSource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Streamer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'source' => StreamSource::class,
        'is_live' => 'bool'
    ];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'streams');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
