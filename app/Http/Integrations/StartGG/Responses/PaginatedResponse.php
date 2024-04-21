<?php

namespace App\Http\Integrations\StartGG\Responses;

use Illuminate\Support\Collection;
use JsonException;

interface PaginatedResponse
{
    public function items(): Collection;

    public function hasNextPage(): bool;
}
