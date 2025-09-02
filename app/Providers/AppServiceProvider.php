<?php

namespace App\Providers;

use App\Models\IcePersonalAccessToken;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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
        if (Schema::hasTable('ice_settings')) {
            $language = Setting::getSetting('language');
            App::setLocale(!empty($language) ? $language : 'en');
        } else {
            App::setLocale('en'); // safe default
        }     
    }
}
