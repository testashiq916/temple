<?php

namespace App\Http\Controllers\API\V1\Inventory;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Inventory\InventoryCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryCategoryController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            InventoryCategory::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $category = InventoryCategory::create($data);

        return response()->json($category, 201);
    }
}
