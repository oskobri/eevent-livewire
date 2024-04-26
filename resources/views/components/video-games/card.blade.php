<div x-data="{collapsed: false}">
    <div x-on:click="collapsed = !collapsed"
         class="flex flex-row justify-between bg-gray-800 rounded-lg text-white shadow-xl text-xl p-2 cursor-pointer mb-4">
        <div>{{ $videoGame->name }}</div>
    </div>
    <div x-cloak x-show="!collapsed" x-transition class="flex flex-col gap-4">
        @foreach($videoGame->events as $event)
            <x-events.card :event="$event"/>
        @endforeach
    </div>
</div>
