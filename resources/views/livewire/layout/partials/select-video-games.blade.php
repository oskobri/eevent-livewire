<?php
use function Livewire\Volt\{state, mount};

state('selectedVideoGame');

mount(function () {
    $this->selectedVideoGame = request()->videoGame?->id;
});

?>

<li class="text-white cursor-pointer select-none hover:bg-gray-700"
    x-data="{isOpen: false}"
>
    <details x-on:click="isOpen = !isOpen"
             x-on:click.outside="isOpen = false; $el.removeAttribute('open');">
        <summary class="list-none pl-2 flex py-5">
            {{
                $this->selectedVideoGame ?
                    $videoGames->firstWhere('id', $this->selectedVideoGame)?->name :
                    'Tous les jeux vidéos'
            }}
            <template x-if="isOpen">
                <x-heroicon-o-chevron-up class="w-6 h-6 inline"/>
            </template>
            <template x-if="!isOpen">
                <x-heroicon-o-chevron-down class="w-6 h-6 inline"/>
            </template>
        </summary>
        <ul class="absolute z-10">
            @if($this->selectedVideoGame)
                <li class="bg-gray-800 hover:bg-gray-500 p-1">
                    <x-nav-link
                        :href="route('home')"
                        :active="request()->routeIs('home')"
                        wire:navigate
                        class="m-0 block w-full h-full">
                        Tous les jeux
                    </x-nav-link>
                </li>
            @endif
            @foreach($videoGames as $videoGame)
                <li class="bg-gray-800 hover:bg-gray-500 p-1">
                    <x-nav-link
                        :href="route('video-games.show', ['videoGame' => $videoGame->id])"
                        :active="request()->fullUrlIs(route('video-games.show', ['videoGame' => $videoGame->id]))"
                        wire:navigate
                        class="m-0 block w-full h-full">
                        {{ $videoGame->name }}
                    </x-nav-link>
                </li>
            @endforeach
        </ul>
    </details>
</li>
