<?php

use function Livewire\Volt\{state, computed};

state([
    'opponentCountry'
])->url(as: 'opponent_country', history: true, keep: true);

state([
    'withPlayersCountry'
])->url(as: 'with_players_country', history: true, keep: true);


$countries = computed(function () {
    return \App\Models\Country::all();
});

$videoGames = computed(function () {
    return \App\Models\VideoGame::getList([
        'opponentCountry' => $this->opponentCountry,
        'withPlayersCountry' => $this->withPlayersCountry,
    ]);
});

?>

<div>
    <div class="flex flex-row mb-2 items-center gap-4">
        <div>
            <label for="filter-opponent-country" class="mr-2">Filtrer selon le pays du club</label>
            <select id="filter-opponent-country" wire:model.live="opponentCountry" class="text-black">
                <option value="">Tous les pays</option>
                @foreach($this->countries as $country)
                    <option value="{{ $country->id }}" wire:key="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <input id="filter-with-players-country" type="checkbox" wire:model.live="withPlayersCountry">
            <label for="filter-with-players-country" class="ml-1">Filtrer aussi sur les joueurs du club</label>
        </div>

    </div>
    <div class="flex flex-col gap-14 mt-6">
        @foreach($this->videoGames as $videoGame)
            <x-video-games.card :video-game="$videoGame"/>
        @endforeach
    </div>
</div>
