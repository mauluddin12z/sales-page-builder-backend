<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Ai\AiManager;
use App\Services\Ai\Parsers\JsonResponseParser;
use App\Services\Ai\Providers\GeminiProvider;
use App\Services\Ai\Providers\OpenRouterProvider;
use App\Services\Ai\Validators\LandingPageOutputValidator;

class AiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Core AI Services
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            JsonResponseParser::class
        );

        $this->app->singleton(
            LandingPageOutputValidator::class
        );

        /*
        |--------------------------------------------------------------------------
        | Providers
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            GeminiProvider::class
        );

        $this->app->singleton(
            OpenRouterProvider::class
        );

        /*
        |--------------------------------------------------------------------------
        | AI Manager
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            AiManager::class,
            function ($app) {

                return new AiManager(
                    gemini: $app->make(GeminiProvider::class),
                    openrouter: $app->make(OpenRouterProvider::class),
                    parser: $app->make(JsonResponseParser::class),
                    validator: $app->make(LandingPageOutputValidator::class),
                );
            }
        );
    }

    public function boot(): void
    {
        //
    }
}
