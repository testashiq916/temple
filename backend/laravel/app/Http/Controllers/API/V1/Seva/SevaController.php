<?php

namespace App\Http\Controllers\API\V1\Seva;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Seva\SevaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SevaController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $sevas = $this->paginate(
            SevaService::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->when($request->query('category_id'), fn ($q, $v) => $q->where('category_id', $v))
                ->where('is_active', true)
                ->with('category'),
            $request
        );

        return response()->json($sevas);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'category_id' => ['required', 'exists:seva_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sanskrit_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'procedure' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'gst_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'max_devotees' => ['nullable', 'integer', 'min:1'],
            'requires_approval' => ['nullable', 'boolean'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['seva_id'] = 'SEVA-'.Str::upper(Str::random(8));
        $data['created_by'] = $request->user()->id;

        $seva = SevaService::create($data);

        return response()->json($seva, 201);
    }

    public function show(Request $request, SevaService $seva): JsonResponse
    {
        abort_unless($seva->company_id === $this->companyId($request), 403);

        return response()->json($seva->load('category', 'slots'));
    }

    public function update(Request $request, SevaService $seva): JsonResponse
    {
        abort_unless($seva->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $seva->update($data);

        return response()->json($seva);
    }

    public function destroy(Request $request, SevaService $seva): JsonResponse
    {
        abort_unless($seva->company_id === $this->companyId($request), 403);
        $seva->delete();

        return response()->json(null, 204);
    }
}
