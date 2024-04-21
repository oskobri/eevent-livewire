<?php

namespace App\Services\Crawler\Liquipedia;

use App\Enums\StreamSource;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

readonly class ParseStreams
{
    use ParseCountriesByFlag;

    public function __construct(public string $html) { }

    public function __invoke(): Collection
    {
        $crawler = new Crawler($this->html);

        $streamTablesNode = $crawler->filterXpath('//span[@id="Streams"]/following::div[1]//table');

        if (!$streamTablesNode->count()) {
            return collect();
        }

        return collect(
            $streamTablesNode->each(function (Crawler $node) {
                $languagesData = $this->getLanguagesByFlag($node);

                $streamsData = $this->getStreamUrls($node);

                return $this->matchStreamUrlsWithLanguages($streamsData, $languagesData);
            })
        )->flatten(1);
    }


    protected function getStreamUrls(Crawler $node): array
    {
        return $node->filterXPath('//th[text() = "Streams"]/parent::*/td')
            ->each(fn ($node) => collect($node->filterXPath('//a')->extract(['href'])));
    }

    protected function matchStreamUrlsWithLanguages($streamsData, $languagesData): Collection
    {
        return collect($streamsData)
            ->transform(fn ($streamUrls, $key) => $streamUrls
                ->transform(function ($streamUrl) use ($languagesData, $key) {
                    $source = StreamSource::matchFromUrl($streamUrl);

                    if (!$source) {
                        return null;
                    }

                    return [
                        'language_id' => $languagesData[$key],
                        'url' => $streamUrl,
                        'source' => $source,
                        'source_id' => StreamSource::getSourceIdFromUrl($streamUrl)
                    ];
                })->filter()
            )->flatten(1);
    }
}
