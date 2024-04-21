<?php

namespace App\Services\Crawler\Liquipedia;

use App\Models\Country;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

trait ParseCountriesByFlag
{
    public function getCountriesByFlag(Crawler $node): Collection
    {
        return collect($this->getCountryCodesByFlag($node))
            ->transform(function ($countryCode) {
                return Country::firstWhere('code', $countryCode);
            });
    }

    public function getLanguagesByFlag(Crawler $node): Collection
    {
        return collect($this->getCountryCodesByFlag($node))
            ->transform(function ($countryCode) {
                return Country::firstWhere('code', $countryCode)?->getMainLanguage()?->id;
            });
    }

    private function getCountryCodesByFlag(Crawler $node): array
    {
        return $node->filterXPath('//span[@class="flag"]')
            ->each(function ($node) {
                $explodedImgSrc = explode('/', $node->filterXPath('//img')->attr('src'));
                $explodedImgName = explode('_', end($explodedImgSrc));
                $explodedImgFinal = explode('-', $explodedImgName[0]);

                $countryCode = end($explodedImgFinal);
                return Str::of($countryCode)->substr(0, 2)->lower();
            });
    }
}
