<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Orchid\Platform\Models\User;
use Orchid\Support\Facades\Dashboard;

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
        Dashboard::useModel(
            \Orchid\Platform\Models\User::class,
            \App\Models\User::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Dashboard::configure([
            'models' => [
                User::class => \App\Models\User::class,
            ],
        ]);

    }
}
