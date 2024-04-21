<?php

use App\Models\Provider;
use App\Models\VideoGame;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('provider_video_game', function (Blueprint $table) {
            $table->foreignIdFor(Provider::class)->index();
            $table->foreignIdFor(VideoGame::class)->index();
            $table->string('provider_video_game_id')->nullable();

            $table->unique([
                'provider_id',
                'video_game_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_video_game');
    }
};
