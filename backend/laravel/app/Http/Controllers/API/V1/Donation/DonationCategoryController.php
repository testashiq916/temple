<?php

namespace App\Http\Controllers\API\V1\Donation;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Donation\DonationCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationCategoryController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            DonationCategory::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $category = DonationCategory::create($data);

        return response()->json($category, 201);
    }
}
