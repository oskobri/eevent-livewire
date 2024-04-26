<?php
use function Laravel\Folio\{name};

name('video-games.show');
?>

<x-app-layout>
    <div class="py-12">
        <livewire:components.events-list :video-game="$videoGame->id"/>
    </div>
</x-app-layout>
