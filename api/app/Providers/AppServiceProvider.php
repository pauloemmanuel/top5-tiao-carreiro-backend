<?php

namespace App\Providers;

use App\Repositories\Interface\MusicaRepositoryInterface;
use App\Repositories\Interface\SugestaoRepositoryInterface;
use Illuminate\Support\ServiceProvider;

use App\Repositories\EloquentMusicaRepository;

use App\Repositories\EloquentSugestaoRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MusicaRepositoryInterface::class, EloquentMusicaRepository::class);
        $this->app->bind(SugestaoRepositoryInterface::class, EloquentSugestaoRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
