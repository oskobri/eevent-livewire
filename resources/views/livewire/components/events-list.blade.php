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
    'tiers' => ['nullable', 'array'],
    'tiers.*' => ['required', Rule::enum(Tier::class)],
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
state(['tiers' => []])->url(as: 'tiers', history: true, keep: true);
state(['date' => now()->format('Y-m-d')])/*->url(as: 'date', history: true, keep: true)*/
;
state('opponent')/*->url(as: 'opponent', history: true, keep: true)*/
;

$countries = computed(fn() => Country::all());

$events = computed(function () {
    return Event::getListWithMatches([
        'opponentCountry' => $this->opponentCountry,
        'withPlayersCountry' => $this->withPlayersCountry,
        'tiers' => $this->tiers,
        'date' => $this->date,
        'opponent' => $this->opponent,
        'video_game_id' => $this->videoGame,
    ]);
});

$filterDate = fn($date) => $this->date = $date;
?>

<div>
    <div class="flex flex-col md:flex-row divide-x divide-gray-700 gap-4">
        <div class="w-3/4 flex flex-col gap-14 mt-6 order-2 md:order-1">
            @foreach($this->events as $event)
                <x-events.card :$event :$videoGame/>
            @endforeach
        </div>
        <div class="w-1/4 flex flex-col mb-2 items-start gap-4 order-1 md:order-2 text-gray-300 pl-4">
            <div class="flex flex-col gap-6">
                {{--Filter date--}}
                <div class="flex flex-row gap-2 border-b border-gray-700">
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
                                'rounded-t p-1 bg-gray-800 text-sm hover:bg-gray-700',
                                'bg-white text-black' => $this->date === $dateFormatted
                            ])>
                            <div>{{ $dateLabel }}</div>
                            <div>{{ $dateDayOfMonth }}</div>
                        </button>
                    @endfor
                </div>
                <div>
                    <label for="filter-opponent-country">Filtrer selon le pays du club</label>
                    <x-select
                        if="filter-opponent-country"
                        name="opponentCountry"
                        :data="$this->countries->pluck('name', 'id')->toJson()"
                        placeholder="Tous les pays"
                    ></x-select>
                    <label for="filter-with-players-country" class="flex items-center mt-2 text-sm">
                        <input id="filter-with-players-country" type="checkbox"
                               wire:model.live="withPlayersCountry"
                               @disabled(!$this->opponentCountry)
                               @class([
                                   "rounded-sm cursor-pointer dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-600 shadow-sm focus:ring-gray-500 dark:focus:ring-gray-600 dark:focus:ring-offset-gray-800 mr-1",
                                   "cursor-not-allowed" => !$this->opponentCountry
                               ])
                               @if(!$this->opponentCountry)
                                   title="Un pays doit d'abord être sélectionné"
                                @endif
                        >
                        Inclure les joueurs
                    </label>
                </div>
                <div>
                    <label>Tier</label>
                    <div class="flex flex-col">
                        @foreach(\App\Enums\Tier::collection() as $tierKey => $tierLabel)
                            <label class="flex items-center gap-2" wire:key="{{ $tierKey }}">
                                <input type="checkbox" value="{{ $tierKey }}"
                                       wire:model.live="tiers"
                                       class="rounded-sm cursor-pointer dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-600 shadow-sm focus:ring-gray-500 dark:focus:ring-gray-600 dark:focus:ring-offset-gray-800">
                                <span>{{ $tierLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label for="filter-opponent">Chercher une équipe</label>
                    <div>
                        <input id="filter-opponent"
                               type="text"
                               placeholder="Nom de l'équipe"
                               wire:model.live.debounce.250ms="opponent"
                               class="bg-gray-900 w-full border-0 border-b border-gray-700 focus:outline-none focus:outline-0 pl-0">
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
