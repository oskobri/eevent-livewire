<?php

namespace App\Http\Integrations\Liquipedia;

use Illuminate\Support\Facades\Cache;
use Saloon\Http\Connector;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Limit;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;

class LiquipediaConnector extends Connector
{
    use HasRateLimits;

    public function resolveBaseUrl(): string
    {
        return config('services.liquipedia.host_api');
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept-Encoding' => 'gzip',
            'User-Agent' => 'test (bobrembs@gmail.com)'
        ];
    }

    protected function resolveLimits(): array
    {
        return [
            // Parse action is limited to 1 request every 30 seconds, others is 1 request every 2 seconds
            Limit::allow(1)->everySeconds(30)->sleep()
        ];
    }

    protected function resolveRateLimitStore(): RateLimitStore
    {
        return new LaravelCacheStore(Cache::store('file'));
    }
}
