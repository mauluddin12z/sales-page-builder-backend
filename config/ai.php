<?php

return [

    /*
    | Default AI Provider
    */

    'priority' => explode(
        ',',
        env(
            'AI_PROVIDER_PRIORITY',
            'gemini,openrouter'
        )
    ),

    /*
    | Global Timeout
    |--------------------------------------------------------------------------
    */

    'timeout' => env('AI_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Gemini
    |--------------------------------------------------------------------------
    */

    'gemini' => [

        'models' => array_filter([
            env('GEMINI_MODEL'),
            env('GEMINI_MODEL_FALLBACK_1'),
            env('GEMINI_MODEL_FALLBACK_2'),
        ]),

        'keys' => array_filter([
            env('GEMINI_API_KEY'),
            env('GEMINI_API_KEY_2'),
        ]),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenRouter
    */

    'openrouter' => [

        'base_url' => env(
            'OPENROUTER_BASE_URL',
            'https://openrouter.ai/api/v1/chat/completions'
        ),

        'models' => array_filter([
            env('OPENROUTER_MODEL'),
            env('OPENROUTER_MODEL_FALLBACK_1'),
            env('OPENROUTER_MODEL_FALLBACK_2'),
        ]),

        'keys' => array_filter([
            env('OPENROUTER_API_KEY'),
            env('OPENROUTER_API_KEY_2'),
        ]),
    ],
];
