<?php

namespace App\Services\Crawler\Liquipedia;

use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

readonly class ParseTeamPlayers
{
    use ParseCountriesByFlag;

    private Collection $players;

    public function __construct(public string $html)
    {
        $this->players = collect();
    }

    public function __invoke(): Collection
    {
        $crawler = new Crawler($this->html);

        $rosterNode = $crawler->filterXpath('//table[contains(concat(" ", normalize-space(@class), " "), " roster-card ")]')->first();

        if (!$rosterNode->count()) {
            return collect();
        }

        $rosterNode
            ->filterXPath('//tr[contains(concat(" ", normalize-space(@class), " "), " Player ")]')
            ->each(fn (Crawler $playerNode) => $this->parsePlayer($playerNode));

        return $this->players;
    }

    private function parsePlayer(Crawler $playerNode): void
    {
        $nickname = $playerNode->filterXPath('//td[contains(concat(" ", normalize-space(@class), " "), " ID ")]//a')->text();

        $countries = $this->getCountriesByFlag($playerNode);
        $countryId = $countries->first()?->id;

        $providerUrl = $playerNode->filterXPath('//td[contains(concat(" ", normalize-space(@class), " "), " ID ")]//a')->attr('href');

        $this->players->push([
            'nickname' => $nickname,
            'country_id' => $countryId,
            'provider_url' => config('services.liquipedia.host_website') . $providerUrl,
        ]);
    }
}
