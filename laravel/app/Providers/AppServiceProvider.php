<?php

namespace App\Providers;

use App\Services\AvatarUploader;
use App\Services\Telegram\CheckAuthorizationService;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        $url->forceScheme('https');

        $this->app->bind(CheckAuthorizationService::class, function (Application $app) {
            return new CheckAuthorizationService(config('telegram.token'));
        });

        $this->app->bind(AvatarUploader::class, function (Application $app) {
            new AvatarUploader(Storage::disk('s3-avatar'), new Client());
        });
    }
}
