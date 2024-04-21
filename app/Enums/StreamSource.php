<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

enum StreamSource: string implements HasLabel
{
    use Enumerable;

    case Twitch = 'twitch';
    case Youtube = 'youtube';
    case Facebook = 'facebook';
    case Tiktok = 'tiktok';
    case Huya = 'huya';
    case Trovo = 'trovo';
    case Afreecatv = 'afreecatv';
    case Bilibili = 'bilibili';
    case Douyu = 'douyu';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public static function match(ProviderEnum $providerEnum, string $source): ?StreamSource
    {
        if ($providerEnum === ProviderEnum::StartGG) {
            return self::matchFromStartGG($source);
        }

        if ($providerEnum === ProviderEnum::Liquipedia) {
            return self::matchFromUrl($source);
        }

        return null;
    }

    public static function matchFromUrl(string $url): ?StreamSource
    {
        $streamSource = collect(self::cases())
            ->filter(fn (StreamSource $streamSource) => Str::of($url)->contains($streamSource->value))
            ->first();

        if ($streamSource) {
            return $streamSource;
        }

        Log::info("New stream source detected: $url");
        return null;
    }

    public static function getSourceIdFromUrl(string $url): ?string
    {
        try {
            return match (self::matchFromUrl($url)) {
                self::Twitch,
                self::Tiktok,
                self::Huya,
                self::Afreecatv,
                self::Bilibili,
                self::Douyu,
                self::Trovo, => (function () use ($url) {
                    $explodedUrl = explode('/', $url);
                    return end($explodedUrl);
                })(),
                self::Youtube => (function () use ($url) {
                    preg_match('#/([^/]+)/(live|streams)#', $url, $matches);
                    return $matches[1];
                })(),
                self::Facebook => (function () use ($url) {
                    preg_match('#/([^/]+)/live#', $url, $matches);
                    return $matches[1];
                })(),
                default => null
            };
        } catch (\Exception) {

            // Exception for not logging (don't want to keep these streams)
            if (Str::of($url)->startsWith('https://web.archive.org')) {
                return null;
            }

            Log::info("New stream url detected: $url");
            return null;
        }
    }

    public function getStreamerUrl(string $streamerSourceId): string
    {
        return match ($this) {
            self::Twitch => "https://www.twitch.tv/$streamerSourceId",
            self::Tiktok => "https://www.tiktok.com/$streamerSourceId",
            default => ''
        };
    }

    private static function matchFromStartGG($source): ?StreamSource
    {
        // Don't know others sources yet
        return match ($source) {
            'TWITCH' => self::Twitch,
            default => (function () use ($source) {
                Log::info("New stream source detected: $source");
                return null;
            })()
        };
    }
}
