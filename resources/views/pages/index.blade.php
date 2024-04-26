<?php
use function Laravel\Folio\{name};

name('home');
?>
<x-app-layout>
    <div class="py-12">
        <livewire:video-games.video-games-list/>
    </div>
</x-app-layout>
