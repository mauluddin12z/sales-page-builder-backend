<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Ai\GeminiAiService;
use Illuminate\Http\JsonResponse;

class AiController extends Controller
{
    public function __construct(
        protected GeminiAiService $ai
    ) {}

    /**
     * FULL GENERATION
     */
    public function generate(Request $request): JsonResponse
    {
        $data = $this->validateInput($request);

        return $this->handleAiRequest(
            fn() => $this->buildFullPrompt($data),
            null
        );
    }

    /**
     * PARTIAL REGENERATION
     */
    public function regenerate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'field' => 'required|string|in:headline,subheadline,description,benefits,features,social_proof,pricing,cta',
            'current_output' => 'required',
            'product_name' => 'required|string',
            'description' => 'required|string',
            'target_audience' => 'required|string',
            'features' => 'nullable|array',
            'price' => 'nullable|string',
            'usp' => 'nullable|string',
        ]);

        $current = $this->parseCurrentOutput($validated['current_output']);
        $field = $validated['field'];

        if (!array_key_exists($field, $current)) {
            return $this->error("Field '{$field}' not found", 422);
        }

        return $this->handleAiRequest(
            fn() => $this->buildRegenerationPrompt($validated, $field, $current),
            $field
        );
    }

    /**
     * CENTRAL AI HANDLER (CLEAN)
     */
    private function handleAiRequest(callable $promptBuilder, ?string $requiredKey): JsonResponse
    {
        try {
            $prompt = $promptBuilder();

            $response = $this->ai->generate($prompt);

            $clean = $this->cleanJson($response['text'] ?? '');
            $data = $this->decodeJson($clean);

            if ($requiredKey && !array_key_exists($requiredKey, $data)) {
                return $this->error('AI missing required field', 500);
            }

            return $this->success([
                'data' => $data,
                'meta' => [
                    'model' => $response['model_used'] ?? null,
                    'key' => $response['api_key_index'] ?? null,
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->error('AI request failed', 503);
        }
    }

    /**
     * CLEAN JSON
     */
    private function cleanJson(string $text): string
    {
        return trim(preg_replace('/```json|```/i', '', $text));
    }

    /**
     * SAFE JSON DECODE
     */
    private function decodeJson(string $text): array
    {
        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON from AI");
        }

        return $decoded;
    }

    /**
     * VALIDATION
     */
    private function validateInput(Request $request): array
    {
        return $request->validate([
            'product_name' => 'required|string',
            'description' => 'required|string',
            'target_audience' => 'required|string',
            'features' => 'nullable|array',
            'price' => 'nullable|string',
            'usp' => 'nullable|string',
        ]);
    }

    /**
     * PARSE CURRENT OUTPUT
     */
    private function parseCurrentOutput($input): array
    {
        if (is_array($input)) return $input;

        $decoded = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            abort(response()->json([
                'success' => false,
                'message' => 'Invalid current_output JSON',
            ], 422));
        }

        return $decoded;
    }

    /**
     * FULL PROMPT
     */
    private function buildFullPrompt(array $data): string
    {
        return json_encode([
            'instruction' => [
                'role' => 'world-class direct-response copywriter',
                'rules' => [
                    'Return ONLY valid JSON',
                    'Do NOT use markdown',
                    'Do NOT wrap in backticks'
                ]
            ],
            'output_schema' => [
                "headline" => "string",
                "subheadline" => "string",
                "description" => "string",
                "benefits" => ["string"],
                "features" => ["string"],
                "social_proof" => "string",
                "pricing" => "string",
                "cta" => "string"
            ],
            'input' => $data
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * REGEN PROMPT
     */
    private function buildRegenerationPrompt(array $data, string $field, array $current): string
    {
        $arrayFields = ['features', 'benefits'];
        $type = in_array($field, $arrayFields) ? 'array of strings' : 'string';

        return json_encode([
            'instruction' => [
                'task' => "Modify ONLY '{$field}'",
                'rules' => [
                    'Keep all other fields identical',
                    'Return ONLY valid JSON',
                    'No markdown, no backticks'
                ]
            ],
            'expected_type' => $type,
            'current_output' => $current,
            'input' => [
                'product_name' => $data['product_name'],
                'description' => $data['description'],
                'target_audience' => $data['target_audience'],
                'features' => $data['features'] ?? [],
                'price' => $data['price'] ?? '',
                'usp' => $data['usp'] ?? '',
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * MOCK REMOVED
     */

    /**
     * RESPONSE HELPERS
     */
    private function success(array $data): JsonResponse
    {
        return response()->json(['success' => true] + $data);
    }

    private function error(string $message, int $code): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }
}
