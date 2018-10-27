<?php namespace Igaster\ModelEvents;

use Illuminate\Support\ServiceProvider;

class ModelEventsServiceProvider extends ServiceProvider
{
    /*--------------------------------------------------------------------------
    | Bind in IOC
    |--------------------------------------------------------------------------*/

    public function register()
    {

        $this->app->singleton('igaster.xxx', function () {
            return null; // eg new myService();
        });

    }

    /*--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------*/

    public function boot()
    {
        $this->handleMigrations();
        $this->handleViews();
        $this->handleRoutes();
    }

    /*--------------------------------------------------------------------------
    | Handlers
    |--------------------------------------------------------------------------*/

    private function handleMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');

         // Optional: Publish the migrations:
         $this->publishes([
              __DIR__ . '/Migrations' => base_path('database/migrations'),
         ]);
    }


    private function handleViews()
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'model-events');

        $this->publishes([
            __DIR__ . '/views' => base_path('resources/views/vendor/model-events')
        ]);
    }


    private function handleRoutes()
    {
        include __DIR__ . '/routes.php';
    }

}
