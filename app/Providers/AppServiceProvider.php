<?php

namespace App\Providers;

use App\Models\IcePersonalAccessToken;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadTranslationsFrom(resource_path('lang'), 'app');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            if (Schema::hasTable('ice_settings')) {
                $language = Setting::getSetting('language');
                App::setLocale(!empty($language) ? $language : 'en');

                return;
            }
        } catch (\Throwable $exception) {
            App::setLocale('en'); // safe default
            return;
        }

        App::setLocale('en'); // safe default
    }
}
