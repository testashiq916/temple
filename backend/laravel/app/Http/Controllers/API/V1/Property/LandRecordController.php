<?php

namespace App\Http\Controllers\API\V1\Property;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Property\LandRecord;
use App\Models\Property\TempleProperty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandRecordController extends CompanyScopedController
{
    public function index(Request $request, TempleProperty $property): JsonResponse
    {
        abort_unless($property->company_id === $this->companyId($request), 403);

        return response()->json($property->landRecords);
    }

    public function store(Request $request, TempleProperty $property): JsonResponse
    {
        abort_unless($property->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'survey_number' => ['nullable', 'string', 'max:100'],
            'khata_number' => ['nullable', 'string', 'max:100'],
            'plot_number' => ['nullable', 'string', 'max:100'],
            'village' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'area_hectares' => ['nullable', 'numeric'],
            'record_date' => ['nullable', 'date'],
        ]);

        $data['company_id'] = $property->company_id;
        $data['temple_id'] = $property->temple_id;
        $data['property_id'] = $property->id;
        $data['record_id'] = 'LAND-'.Str::upper(Str::random(8));

        $record = LandRecord::create($data);

        return response()->json($record, 201);
    }
}
