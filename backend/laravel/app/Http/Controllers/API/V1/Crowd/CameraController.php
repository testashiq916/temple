<?php

namespace App\Http\Controllers\API\V1\Crowd;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Crowd\AiDetectionAlert;
use App\Models\Crowd\CctvCamera;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CameraController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $cameras = CctvCamera::where('company_id', $this->companyId($request))
            ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
            ->get();

        return response()->json($cameras);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'camera_type' => ['nullable', 'in:fixed,ptz,ai'],
            'rtsp_url' => ['nullable', 'string', 'max:255'],
            'ai_enabled' => ['nullable', 'boolean'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['camera_id'] = 'CAM-'.Str::upper(Str::random(8));

        $camera = CctvCamera::create($data);

        return response()->json($camera, 201);
    }

    public function alerts(Request $request, CctvCamera $camera): JsonResponse
    {
        abort_unless($camera->company_id === $this->companyId($request), 403);

        return response()->json($camera->alerts()->latest()->get());
    }

    public function resolveAlert(Request $request, AiDetectionAlert $alert): JsonResponse
    {
        abort_unless($alert->company_id === $this->companyId($request), 403);
        $alert->update(['is_resolved' => true, 'resolved_at' => now()]);

        return response()->json($alert);
    }
}
