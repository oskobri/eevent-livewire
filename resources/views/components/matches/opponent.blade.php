@props([
    'flagBeforeName' => true,
    'opponent'
])
<div>
    <div>
        @if($flagBeforeName && $opponent->country)
            <span class="fi fi-{{ $opponent->country->code }} mr-2"></span>
        @endif
        <span class="uppercase text-md">{{ $opponent->name ?? $opponent->nickname }}</span>
        @if(!$flagBeforeName && $opponent->country)
            <span class="fi fi-{{ $opponent->country->code }} ml-2"></span>
        @endif
    </div>
    <div class="text-xs hidden">
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
