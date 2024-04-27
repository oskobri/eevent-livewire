<div x-data="{collapsed: true}" class="border-b first:border-t border-gray-700 py-4">
    <div
        class="flex justify-between text-white p-2">
        <div></div>
        <div class="flex justify-center items-center gap-8">
            <x-matches.opponent :opponent="$match->leftOpponent"/>
            <div class="flex items-center time">{{ $match->time ? $match->time->format('H:i') : 'vs' }}</div>
            <x-matches.opponent :opponent="$match->rightOpponent"/>
        </div>
        <button type="button" x-on:click="collapsed = !collapsed"
                class="rounded-lg bg-gray-800 py-1 px-4 hover:bg-gray-600">
            Regarder
        </button>
    </div>
    <div x-cloak x-show="!collapsed" x-transition
        class="flex justify-center text-white">
        @if($streamers->isNotEmpty())
            <div>
                Streams officiels
                @foreach($streamers as $streamer)
                    <div>
                        @if($streamer->language)
                            <span class="fi fi-{{  $streamer->language->code }} mr-2"></span>
                        @endif
                        <a href="{{ $streamer->url }}">{{ $streamer->source_id }}</a> ({{ $streamer->source }})
                    </div>
                @endforeach
            </div>
        @else
            <div>
                No stream available for this match
            </div>
        @endif
    </div>
</div>
