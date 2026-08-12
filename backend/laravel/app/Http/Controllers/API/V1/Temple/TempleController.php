<?php

namespace App\Http\Controllers\API\V1\Temple;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Temple\Temple;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TempleController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $temples = $this->paginate(
            Temple::where('company_id', $this->companyId($request))
                ->with(['deities', 'festivals'])
                ->latest(),
            $request
        );

        return response()->json($temples);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sanskrit_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'history' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'website' => ['nullable', 'string', 'max:255'],
            'established_year' => ['nullable', 'integer'],
            'deity_name' => ['nullable', 'string', 'max:255'],
            'deity_description' => ['nullable', 'string'],
            'temple_type' => ['nullable', 'in:jyotirlinga,shaktipeeth,divyadesam,pancha_linga,vaishno,other'],
            'capacity' => ['nullable', 'integer'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['temple_id'] = 'TMPL-'.Str::upper(Str::random(8));
        $data['created_by'] = $request->user()->id;

        $temple = Temple::create($data);

        return response()->json($temple, 201);
    }

    public function show(Request $request, Temple $temple): JsonResponse
    {
        $this->authorizeTemple($request, $temple);

        return response()->json($temple->load(['deities', 'festivals', 'images']));
    }

    public function update(Request $request, Temple $temple): JsonResponse
    {
        $this->authorizeTemple($request, $temple);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'sanskrit_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'history' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'website' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $temple->update($data);

        return response()->json($temple);
    }

    public function destroy(Request $request, Temple $temple): JsonResponse
    {
        $this->authorizeTemple($request, $temple);
        $temple->delete();

        return response()->json(null, 204);
    }

    private function authorizeTemple(Request $request, Temple $temple): void
    {
        abort_unless($temple->company_id === $this->companyId($request), 403);
    }
}
