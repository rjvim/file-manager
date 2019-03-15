<?php

namespace Betalectic\FileManager;

use Illuminate\Support\ServiceProvider;

class FileManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes.php');

        $this->publishes([
            __DIR__.'/../config/file-manager.php' => config_path('file-manager.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        \Cloudinary::config([
          'cloud_name' => config('file-manager.cloudinary_cloud_name'),
          'api_key' => config('file-manager.cloudinary_api_key'),
          'api_secret' => config('file-manager.cloudinary_api_secret')
        ]);
    }
}
