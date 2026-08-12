<?php

namespace App\Http\Controllers\API\V1\Crowd;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Crowd\CrowdAnalytics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $analytics = CrowdAnalytics::where('company_id', $this->companyId($request))
            ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
            ->when($request->query('date'), fn ($q, $v) => $q->where('analytics_date', $v))
            ->orderBy('analytics_date')
            ->orderBy('hour')
            ->get();

        return response()->json($analytics);
    }

    public function heatmap(Request $request): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        $data = CrowdAnalytics::where('company_id', $this->companyId($request))
            ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
            ->where('analytics_date', $date)
            ->orderBy('hour')
            ->get(['hour', 'total_devotees', 'congestion_level', 'heatmap_data']);

        return response()->json($data);
    }
}
