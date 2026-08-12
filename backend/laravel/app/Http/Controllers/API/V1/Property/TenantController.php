<?php

namespace App\Http\Controllers\API\V1\Property;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Property\PropertyTenant;
use App\Models\Property\TempleProperty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends CompanyScopedController
{
    public function index(Request $request, TempleProperty $property): JsonResponse
    {
        abort_unless($property->company_id === $this->companyId($request), 403);

        return response()->json($property->tenants);
    }

    public function store(Request $request, TempleProperty $property): JsonResponse
    {
        abort_unless($property->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'business_type' => ['nullable', 'string', 'max:100'],
        ]);

        $data['company_id'] = $property->company_id;
        $data['temple_id'] = $property->temple_id;
        $data['property_id'] = $property->id;
        $data['tenant_id'] = 'TEN-'.Str::upper(Str::random(8));

        $tenant = PropertyTenant::create($data);

        return response()->json($tenant, 201);
    }

    public function update(Request $request, TempleProperty $property, PropertyTenant $tenant): JsonResponse
    {
        abort_unless($property->company_id === $this->companyId($request), 403);
        abort_unless($tenant->property_id === $property->id, 404);

        $data = $request->validate(['status' => ['required', 'in:active,inactive,evicted']]);
        $tenant->update($data);

        return response()->json($tenant);
    }
}
