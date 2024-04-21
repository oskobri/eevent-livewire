<div>
    <div>
        @if($opponent->country)
            <span class="fi fi-{{ $opponent->country->code }} mr-2"></span>
        @endif
        {{ $opponent->name ?? $opponent->nickname }}
    </div>
    <div class="text-xs">
        @if($opponent->players)
            Joueurs
            <div class="flex flex-col">
                @foreach($opponent->players as $player)
                    <div>
                        @if($player->country)
                            <span class="fi fi-{{ $player->country->code }} mr-2"></span>
                        @endif
                        {{ $player->nickname }}
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
