<?php

namespace Stesa\CloudlinkerClient;

use Illuminate\Support\ServiceProvider;
use Stesa\CloudlinkerClient\Commands\HttpCommandTestCommand;
use Stesa\CloudlinkerClient\Commands\ListClientsCommand;
use Stesa\CloudlinkerClient\Commands\TestConnectionCommand;

class CloudlinkerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/cloudlinker.php',
            'cloudlinker'
        );

        $this->app->singleton(CloudlinkerClient::class, function ($app) {
            return new CloudlinkerClient(
                config('cloudlinker.organisation_id'),
                config('cloudlinker.api_key'),
                config('cloudlinker.base_url')
            );
        });

        $this->app->alias(CloudlinkerClient::class, 'cloudlinker');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/cloudlinker.php' => config_path('cloudlinker.php'),
            ], 'cloudlinker-config');

            $this->commands([
                TestConnectionCommand::class,
                ListClientsCommand::class,
                HttpCommandTestCommand::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [
            CloudlinkerClient::class,
            'cloudlinker',
        ];
    }
}
