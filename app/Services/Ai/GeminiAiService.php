<?php

namespace App\Services\Ai;

use Gemini;

class GeminiAiService
{
    private function models(): array
    {
        return array_filter([
            env('GEMINI_MODEL'),
            env('GEMINI_MODEL_FALLBACK_1'),
            env('GEMINI_MODEL_FALLBACK_2'),
        ]);
    }

    private function apiKeys(): array
    {
        return array_filter([
            env('GEMINI_API_KEY'),
            env('GEMINI_API_KEY_2'),
        ]);
    }

    /**
     * MAIN ENTRY POINT
     */
    public function generate(string $prompt): array
    {
        $models = $this->models();
        $keys = $this->apiKeys();

        foreach ($keys as $keyIndex => $apiKey) {

            $client = Gemini::client($apiKey);

            foreach ($models as $model) {

                try {
                    $result = $client
                        ->generativeModel(model: $model)
                        ->generateContent($prompt);

                    return [
                        'success' => true,
                        'text' => $result->text(),
                        'model_used' => $model,
                        'api_key_index' => $keyIndex,
                    ];
                } catch (\Exception $e) {

                    $msg = strtolower($e->getMessage());

                    /**
                     * CASE 1: QUOTA / TOKEN LIMIT → switch API KEY
                     */
                    if (
                        str_contains($msg, 'quota') ||
                        str_contains($msg, 'token') ||
                        str_contains($msg, 'limit') ||
                        str_contains($msg, 'resource exhausted')
                    ) {
                        break; // go next API key
                    }

                    /**
                     * CASE 2: HIGH LOAD → switch MODEL
                     */
                    if (
                        str_contains($msg, 'high demand') ||
                        str_contains($msg, 'overloaded') ||
                        str_contains($msg, 'try again')
                    ) {
                        usleep(500000); // 0.5s delay
                        continue; // next model
                    }

                    /**
                     * CASE 3: UNKNOWN ERROR → fail fast
                     */
                    throw $e;
                }
            }
        }

        throw new \Exception("All Gemini models and API keys failed");
    }
}
