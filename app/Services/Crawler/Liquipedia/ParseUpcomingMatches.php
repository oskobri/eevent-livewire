<?php

namespace App\Services\Crawler\Liquipedia;

use App\Models\Event;
use App\Models\VideoGame;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

readonly class ParseUpcomingMatches
{
    use ParseCountriesByFlag;

    public function __construct(public string $html, public VideoGame $videoGame) { }

    public function __invoke(): Collection
    {
        $crawler = new Crawler($this->html);

        $matches = collect();

        $crawler->filterXpath('//table[contains(concat(" ", normalize-space(@class), " "), "infobox_matches_content")]')
            ->each(function (Crawler $upcomingMatchNode) use (&$matches) {

                if (!($date = $this->getDateFromNode($upcomingMatchNode))) {
                    return;
                }

                $event = $this->getEventByTournamentPageName($upcomingMatchNode);

                // If tournament does not exist, we don't want its matches
                if (!$event) {
                    return;
                }

                $teamLeftNode = $upcomingMatchNode->filterXpath('//td[contains(concat(" ", normalize-space(@class), " "), "team-left")]');

                // No teams (there is always right team when there is a left team so no need to check right)
                if (!$teamLeftNode->count()) {
                    \Log::info('No team matches for video game:' . $this->videoGame->name);
                    return;
                }

                $teamRightNode = $upcomingMatchNode->filterXpath('//td[contains(concat(" ", normalize-space(@class), " "), " team-right ")]');

                $leftTeam = $this->getTeamNodeInformation($teamLeftNode);
                $rightTeam = $this->getTeamNodeInformation($teamRightNode);

                // Avoid importing TBD teams
                if (!$leftTeam || !$rightTeam) {
                    return;
                }

                $matches->push([
                    'event_id' => $event->id,
                    'left' => $leftTeam,
                    'right' => $rightTeam,
                    'date' => $date,
                ]);
            });

        return $matches->filter()->unique();
    }

    protected function getEventByTournamentPageName(Crawler $upcomingMatchNode): ?Event
    {
        // Get tournament page url
        $tournamentPageName = $upcomingMatchNode
            ->filterXpath('//td[contains(concat(" ", normalize-space(@class), " "), "match-filler")]//a')
            ->last()
            ->attr('href');

        return getEventByLiquipediaTournamentPageName($tournamentPageName);
    }

    protected function getTeamNodeInformation($nodeTeam): ?array
    {
        // We don't want to import To be defined team
        if ($nodeTeam->text() === 'TBD') {
            return null;
        }

        $nodeTeamDetails = $nodeTeam->filterXpath('//span[contains(concat(" ", normalize-space(@class), " "), " team-template-image")]');
        $opponentType = 'team';

        // If class is not defined opponentType is player
        if (!$nodeTeamDetails->count()) {
            $opponentType = 'player';
            $nodeTeamDetails = $nodeTeam->filterXpath('//span[contains(concat(" ", normalize-space(@class), " "), "inline-player")]');
        }

        $teamUrl = $nodeTeamDetails->filterXpath('//a')->attr('href');
        $teamImage = $nodeTeamDetails->filterXpath('//img')->attr('src');

        return [
            'opponent_type' => $opponentType,
            'short' => $nodeTeam->text(),
            'name' => $nodeTeamDetails->filterXpath('//a')->attr('title'),
            'url' => $teamUrl ? config('services.liquipedia.host_website') . $teamUrl : null,
            'icon' => ($opponentType === 'team' && $teamImage) ? config('services.liquipedia.host_website') . $teamImage : null,
            'country_id' => $opponentType === 'player' ?  $this->getCountriesByFlag($nodeTeam)->first()?->id : null
        ];
    }

    private function getDateFromNode(Crawler $upcomingMatchNode): ?Carbon
    {
        $dateNode = $upcomingMatchNode->filterXpath('//span[contains(concat(" ", normalize-space(@class), " "), " timer-object ")]');
        if (!$dateNode->count()) {
            return null;
        }

        $date = $dateNode->attr('data-timestamp');

        // Date can be null or not a timestamp
        try {
            $date = Carbon::createFromTimestamp($date);

            // Check if date is past, no need to get matches
            if ($date->lessThan(now())) {
                return null;
            }
        } catch (\Exception) {
            return null;
        }

        return $date;
    }
}
