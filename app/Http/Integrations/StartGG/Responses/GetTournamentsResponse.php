<?php

namespace App\Http\Integrations\StartGG\Responses;

use Illuminate\Support\Collection;
use JsonException;
use Saloon\Http\Response;

class GetTournamentsResponse extends Response implements PaginatedResponse
{
    public function items(): Collection
    {
        try {
            return $this->collect('data.tournaments.nodes');
        } catch (JsonException) {
            return collect();
        }
    }

    public function hasNextPage(): bool
    {
        try {
            $pageInfo = $this->json('data.tournaments.pageInfo');
        } catch (JsonException) {
            return false;
        }

        return $pageInfo['totalPages'] > $pageInfo['page'];
    }
}
