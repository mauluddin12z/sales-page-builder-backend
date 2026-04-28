<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesPage;
use Illuminate\Http\Request;

class SalesPageController extends Controller
{
    public function index()
    {
        return SalesPage::latest()->paginate(10);
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
            'generated_content' => 'required|string',
            'template' => 'required|string',
        ]);

        $validated['user_id'] = $request->user()->id ?? $request->user_id;
        // features handled automatically via cast (no json_encode needed)

        $salesPage = SalesPage::create($validated);

        return response()->json([
            'message' => 'Created successfully',
            'data' => $salesPage
        ], 201);
    }

    public function show(SalesPage $salesPage)
    {
        return $salesPage;
    }

    public function update(Request $request, SalesPage $salesPage)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'description' => 'required|string',
            'features' => 'required|array',
            'target_audience' => 'required|string',
            'price' => 'nullable|string',
            'usp' => 'nullable|string',
            'template' => 'required|string',
        ]);

        $salesPage->update($validated);

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $salesPage
        ]);
    }

    public function destroy(SalesPage $salesPage)
    {
        $salesPage->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
