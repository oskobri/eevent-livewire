<?php

use App\Models\VideoGame;


if (!function_exists('formatUrlWithoutVideoGame')) {
    /**
     * Remove video game name from url crawled from liquipedia
     * @param VideoGame $videoGame
     * @param string $url
     * @return string
     */
    function formatUrlWithoutVideoGame(VideoGame $videoGame, string $url): string
    {
        $urlExploded = explode('/', $url);

        $newUrlExploded = array_diff($urlExploded, ["", $videoGame->pivot->provider_video_game_id]);

        return implode('/', $newUrlExploded);
    }
}

if (!function_exists('getEventByLiquipediaTournamentPageName')) {
    /**
     * Retrieve event by liquipedia tournament page name
     * Useful when page names are coming from upcoming matches because that's not often the main page
     * @param string $liquipediaPageName
     * @return \App\Models\Event|null
     */
    function getEventByLiquipediaTournamentPageName(string $liquipediaPageName): ?\App\Models\Event
    {
        $url = config('services.liquipedia.host_website') . $liquipediaPageName;

        $event = \App\Models\Event::firstWhere('provider_event_url', $url);

        if ($event) {
            return $event;
        }

        // In upcoming matches page, urls are often not the main page (Group_stage or Play_off, ...)
        // so we need to remove last part of url to get the main page
        $newUrl = rtrim(dirname($url), '/');

        return \App\Models\Event::firstWhere('provider_event_url', $newUrl);
    }
}

if (!function_exists('formatPageName')) {
    /**
     * Remove host and video game from absolute url to get page name
     * @param VideoGame $videoGame
     * @param string $url
     * @return string
     */
    function formatPageName(VideoGame $videoGame, string $url): string
    {
        $relativeUrl = str_replace(config('services.liquipedia.host_website'), '', $url);
        return formatUrlWithoutVideoGame($videoGame, $relativeUrl);
    }
}
