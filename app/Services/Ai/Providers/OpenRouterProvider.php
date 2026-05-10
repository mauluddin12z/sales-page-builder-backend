<?php

namespace App\Services\Ai\Providers;

use Throwable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\Ai\DTO\AiResponse;
use App\Services\Ai\Exceptions\AiException;
use App\Services\Ai\Contracts\AiProviderInterface;

class OpenRouterProvider implements AiProviderInterface
{
    public function generate(string $prompt): AiResponse
    {
        $models = config('ai.openrouter.models');
        $keys = config('ai.openrouter.keys');

        foreach ($keys as $keyIndex => $apiKey) {

            foreach ($models as $model) {

                try {

                    $started = microtime(true);

                    $response = $this->request(
                        apiKey: $apiKey,
                        model: $model,
                        prompt: $prompt,
                    );

                    $content = $response->json(
                        'choices.0.message.content'
                    );

                    if (!$content) {
                        throw new AiException(
                            'Empty AI response content'
                        );
                    }

                    Log::info('OpenRouter success', [
                        'provider' => 'openrouter',
                        'model' => $model,
                        'key_index' => $keyIndex,
                        'duration_ms' => round(
                            (microtime(true) - $started) * 1000
                        ),
                    ]);

                    return new AiResponse(
                        success: true,
                        content: $content,
                        provider: 'openrouter',
                        model: $model,
                    );

                } catch (Throwable $e) {

                    Log::error('OpenRouter failed', [
                        'provider' => 'openrouter',
                        'model' => $model,
                        'key_index' => $keyIndex,
                        'error' => $e->getMessage(),
                    ]);

                    $message = strtolower($e->getMessage());

                    /*
                    |--------------------------------------------------------------------------
                    | Quota / Credit / Limit
                    |--------------------------------------------------------------------------
                    */

                    if (
                        str_contains($message, 'quota') ||
                        str_contains($message, 'credit') ||
                        str_contains($message, 'limit')
                    ) {
                        break;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Temporary Overload
                    |--------------------------------------------------------------------------
                    */

                    if (
                        str_contains($message, 'overloaded') ||
                        str_contains($message, 'try again')
                    ) {
                        sleep(1);
                        continue;
                    }
                }
            }
        }

        throw new AiException('All OpenRouter providers failed');
    }

    private function request(
        string $apiKey,
        string $model,
        string $prompt,
    ): Response {

        $response = Http::retry(
                times: 3,
                sleepMilliseconds: 1000,
            )
            ->timeout(config('ai.timeout'))
            ->acceptJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])
            ->post(
                config('ai.openrouter.base_url'),
                [

                    'model' => $model,

                    'messages' => [

                        [
                            'role' => 'system',
                            'content' => implode("\n", [
                                'You are a professional copywriter.',
                                'Return ONLY valid JSON.',
                                'No markdown.',
                                'No explanations.',
                            ]),
                        ],

                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],

                    'temperature' => 0.7,

                    /*
                    |--------------------------------------------------------------------------
                    | Strong JSON Mode
                    |--------------------------------------------------------------------------
                    */

                    'response_format' => [
                        'type' => 'json_object',
                    ],
                ]
            );

        if (!$response->successful()) {

            throw new AiException(
                'OpenRouter Error: ' . $response->body()
            );
        }

        return $response;
    }
}
