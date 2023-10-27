<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Model::unguard();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app()->setLocale('es');

        Inertia::share([
            'locale' => function () {
                return app()->getLocale();
            },
            'language' => function () {
                $locale = app()->getLocale();
                return translations(
                    resource_path('lang/'. $locale .'.json')
                );
            },
            'dir' => function () {
                $locale = app()->getLocale();
                $rtlCodes = ['sa', 'he', 'ur'];
                return in_array($locale, $rtlCodes) ? 'rtl' : 'ltr';
            }
        ]);
    }

}
