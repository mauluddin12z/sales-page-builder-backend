<?php

namespace App\Services\Ai\Prompts;

class LandingPagePromptBuilder
{
    public static function full(array $data): string
    {
        return json_encode([

            'role' => 'world-class direct response copywriter',

            'task' => 'Generate high-converting landing page copy',

            'rules' => [
                'Return ONLY valid JSON',
                'Do NOT use markdown',
                'Do NOT use backticks',
                'Do NOT explain anything',
            ],

            'output_schema' => [

                'headline' => 'string',
                'subheadline' => 'string',
                'description' => 'string',

                'benefits' => [
                    'string'
                ],

                'features' => [
                    'string'
                ],

                'social_proof' => 'string',
                'pricing' => 'string',
                'cta' => 'string',
            ],

            'input' => $data,

        ], JSON_UNESCAPED_UNICODE);
    }

    public static function regenerate(
        array $input,
        string $field,
        array $current
    ): string {

        return json_encode([

            'role' => 'world-class direct response copywriter',

            'task' => "Regenerate ONLY '{$field}'",

            'rules' => [
                'Keep all other fields identical',
                'Return ONLY valid JSON',
                'No markdown',
                'No explanations',
            ],

            'current_output' => $current,

            'input' => $input,

        ], JSON_UNESCAPED_UNICODE);
    }
}
