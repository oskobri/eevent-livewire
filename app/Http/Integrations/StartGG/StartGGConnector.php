<?php

namespace App\Http\Integrations\StartGG;

use GuzzleHttp\HandlerStack;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Spatie\GuzzleRateLimiterMiddleware\RateLimiterMiddleware;

class StartGGConnector extends Connector
{
    protected static int $maxRequestsPerMinute = 80;

    public function resolveBaseUrl(): string
    {
        return config('services.start_gg.host_api');
    }

    protected function defaultAuth(): ?Authenticator
    {
        return new TokenAuthenticator(config('services.start_gg.key'));
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function defaultConfig(): array
    {
        $stack = HandlerStack::create();
        $stack->push(RateLimiterMiddleware::perMinute(static::$maxRequestsPerMinute, new RateLimiterStore));

        return [
            'handler' => $stack
        ];
    }
}
