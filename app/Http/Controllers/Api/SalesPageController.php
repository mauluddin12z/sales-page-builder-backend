<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesPage;
use Illuminate\Http\Request;

class SalesPageController extends Controller
{
    public function index(Request $request)
    {
        return SalesPage::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'description' => 'required|string',
            'features' => 'required|array',
            'target_audience' => 'required|string',
            'price' => 'nullable|string',
            'usp' => 'nullable|string',
            'generated_content' => 'nullable|string',
            'template' => 'nullable|string',
            'cta_label' => 'nullable|string',
            'cta_url' => 'nullable|url',
        ]);

        $validated['user_id'] = $request->user()->id ?? $request->user_id;
        // features handled automatically via cast (no json_encode needed)

        $salesPage = SalesPage::create($validated);

        return response()->json([
            'message' => 'Created successfully',
            'data' => $salesPage
        ], 201);
    }

    public function show(Request $request, int $id)
    {
        $salesPage = SalesPage::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return $salesPage;
    }

    public function update(Request $request, int $id)
    {
        $salesPage = SalesPage::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            'product_name' => 'required|string',
            'description' => 'required|string',
            'features' => 'required|array',
            'target_audience' => 'required|string',
            'price' => 'nullable|string',
            'usp' => 'nullable|string',
            'generated_content' => 'nullable|string',
            'template' => 'nullable|string',
            'cta_label' => 'nullable|string',
            'cta_url' => 'nullable|url',
        ]);

        $salesPage->update($validated);

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $salesPage
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $salesPage = SalesPage::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $salesPage->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
