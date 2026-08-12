<?php

namespace App\Http\Controllers\API\V1\Temple;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Temple\Deity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeityController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $deities = $this->paginate(
            Deity::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->latest(),
            $request
        );

        return response()->json($deities);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'name' => ['required', 'string', 'max:255'],
            'sanskrit_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'consort_name' => ['nullable', 'string', 'max:255'],
            'vehicle' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:50'],
            'mantra' => ['nullable', 'string'],
            'significance' => ['nullable', 'string'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['deity_id'] = 'DEITY-'.Str::upper(Str::random(8));

        $deity = Deity::create($data);

        return response()->json($deity, 201);
    }

    public function show(Request $request, Deity $deity): JsonResponse
    {
        abort_unless($deity->company_id === $this->companyId($request), 403);

        return response()->json($deity);
    }

    public function update(Request $request, Deity $deity): JsonResponse
    {
        abort_unless($deity->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'significance' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $deity->update($data);

        return response()->json($deity);
    }

    public function destroy(Request $request, Deity $deity): JsonResponse
    {
        abort_unless($deity->company_id === $this->companyId($request), 403);
        $deity->delete();

        return response()->json(null, 204);
    }
}
