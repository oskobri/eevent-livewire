<?php

namespace App\Services\Crawler\Liquipedia;

use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

readonly class ParseInfoBox
{
    use ParseCountriesByFlag;

    public function __construct(public string $html) { }

    public function __invoke(): array
    {
        $crawler = new Crawler($this->html);

        $data = [];

        $infoBoxNode = $crawler->filterXpath('//div[contains(concat(" ", normalize-space(@class), " "), "fo-nttax-infobox ")]')->first();

        $infoBoxHeaderNode = $infoBoxNode->filterXPath('//div[contains(concat(" ", normalize-space(@class), " "), " infobox-header ")]/text()');

        $data['name'] = $infoBoxHeaderNode->text('Not found');

        $infoBoxDescriptionNodes = $infoBoxNode->filterXPath('//div[contains(concat(" ", normalize-space(@class), " "), " infobox-description ")]/parent::*');

        if (!$infoBoxDescriptionNodes->count()) {
            return $data;
        }

        $infoBoxDescriptionNodes->each(function (Crawler $infoBoxDescriptionNode) use(&$data) {
            $description = $infoBoxDescriptionNode
                ->filterXPath('//div[contains(concat(" ", normalize-space(@class), " "), " infobox-description ")]')
                ->text();

            $value = $infoBoxDescriptionNode
                ->filterXPath('//div')
                ->last()
                ->text();

            // Remove ASCII
            $value = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $value);

            if (Str::contains($description, 'Type')) {
                $data['is_online'] = $value === 'Online';
            }

            if (Str::contains($description, 'Location')) {
                $countries = $this->getCountriesByFlag($infoBoxDescriptionNode);
                if($countries->isNotEmpty()) {
                    $data['country_id'] = $countries->first()?->id;
                }
            }

            if (Str::contains($description, 'Prize')) {
                $data['prize'] = $value;
            }

            if (Str::contains($description, 'Start Date')) {
                $data['start_at'] = Str::contains($value, '?') ? null : $value;
            }

            if (Str::contains($description, 'End Date')) {
                $data['end_at'] = Str::contains($value, '?') ? null : $value;
            }

            if (Str::contains($description, 'Liquipedia Tier')) {
                $data['tier'] = $value;
            }
        });

        return $data;
    }
}
