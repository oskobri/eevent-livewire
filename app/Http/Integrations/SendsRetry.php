<?php

namespace App\Http\Integrations;

use Saloon\Contracts\MockClient;

/**
 * Missing in the Saloon Request so added it temporary
 */
trait SendsRetry
{
    /**
     * Send a synchronous request and retry if it fails
     *
     * @param int $maxAttempts
     * @param int $interval
     * @param callable|null $handleRetry
     * @param bool $throw
     * @param MockClient|null $mockClient
     * @return mixed
     */
    public function sendAndRetry(int $maxAttempts, int $interval = 0, callable $handleRetry = null, bool $throw = true, MockClient $mockClient = null): mixed
    {
        return $this->connector()->sendAndRetry($this, $maxAttempts, $interval, $handleRetry, $throw, $mockClient);
    }
}
