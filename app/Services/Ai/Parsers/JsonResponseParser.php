<?php

namespace App\Services\Ai\Parsers;

use App\Services\Ai\Exceptions\AiException;

class JsonResponseParser
{
    public function parse(string $text): array
    {
        $text = trim($text);

        /*
        | Remove Markdown
        */

        $text = preg_replace('/^```json|```$/mi', '', $text);

        /*
        | Extract JSON Object
        */

        preg_match('/\{.*\}/s', $text, $matches);

        if (!isset($matches[0])) {
            throw new AiException('No valid JSON object found');
        }

        $decoded = json_decode($matches[0], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new AiException(
                'Invalid AI JSON: ' . json_last_error_msg()
            );
        }

        return $decoded;
    }
}
