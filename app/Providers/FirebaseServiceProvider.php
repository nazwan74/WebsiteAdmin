<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Singleton untuk Firestore
        $this->app->singleton('firebase.firestore', function ($app) {
            return (new Factory)
                ->withServiceAccount(config('firebase.credentials'))
                ->createFirestore()
                ->database();
        });

        // Singleton untuk Storage
        $this->app->singleton('firebase.storage', function ($app) {
            return (new Factory)
                ->withServiceAccount(config('firebase.credentials'))
                ->withDefaultStorageBucket(config('firebase.storage_bucket'))
                ->createStorage();
        });

        // Singleton untuk Auth
        $this->app->singleton('firebase.auth', function ($app) {
            return (new Factory)
                ->withServiceAccount(config('firebase.credentials'))
                ->createAuth();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
