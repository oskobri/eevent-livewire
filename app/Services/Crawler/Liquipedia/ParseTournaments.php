<?php

namespace App\Services\Crawler\Liquipedia;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

readonly class ParseTournaments
{
    public function __construct(public string $html) { }

    public function __invoke(): Collection
    {
        $crawler = new Crawler($this->html);

        $tournamentsPage = collect();

        // Parent of "divCell Tournament Header" so => row of a table => a tournament
        $crawler->filterXpath('//div[contains(concat(" ", normalize-space(@class), " "), " Tournament ")]/parent::*')
            ->each(function (Crawler $tournamentInformationNode) use (&$tournamentsPage) {
                $tournamentsPage->push($this->getTournamentPage($tournamentInformationNode));
            });

        return $tournamentsPage->filter();
    }

    /**
     * Get only upcoming tournament's page
     * @param Crawler $tournamentInformationNode
     * @return string|null
     */
    protected function getTournamentPage(Crawler $tournamentInformationNode): ?string
    {
        // We don't fetch past tournament
        if ($this->isTournamentOver($tournamentInformationNode)) {
            return null;
        }

        // Get tournament page url
        $tournamentNameNode = $tournamentInformationNode->filterXpath('//div[contains(concat(" ", normalize-space(@class), " "), " Tournament ")]');
        $explodedUrl = explode('/', $tournamentNameNode->filterXPath('//a')->last()->attr('href'));
        return urldecode(implode('/', array_slice($explodedUrl, 2)));
    }


    protected function isTournamentOver($tournamentInformationNode): bool
    {
        $tournamentIsOver = true;
        $dateFound = false;

        $tournamentInformationNode
            ->filterXpath('//div')
            ->each(function ($columnNode) use (&$tournamentIsOver, &$dateFound) {

                // First column without html is a date (common to all video games)
                if (!$dateFound && !Str::contains($columnNode->html(), '<')) {
                    $dateFound = true;

                    // We don't fetch unknown date
                    if (!Str::contains($columnNode->text(), '?')) {
                        $dates = $this->parseDate($columnNode->text());

                        if (now()->lessThanOrEqualTo(end($dates))) {
                            $tournamentIsOver = false;
                        }
                    }
                }
            });

        return $tournamentIsOver;
    }

    protected function parseDate($liquipediaDate): array
    {
        try {
            if (Str::contains($liquipediaDate, 'Postponed')) {
                return [];
            }

            $period = explode(',', $liquipediaDate);
            $year = trim(end($period));

            $dates = explode('-', $period[0]);
            $finalDates = [];

            foreach ($dates as $date) {
                $date = trim($date);

                $dateExploded = explode(' ', $date);

                // Only day number
                if (count($dateExploded) === 1) {
                    $day = $dateExploded[0];
                } // month and day
                else {
                    [$month, $day] = $dateExploded;
                }

                if (isset($month)) {
                    $finalDates[] = (new Carbon("$month $day $year"));
                }
            }
        } catch (\Exception) {
            Log::error("Error while parsing Liquipedia date $liquipediaDate");
            return [];
        }

        return $finalDates;
    }
}
