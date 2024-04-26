<?php

use App\Enums\Tier;
use App\Models\Country;
use App\Models\Event;
use App\Models\VideoGame;
use Illuminate\Validation\Rule;
use function Livewire\Volt\{state, computed, rules, mount};


rules(fn() => [
    'videoGame' => ['nullable', 'int'],
    'opponentCountry' => ['nullable', 'int'],
    'withPlayersCountry' => ['nullable', 'boolean'],
    'tier' => ['nullable', Rule::enum(Tier::class)],
    'date' => ['nullable', 'date_format:Y-m-d'],
    'opponent' => ['nullable', 'string'],
]);

// Avoid user to change params directly from url
// TODO not good, only loaded on page refresh
// supprimer les parametres de l'url
mount(function () {
    try {
        $this->validate();
    } catch (Exception $e) {
        $this->redirectRoute('home');
    }
});

state('videoGame')/*->url(as: 'video_game', history: true, keep: true)*/
;
state('opponentCountry')/*->url(as: 'opponent_country', history: true, keep: true)*/
;
state(['withPlayersCountry' => false])/*->url(as: 'with_players_country', history: true, keep: true)*/
;
state('tier')/*->url(as: 'tier', history: true, keep: true)*/
;
state(['date' => now()->format('Y-m-d')])/*->url(as: 'date', history: true, keep: true)*/
;
state('opponent')/*->url(as: 'opponent', history: true, keep: true)*/
;

$countries = computed(fn() => Country::all());

$events = computed(function () {
    return Event::getListWithMatches([
        'opponentCountry' => $this->opponentCountry,
        'withPlayersCountry' => $this->withPlayersCountry,
        'tier' => $this->tier,
        'date' => $this->date,
        'opponent' => $this->opponent,
        'video_game_id' => $this->videoGame,
    ]);
});

$filterDate = fn($date) => $this->date = $date;
?>

<div>
    <div class="flex flex-col mb-2 items-center gap-4">
        <div>
            <label for="filter-opponent-country" class="mr-2">Filtrer selon le pays du club</label>
            <select id="filter-opponent-country" wire:model.live="opponentCountry" class="text-black">
                <option value="">Tous les pays</option>
                @foreach($this->countries as $country)
                    <option value="{{ $country->id }}" wire:key="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
            <input id="filter-with-players-country" type="checkbox"
                   wire:model.live="withPlayersCountry" @disabled(!$this->opponentCountry)>
            <label for="filter-with-players-country" class="ml-1">Filtrer aussi sur les joueurs du club</label>
        </div>
        <div>
            <label for="filter-tier" class="mr-2">Tier</label>
            <select id="tier" wire:model.live="tier" class="text-black">
                <option value="">Tous les tiers</option>
                @foreach(\App\Enums\Tier::collection() as $tierKey => $tierLabel)
                    <option value="{{ $tierKey }}">{{ $tierLabel }}</option>
                @endforeach
            </select>
        </div>
        {{--Filter date--}}
        <div class="flex flex-row gap-2">
            @for($day = 0; $day < 4; $day++)
                @php
                    $date = now()->addDays($day);
                    $dateFormatted = $date->format('Y-m-d');
                    $dateLabel = $day === 0 ? 'Aujourd\'hui' : $date->localeDayOfWeek;
                    $dateDayOfMonth = $date->dayOfMonth;
                @endphp
                <button type="button"
                        wire:click="filterDate('{{ $dateFormatted }}')"
                    @class([
                        'rounded p-1 bg-gray-300',
                        'bg-gray-500 text-white' => $this->date === $dateFormatted
                    ])>
                    <div>{{ $dateLabel }}</div>
                    <div>{{ $dateDayOfMonth }}</div>
                </button>
            @endfor
        </div>
        <div>
            <label for="filter-opponent">Opponent</label>
            <input id="filter-opponent" type="text" wire:model.live.debounce.250ms="opponent">
        </div>
    </div>
    <div class="flex flex-col gap-14 mt-6">
        @foreach($this->events as $event)
            <x-events.card :$event :$videoGame/>
        @endforeach
    </div>
</div>
