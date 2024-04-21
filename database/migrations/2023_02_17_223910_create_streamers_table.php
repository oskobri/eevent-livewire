<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('streamers', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->string('source_id');
            $table->unsignedBigInteger('language_id')->index()->nullable();
            $table->string('url');
            $table->boolean('is_live')->default(false);
            $table->unsignedBigInteger('followers_count')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streamers');
    }
};
