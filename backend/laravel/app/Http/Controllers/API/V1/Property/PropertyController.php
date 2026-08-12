<?php

namespace App\Http\Controllers\API\V1\Property;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Property\TempleProperty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PropertyController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $properties = $this->paginate(
            TempleProperty::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->when($request->query('property_type'), fn ($q, $v) => $q->where('property_type', $v))
                ->latest(),
            $request
        );

        return response()->json($properties);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'name' => ['required', 'string', 'max:255'],
            'property_type' => ['required', 'in:land,building,commercial,residential,agricultural,mixed'],
            'address' => ['nullable', 'string'],
            'total_area' => ['nullable', 'numeric'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric'],
            'current_value' => ['nullable', 'numeric'],
            'ownership_type' => ['nullable', 'in:temple,trust,lease'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['property_id'] = 'PROP-'.Str::upper(Str::random(8));
        $data['created_by'] = $request->user()->id;

        $property = TempleProperty::create($data);

        return response()->json($property, 201);
    }

    public function show(Request $request, TempleProperty $property): JsonResponse
    {
        abort_unless($property->company_id === $this->companyId($request), 403);

        return response()->json($property->load('landRecords', 'tenants'));
    }

    public function update(Request $request, TempleProperty $property): JsonResponse
    {
        abort_unless($property->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'current_value' => ['nullable', 'numeric'],
            'status' => ['nullable', 'in:active,inactive,under_maintenance,disposed'],
        ]);

        $property->update($data);

        return response()->json($property);
    }
}
