<?php

namespace App\Http\Integrations\Liquipedia\Requests;

use App\Http\Integrations\Liquipedia\LiquipediaConnector;
use Illuminate\Support\Facades\Log;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Request\HasConnector;

class GetPageRequest extends Request
{
    use HasConnector;

    protected string $connector = LiquipediaConnector::class;

    protected Method $method = Method::GET;

    public function __construct(protected readonly string $providerVideoGameId, protected readonly string $pageName) { }

    public function resolveEndpoint(): string
    {
        return "{$this->providerVideoGameId}/api.php";
    }

    public function getPageHTML(): ?string
    {
        $response = $this->getPage();

        return $response ? $response['html']: null;
    }

    public function getPageId(): ?string
    {
        $response = $this->getPage();

        return $response ? $response['id']: null;
    }

    public function getPage(): ?array
    {
        $response = $this->send();

        if ($response->failed()) {
            Log::error(
                "Liquipedia (GetPageRequest). Request failed for video game $this->providerVideoGameId and page $this->pageName",
                ['error' => $response->body()]
            );
            return null;
        }

        $html = $response->json('parse.text.*');
        $id = $response->json('parse.pageid');

        if (!$html) {
            Log::error("Liquipedia (GetPageRequest). Missing page $this->pageName for video game: $this->providerVideoGameId", [
                'content' => $response->json('parse')
            ]);
            return null;
        }

        return compact('id', 'html');
    }

    protected function defaultQuery(): array
    {
        return [
            'action' => 'parse',
            'format' => 'json',
            'page' => $this->pageName
        ];
    }
}
