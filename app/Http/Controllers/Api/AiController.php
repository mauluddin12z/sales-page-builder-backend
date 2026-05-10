<?php

namespace App\Http\Controllers\Api;

use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\Ai\AiManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateLandingPageRequest;
use App\Http\Requests\RegenerateLandingPageRequest;
use App\Services\Ai\Prompts\LandingPagePromptBuilder;

class AiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Generate Full Landing Page
    |--------------------------------------------------------------------------
    */

    public function generate(
        GenerateLandingPageRequest $request,
        AiManager $ai,
    ): JsonResponse {

        try {

            $prompt = LandingPagePromptBuilder::full(
                $request->validated()
            );

            $result = $ai->generate($prompt);

            return response()->json([

                'success' => true,

                'data' => $result,

            ]);

        } catch (Throwable $e) {

            Log::error('AI generate failed', [

                'error' => $e->getMessage(),

            ]);

            return response()->json([

                'success' => false,

                'message' => 'AI generation failed',

            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Regenerate Specific Field
    |--------------------------------------------------------------------------
    */

    public function regenerate(
        RegenerateLandingPageRequest $request,
        AiManager $ai,
    ): JsonResponse {

        try {

            $validated = $request->validated();

            $prompt = LandingPagePromptBuilder::regenerate(
                input: $validated,
                field: $validated['field'],
                current: $validated['current_output'],
            );

            $result = $ai->generate($prompt);

            return response()->json([

                'success' => true,

                'data' => $result,

            ]);

        } catch (Throwable $e) {

            Log::error('AI regenerate failed', [

                'error' => $e->getMessage(),

            ]);

            return response()->json([

                'success' => false,

                'message' => 'AI regeneration failed',

            ], 500);
        }
    }
}
