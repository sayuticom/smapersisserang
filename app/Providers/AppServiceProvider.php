<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->runningUnitTests()) {
            Config::set('app.env', 'testing');
            Config::set('database.default', 'sqlite');
            Config::set('database.connections.sqlite.database', ':memory:');
            Config::set('database.connections.sqlite.foreign_key_constraints', true);
            Config::set('session.driver', 'array');
            Config::set('cache.default', 'array');
            Config::set('queue.default', 'sync');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('testing') || $this->app->runningUnitTests()) {
            $defaultConnection = config('database.default');
            $sqliteDatabase = config('database.connections.sqlite.database');

            if ($defaultConnection !== 'sqlite' || $sqliteDatabase !== ':memory:') {
                throw new \RuntimeException(
                    'Testing must use SQLite memory database. Current config: ' .
                    "database.default={$defaultConnection}, sqlite.database={$sqliteDatabase}"
                );
            }
        }

        // Memuat pengaturan dari storage/app/public/config
        if ($this->app->runningInConsole() || $this->app->runningUnitTests()) {
            $configPath = storage_path('app/public/config/school.json');
            if (file_exists($configPath)) {
                $config = json_decode(file_get_contents($configPath), true);
                if ($config) {
                    foreach ($config as $key => $value) {
                        Config::set("school.{$key}", $value);
                    }
                }
            }
        }
    }
}
