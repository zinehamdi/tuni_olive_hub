<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure all console commands resolved via the container receive the app instance.
        $this->app->afterResolving(\Illuminate\Console\Command::class, function ($command, $app): void {
            if (method_exists($command, 'setLaravel')) {
                $command->setLaravel($app);
            }
        });
    }
}
