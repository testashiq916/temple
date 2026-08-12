<?php

namespace App\Http\Controllers\API\V1\Staff;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Staff\StaffPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffPositionController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            StaffPosition::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'name' => ['required', 'string', 'max:100'],
            'salary_range_min' => ['nullable', 'numeric'],
            'salary_range_max' => ['nullable', 'numeric'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $position = StaffPosition::create($data);

        return response()->json($position, 201);
    }
}
