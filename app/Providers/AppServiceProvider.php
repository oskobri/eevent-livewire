<?php

namespace App\Providers;

use App\Models\Player;
use App\Models\Team;
use App\Models\VideoGame;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'team' => Team::class,
            'player' => Player::class
        ]);

        View::share('videoGames', VideoGame::all());

        Router::macro('isWith', function ($name, $parameters) {
            return url()->current() === route($name, $parameters);
        });
    }
}
