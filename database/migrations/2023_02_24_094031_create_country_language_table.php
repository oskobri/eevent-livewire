<?php

use App\Models\Country;
use App\Models\Language;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_language', function (Blueprint $table) {
            $table->unsignedInteger('country_id')->index();
            $table->unsignedInteger('language_id')->index();
            $table->boolean('main');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_language');
    }
};
