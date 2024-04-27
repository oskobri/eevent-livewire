<?php

use App\Models\Country;
use App\Models\Team;
use App\Models\VideoGame;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('video_game_id')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('nickname');
            $table->string('provider_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
