<?php

use App\Models\Provider;
use App\Models\VideoGame;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('video_game_id')->index()->nullable();
            $table->unsignedInteger('provider_id')->index()->nullable();
            $table->unsignedInteger('provider_event_id')->nullable();
            $table->string('slug')->nullable();
            $table->string('name');
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->string('tier')->nullable();
            $table->boolean('is_online');

            // Columns that can be updated by provider scripts
            $table->string('provider_event_name')->nullable();
            $table->date('provider_event_start_at')->nullable();
            $table->date('provider_event_end_at')->nullable();
            $table->string('provider_event_tier')->nullable();
            $table->string('provider_event_is_online')->nullable();
            $table->string('provider_event_url')->nullable()->index();

            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
