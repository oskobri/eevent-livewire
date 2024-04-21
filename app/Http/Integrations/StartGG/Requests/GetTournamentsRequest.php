<?php

namespace App\Http\Integrations\StartGG\Requests;

use App\Http\Integrations\SendsRetry;
use App\Http\Integrations\StartGG\HasPagination;
use App\Http\Integrations\StartGG\Responses\GetTournamentsResponse;
use App\Http\Integrations\StartGG\StartGGConnector;
use Illuminate\Support\Collection;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Saloon\Traits\Request\HasConnector;

class GetTournamentsRequest extends Request implements HasBody
{
    use HasConnector;
    use HasJsonBody;
    use HasPagination;
    use SendsRetry;

    protected string $connector = StartGGConnector::class;

    protected ?string $response = GetTournamentsResponse::class;

    protected Method $method = Method::POST;

    /**
     * @param Collection<int> $videoGameIds
     */
    public function __construct(protected readonly Collection $videoGameIds) { }

    public function resolveEndpoint(): string
    {
        return '';
    }

    protected function defaultBody(): array
    {
        $query = <<<GQL
        {
            tournaments(query: {
                perPage: $this->perPage
                page: $this->page
                sortBy: "startAt asc"
                filter: {
                    isFeatured: true
                    published: true
                    upcoming: true
                    videogameIds: [{$this->videoGameIds->implode(',')}]
                }
            }) {
                nodes {
                    id
                    events(filter: {
                        published: true
                    }) {
                        id
                        competitionTier
                        isOnline
                        name
                        slug
                        startAt
                        videogame {
                            id
                        }
                    }
                    name
                    streams {
                        id
                        followerCount
                        isOnline
                        streamGame
                        streamName
                        streamSource
                    }
                    timezone
                }
                pageInfo {
                    totalPages
                    page
                }
            }
        }
        GQL;

        return [
            'query' => $query
        ];
    }
}
