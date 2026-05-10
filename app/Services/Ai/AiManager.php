<?php

namespace App\Services\Ai;

use Throwable;
use Illuminate\Support\Facades\Log;
use App\Services\Ai\Exceptions\AiException;
use App\Services\Ai\Parsers\JsonResponseParser;
use App\Services\Ai\Providers\GeminiProvider;
use App\Services\Ai\Providers\OpenRouterProvider;
use App\Services\Ai\Validators\LandingPageOutputValidator;

class AiManager
{
    protected array $providers = [];

    public function __construct(
        protected GeminiProvider $gemini,
        protected OpenRouterProvider $openrouter,
        protected JsonResponseParser $parser,
        protected LandingPageOutputValidator $validator,
    ) {

        $this->providers = [

            'gemini' => $this->gemini,

            'openrouter' => $this->openrouter,
        ];
    }

    public function generate(string $prompt): array
    {
        $priority = config('ai.priority', []);

        $errors = [];

        foreach ($priority as $providerName) {

            try {

                if (!isset($this->providers[$providerName])) {
                    continue;
                }

                Log::info('Trying AI provider', [
                    'provider' => $providerName,
                ]);

                $response = $this->providers[$providerName]
                    ->generate($prompt);

                $parsed = $this->parser
                    ->parse($response->content);

                return $this->validator
                    ->validate($parsed);

            } catch (Throwable $e) {

                $errors[$providerName] = $e->getMessage();

                Log::error('AI provider failed', [
                    'provider' => $providerName,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }
        }

        throw new AiException(
            'All AI providers failed: ' .
            json_encode($errors)
        );
    }
}
