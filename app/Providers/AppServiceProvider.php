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
        // Fix for Livewire update route CORS issues on Hostinger
        \Livewire\Livewire::setUpdateRoute(function ($handle) {
            return \Illuminate\Support\Facades\Route::post('/livewire/update', $handle);
        });

        \Illuminate\Support\Facades\Gate::define('viewPulse', function ($user) {
            return $user->role === 'admin';
        });

        // Ensure all console commands resolved via the container receive the app instance.
        $this->app->afterResolving(\Illuminate\Console\Command::class, function ($command, $app): void {
            if (method_exists($command, 'setLaravel')) {
                $command->setLaravel($app);
            }
        });
    }
}
