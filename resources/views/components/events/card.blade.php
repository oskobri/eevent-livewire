<div x-data="{collapsed: false}">
    <div x-on:click="collapsed = !collapsed"
         class="flex flex-row justify-between text-gray-300 text-xl p-2 cursor-pointer mb-4">
        <div>
            @if(!$videoGame)
                <span>{{ $event->videoGame->name }}:</span>
            @endif
            {{ $event->name }}
        </div>
    </div>
    <div x-cloak x-show="!collapsed" x-transition class="flex flex-col">
        @foreach($event->matches as $match)
            <x-matches.card :match="$match" :streamers="$event->streamers"/>
        @endforeach
    </div>
</div>
