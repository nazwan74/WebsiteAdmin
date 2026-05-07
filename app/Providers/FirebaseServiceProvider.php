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
        $credentialsPath = config('firebase.credentials');

        if ($credentialsPath && file_exists($credentialsPath)) {
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);
        }

        // Singleton untuk Firestore
        $this->app->singleton('firebase.firestore', function ($app) use ($credentialsPath) {
            $factory = (new Factory);
            if ($credentialsPath && file_exists($credentialsPath)) {
                $factory = $factory->withServiceAccount($credentialsPath);
            }
            return $factory->createFirestore()->database();
        });

        // Singleton untuk Storage
        $this->app->singleton('firebase.storage', function ($app) use ($credentialsPath) {
            $factory = (new Factory);
            if ($credentialsPath && file_exists($credentialsPath)) {
                $factory = $factory->withServiceAccount($credentialsPath);
            }
            return $factory->withDefaultStorageBucket(config('firebase.storage_bucket'))
                ->createStorage();
        });

        // Singleton untuk Auth
        $this->app->singleton('firebase.auth', function ($app) use ($credentialsPath) {
            $factory = (new Factory);
            if ($credentialsPath && file_exists($credentialsPath)) {
                $factory = $factory->withServiceAccount($credentialsPath);
            }
            return $factory->createAuth();
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
