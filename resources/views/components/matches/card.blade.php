<div x-data="{collapsed: true}" class="bg-gray-800 shadow-xl rounded-md text-white p-2 flex flex-col items-center ">
    <div class="flex justify-center gap-4">
        <x-matches.opponent :opponent="$match->leftOpponent"/>
        <div>{{ $match->time ? $match->time->format('H:i') : 'vs' }}</div>
        <x-matches.opponent :opponent="$match->rightOpponent"/>
    </div>
    <button type="button" x-on:click="collapsed = !collapsed"
            class="rounded-lg bg-purple-900 mt-2 py-1 px-4 hover:bg-purple-600">
        Voir le match
    </button>
    <div x-cloak x-show="!collapsed" x-transition>
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
