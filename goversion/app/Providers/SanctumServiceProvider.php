<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use App\Models\IcePersonalAccessToken;

class SanctumServiceProvider extends ServiceProvider
{
    /**
    * Register services.
    *
    * @return void
    */
    public function register()
    {
    //
    }

    /**
    * Bootstrap services.
    *
    * @return void
    */
    public function boot()
    {
        Sanctum::usePersonalAccessTokenModel(IcePersonalAccessToken::class);
    }
}
