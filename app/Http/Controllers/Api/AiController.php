<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Gemini;

class AiController extends Controller
{
    protected $client;

    public function __construct()
    {
        $apiKey = env('GEMINI_API_KEY');
        $this->client = Gemini::client($apiKey);
    }

    /**
     * FULL generation
     */
    public function generate(Request $request)
    {
        $validated = $this->validateInput($request);
        $model = env('GEMINI_MODEL');
        try {
            $prompt = $this->buildFullPrompt($validated);

            // DEBUG MODE: return prompt only
            if (env('AI_DEBUG_PROMPT', false)) {
                return response()->json([
                    'success' => true,
                    'debug' => true,
                    'prompt' => $prompt,
                ]);
            }

            // MOCK MODE: no Gemini usage
            if (env('AI_MOCK', false)) {
                return response()->json([
                    'success' => true,
                    'mock' => true,
                    'text' => [
                        "headline" => "Mock Headline: {$validated['product_name']}",
                        "subheadline" => "Mock Subheadline for testing",
                        "description" => "This is a mocked description for development purposes.",
                        "benefits" => ["Mock benefit 1", "Mock benefit 2"],
                        "features" => $validated['features'] ?? [],
                        "social_proof" => "Mock testimonial: users love it",
                        "pricing" => $validated['price'] ?? "N/A",
                        "cta" => "Buy Now (Mock)"
                    ]
                ]);
            }

            // REAL GEMINI CALL
            $result = $this->client
                ->generativeModel(model: $model)
                ->generateContent($prompt);

            return response()->json([
                'success' => true,
                'text' => $result->text(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI Generation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PARTIAL regeneration (single field)
     * Example: description, headline, cta, etc.
     */
    public function regenerate(Request $request)
    {
        $model = env('GEMINI_MODEL');
        $validated = $this->validateInput($request);

        $request->validate([
            'field' => 'required|string|in:headline,subheadline,description,benefits,features,social_proof,pricing,cta',
            'current_output' => 'required|array'
        ]);

        try {
            $prompt = $this->buildRegenerationPrompt(
                $validated,
                $request->field,
                $request->current_output
            );

            // DEBUG MODE
            if (env('AI_DEBUG_PROMPT', false)) {
                return response()->json([
                    'success' => true,
                    'debug' => true,
                    'prompt' => $prompt,
                ]);
            }

            // MOCK MODE (simulate edited field only)
            if (env('AI_MOCK', false)) {
                $mock = $request->current_output;
                $mock[$request->field] = "MOCK UPDATED: {$request->field} content";

                return response()->json([
                    'success' => true,
                    'mock' => true,
                    'text' => $mock
                ]);
            }

            //  REAL GEMINI CALL
            $result = $this->client
                ->generativeModel(model: $model)
                ->generateContent($prompt);

            return response()->json([
                'success' => true,
                'text' => $result->text(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI Regeneration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Shared validation
     */
    private function validateInput($request)
    {
        return $request->validate([
            'product_name'     => 'required|string',
            'description'      => 'required|string',
            'target_audience'  => 'required|string',
            'features'         => 'nullable|array',
            'price'            => 'nullable|string',
            'usp'              => 'nullable|string',
        ]);
    }

    /**
     * FULL generation prompt
     */
    private function buildFullPrompt($data): string
    {
        $features = json_encode($data['features'] ?? []);

        return "
You are a world-class direct-response copywriter.

Return ONLY valid JSON. No markdown. No explanations.

Schema:
{
  \"headline\": \"string\",
  \"subheadline\": \"string\",
  \"description\": \"string\",
  \"benefits\": [\"string\"],
  \"features\": [\"string\"],
  \"social_proof\": \"string\",
  \"pricing\": \"string\",
  \"cta\": \"string\"
}

Rules:
- Valid JSON only
- All fields required
- Conversion-focused copy

Product Info:
Name: {$data['product_name']}
Description: {$data['description']}
Target audience: {$data['target_audience']}
Features: {$features}
Price: {$data['price']}
USP: {$data['usp']}
";
    }

    /**
     * FIELD-LEVEL regeneration prompt
     */
    private function buildRegenerationPrompt($data, $field, $current): string
    {
        $features = json_encode($data['features'] ?? []);

        $currentJson = json_encode($current, JSON_PRETTY_PRINT);

        return "
You are an expert direct-response copy editor.

You MUST modify ONLY ONE FIELD and keep everything else EXACTLY the same.

Return ONLY valid JSON.

CRITICAL RULES:
- Output must be valid JSON only
- Do NOT change any field except: {$field}
- Keep all other values identical
- No markdown, no explanation, no backticks

CURRENT OUTPUT:
{$currentJson}

Product Info:
Name: {$data['product_name']}
Description: {$data['description']}
Target audience: {$data['target_audience']}
Features: {$features}
Price: {$data['price']}
USP: {$data['usp']}

TASK:
Regenerate ONLY the \"{$field}\" field to improve conversion quality.
";
    }
}
