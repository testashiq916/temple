<?php

namespace App\Http\Controllers\API\V1\Temple;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Temple\Festival;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FestivalController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $festivals = $this->paginate(
            Festival::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->orderBy('start_date'),
            $request
        );

        return response()->json($festivals);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'name' => ['required', 'string', 'max:255'],
            'sanskrit_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'significance' => ['nullable', 'string'],
            'festival_type' => ['nullable', 'in:annual,monthly,weekly,special'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_recurring' => ['nullable', 'boolean'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['festival_id'] = 'FEST-'.Str::upper(Str::random(8));

        $festival = Festival::create($data);

        return response()->json($festival, 201);
    }

    public function show(Request $request, Festival $festival): JsonResponse
    {
        abort_unless($festival->company_id === $this->companyId($request), 403);

        return response()->json($festival);
    }

    public function update(Request $request, Festival $festival): JsonResponse
    {
        abort_unless($festival->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $festival->update($data);

        return response()->json($festival);
    }

    public function destroy(Request $request, Festival $festival): JsonResponse
    {
        abort_unless($festival->company_id === $this->companyId($request), 403);
        $festival->delete();

        return response()->json(null, 204);
    }
}
