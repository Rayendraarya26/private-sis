<?php

namespace App\Providers;

use App\View\Components\LinkedIcon;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Carbon::setLocale('id');
        Blade::component('linked-icon', LinkedIcon::class);

        // set https if app.url contains https
        URL::forceRootUrl(config('app.url'));
        if (str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
