<?php

namespace App\Http\Controllers\API\V1\Seva;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Seva\SevaCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SevaCategoryController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            SevaCategory::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->orderBy('sort_order')
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'name' => ['required', 'string', 'max:255'],
            'sanskrit_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $category = SevaCategory::create($data);

        return response()->json($category, 201);
    }

    public function update(Request $request, SevaCategory $sevaCategory): JsonResponse
    {
        abort_unless($sevaCategory->company_id === $this->companyId($request), 403);
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:255'], 'is_active' => ['nullable', 'boolean']]);
        $sevaCategory->update($data);

        return response()->json($sevaCategory);
    }
}
