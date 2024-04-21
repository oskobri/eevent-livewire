<?php

use App\Models\Streamer;
use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->foreignIdFor(Streamer::class)->index();
            $table->foreignIdFor(Event::class)->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streams');
    }
};
