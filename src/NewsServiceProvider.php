<?php

namespace Scaupize1123\JustOfficalNews;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class NewsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->app->bind(
            'Scaupize1123\JustOfficalNews\Interfaces\NewsRepositoryInterface',
            'Scaupize1123\JustOfficalNews\Repositories\NewsRepository'
        );
        $this->app->bind(
            'Scaupize1123\JustOfficalNews\Interfaces\NewsCategoryRepositoryInterface',
            'Scaupize1123\JustOfficalNews\Repositories\NewsCategoryRepository'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
