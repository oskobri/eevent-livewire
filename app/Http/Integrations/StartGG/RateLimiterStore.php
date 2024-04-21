<?php

namespace App\Http\Integrations\StartGG;

use Illuminate\Support\Facades\Cache;
use Spatie\GuzzleRateLimiterMiddleware\Store;

class RateLimiterStore implements Store
{
    public function get(): array
    {
        return Cache::get('start-gg-rate-limiter', []);
    }

    public function push(int $timestamp, int $limit)
    {
        Cache::put('start-gg-rate-limiter', array_merge($this->get(), [$timestamp]));
    }
}
