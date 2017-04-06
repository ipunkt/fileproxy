<?php

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! defined('MAX_STRING_LENGTH')) {
            define('MAX_STRING_LENGTH', 191);
        }
        Schema::defaultStringLength(MAX_STRING_LENGTH);

        $this->app->bind(Manager::class, function () {
            $fractal = new Manager();
            $fractal->setSerializer(new JsonApiSerializer());

            return $fractal;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
