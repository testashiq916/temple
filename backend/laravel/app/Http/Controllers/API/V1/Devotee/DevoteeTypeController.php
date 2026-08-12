<?php

namespace App\Http\Controllers\API\V1\Devotee;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Devotee\DevoteeType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DevoteeTypeController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            DevoteeType::where('company_id', $this->companyId($request))
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
        $type = DevoteeType::create($data);

        return response()->json($type, 201);
    }
}
