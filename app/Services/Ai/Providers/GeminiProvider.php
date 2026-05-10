<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\Contracts\AiProviderInterface;
use Gemini;
use Throwable;
use Illuminate\Support\Facades\Log;
use App\Services\Ai\DTO\AiResponse;
use App\Services\Ai\Exceptions\AiException;


class GeminiProvider implements AiProviderInterface
{
    public function generate(string $prompt): AiResponse
    {
        $models = config('ai.gemini.models');
        $keys = config('ai.gemini.keys');

        foreach ($keys as $keyIndex => $apiKey) {

            $client = Gemini::client($apiKey);

            foreach ($models as $model) {

                try {

                    $started = microtime(true);

                    $result = $client
                        ->generativeModel(model: $model)
                        ->generateContent($prompt);

                    Log::info('Gemini success', [
                        'provider' => 'gemini',
                        'model' => $model,
                        'key_index' => $keyIndex,
                        'duration_ms' => round(
                            (microtime(true) - $started) * 1000
                        ),
                    ]);

                    return new AiResponse(
                        success: true,
                        content: $result->text(),
                        provider: 'gemini',
                        model: $model,
                    );

                } catch (Throwable $e) {

                    Log::error('Gemini failed', [
                        'provider' => 'gemini',
                        'model' => $model,
                        'key_index' => $keyIndex,
                        'error' => $e->getMessage(),
                    ]);

                    $message = strtolower($e->getMessage());

                    /*
                    | Quota / Rate Limit
                    */

                    if (
                        str_contains($message, 'quota') ||
                        str_contains($message, 'limit') ||
                        str_contains($message, 'resource exhausted')
                    ) {
                        break;
                    }

                    /*
                    | Temporary Overload
                    */

                    if (
                        str_contains($message, 'overloaded') ||
                        str_contains($message, 'high demand')
                    ) {
                        sleep(1);
                        continue;
                    }
                }
            }
        }

        throw new AiException('All Gemini providers failed');
    }
}
