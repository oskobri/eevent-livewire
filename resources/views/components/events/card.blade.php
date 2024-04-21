<div x-data="{collapsed: true}">
    <div x-on:click="collapsed = !collapsed"
         class="flex flex-row justify-between bg-gray-600 rounded-lg text-white shadow-xl text-xl p-2 cursor-pointer mb-4">
        <div>{{ $event->name }}</div>
        <div>({{ $event->matches_count }})</div>
    </div>
    <div x-cloak x-show="!collapsed" x-transition class="flex flex-col gap-4">
        @foreach($event->matches as $match)
            <x-matches.card :match="$match" :streamers="$event->streamers"/>
        @endforeach
    </div>
</div>
