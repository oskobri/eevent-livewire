<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

enum Tier: int implements HasLabel
{
    use Enumerable;

    case S = 1;
    case A = 2;
    case B = 3;
    case C = 4;
    case D = 5;
    case E = 6;

    public function getLabel(): ?string
    {
        return $this->name;
    }

    /**
     * @param ProviderEnum $providerEnum
     * @param string $tier
     * @return Tier|null
     */
    public static function match(ProviderEnum $providerEnum, mixed $tier): ?Tier
    {
        if ($providerEnum === ProviderEnum::StartGG) {
            return Tier::matchFromStartGG($tier);
        }

        if ($providerEnum === ProviderEnum::Liquipedia) {
            return Tier::matchFromLiquipedia($tier);
        }

        return null;
    }

    /**
     * Not used for the moment, start.gg return tier is always 5
     * Used for detecting new tier
     * @param mixed $tier
     * @return ?Tier
     */
    private static function matchFromStartGG(mixed $tier): ?Tier
    {
        return match ($tier) {
            //'1' => Tier::S,
            //'2' => Tier::A,
            //'3' => Tier::B,
            //'4' => Tier::C,
            '5', 5 => Tier::D,
            default => (function () use ($tier) {
                Log::info("New Start.gg tier detected: $tier");
                return null;
            })()
        };
    }

    private static function matchFromLiquipedia(mixed $tier): ?Tier
    {
        switch ($tier) {
            case Str::contains($tier, ['S-Tier', 'Tier 1', 'Premier']):
                return Tier::S;
            case Str::contains($tier, ['A-Tier', 'Major']):
                return Tier::A;
            case Str::contains($tier, 'B-Tier'):
                return Tier::B;
            case Str::contains($tier, 'C-Tier'):
                return Tier::C;
            case Str::contains($tier, 'D-Tier'):
                return Tier::D;
            case Str::contains($tier, 'E-Tier'):
                return Tier::E;
            default:
                Log::info("New Liquipedia tier detected: $tier");
                return null;
        }
    }
}
